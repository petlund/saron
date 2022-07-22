/* global saron, 
DATE_FORMAT
 */
"use strict";
    
$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.news.nameId);
    tablePlaceHolder.jtable(newsTableDef(null, saron.table.news.name, null, null));
    
    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();
    
    var postData = getPostData(null, saron.table.news.name, null, saron.table.news.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
});


function newsTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.news.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef =  {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
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
            alowedToUpdateOrDelete(event, data, tableDef);
            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            alowedToAddRecords(event, data, tableDef);
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
    
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configNewsTableDef(tableDef);
    
    return tableDef;
}    



function configNewsTableDef(tableDef){

    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    //tableDef.actions.createAction = null;
}
