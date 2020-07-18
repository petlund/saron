/* global J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";
    
$(document).ready(function () {
    const TABLE_ID = "#ORG_ROLE_STATUS";

    $(TABLE_ID).jtable(statusTableDef(TABLE_ID));
    $(TABLE_ID).jtable('load');
    }
);

function statusTableDef(tableId){
    return {
        title: 'Status',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'SortOrder', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php',
            updateAction:   '/' + SARON_URI + 'app/web-api/updateOrganizationStatus.php',
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            Name: {
                title: 'Benämning',
                width: '15%'
            },
            Description: {
                title: 'Beskrivning',
                width: '50%'
            },
            SortOrder: {
                title: 'Sorteringsordning',
                width: '10%'
            },
            Updater: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '15%',
                options: function (){
                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php'           
                }
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: 'yy-mm-dd',
                width: '15%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.OrgRole_FK !== null)
                data.row.find('.jtable-delete-command-button').hide();
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit'){ 
                $(tableId).find('.jtable-toolbar-item-add-record').show();
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
        },
        deleteFormCreated: function (event, data){
            data.row[0].style.backgroundColor = 'red';
        },
        deleteFormClosed: function (event, data){
            data.row[0].style.backgroundColor = '';
        }
    }
}
