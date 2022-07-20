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

    $(saron.table.org_role_status.nameId).jtable('load');
    }
);

function statusTableDef(tableTitle, tablePath, parentId, parentTableDef){
    return {
        title: 'Status',
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
                list: false,
                edit: false,
                create: false
            },
            Name: {
                edit: false,
                create: false, 
                title: 'Benämning',
                width: '15%'
            },
            Description: {
                title: 'Beskrivning',
                width: '50%'
            },
            UpdaterName:{
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '15%'
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: DATE_FORMAT,
                width: '15%'
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
}
