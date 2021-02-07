/* global PERSON, CHILD_TABLE_PREFIX, SARON_URI, SARON_IMAGES_URI */
"use strict";

const TABLE_ID = '#STATISTICS';

$(document).ready(function () {

    $(TABLE_ID).jtable({
        title: 'Medlemsstatistik',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'year desc', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listStatistics.php'
        },
        fields: {
            year: {
                title: 'År',
                key: true,
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data.record, "year", PERSON);
                }       
            },
            number_of_members: {
                edit: false,
                create: false, 
                title: 'Medlemmar',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data.record, "number_of_members", PERSON);
                }       
            },
            number_of_new_members:  {
                edit: false,
                create: false, 
                title: 'Nya',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data.record, "number_of_new_members", PERSON);
                }       
            },
            number_of_finnished_members:  {
                edit: false,
                create: false, 
                title: 'Avslutade',
                format: 'number',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data.record, "number_of_finnished_members", PERSON);
                }       
            },
            number_of_dead:  {
                edit: false,
                create: false, 
                title: 'Avlidna',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data.record, "number_of_dead", PERSON);
                }       
            },
            number_of_baptist_people:  {
                edit: false,
                create: false, 
                title: 'Döpta',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data.record, "number_of_baptist_people", PERSON);
                }       
            },
            avg_age:  {
                edit: false,
                create: false, 
                title: 'Medelålder',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data.record, "avg_age", PERSON);
                }       
            },
            avg_membership_time:  {
                edit: false,
                create: false, 
                title: 'Medelålder',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data.record, "avg_membership_time", PERSON);
                }       
            },
            diff:  {
                edit: false,
                create: false, 
                title: 'Differens',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data.record, "diff", PERSON);
                }       
            },
            Details: {
                title: 'Detaljer',
                key: true,
                sorting: false,
                width: '10%',
                display: function(data){
                    var $imgDetails = $('<img align="right" src="/' + SARON_URI + SARON_IMAGES_URI + 'member.png" title="Detaljer" />');
                    var YEAR =data.record.year.substring(0, 4);
                    $imgDetails.click(function () {
                        $(TABLE_ID).jtable('openChildTable', $imgDetails.closest('tr'),{
                            title: 'Medlemsförändringar under ' + YEAR,                            
                            paging: true, //Enable paging
                            pageSize: 10, //Set page size (default: 10)
                            pageList: 'minimal',
                            sorting: true, //Enable sorting
                            multiSorting: true,
                            defaultSorting: 'event_date desc, LastName ASC, FirstName ASC', //Set default sorting        
                            //showCloseButton: false,
                            actions: {
                                listAction:   '/' + SARON_URI + 'app/web-api/listStatistics.php?year=' + YEAR + '&selection=details'
                            },
                            fields: {
                                link:{
                                    title: '',
                                    width: '1%',
                                    sorting: false,
                                    display: function(data){
                                        var imgLink = '<img class="Person" src="/' + SARON_URI + SARON_IMAGES_URI + 'haspos.png" title="Personuppgifter">';
                                        var hrefLink = '<a href="/' + SARON_URI + 'app/views/people.php?tableview=people&PersonId=' + data.record.PersonId + '">' + imgLink + '</a>';
                                        console.log(hrefLink);
                                        var $img = $(hrefLink);
                                        return $img;
                                    }
                                },
                                event_date: {
                                    title: 'Datum',
                                    key: true,
                                    display: function (data){
                                        return _setClassAndValue(data.record, "event_date", PERSON);
                                    }       
                                },
                                LastName: {
                                    title: 'Efternamn',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "LastName", PERSON);
                                    }       
                                },
                                FirstName: {
                                    title: 'Förnamn',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "FirstName", PERSON);
                                    }       
                                },
                                DateOfBirth: {
                                    title: 'Födelsedatum',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "DateOfBirth", PERSON);
                                    }       
                                },
                                event_type: {
                                    title: 'Händelse',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "event_type", PERSON);
                                    }       
                                },
                                Comment: {
                                    title: 'Notering',
                                    width: '50%',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "Comment", PERSON);
                                    }       
                                }
                            }
                        }, 
                        function (data) { //opened handler
                            data.childTable.jtable('load');
                        });                        
                    });
                    return $imgDetails;
                }
            }
            
        }
    });
    $('#STATISTICS').jtable('load');
    $('#STATISTICS').find('.jtable-toolbar-item-add-record').hide();
});
