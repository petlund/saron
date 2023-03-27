/* global DATE_FORMAT, 
saron, 
inputFormWidth, inputFormFieldWidth, 
saron.table.org_role_status.nameId, saron.table.org_role_status.name
*/

"use strict";
    
$(document).ready(function () {

    var tablePlaceHolder = $(saron.table.org_role_status.nameId);
    tablePlaceHolder.jtable(statusTableDef(null, saron.table.org_role_status.name, null, null));

    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();

    var postData = getPostData(null, saron.table.org_role_status.name, null, saron.table.org_role_status.name, saron.source.list, saron.responsetype.records);
    $(saron.table.org_role_status.nameId).jtable('load', postData);
    }
);

function statusTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.role.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title: 'Status på positioner',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   saron.root.webapi  + 'listOrganizationPosStatus.php',
            updateAction: saron.root.webapi  + 'updateOrganizationPosStatus.php',
        },
        fields: {
            AppCanvasName:{
                list: false,
                edit: false,
                create: false
            },
            Id: {
                key: true,
                list: true,
                edit: false,
                create: false,
                width: '3%',
                title: 'Id'
            },
            Name: {
                edit: false,
                create: false, 
                title: 'Benämning',
                width: '15%'
            },
            Amount: {
                create: false,
                edit: false,
                title: 'Antal',
                width: '3%'
            },
            Description: {
                title: 'Beskrivning',
                width: '74%'
            },
            Updated:{
                title: 'Uppdaterad',
                width: '5%',
                create: false,
                edit: false,
                display: function (data){
                    return getUpdateInfo(data);
                }
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.OrgRole_FK !== null)
                data.row.find('.jtable-delete-command-button').hide();
        },        
        recordsLoaded: function(event, data) {
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

    return tableDef;
}
