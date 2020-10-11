/* global SARON_URI, DATE_FORMAT, NEWS */
"use strict";

var TABLE_MEMBER_STATE = '#MEMBER_STATE';
    
$(document).ready(function () {
    $(TABLE_MEMBER_STATE).jtable(memberstateTableDef());
    $(TABLE_MEMBER_STATE).jtable('load');
    $(TABLE_MEMBER_STATE).find('.jtable-toolbar-item-add-record').hide();
});


function clog(lbl, str){
    console.log(lbl + ': ' + str);
}

function memberstateTableDef(){
    return {
        title: 'Personstatus',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'news_date desc', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listMemberState.php?ts=',
            //createAction:   '/' + SARON_URI + 'app/web-api/createNews.php',
            //updateAction:   '/' + SARON_URI + 'app/web-api/updateNews.php'
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updateMemberState.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
//                            var rec = {
//                                    record: {
//                                        'Id': 1, 
//                                        'FilterUpdate': 1, 
//                                        'FilterCreate': 1
//                                    },
//                                    'clientOnly': true,
//                                    'animationsEnabled': false
//                                    };
//                            $(TABLE_MEMBER_STATE).jtable('updateRecord', rec);        
                            
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            //deleteAction: '/' + SARON_URI + 'app/web-api/deleteNews.php'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            Name: {
                edit: false,
                title: 'Namn',
                width: '15%',
            },
            Description: {
                edit: true,
                title: 'Beskrivning',
                width: '40%'
            },
            FilterUpdate: {
                edit: true,
                title: 'Filter för uppdatering',
                width: '15%',
                options: {"0":"Nej", "1":"Ja"}
            },
            FilterCreate: {
                edit: true,
                title: 'Filter för lägga till',
                width: '15%',
                options: {"0":"Nej", "1":"Ja"}
            },
            Updater: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '5%',
                options: function (){
                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php?selection=MemberState';           
                }
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: DATE_FORMAT,
                width: '5%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit'){ 
                $('#NEWS').find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        },
        deleteFormCreated: function (event, data){
            data.row[0].style.backgroundColor = 'red';
        },
        deleteFormClosed: function (event, data){
            data.row[0].style.backgroundColor = '';
        }
    };
};
