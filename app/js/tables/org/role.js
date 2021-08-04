/* global DATE_FORMAT,
 SARON_URI, SARON_IMAGES_URI, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
ORG,
TABLE_VIEW_ROLE, TABLE_NAME_ROLE, 
TABLE_VIEW_UNITTYPE, TABLE_NAME_UNITTYPE,
TABLE_VIEW_UNIT, TABLE_NAME_UNIT,
TABLE_VIEW_UNITLIST, TABLE_NAME_UNITLIST,
TABLE_VIEW_UNITTREE, TABLE_NAME_UNITTREE,
RECORDS, RECORD, OPTIONS, SOURCE_LIST, SOURCE_CREATE, SOURCE_EDIT
*/
 
"use strict";

$(document).ready(function () {
        $(TABLE_VIEW_ROLE).jtable(roleTableDef(TABLE_VIEW_ROLE, null, -1, null));
        $(TABLE_VIEW_ROLE).jtable('load');
    }
);



function roleTableDef(tableViewId, parentTablePath, parentId, childTableTitle){
    const tableName = TABLE_NAME_ROLE;
    var tablePath = tableName;
    if(parentTablePath !== null)
        tablePath = parentTablePath + "/" + tableName;

    return {
        title: function (){
            if(childTableTitle !== null)
                return childTableTitle;
            else
                return 'Roller';
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Lägg till ny roll'},
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?ParentId=' + parentId + '&TablePath=' + tablePath + "&ResultType=" + RECORDS,
            createAction: '/' + SARON_URI + 'app/web-api/createOrganizationRole.php',
            updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationRole.php',  
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationRole.php'
        },
        fields: {
            TablePath:{
                list: true,
                edit: false,
                create: false
            },
            Id: {
                key: true,
                list: false
            },
            UsedInUnit:{
                width: '5%',
                create: false,
                title: "Används",
                edit: false,
                list: includedIn(tableViewId, TABLE_VIEW_ROLE),
                sorting: false,
                display: function(data){                    
                    var srcChild;
                    if(data.record.HasChild === '0'){
                        srcChild = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit_empty.png" title="Inga organisatoriska enheter"';
                    }
                    else{
                        srcChild = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit.png" title="Organisatoriska enheter"';
                    }
                    
                    var imgTagChild = _setImageClass(data.record, TABLE_NAME_UNIT, srcChild, data.record.Id);
                    var $imgChild = $(imgTagChild);
                    const tag = TABLE_NAME_ROLE + '_Child_' +  data.record.Id;
                    $imgChild.click(data, function (event){
                        var $tr = $imgChild.closest('tr'); 
                        $tr.removeClass(TABLE_NAME_ROLE + '_Child_' +  data.record.Id + ' ' + TABLE_NAME_UNITTYPE + '_' + data.record.Id);
                        $tr.addClass(tag);
                        
                        var childTableTitleUsedInUnit = 'Rollen "' + data.record.Name + '" används i nedanstående organisatoriska enheter';
                        $(tableViewId).jtable('openChildTable', $tr, unitTableDef(tableViewId, tablePath,  data.record.Id, childTableTitleUsedInUnit), function(data){
                            data.childTable.jtable('load');
                            updateRoleRecord(tableViewId, event.data);
                        });
                    });
                    
                    var srcClose = '"/' + SARON_URI + SARON_IMAGES_URI + 'cross.png" title="Stäng"';
                    var imgTagClose= _setImageClass(data.record, TABLE_VIEW_ROLE, srcClose, data.record.Id);
                    var $imgClose = $(imgTagClose);

                    $imgClose.click(data, function(event) {
                        var $tr = $imgClose.closest('tr'); 
                        $tr.removeClass(TABLE_NAME_UNIT + '_' +  data.record.Id + ' ' + TABLE_NAME_UNITTYPE + '_' + data.record.Id);
                        var $currentRow = $(tableViewId).jtable('getRowByKey', data.record.Id);
                        $(tableViewId).jtable('closeChildTable', $currentRow, function(data){  
                            updateRoleRecord(tableViewId, event.data);
                        });
                    });     

                    var isChildRowOpen = $("." + TABLE_NAME_UNITTYPE + '_Child_' +  data.record.Id).length > 0;
                    
                    if(isChildRowOpen)
                        return $imgClose;
                    else
                        return $imgChild;
                }               
            },
            Org:{
                width: '5%',
                create: false,
                title: "Ingår i",
                edit: false,
                list: includedIn(tableViewId, TABLE_VIEW_ROLE),
                sorting: false,
                display: function(data){
                    var childTableTitleOrg = 'Rollen "' + data.record.Name + '" används i nedanstående organisatoriska enhetstyper';

                    var src;
                    if(data.record.HasChild === '0'){
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'unittype.png" title="Inga organisatoriska enhetstyper"';
                    }
                    else{
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'used_unittype.png" title="Organisatoriska enhetstyper"';
                    }
                    
                    var imgTag = _setImageClass(data.record, TABLE_NAME_ROLE, src, data.record.Id, ORG);
                    var $imgChild = $(imgTag);
                    
                    $imgChild.click(data, function (event){
                        var $tr = $imgChild.closest('tr');
                        $tr.removeClass(_getClassName_Id(data.record, TABLE_NAME_UNIT, ORG) + ' ' + _getClassName_Id(data.record, TABLE_NAME_UNITTYPE, ORG));
                        $tr.addClass(_getClassName_Id(data.record, TABLE_NAME_UNITTYPE, ORG));
                        $(tableViewId).jtable('openChildTable', $tr, unitTypeTableDef(tableViewId, tablePath, data.record.Id, childTableTitleOrg), function(data){
                            data.childTable.jtable('load');
                            updateRoleRecord(tableViewId, event.data);
                        });
                    });

                    var srcClose = '"/' + SARON_URI + SARON_IMAGES_URI + 'cross.png" title="Stäng"';
                    var imgTagClose= _setImageClass(data.record, TABLE_NAME_UNITTYPE, srcClose, data.record.Id, ORG);
                    var $imgClose = $(imgTagClose);

                    $imgClose.click(data, function(event) {
                        var $tr = $imgClose.closest('tr');
                        $tr.removeClass(_getClassName_Id(data.record, TABLE_NAME_UNIT, ORG) + ' ' + _getClassName_Id(data.record, TABLE_NAME_UNITTYPE, ORG));
                        var $currentRow = $(tableViewId).jtable('getRowByKey', data.record.Id);
                        $(tableViewId).jtable('closeChildTable', $currentRow, function(data){  
                            updateRoleRecord(tableViewId, event.data);
                        });
                    });     


                    var isChildRowOpen = $("." + _getClassName_Id(data.record, TABLE_NAME_UNITTYPE, ORG)).length > 0;
                    if(isChildRowOpen)
                        return $imgClose;
                    else
                        return $imgChild;
                }
            },
            Name: {
                title: 'Benämning',
                width: '15%'
            },
            PosOccurrency:{
                edit: false,
                create: false,
                title: "Antal positioner",
                width: "10%",
                display: function (data){
                    return _setClassAndValue(data.record, "PosOccurrency", -1);
                }                  
            },
            Description: {
                title: 'Beskrivning',
                width: '40%'
            },
            RoleType:{
                edit: false,
                title: 'Typ',
                width: '10%',
                options:  { '0' : 'Verksamhetsroll', '1' : 'Organisationsroll [Org]' }
            },
            UpdaterName: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '5%'
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: DATE_FORMAT,
                width: '5%'
            }
        },
        recordUpdated(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            else
                if (data.record.HasChild === '0')
                    data.row.find('.jtable-delete-command-button').show();
                else
                    data.row.find('.jtable-delete-command-button').hide();
            
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if (data.record.HasChild !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            
            addDialogDeleteListener(data);
            
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        }
    };
}



function updateRoleRecord(tableViewId, data){
    var url = '/' + SARON_URI + 'app/web-api/listOrganizationRole.php';
    var options = {record:{"Id": data.record.Id}, "clientOnly": false, "url":url};
    $(tableViewId).jtable('updateRecord', options);
}
