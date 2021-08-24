/* global PERSON, CHILD_TABLE_PREFIX, 
saron,
ORG, 
RECORD, OPTIONS

 */
"use strict";


$(document).ready(function () {
    $(saron.table.statistics.viewid).jtable(statisticTableDef(saron.table.statistics.viewid, null));    
    var options = getPostData(saron.table.statistics.viewid, null, saron.table.statistics.name, null, saron.responsetype.records);
    $(saron.table.statistics.viewid).jtable('load',options);
    $(saron.table.statistics.viewid).find('.jtable-toolbar-item-add-record').hide();
});

function statisticTableDef(tableViewId, tableTitle){
    var tableName = saron.table.statistics.name;
    var title = 'Statistik';
    if(tableTitle !== null)
        title = tableTitle; 

    return {
        title: title,
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'year desc', //Set default sorting        
        actions: {
            listAction:   '/' + saron.uri.saron + 'app/web-api/listStatistics.php'
        },
        fields: {
            People: { 
                title: 'Detaljer',
                key: true,
                sorting: false,
                width: '10%',
                display: function(data){
                    var YEAR =data.record.year.substring(0, 4);
                    var childTableTitle = 'Statistik för ' + YEAR;
                    var childTableName = saron.table.statistics_detail.name;
                    var tooltip = 'title="Detaljer"';
                    var imgFile = "member.png";
                    var listUri = 'app/web-api/listPeople.php';

                    var childTableDef = detailTableDef(tableViewId, childTableTitle);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, ORG, listUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, ORG, listUri);

                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                }
            },
            Id:{
                title: 'id'
            },
            year: {
                key: true,
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


function detailTableDef(tableViewId, childTableTitle){
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
        //showCloseButton: false,
        actions: {
            listAction:   '/' + saron.uri.saron + 'app/web-api/listStatistics.php'
        },
        fields: {
            People:{
                title: '',
                width: '1%',
                sorting: false,
                display: function(data){
                    var childTableTitle = 'Personuppgifter för "' + data.record.Name + '"';
                    var childTableName = saron.table.people.name;
                    var tooltip = 'title="Personuppgifter"';
                    var imgFile = "haspos.png";
                    var listUri = 'app/web-api/listPeople.php';

                    var childTableDef = peopleTableDef(tableViewId, childTableTitle);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, ORG, listUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, ORG, listUri);

                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                }
            },
            event_date: {
                title: 'Datum',
                key: true,
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

