/* global DATE_FORMAT, SARON_URI, PERSON */
"use strict";

    $(document).ready(function () {

        const J_TABLE_ID = '#birthdays';

        $(J_TABLE_ID).jtable(birthdayTableDef(J_TABLE_ID));
        $(J_TABLE_ID).jtable('load');
        $(J_TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
    });
    
    
    function birthdayTableDef(placeHolder){
        return {
            title: 'Födelsedagar',
                paging: true, //Enable paging
                pageSize: 10, //Set page size (default: 10)
                pageList: 'minimal',
                sorting: true, //Enable sorting
                multiSorting: true,
                defaultSorting: 'NextBirthday ASC', //Set default sorting        
            actions: {
                listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php?tableview=birthdays'
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
                    displayFormat: DATE_FORMAT,                
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
                    displayFormat: DATE_FORMAT,
                    display: function (data){
                        return _setClassAndValue(data.record, "NextBirthday", PERSON);
                    }       
                },
                MemberState: {
                    title: 'Status',               
                    width: '5%'
                },
                Email: {
                    width: '13%',
                    title: 'Mail',
                    display: function (data){
                        return _setMailClassAndValue(data.record, "Email", '', PERSON);
                    }       
                },  
                Phone: {
                    title: 'Telefon',
                    width: '10%',
                    edit: true,
                    display: function (data){
                        return _setClassAndValue(data.record, "Phone", PERSON);
                    }                   
                },
                Mobile: {
                    title: 'Mobil',
                    inputTitle: 'Mobil <BR> - Hemtelefonuppgifter matas in under "Adressuppgifter"',
                    width: '7%',
                    display: function (data){
                        return _setClassAndValue(data.record, "Mobile", PERSON);
                    }       
                },
                Co: {
                    title: 'Co',
                    width: '10%',
                    edit: true,
                    display: function (data){
                        return _setClassAndValue(data.record, "Co", PERSON);
                    }       
                },
                Address: {
                    title: 'Adress',
                    width: '10%',
                    edit: true,
                    display: function (data){
                        return _setClassAndValue(data.record, "Address", PERSON);
                    }       
                },
                Zip: {
                    title: 'PA',
                    width: '5%',
                    edit: true,
                    display: function (data){
                        return _setClassAndValue(data.record, "Zip", PERSON);
                    }       
                },
                City: {
                    title: 'Stad',
                    width: '10%',
                    edit: true,
                    display: function (data){
                        return _setClassAndValue(data.record, "City", PERSON);
                    }       
                },
                Country: {
                    title: 'Land',
                    width: '10%',
                    edit: true,
                    display: function (data){
                        return _setClassAndValue(data.record, "Country", PERSON);
                    }       
                }
            }
        };
    
        //Re-load records when user click 'load records' button.
//           $('#search_birthdays').click(function (e) {
//               e.preventDefault();
//               filterPeople(placeHolder);
//           });
//
//           //Load all records when page is first shown
//           $('#search_birthdays').click();
    };
    
