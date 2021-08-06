/* global DATE_FORMAT, 
SARON_URI, SARON_IMAGES_URI, 
inputFormWidth, inputFormFieldWidth, 
TABLE_VIEW_ORG_ROLE_STATUS, TABLE_NAME_ORG_ROLE_STATUS
*/

"use strict";
    
$(document).ready(function () {

    $(TABLE_VIEW_ORG_ROLE_STATUS).jtable(statusTableDef(TABLE_VIEW_ORG_ROLE_STATUS));
    $(TABLE_VIEW_ORG_ROLE_STATUS).jtable('load');
    }
);

function statusTableDef(tableViewId){
    return {
        title: 'Status',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationPosStatus.php',
            updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationPosStatus.php',
        },
        fields: {
            TablePath:{
                list: false,
                edit: false,
                create: false
            },
            Id: {
                key: true,
                list: false
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
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.OrgRole_FK !== null)
                data.row.find('.jtable-delete-command-button').hide();
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
