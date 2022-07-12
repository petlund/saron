/* global DATE_FORMAT,
 saron, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
ORG, TABLE,
saron
*/
 
"use strict";

function role_role_unitType_TableDef(newTableTitle, tablePath){

    var title = "Alla kopplingar mellan roller och enhetstyper";
    
    
    return {
        appCanvasName: saron.table.role_unittype,
        showCloseButton: false,
        title: title,
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: getDefaultSorting(mainTableViewId),   
        messages: getMessageAddNewRecord(mainTableViewId),
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
                defaultValue: saron.table.people.name
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.people.name
            },
            OrgUnitType_FK: {
                list: mainTableViewId.includes(saron.table.role.nameId),
                create: mainTableViewId.includes(saron.table.role.nameId),
                edit: false,
                title: 'Enhetstyp',
                width: '50%',
                optionsSorting:'text',
                options: function(data){
                    var url = saron.root.webapi + 'listOrganizationUnitType.php';
                    var field = null;
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, data.record.ParentId, data.record.AppCanvasPath, field);                    
                    return url + parameters;
                }
            },
            OrgRole_FK: {
                list: mainTableViewId.includes(saron.table.unittype.nameId),
                create: mainTableViewId.includes(saron.table.unittype.nameId),
                edit: false,
                title: 'Roll',
                width: '50%',
                optionsSorting:'text',
                options: function(data){
                    var url = saron.root.webapi + 'listOrganizationRole.php';
                    var field = null;
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, data.record.ParentId, data.record.AppCanvasPath, field);                    
                    return url + parameters;
                }
            },
            SortOrder: {
                list: mainTableViewId.includes(saron.table.unittype.nameId),
                edit: mainTableViewId.includes(saron.table.unittype.nameId),
                create: mainTableViewId.includes(saron.table.unittype.nameId),
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
            if(mainTableViewId.includes(saron.table.role.nameId))
                data.row.find('.jtable-edit-command-button').hide();

            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }            
        },
        rowInserted: function(event, data){
            if(mainTableViewId.includes(saron.table.role.nameId))
                data.row.find('.jtable-edit-command-button').hide();

            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            
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


function getDefaultSorting(mainTableViewId){
    if(mainTableViewId.includes(saron.table.unittype.nameId))
        return 'SortOrder';
    else
        return 'OrgUnitType_FK';                    
}


function getMessageAddNewRecord(tableName){
    if(tableName.includes(saron.table.unittype.name))
        return {addNewRecord: 'Koppla roll till enhetstypen'};
    if(tableName.includes(saron.table.role.name))
        return {addNewRecord: 'Koppla enhetstyp till rollen'};
}