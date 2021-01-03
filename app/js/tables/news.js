/* global SARON_URI, DATE_FORMAT, NEWS */
"use strict";
    
$(document).ready(function () {
    $('#NEWS').jtable(newsTableDef());
    $('#NEWS').jtable('load');
    $('#NEWS').find('.jtable-toolbar-item-add-record').hide();
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
            listAction:   '/' + SARON_URI + 'app/web-api/listNews.php',
            createAction:   '/' + SARON_URI + 'app/web-api/createNews.php',
            updateAction:   '/' + SARON_URI + 'app/web-api/updateNews.php',
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteNews.php'
        },
        fields: {
            id: {
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
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
                $('#NEWS').find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=information]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        }
    };

};