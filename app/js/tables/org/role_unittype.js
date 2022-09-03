/* global DATE_FORMAT,
 saron, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
ORG, TABLE,
saron
*/
 
"use strict";

function role_role_unitType_TableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.role_unittype.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

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
                defaultValue: parentId,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: tableName
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: tablePath
            },
            Instances:{
                title: 'Instanser',
                width: '3%',
                edit: false,
                sorting: false,
                create: false,
                list: true,
                display: function(data){
                    var childTableTitle = 'Instansiserade positioner ';                            
                    var tooltip = "Enhetstypen används inom följande organisatoriska enheter";
                    var imgFile = "unit.png";
                    var type = 0;
                    var clientOnly = true;
                    
                    if(data.record.Amount !==  "0"){                        
                        var childTableDef = posInstancesTableDef(childTableTitle, tablePath, data.record.Id, tableDef); // PersonId point to childtable unic id   
                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                        var $imgClose = getImageCloseTag(data, childTableDef, type);

                        $imgChild.click(data, function (event){
                            openChildTable(childTableDef, $imgChild, event.data, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            closeChildTable(childTableDef, $imgClose, event.data, clientOnly);
                        });    

                        return getClickImg(data, childTableDef, $imgChild, $imgClose);
                    }
                    return null;
                },
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
                    var field = "OrgUnitType_FK";
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
                    var field = "OrgRole_FK";
                    var parameters = getOptionsUrlParameters(data, saron.table.role_unittype.name, parentId, tableDef.tablePath, field);                    
                    return url + parameters;
                }
            },
            Amount: {
                edit: false,
                create: false,
                title: 'Antal positioner'
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
            updateParentRow(event, data, tableDef);
            alowedToUpdateOrDelete(event, data, tableDef);

            if(data.record.Amount !== '0' )
                data.row.find('.jtable-delete-command-button').hide();
            
        },
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
            addDialogDeleteListener(data);

            if(data.record.Amount !== '0' )
                data.row.find('.jtable-delete-command-button').hide();
            
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
        tableDef.fields.OrgRole_FK.create = false;
        tableDef.messages =  {addNewRecord: 'Koppla enhetstyp till rollen'};
        tableDef.actions.updateAction = null;
    }    
}


function posInstancesTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.posinstances.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef =  {
        parentId: parentId,
        tablePath: tablePath,
        tableName: tableName,
        parentTableDef: parentTableDef,
        showCloseButton: false,
        title: "Har följande positioner",
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: "UnitTypeName",   
        actions: {            
            listAction:   saron.root.webapi + 'listOrganizationRole-UnitType.php'
        },
        fields: {
            Id: {
                key: true,
                list: false,
                edit: false,
                create: false
            },
            UnitTypeName:{
                title: 'Namn på enhetstypen'
            },            
            RoleName:{
                title: 'Rollnamn'                
            },            
            UnitName:{
                title: 'Enhhetsnamn'                
            },            
            Amount:{
                title: 'Antal positioner'
            }            
        }
    };
    
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configPosInstancesTableDef(tableDef);
    
    return tableDef;
}


function configPosInstancesTableDef(tableDef){
    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.unittype.name){ // && appCanvasLast === tableDef.appCanvasName

    }
    else if(tablePathRoot === saron.table.role.name ){ //&& appCanvasLast === tableDef.appCanvasName
    }    
}



