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
                    
                    var $imgChild=null;                    
                    if(data.record.HasChild === '0')
                        $imgChild = getImageTag(data, "unit_empty.png", "Inga organisatoriska enheter", TABLE_NAME_UNIT);
                    else
                        $imgChild = getImageTag(data, "unit.png", "Organisatoriska enheter", TABLE_NAME_UNIT);
                    
                    var allOpenClasses = getChildOpenClass(data, TABLE_NAME_UNIT) + ' ' + getChildOpenClass(data, TABLE_NAME_UNITTYPE);
                    var currentOpenClass = getChildOpenClass(data, TABLE_NAME_UNIT);
                    
                    $imgChild.click(data, function (event){
                        var $tr = $imgChild.closest('tr'); 
                        $tr.removeClass(allOpenClasses);
                        $tr.addClass(currentOpenClass);
                        
                        var childTableTitleUsedInUnit = 'Rollen "' + data.record.Name + '" används i nedanstående organisatoriska enheter';
                        $(tableViewId).jtable('openChildTable', $tr, unitTableDef(tableViewId, tablePath,  data.record.Id, childTableTitleUsedInUnit), function(data){
                            data.childTable.jtable('load');
                            updateRoleRecord(tableViewId, event.data);
                        });
                    });
                    
                    var $imgClose = getImageCloseTag(data, TABLE_NAME_ROLE);
                    $imgClose.click(data, function(event) {
                        var $tr = $imgChild.closest('tr'); 
                        $tr.removeClass(allOpenClasses);
                        var $currentRow = $(tableViewId).jtable('getRowByKey', data.record.Id);
                        $(tableViewId).jtable('closeChildTable', $currentRow, function(data){  
                            updateRoleRecord(tableViewId, event.data);
                        });
                    });     

                    var isChildRowOpen = $("." + currentOpenClass).length > 0;
                    
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
                    var childTableTitle = 'Rollen "' + data.record.Name + '" används i nedanstående organisatoriska enhetstyper';
                    var $imgChild;

                    if(data.record.HasChild === '0')
                        $imgChild = getImageTag(data, "unittype.png", "Inga organisatoriska enhetstyper", TABLE_NAME_UNITTYPE);
                    else
                        $imgChild = getImageTag(data, "used_unittype.png", "Organisatoriska enhetstyper", TABLE_NAME_UNITTYPE);

                    var allOpenClasses = getChildOpenClass(data, TABLE_NAME_UNIT) + ' ' + getChildOpenClass(data, TABLE_NAME_UNITTYPE);
                    var currentOpenClass = getChildOpenClass(data, TABLE_NAME_UNITTYPE);
                    
                    $imgChild.click(data, function (event){
                        var $tr = $imgChild.closest('tr'); 
                        $tr.removeClass(allOpenClasses);
                        $tr.addClass(currentOpenClass);
                        $(tableViewId).jtable('openChildTable', $tr, unitTypeTableDef(tableViewId, tablePath, data.record.Id, childTableTitle), function(data){
                            data.childTable.jtable('load');
                            updateRoleRecord(tableViewId, event.data);
                        });
                    });

                    var $imgClose = getImageCloseTag(data, TABLE_NAME_ROLE);

                    $imgClose.click(data, function(event) {
                        var $tr = $imgChild.closest('tr'); 
                        $tr.removeClass(allOpenClasses);
                        var $currentRow = $(tableViewId).jtable('getRowByKey', data.record.Id);
                        $(tableViewId).jtable('closeChildTable', $currentRow, function(data){  
                            updateRoleRecord(tableViewId, event.data);
                        });
                    });     

                    var isChildRowOpen = $("." + currentOpenClass).length > 0;
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
