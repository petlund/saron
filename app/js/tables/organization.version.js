/* global SARON_URI, DATE_FORMAT, NEWS */
"use strict";

const TABLE_ID = "#ORG_VER";
    
$(document).ready(function () {
    $(TABLE_ID).jtable(orgVersionTableDef());
    $(TABLE_ID).jtable('load');
    $(TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
});


function orgVersionTableDef(){
    return {
        title: 'Beslutslog över organisatationsförändringar',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'decision_date desc', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationVersion.php',
            createAction: '/' + SARON_URI + 'app/web-api/createOrganizationVersion.php',
        },
        fields: {
            id: {
                key: true,
                list: false
            },
            information: {
                inputTitle: "Kommandot kan inte ångras. Kommandot tar bort alla markeringar om förändringar.",
                title: 'Beslutstillfälle',
                width: '70%'
            },
            UpdaterName: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                width: '15%'
            },
            decision_date: {
                edit: false,
                create: false, 
                title: 'Datum',
                width: '15%',
                type: 'date',
                displayFormat: DATE_FORMAT
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
                $(TABLE_ID).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=information]').css('width','580px');
            
            var dbox = document.getElementsByClassName('ui-dialog-title');            
            for(var i=0; i<dbox.length; i++){
                dbox[i].innerHTML="Ange beslutstillfälle för organisationsförändring";
            }
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
    };

};