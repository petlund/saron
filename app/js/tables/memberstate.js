/* global DATE_FORMAT, 
saron
*/
"use strict";

    
$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.org_member_state.nameId);
    tablePlaceHolder.jtable(memberstateTableDef(null, saron.table.org_member_state.name));
    var postData = getPostData(null, saron.table.org_member_state.name, null, saron.table.org_member_state.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
});



function memberstateTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.org_member_state.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title: 'Personstatus',
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
                key: true,
                list: false
            },
            Name: {
                edit: false,
                title: 'Namn',
                width: '10%',
            },
            Description: {
                edit: true,
                title: 'Beskrivning',
                width: '40%'
            },
            FilterUpdate: {
                edit: false,
                title: 'Kan kopplas till uppdaterade uppdrag',
                width: '15%',
                options: {"0":"Nej", "1":"Ja"}
            },
            FilterCreate: {
                edit: false,
                title: 'Kan kopplas till nya uppdrag',
                width: '15%',
                options: {"0":"Nej", "1":"Ja"}
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

    if(tablePathRoot !== saron.table.org_member_state.name){
        
    }
}