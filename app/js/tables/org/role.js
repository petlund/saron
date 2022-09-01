/* global DATE_FORMAT,
 saron, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
ORG, TABLE,
saron
*/
 
"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.role.nameId);
    tablePlaceHolder.jtable(roleTableDef(null, null, null, null));

    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();
    
    var postData = getPostData(null, saron.table.role.name, null, saron.table.role.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
});



function roleTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.role.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        showCloseButton: false,
        title: "Alla roller",
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Lägg till ny roll'},
        actions: {            
            listAction:    saron.root.webapi + 'listOrganizationRole.php',
            createAction:  saron.root.webapi + 'createOrganizationRole.php',
            updateAction:  saron.root.webapi + 'updateOrganizationRole.php',  
            deleteAction:  saron.root.webapi + 'deleteOrganizationRole.php'
        },
        fields: {
            Id: {
                key: true,
                list: false,
                edit: false,
                create: false
            },
            ParentId:{
                defaultValue: -1,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.role.name
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.role.name
            },
            UsedInUnitType:{
                width: '5%',
                create: false,
                title: "Enhetstyper",
                edit: false,
                list: true,
                sorting: false,
                display: function(data){
                    var childTableName = saron.table.unittype.name;
                    var childTableTitle = 'Rollen "' + data.record.Name + '" ingår i följande enhetstyper';
                    var tooltip = "";
                    var imgFile = "";
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
                    var childTableDef = role_role_unitType_TableDef(childTableTitle, tablePath, data.record.Id, tableDef);  
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);

                    $imgChild.click(data, function (event){
                        openChildTable(childTableDef, $imgChild, event.data, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        closeChildTable(childTableDef, $imgClose, event.data, clientOnly);
                    });    

                    return getClickImg(data, childTableDef, $imgChild, $imgClose);
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
            UpdateInfo:{
                title: 'Uppdaterad',
                width: '5%',
                create: false,
                edit: false,
                display: function (data){
                    return getUpdateInfo(data);
                }
            }
        },
        recordUpdated(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);

            if (data.record.UsedInUnitType !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            
        },
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);

            if (data.record.UsedInUnitType !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            
            addDialogDeleteListener(data);
            
        },        
        recordsLoaded: function(event, data) {
            alowedToAddRecords(event, data, tableDef);
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


    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    
    configRoleTableDef(tableDef);

    return tableDef;
}


function configRoleTableDef(tableDef){

    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.unittype.name){
    }
    else if(tablePathRoot === saron.table.unittype.name){ 
        tableDef.actions.createAction = null;
    }   
}