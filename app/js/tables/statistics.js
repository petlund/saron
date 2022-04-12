/* global PERSON, CHILD_TABLE_PREFIX, 
saron,
peopleListUri,
ORG, TABLE, 
RECORD, OPTIONS

 */
"use strict";
const statisticsListUri = 'app/web-api/listStatistics.php';


$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.statistics.viewid);
    tablePlaceHolder.jtable(statisticTableDef(tablePlaceHolder, null, null));    
    var options = getPostData(null, tablePlaceHolder, null, saron.table.statistics.name, saron.source.list, saron.responsetype.records, statisticsListUri);
    tablePlaceHolder.jtable('load',options);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});

function statisticTableDef(tablePlaceHolder, tablePath, tableTitle){
    var tableName = saron.table.statistics.name;
    var title = 'Statistik';
    if(tableTitle !== null)
        title = tableTitle; 
    
    if(tablePath===null)
        tablePath = tableName;
    else
        tablePath+= tableName;

    return {
        title: title,
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'year desc', //Set default sorting        
        actions: {
            listAction:   '/' + saron.uri.saron + statisticsListUri
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
                    var childTableName = saron.table.statistics_detail.name;
                    var childTablePath = tablePath + "/" + childTableName;
                    var tooltip = 'Detaljer';
                    var imgFile = "member.png";

                    var childTableDef = detailTableDef(tablePlaceHolder, childTablePath, childTableTitle, data.record.Id);
                    var $imgChild = openChildTable(data, tablePlaceHolder, childTableDef, imgFile, tooltip, childTableName, TABLE, statisticsListUri);
                    var $imgClose = closeChildTable(data, tablePlaceHolder, childTableName, TABLE, statisticsListUri);

                    return getIcon(data,  tablePlaceHolder, childTableName, $imgChild, $imgClose);
                }
            },
            TablePath:{
                type: 'hidden',
                defaultValue: tableName
            },
            year: {
                title: 'År',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "year", PERSON);
                }       
            },
            number_of_members: {
                edit: false,
                create: false, 
                title: 'Medlemmar',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "number_of_members", PERSON);
                }       
            },
            number_of_new_members:  {
                edit: false,
                create: false, 
                title: 'Nya',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "number_of_new_members", PERSON);
                }       
            },
            number_of_finnished_members:  {
                edit: false,
                create: false, 
                title: 'Avslutade',
                format: 'number',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "number_of_finnished_members", PERSON);
                }       
            },
            number_of_dead:  {
                edit: false,
                create: false, 
                title: 'Avlidna',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "number_of_dead", PERSON);
                }       
            },
            number_of_baptist_people:  {
                edit: false,
                create: false, 
                title: 'Döpta',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "number_of_baptist_people", PERSON);
                }       
            },
            avg_age:  {
                edit: false,
                create: false, 
                title: 'Medelålder',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "avg_age", PERSON);
                }       
            },
            avg_membership_time:  {
                edit: false,
                create: false, 
                title: 'Medelålder',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "avg_membership_time", PERSON);
                }       
            },
            diff:  {
                edit: false,
                create: false, 
                title: 'Differens',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "diff", PERSON);
                }       
            }
        }
    }
}


function detailTableDef(tableViewId, tablePath, childTableTitle, parentId){
    var tableName = saron.table.statistics_detail.name;
    var title = 'Statistikdetaljer';
    if(childTableTitle !== null)
        title = childTableTitle;


    return {
        title: title,                            
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'event_date desc, LastName ASC, FirstName ASC', //Set default sorting        
        showCloseButton: false,
        actions: {
            listAction:   '/' + saron.uri.saron + statisticsListUri
        },
        fields: {
            Id: { // unic rowId
                key: true,
                list: true
            },
            TablePath:{
                type: 'hidden',
                defaultValue: tableName
            },
            PersonId:{
                title: 'PersonId',
                list: false //not unic in this view
            },
            Person:{
                title: '',
                width: '1%',
                sorting: false,
                display: function(data){
                    var childTableTitle = 'Personuppgifter för "' + data.record.LastName + ' '  + data.record.FirstName + ' ' + data.record.DateOfBirth;
                    var childTableName = saron.table.people.name;
                    var childTablePath = tablePath + "/" + childTableName;
                    var tooltip = 'Personuppgifter';
                    var imgFile = "haspos.png";


                    var childTableDef = peopleTableDef(tableViewId, childTablePath, childTableTitle, data.record.PersonId); // PersonId point to childtable unic id   
                    
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, TABLE, statisticsListUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, TABLE, statisticsListUri);
                    
                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                    
//                    var $img = openCloseChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, TABLE, statisticsListUri);
//                    return $img;
                    
//                    var $imgClose = closeChildTable(data, tableViewId, childTableName, TABLE, statisticsListUri);
//
//                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);                    
                }
            },
            event_date: {
                title: 'Datum',
                display: function (data){
                    return _setClassAndValue(data, "event_date", PERSON);
                }       
            },
            LastName: {
                title: 'Efternamn',
                display: function (data){
                    return _setClassAndValue(data, "LastName", PERSON);
                }       
            },
            FirstName: {
                title: 'Förnamn',
                display: function (data){
                    return _setClassAndValue(data, "FirstName", PERSON);
                }       
            },
            DateOfBirth: {
                title: 'Födelsedatum',
                display: function (data){
                    return _setClassAndValue(data, "DateOfBirth", PERSON);
                }       
            },
            event_type: {
                title: 'Händelse',
                display: function (data){
                    return _setClassAndValue(data, "event_type", PERSON);
                }       
            },
            Comment: {
                title: 'Notering',
                width: '50%',
                display: function (data){
                    return _setClassAndValue(data, "Comment", PERSON);
                }       
            }
        }
    };
}

