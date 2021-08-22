/* global DATE_FORMAT, SARON_URI, PERSON, 
TABLE_VIEW_BIRTHDAY, TABLE_NAME_BIRTHDAY,
RECORD, RECORDS, OPTIONS
*/
"use strict";

    $(document).ready(function () {

        const J_TABLE_ID = '#birthdays';

        $(TABLE_VIEW_BIRTHDAY).jtable(birthdayTableDef(TABLE_VIEW_BIRTHDAY, null));
        var options = getPostData(TABLE_VIEW_BIRTHDAY, null, TABLE_NAME_BIRTHDAY, null, RECORDS);
        $(TABLE_VIEW_BIRTHDAY).jtable('load', options);
        $(TABLE_VIEW_BIRTHDAY).find('.jtable-toolbar-item-add-record').hide();
    });
    
    
    function birthdayTableDef(tableViewId, tableTitle){
        var tableName = TABLE_NAME_BIRTHDAY;
        var title = 'Födelsedagar';
        if(tableTitle !== null)
            title = tableTitle; 
    
        return {
            title: title,
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
                Id: {
                    title: 'id',
                    list: false,
                    key: true

                },
                Name: {
                    title: 'Namn',
                    width: '15%',
                    display: function (data){
                        return _setClassAndValue(data, "Name", PERSON);
                    }       
                },
                DateOfBirth: {
                    title: 'Född',
                    width: '7%',
                    type: 'date',
                    displayFormat: DATE_FORMAT,                
                    display: function (data){
                        return _setClassAndValue(data, "DateOfBirth", PERSON);
                    }       
                },
                Age: {
                    title: 'Blir',
                    width: '5%',
                    display: function (data){
                        return _setClassAndValue(data, "Age", PERSON);
                    }       
                },
                NextBirthday: {
                    title: 'När',
                    width: '7%',
                    type: 'date',
                    displayFormat: DATE_FORMAT,
                    display: function (data){
                        return _setClassAndValue(data, "NextBirthday", PERSON);
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
        };
    };
    
