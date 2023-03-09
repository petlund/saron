/* global PERSON, CHILD_TABLE_PREFIX, 
saron,
peopleListUri,
ORG, TABLE, EVENT_TYPE,
RECORD, OPTIONS

 */
"use strict";


$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.statistics.nameId);
    tablePlaceHolder.jtable(statisticTableDef(null, null, null, null));    
    
    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();

    var options = getPostData(null, saron.table.statistics.name, null,  saron.table.statistics.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
});

function statisticTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.statistics.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title:'Statistik',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'year desc', //Set default sorting        
        actions: {
            listAction:   saron.root.webapi + 'listStatistics.php'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            Detail:{
                list: true,
                edit: false,
                create: false,
                sorting: false,
                width: '1%',
                display: function(data){
                    var YEAR = data.record.year.substring(0, 4);
                    var childTableTitle = 'Statistik för ' + YEAR;
                    var tooltip = 'Detaljer';
                    var imgFile = "member.png";
                    var clientOnly = true;
                    var type = 0;

                    var childTableDef = statisticsDetailTableDef(childTableTitle, tablePath, data.record.Id, tableDef);   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                    var $imgClose = getImageCloseTag(data, childTableDef, type);
                        
                    $imgChild.click(data, function (event){
                        event.data.record.ParentId = data.record.Id;
                        openChildTable(childTableDef, $imgChild, event.data, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        closeChildTable(childTableDef, $imgClose, event.data, clientOnly);
                    });    

                    return getClickImg(data, childTableDef, $imgChild, $imgClose);
                }
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.statistics.name
            },
            year: {
                title: 'År',
                width: '10%',
                type: 'date',
                listClass: 'year'
            },
            ParentId:{
                defaultValue: -1,
                type: 'hidden'
            },
            number_of_members: {
                edit: false,
                create: false, 
                title: 'Medlemmar',
                width: '10%',
                listClass: 'number_of_members'
            },
            number_of_new_members:  {
                edit: false,
                create: false, 
                title: 'Nya',
                width: '10%',
                listClass: 'number_of_new_members'
            },
            number_of_finnished_members:  {
                edit: false,
                create: false, 
                title: 'Avslutade',
                format: 'number',
                width: '10%',
                listClass: 'number_of_finnished_members'
            },
            number_of_dead:  {
                edit: false,
                create: false, 
                title: 'Avlidna',
                width: '10%',
                listClass: 'number_of_dead'
            },
            number_of_baptist_people:  {
                edit: false,
                create: false, 
                title: 'Döpta',
                width: '10%',
                listClass: 'number_of_baptist_people'
            },
            avg_age:  {
                edit: false,
                create: false, 
                title: 'Medelålder',
                width: '10%',
                listClass: 'avg_age'
            },
            avg_membership_time:  {
                edit: false,
                create: false, 
                title: 'Medelålder',
                width: '10%',
                listClass: 'avg_membership_time'
            },
            diff:  {
                edit: false,
                create: false, 
                title: 'Differens',
                width: '10%',
                listClass: 'diff'
            }
        }
    };
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configStatisticDetailsTableDef(tableDef);
    
    return tableDef;
}    



function configStatisticTableDef(tableDef){

    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.statistics.name){

    }    
}


function statisticsDetailTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.statistics_detail.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        appCanvasName: saron.table.statistics_detail.name,
        title: 'Statistikdetaljer',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'event_date desc, Name', //Set default sorting        
        showCloseButton: false,
        actions: {
            listAction:   saron.root.webapi + 'listStatistics.php'
        },
        fields: {
            Id: { // unic rowId
                key: true,
                list: false
            },
            ParentId:{
                defaultValue: -1,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.statistics_detail.name
            },
            PersonId:{
                type: 'hidden'
            },
            Person:{
                title: '',
                width: '1%',
                sorting: false,
                display: function(data){
                    var childTableTitle = 'Registeruppgifter för ' + data.record.Name;
                    var childTableName = saron.table.people.name;
                    var tooltip = 'Personuppgifter';
                    var imgFile = "haspos.png";
                    var clientOnly = true;
                    var url = null;
                    var type = 0;

                    var childTableDef = peopleTableDef(childTableTitle, tablePath, data.record.PersonId, tableDef); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        openChildTable(childTableDef, $imgChild, event.data, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        closeChildTable(childTableDef, $imgClose, event.data, url, clientOnly);
                    });    

                    return getClickImg(data, childTableDef, $imgChild, $imgClose);                }
            },
            event_date: {
                title: 'Datum',
                display: function (data){
                    return _setClassAndValueWidthEventType(data, "event_date", PERSON);
                }       
            },
            event_type:{
                title: 'Händelse'
            },
            Name: {
                title: 'Efternamn',
                width: '30%',
                display: function (data){
                    return _setClassAndValueWidthEventType(data, "Name", PERSON);
                }       
            },
            LastName: {
                list: false,
                title: 'Efternamn',
                display: function (data){
                    return _setClassAndValueWidthEventType(data, "LastName", PERSON);
                }       
            },
            FirstName: {
                list: false,
                title: 'Förnamn',
                display: function (data){
                    return _setClassAndValueWidthEventType(data, "FirstName", PERSON);
                }       
            },
            DateOfBirth: {
                list: false,
                title: 'Födelsedatum',
                display: function (data){
                    return _setClassAndValueWidthEventType(data, "DateOfBirth", PERSON);
                }       
            },
            Comment: {
                title: 'Notering',
                width: '30%',
                display: function (data){
                    return _setClassAndValueWidthEventType(data, "Comment", PERSON);
                }       
            }
        }
    };
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configStatisticDetailsTableDef(tableDef);
    
    return tableDef;
}    



function configStatisticDetailsTableDef(tableDef){

    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);
}
