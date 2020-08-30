/* global DATE_FORMAT, SARON_URI, PERSON */
"use strict";

$(document).ready(function () {

    $('#birthdays').jtable({
        title: 'Födelsedagar',
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'NextBirthday ASC', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php'
        },
        
        fields: {
            PersonId: {
                title: 'id',
                list: false,
                key: true
                
            },
            Name: {
                title: 'Namn',
                width: '15%',
                display: function (data){
                    return _setClassAndValue(data.record, "Name", PERSON);
                }       
            },
            DateOfBirth: {
                title: 'Född',
                width: '7%',
                type: 'date',
                //displayFormat: DATE_FORMAT,                
                display: function (data){
                    return _setClassAndValue(data.record, "DateOfBirth", PERSON);
                }       
            },
            Age: {
                title: 'Blir',
                width: '5%',
                display: function (data){
                    return _setClassAndValue(data.record, "Age", PERSON);
                }       
            },
            NextBirthday: {
                title: 'När',
                width: '7%',
                type: 'date',
                //displayFormat: DATE_FORMAT,
                display: function (data){
                    return _setClassAndValue(data.record, "NextBirthday", PERSON);
                }       
            },
            MemberState: {
                title: 'Status',               
                width: '5%'
            },
            Contact:{
                title: 'Kontaktuppgifter',
                width: '50%'               
            }
        }
    });
 //Re-load records when user click 'load records' button.
        $('#search_birthdays').click(function (e) {
            e.preventDefault();
            filterPeople('birthdays');
        });
 
        //Load all records when page is first shown
        $('#search_birthdays').click();
});
    
