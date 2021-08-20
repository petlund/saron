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
        $(TABLE_VIEW_ROLE).jtable(roleTableDef(TABLE_VIEW_ROLE, null, null));
        var options = getPostData(TABLE_VIEW_ROLE, null, TABLE_NAME_ROLE, null, RECORDS);
        $(TABLE_VIEW_ROLE).jtable('load', options);
    }
);



function roleTableDef(tableViewId, childTableTitle){
    const tableName = TABLE_NAME_ROLE;
    const listUri = 'app/web-api/listOrganizationRole.php';
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
            listAction:   '/' + SARON_URI + listUri,
            createAction: '/' + SARON_URI + 'app/web-api/createOrganizationRole.php',
            updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationRole.php',  
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationRole.php'
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
                list: includedIn(tableViewId, TABLE_VIEW_ROLE),
                sorting: false,
                display: function(data){
                    var childTableName = TABLE_NAME_UNIT;
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

                    var childTableDef = unitTableDef(tableViewId, childTableTitle);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, ORG, childUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, ORG, listUri);

                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
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
                    var childTableName = TABLE_NAME_UNITTYPE;
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

                    var childTableDef = unitTypeTableDef(tableViewId, childTableTitle);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, ORG, childUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, ORG, listUri);

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
