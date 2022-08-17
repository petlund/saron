/* global DATE_FORMAT,
 saron
 */
"use strict";

    
$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.orgversion.nameId);
    tablePlaceHolder.jtable(orgVersionTableDef(null, saron.table.orgversion.name, null, null));

    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();
    
    var postData = getPostData(null, saron.table.orgversion.name, null, saron.table.orgversion.name, saron.source.list, saron.responsetype.records);
    $(saron.table.orgversion.nameId).jtable('load', postData);
});


function orgVersionTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.orgversion.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title: 'Beslutslog över organisatationsförändringar',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'decision_date desc', //Set default sorting        
        messages: {addNewRecord: 'Skapa ny version av organisationen'},
        actions: {
            listAction:   saron.root.webapi + 'listOrganizationVersion.php',
            updateAction: saron.root.webapi + 'updateOrganizationVersion.php',
            createAction: saron.root.webapi + 'createOrganizationVersion.php',
        },
        fields: {
            AppCanvasName:{
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
            alowedToAddRecords(event, data, tableDef);        
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
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    return tableDef;

};