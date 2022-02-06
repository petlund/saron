/* global DATE_FORMAT,
 saron, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
ORG, TABLE,
saron
*/
 
"use strict";
const roleListUri = 'app/web-api/listOrganizationRole.php';

$(document).ready(function () {
        $(saron.table.role.viewid).jtable(roleTableDef(saron.table.role.viewid, saron.table.role.name, null, null));
        var options = getPostData(null, saron.table.role.viewid, null, saron.table.role.name, saron.source.list, saron.responsetype.records, roleListUri);
        $(saron.table.role.viewid).jtable('load', options);
    }
);



function roleTableDef(tableViewId, tablePath, childTableTitle, parentId){
    const tableName = saron.table.role.name;
    return {
         showCloseButton: false,
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
            UsedInUnit:{
                width: '5%',
                create: false,
                title: "Används",
                edit: false,
                list: includedIn(tableViewId, saron.table.role.viewid),
                sorting: false,
                display: function(data){
                    var childTableName = saron.table.unit.name;
                    var childTablePath = tablePath + "/" + childTableName;
                    var childTableTitle = 'Rollen "' + data.record.Name + '" ingår i följande organisatoriska enheter';
                    var tooltip = "";
                    var imgFile = "";
                    var childUri = 'app/web-api/listOrganizationUnitType.php';

                    if(data.record.HasChild === '0'){
                        imgFile = "unit_empty.png";
                        tooltip = "Inga organisatoriska enheter";
                    }
                    else{
                        imgFile = "unit.png";
                        tooltip = "Organisatoriska enheter";
                    }

                    var childTableDef = unitTableDef(tableViewId, childTablePath, childTableTitle, data.record.Id);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, TABLE, childUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, TABLE, );

                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                }               
            },
            Org:{
                width: '5%',
                create: false,
                title: "Ingår i",
                edit: false,
                list: includedIn(tableViewId, saron.table.role.viewid),
                sorting: false,
                display: function(data){
                    var childTableName = saron.table.unittype.name;
                    var childTablePath = tablePath + "/" + childTableName;
                    var childTableTitle = 'Rollen "' + data.record.Name + '" ingår i följande enhetstyper';
                    var tooltip = "";
                    var imgFile = "";
                    var childUri = 'app/web-api/listOrganizationUnitType.php';

                    if(data.record.HasChild === '0'){
                        imgFile = "unittype.png";
                        tooltip = "Inga organisatoriska enhetstyper";
                    }
                    else{
                        imgFile = "used_unittype.png";
                        tooltip = "Organisatoriska enhetstyper";
                    }

                    var childTableDef = unitTypeTableDef(tableViewId, childTablePath, childTableTitle, data.record.Id);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, TABLE, childUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, TABLE, );

                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
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
            if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org'){ 
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
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



function updateRoleRecord(tableViewId, data){
    var url = '/' + saron.uri.saron + 'app/web-api/listOrganizationRole.php';
    var options = {record:{"Id": data.record.Id}, "clientOnly": false, "url":url};
    $(tableViewId).jtable('updateRecord', options);
}
