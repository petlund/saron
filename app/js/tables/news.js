/* global saron, 
DATE_FORMAT
 */
"use strict";
    
$(document).ready(function () {
    $(saron.table.news.nameId).jtable(newsTableDef());
    $(saron.table.news.nameId).jtable('load');
    $(saron.table.news.nameId).find('.jtable-toolbar-item-add-record').hide();
});


function newsTableDef(){
    return {
        title: 'Nyheter',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'news_date desc', //Set default sorting        
        actions: {
            listAction:   saron.root.webapi + 'listNews.php',
            createAction: saron.root.webapi + 'createNews.php',
            updateAction: saron.root.webapi + 'updateNews.php',
            deleteAction: saron.root.webapi + 'deleteNews.php'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            news_date: {
                edit: false,
                create: false, 
                title: 'Datum',
                width: '15%',
                type: 'date',
                displayFormat: DATE_FORMAT
            },
            severity:{
                title: "Typ",    
                options: {0:'Meddelande', 1:'Viktigt meddelande', 2:'Varning'},
                display: function(data){
                    if(data.record.severity === "1"){
                        return '<b>' + this.options[1] + '</b>';
                    }
                    if(data.record.severity === "2"){
                        return '<b style="color:red">' + this.options[2] + '</b>';
                    }
                    else 
                        return this.options[0];
                }
            },
            information: {
                title: 'Information',
                width: '70%'
            },
            writer: {
                edit: false,
                create: false, 
                title: 'Skribent',
                width: '15%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org'){ 
                $(saron.table.news.nameId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=information]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };

};