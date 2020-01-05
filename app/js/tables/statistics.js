
var TABLE_ID = '#STATISTICS';

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
                    return "<p class='numericString'>" + data.record.year + "</p>";
                }
            },
            number_of_members: {
                edit: false,
                create: false, 
                title: 'Medlemmar',
                width: '10%',
                display: function (data){
                    return "<p class='numericString'>" + data.record.number_of_members + "</p>";
                }
            },
            number_of_new_members:  {
                edit: false,
                create: false, 
                title: 'Nya',
                display: function (data){
                    return "<p class='numericString'>" + data.record.number_of_new_members + "</p>";
                }                               
            },
            number_of_finnished_members:  {
                edit: false,
                create: false, 
                title: 'Avslutade',
                format: 'number',
                width: '5%',
                display: function (data){
                    return "<p class='numericString'>" + data.record.number_of_finnished_members + "</p>";
                }                               
            },
            number_of_dead:  {
                edit: false,
                create: false, 
                title: 'Avlidna',
                width: '5%',
                display: function (data){
                    return "<p class='numericString'>" + data.record.number_of_dead + "</p>";
                }                               
            },
            number_of_baptist_people:  {
                edit: false,
                create: false, 
                title: 'Döpta',
                width: '5%',
                display: function (data){
                    return "<p class='numericString'>" + data.record.number_of_baptist_people + "</p>";
                }                               
            },
            avg_age:  {
                edit: false,
                create: false, 
                title: 'Medelålder',
                width: '5%',
                display: function (data){
                    return "<p class='numericString'>" + data.record.avg_age + "</p>";
                }                               
            },
            avg_membership_time:  {
                edit: false,
                create: false, 
                title: 'Medelålder',
                width: '5%',
                display: function (data){
                    return "<p class='numericString'>" + data.record.avg_membership_time + "</p>";
                }                               
            },
            diff:  {
                edit: false,
                create: false, 
                title: 'Differens',
                width: '5%',
                display: function (data){
                    return "<p class='numericString'>" + data.record.diff + "</p>";
                }                               
            },
            Details: {
                title: '',
                key: true,
                sorting: false,
                width: '20%',
                display: function(data){
                    var $imgDetails = $('<img align="right" src="/' + SARON_URI + 'app/images/member.png" title="Detaljer" />');
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
                                listAction:   '/' + SARON_URI + 'app/web-api/listStatisticsDetails.php?year=' + YEAR
                            },
                            fields: {
                                event_date: {
                                    title: 'Datum',
                                    key: true,
                                    display: function (data){
                                        return "<p class='numericString'>" + data.record.event_date + "</p>";
                                    }
                                },
                                LastName: {
                                    title: 'Efternamn'
                                },
                                FirstName: {
                                    title: 'Förnamn'
                                },
                                DateOfBirth: {
                                    title: 'Födelsedatum',
                                    display: function (data){
                                        return "<p class='numericString'>" + data.record.DateOfBirth + "</p>";
                                    }
                                },
                                event_type: {
                                    title: 'Händelse'
                                },
                                Comment: {
                                    title: 'Notering',
                                    width: '50%'
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
