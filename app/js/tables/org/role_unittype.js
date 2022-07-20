/* global DATE_FORMAT,
 saron, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
ORG, TABLE,
saron
*/
 
"use strict";

function role_role_unitType_TableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.role_unittype.name;
    var tablePath = getChildTablePath(parentTablePath, tableName, null);

    var tableDef =  {
        parentId: parentId,
        tablePath: tablePath,
        tableName: tableName,
        parentTableDef: parentTableDef,
        showCloseButton: false,
        title: "Alla kopplingar mellan roller och enhetstyper",
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'OrgUnitType_FK',   
        messages: null,
        deleteConfirmation: function(data) {
            var message = "Raderar koppling mellan enhetstyp och roll. <br>Kan inte ångras.";
            data.deleteConfirmMessage = message;
        },         
        actions: {            
            listAction:   saron.root.webapi + 'listOrganizationRole-UnitType.php',
            createAction: saron.root.webapi + 'createOrganizationRole-UnitType.php',
            updateAction: saron.root.webapi + 'updateOrganizationRole-UnitType.php',  
            deleteAction: saron.root.webapi + 'deleteOrganizationRole-UnitType.php'
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
                defaultValue: saron.table.role_unittype.name
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.role_unittype.name
            },
            OrgUnitType_FK: {
                list: true, //config
                create: true,
                edit: false,
                title: 'Enhetstyp',
                width: '50%',
                optionsSorting:'text',
                options: function(data){
                    var url = saron.root.webapi + 'listOrganizationUnitType.php';
                    var field = null;
                    var parameters = getOptionsUrlParameters(data, saron.table.role_unittype.name, parentId, tableDef.tablePath, field);                    
                    return url + parameters;
                }
            },
            OrgRole_FK: {
                list: true,
                create: true,
                edit: false,
                title: 'Roll',
                width: '50%',
                optionsSorting:'text',
                options: function(data){
                    var url = saron.root.webapi + 'listOrganizationRole.php';
                    var field = null;
                    var parameters = getOptionsUrlParameters(data, saron.table.role_unittype.name, parentId, tableDef.tablePath, field);                    
                    return url + parameters;
                }
            },
            SortOrder: {
                list: true,
                edit: true,
                create: true,
                title: 'Sortering',
                width: '5%',
                display: function (data){
                    return _setClassAndValue(data, "SortOrder", 0);
                }, 
                options: {'0':'0','1':'1','2':'2','3':'3','4':'4','5':'5','6':'6','7':'7','8':'8','9':'9'}
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
        },
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
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
    
    configRole_UnitTypeTableDef(tableDef);
    
    return tableDef;
}



function configRole_UnitTypeTableDef(tableDef){
    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.unittype.name){ // && appCanvasLast === tableDef.appCanvasName
        tableDef.fields.OrgUnitType_FK.list = false;
        tableDef.defaultSorting = 'SortOrder';
        tableDef.fields.OrgUnitType_FK.create = false;
        tableDef.messages =  {addNewRecord: 'Koppla roll till enhetstypen'};
    }
    else if(tablePathRoot === saron.table.role.name ){ //&& appCanvasLast === tableDef.appCanvasName
        tableDef.fields.OrgRole_FK.list = false;
        tableDef.fields.SortOrder.list = false;
        tableDef.fields.SortOrder.create = false;
        tableDef.fields.SortOrder.edit = false;
        tableDef.fields.OrgRole_FK.create = false;
        tableDef.messages =  {addNewRecord: 'Koppla enhetstyp till rollen'};
    }    
}









