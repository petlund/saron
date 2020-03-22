"use strict";

$(document).ready(function () {

    $('#member').jtable({
        title: 'Medlemsuppgifter',
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'FamilyName ASC, DateOfBirthr ASC', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php',
            updateAction: function(data) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=membership',
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result !== 'ERROR'){
                                _updateFields(data.Record[0], "MemberState", PERSON);                                                
                                _updateFields(data.Record[0], "VisibleInCalendar", PERSON);                                                
                                _updateFields(data.Record[0], "DateOfMembershipStart", PERSON);                                                
                                _updateFields(data.Record[0], "DateOfMembershipEnd", PERSON);                                                
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            }
            //deleteAction: 'delete.php'
        },
        fields: {
            PersonId: {
                key: true,
                list: false,
                update: false
            },
            Name: {
                title: 'Namn',
                width: '15%',
                edit: false,
                display: function (data){
                    return _setClassAndValue(data.record, "Name", PERSON);
                }       
            },
            DateOfBirth: { 
                title: 'Född',
                width: '7%',
                edit: false,
                type: 'date',
                display: function (data){
                    return _setClassAndValue(data.record, "DateOfBirth", PERSON);
                }       
            },
            PreviousCongregation: {
                title: 'Kommit från församling',
                width: '15%'
            },
            DateOfMembershipStart: {
                width: '7%',
                type: 'date',
                title: 'Start',
                display: function (data){
                    return _setClassAndValue(data.record, "DateOfMembershipStart", PERSON);
                }       
            },
            MembershipNo: {
                width: '3%',
                title: 'Nr.',
                display: function (data){
                    return _setClassAndValue(data.record, "MembershipNo", PERSON);
                },       
                options: function(data){
                    if(clearMembershipNoOptionCache){
                        data.clearCache();
                        clearMembershipNoOptionCache=false;
                    }
                    return '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=' + data.record.PersonId + '&selection=nextMembershipNo';
                }
            },
            DateOfMembershipEnd: {
                width: '7%',
                type: 'date',
                title: 'Avslut',
                display: function (data){
                    return _setClassAndValue(data.record, "DateOfMembershipEnd", PERSON);
                }       
            },
            NextCongregation: {
                width: '15%',
                title: 'Flyttat till församling'
            },
            MemberState:{
                width: '7',
                edit: false,
                title: 'Status',
                display: function (data){
                    return _setClassAndValue(data.record, "MemberState", PERSON);
                }       
            },
            VisibleInCalendar: {
                edit: 'true',
                title: 'Kalender',
                inputTitle: 'Synlig i adresskalendern',
                width: '4%',
                options: _visibilityOptions()
            },
            Comment: {
                type: 'textarea',
                width: '46%',
                title: 'Not'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
        },        
        formCreated: function (event, data){
            data.row[0].style.backgroundColor = "yellow";
            data.form.css('width',inputFormWidth);
            data.form.find('input[name=PreviousCongregation]').css('width',inputFormFieldWidth);
            data.form.find('input[name=NextCongregation]').css('width',inputFormFieldWidth);
            data.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);                                

            var dbox = document.getElementsByClassName('ui-dialog-title');            
            for(var i=0; i<dbox.length; i++)
                dbox[i].innerHTML='Uppdatera uppgifter för: ' + data.record.FirstName + ' ' + data.record.LastName;
        },
        formClosed: function (event, data){
            data.row[0].style.backgroundColor = '';
        }    
    });
    //Re-load records when user click 'load records' button.
    $('#search_member').click(function (e) {
        e.preventDefault();
        filterPeople('member');
    });

    //Load all records when page is first shown
    $('#search_member').click();
});
    
