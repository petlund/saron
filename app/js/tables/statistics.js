/* global PERSON, CHILD_TABLE_PREFIX, 
saron,
peopleListUri,
ORG, TABLE, 
RECORD, OPTIONS

 */
"use strict";
const statisticsListUri = 'app/web-api/listStatistics.php';


$(document).ready(function () {
    var mainTableViewId = saron.table.statistics.viewid;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(statisticTableDef(saron.table.statistics.viewid, null, null));    
    var options = getPostData(null, null, mainTableViewId, saron.source.list, saron.responsetype.records, statisticsListUri);
    tablePlaceHolder.jtable('load', options);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});

function statisticTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    var title = 'Statistik';
    if(newTableTitle !== null)
        title = newTableTitle;
    
    var tableName = saron.table.statistics.name;

    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 

    return {
        title:title,
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),
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
                    var tooltip = 'Detaljer';
                    var imgFile = "member.png";
                    var parentId = data.record.Id;
                    var clientOnly = true;
                    var url = null;
                    var type = 0;

                    var childTableDef = statisticsDetailTableDef(mainTableViewId, tablePath, childTableTitle, parentId);   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose);
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
    };
}


function statisticsDetailTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    var title = 'Statistikdetaljer';
    if(newTableTitle !== null)
        title = newTableTitle;
    
    var tableName = saron.table.statistics_detail.name;
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
    
    return {
        title:title,
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),
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
                list: false
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
                    var tooltip = 'Personuppgifter';
                    var imgFile = "haspos.png";
                    var parentId = data.record.PersonId; //syntetic id denormalized list
                    var clientOnly = true;
                    var url = null;
                    var type = 0;

                    var childTableDef = peopleTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose);                }
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

