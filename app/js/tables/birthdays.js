/* global DATE_FORMAT, PERSON, 
saron, 
RECORD, OPTIONS
*/
"use strict";

    $(document).ready(function () {
        $(saron.table.birthday.viewid).jtable(birthdayTableDef(saron.table.birthday.viewid, null));
        var options = getPostData(saron.table.birthday.viewid, null, saron.table.birthday.name, null, saron.responsetype.records);
        $(saron.table.birthday.viewid).jtable('load', options);
        $(saron.table.birthday.viewid).find('.jtable-toolbar-item-add-record').hide();
    });
    
    
    function birthdayTableDef(tableViewId, tableTitle){
        var tableName = saron.table.birthday.name;
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
                listAction:   '/' + saron.uri.saron + 'app/web-api/listPeople.php'
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
    
