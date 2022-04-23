/* global DATE_FORMAT,
 saron, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
ORG, TABLE,
saron
*/
 
"use strict";
const role_table_typeListUri = 'app/web-api/listOrganizationRole-UnitType.php';

function role_role_unitType_TableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    const tableName = saron.table.role_unittype.name;
    var title = "Alla kopplingar mellan roller och enhetstyper";
    
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
        defaultSorting: getDefaultSorting(mainTableViewId),   
        messages: getMessageAddNewRecord(mainTableViewId),
        deleteConfirmation: function(data) {
            var message = "Raderar koppling mellan enhetstyp och roll. <br>Kan inte ångras.";
            data.deleteConfirmMessage = message;
        },         
        actions: {            
            listAction: '/' + saron.uri.saron + role_table_typeListUri,
            createAction: '/' + saron.uri.saron + 'app/web-api/createOrganizationRole-UnitType.php',
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateOrganizationRole-UnitType.php',  
            deleteAction: '/' + saron.uri.saron + 'app/web-api/deleteOrganizationRole-UnitType.php'
        },
        fields: {
            TablePath:{
                type: 'hidden',
                defaultValue: tablePath
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
            OrgUnitType_FK: {
                list: mainTableViewId.includes(saron.table.role.viewid),
                create: mainTableViewId.includes(saron.table.role.viewid),
                edit: false,
                title: 'Enhetstyp',
                width: '50%',
                optionsSorting:'text',
                options: function(data){
                    var uri = 'app/web-api/listOrganizationUnitType.php';
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
                }
            },
            OrgRole_FK: {
                list: mainTableViewId.includes(saron.table.unittype.viewid),
                create: mainTableViewId.includes(saron.table.unittype.viewid),
                edit: false,
                title: 'Roll',
                width: '50%',
                optionsSorting:'text',
                options: function(data){
                    var uri = 'app/web-api/listOrganizationRole.php';
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
                }
            },
            SortOrder: {
                list: mainTableViewId.includes(saron.table.unittype.viewid),
                edit: mainTableViewId.includes(saron.table.unittype.viewid),
                create: mainTableViewId.includes(saron.table.unittype.viewid),
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
            if(mainTableViewId.includes(saron.table.role.viewid))
                data.row.find('.jtable-edit-command-button').hide();

            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }            
        },
        rowInserted: function(event, data){
            if(mainTableViewId.includes(saron.table.role.viewid))
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
    if(mainTableViewId.includes(saron.table.unittype.viewid))
        return 'SortOrder';
    else
        return 'OrgUnitType_FK';                    
}


function getMessageAddNewRecord(mainTableViewId){
    if(mainTableViewId.includes(saron.table.unittype.viewid))
        return {addNewRecord: 'Koppla roll till enhetstypen'};
    if(mainTableViewId.includes(saron.table.role.viewid))
        return {addNewRecord: 'Koppla enhetstyp till rollen'};
}