/* global DATE_FORMAT,
 saron
 */
"use strict";

    
$(document).ready(function () {
    $(saron.table.orgversion.viewid).jtable(orgVersionTableDef());
    $(saron.table.orgversion.viewid).jtable('load');
    $(saron.table.orgversion.viewid).find('.jtable-toolbar-item-add-record').hide();
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
        messages: {addNewRecord: 'Skapa ny version av organisationen'},
        actions: {
            listAction:   '/' + saron.uri.saron + 'app/web-api/listOrganizationVersion.php',
            updateAction:   '/' + saron.uri.saron + 'app/web-api/updateOrganizationVersion.php',
            createAction: '/' + saron.uri.saron + 'app/web-api/createOrganizationVersion.php',
        },
        fields: {
            TablePath:{
                list: false,
                edit: false,
                create: false,
                display: function(){
                    return 'version';
                }
            },
            Id: {
                key: true,
                list: false
            },
            decision_date: {
                title: 'Beslutsdatum',
                width: '15%',
                type: 'date',
                displayFormat: DATE_FORMAT
            },
            information: {
                title: 'Beskrivning',
                width: '70%'
            },
            UpdaterName: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                width: '15%'
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
        rowInserted: function(event, data){
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org'){ 
                $(saron.table.orgversion.viewid).find('.jtable-toolbar-item-add-record').show();
                $(saron.table.orgversion.viewid).find('.jtable-edit-command-button').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=information]').css('width','580px');
            
            var title = "Ange beslutstillfälle<div class='saronRedSmallText'>Alla förslag till förändringar införs - Kan inte ångras!</div>";
            if(data.formType === saron.formtype.edit)
                title = "Uppdatera information om beslutstillfälle."
            
            var dbox = document.getElementsByClassName('ui-dialog-title');            
            for(var i=0; i<dbox.length; i++){
                dbox[i].innerHTML=title;
            }
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };

};