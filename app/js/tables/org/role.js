/* global DATE_FORMAT,
 saron, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
ORG, TABLE,
saron
*/
 
"use strict";
const roleListUri = 'app/web-api/listOrganizationRole.php';

$(document).ready(function () {
    var mainTableViewId = saron.table.role.viewid;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(roleTableDef(mainTableViewId, null, null, null));
    var postData = getPostData(null, mainTableViewId, null, saron.table.role.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});



function roleTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    const tableName = saron.table.role.name;
    var title = "Alla roller";
    
    if(newTableTitle !== null)
        title = newTableTitle;
    
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
    
    return {
        showCloseButton: false,
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),
        title: title,
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Lägg till ny roll'},
        actions: {            
            listAction: '/' + saron.uri.saron + roleListUri,
            createAction: '/' + saron.uri.saron + 'app/web-api/createOrganizationRole.php',
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateOrganizationRole.php',  
            deleteAction: '/' + saron.uri.saron + 'app/web-api/deleteOrganizationRole.php'
        },
        fields: {
            TablePath:{
                list: false,
                edit: false,
                create: false
            },
            Id: {
                key: true,
                list: false,
                edit: false,
                create: false
            },
            ParentId:{
                defaultValue: parentId,
                type: 'hidden'
            },
            UsedInUnit:{
                width: '5%',
                create: false,
                title: "Används",
                edit: false,
                list: includedIn(mainTableViewId, saron.table.role.viewid),
                sorting: false,
                display: function(data){
                    var childTableName = saron.table.unit.name;
                    var childTableTitle = 'Rollen "' + data.record.Name + '" ingår i följande organisatoriska enheter';
                    var tooltip = "";
                    var imgFile = "";
                    var url = 'app/web-api/listOrganizationRole.php';
                    var parentId = data.record.Id;
                    var type = 0;
                    var clientOnly = false;

                    if(data.record.UsedInUnit === '0'){
                        imgFile = "unit_empty.png";
                        tooltip = "Inga organisatoriska enheter";
                    }
                    else{
                        imgFile = "unit.png";
                        tooltip = "Organisatoriska enheter";
                    }

                    var childTableDef = unitTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);

                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event.data, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event.data, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose);
                }               
            },
            UsedInUnitType:{
                width: '5%',
                create: false,
                title: "Ingår i",
                edit: false,
                list: includedIn(mainTableViewId, saron.table.role.viewid),
                sorting: false,
                display: function(data){
                    var childTableName = saron.table.unittype.name;
                    var childTableTitle = 'Rollen "' + data.record.Name + '" ingår i följande enhetstyper';
                    var tooltip = "";
                    var imgFile = "";
                    var parentId = data.record.Id;
                    var url = 'app/web-api/listOrganizationRole.php';
                    var parentId = data.record.Id;
                    var type = 0;
                    var clientOnly = false;

                    if(data.record.UsedInUnitType === '0'){
                        imgFile = "unittype.png";
                        tooltip = "Inga organisatoriska enhetstyper";
                    }
                    else{
                        imgFile = "used_unittype.png";
                        tooltip = "Organisatoriska enhetstyper";
                    }
                    var childTableDef = role_role_unitType_TableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);

                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event.data, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event.data, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose);
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
                    return _setClassAndValue(data, "PosOccurrency", ORG);
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
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
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
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if (data.record.HasChild !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            
            addDialogDeleteListener(data);
            
        },        
        recordsLoaded: function(event, data) {
            var addButton = $(event.target).find('.jtable-toolbar-item-add-record');
            
            if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org'){ 
                addButton.show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };
}

