/* global DATE_FORMAT, 
saron
*/
"use strict";

    
$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.member_state.nameId);
    tablePlaceHolder.jtable(memberstateTableDef(null, saron.table.member_state.name));
    var postData = getPostData(null, saron.table.member_state.name, null, saron.table.member_state.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
});



function memberstateTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.member_state.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title: 'Medlemsstatus',
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
                listClass: 'number',
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
                listClass: 'number',
                width: '5%'
            },
            Description: {
                edit: true,
                title: 'Beskrivning',
                width: '30%'
            },
//            Inserted: {
//                edit: false,
//                title: 'Datum för registrering',
//                width: '5%',
//                options: {"0":"Nej", "1":"Ja"}
//            },
//            DateOfMembershipStart: {
//                edit: false,
//                title: 'Medlemskap startdatum',
//                width: '5%',
//                options: {"0":"Nej", "1":"Ja"}
//            },
//            DateOfMembershipEnd: {
//                edit: false,
//                title: 'Medlemskap avslutsdatum',
//                width: '5%',
//                options: {"0":"Nej", "1":"Ja"}
//            },
//            DateOfBaptism: {
//                edit: false,
//                title: 'Dopdatum',
//                width: '5%',
//                options: {"0":"Nej", "1":"Ja"}
//            },
//            DateOfDeath: {
//                edit: false,
//                title: 'Datum för dödsfall',
//                width: '5%',
//                options: {"0":"Nej", "1":"Ja"}
//            },
//            DateOfFriendshipStart: {
//                edit: false,
//                title: 'Datum för vänkontakt start',
//                width: '5%',
//                options: {"0":"Nej", "1":"Ja, yngre än ett år"}
//            },
//            HasEngagement: {
//                edit: false,
//                title: 'Har uppdrag',
//                width: '5%',
//                options: {"0":"Nej", "1":"Ja"}
//            },
//            DateOfAnonymization: {
//                edit: false,
//                title: 'Datum för anonymisering',
//                width: '5%',
//                options: {"0":"Nej", "1":"Ja"}
//            },
            Updated:{
                title: 'Uppdaterad',
                width: '5%',
                create: false,
                listClass: 'Date',
                edit: false,
                display: function (data){
                    return getUpdateInfo(data);
                }
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

    if(tablePathRoot !== saron.table.member_state.name){
        
    }
}