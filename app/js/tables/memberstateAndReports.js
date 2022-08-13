/* global DATE_FORMAT, 
saron
*/
"use strict";

    
$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.member_state_report.nameId);
    tablePlaceHolder.jtable(memberstateReportTableDef(null, saron.table.member_state_report.name));
    var postData = getPostData(null, saron.table.member_state_report.name, null, saron.table.member_state_report.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
});



function memberstateReportTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.member_state_report.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title: 'Medlemsstatus och rapporter',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'news_date desc', //Set default sorting        
        actions: {
            listAction:   saron.root.webapi + 'listMemberState.php',
            updateAction:   saron.root.webapi + 'updateMemberState.php'
        },
        fields: {
            Id: {
                title: 'Id',
                width: '3%',
                key: true,
                list: true
            },
            Name: {
                edit: false,
                title: 'Namn',
                width: '10%'
            },
            Amount: {
                edit: false,
                create: false, 
                title: 'Antal personer',
                width: '5%'
            },
            Description: {
                edit: true,
                title: 'Beskrivning',
                width: '30%'
            },
            DossierReport: {
                edit: false,
                title: 'Samtyckesunderlag',
                width: '5%',
                options: {"0":"-", "1":"Ja"}
            },
            DirectoryReport: {
                edit: false,
                title: 'Medlemskalender',
                width: '5%',
                options: {"0":"-", "1":"Ja"}
            },
            BaptistDirectoryReport: {
                edit: false,
                title: 'Dopregister',
                width: '5%',
                options: {"0":"-", "1":"Ja"}
            },
            SendMessages: {
                edit: false,
                title: 'Utskick',
                width: '5%',
                options: {"0":"-", "1":"Ja"}
            },
            UpdaterName: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '5%'
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
            alowedToUpdateOrDelete(event, data, tableDef);
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
    
    configMemberStateTableDef(tableDef);
    
    return tableDef;    
}


function configMemberStateTableDef(tableDef){
    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot !== saron.table.member_state_report.name){
        
    }
}