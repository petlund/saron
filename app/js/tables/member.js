/* global DATE_FORMAT, SARON_URI, PERSON, inputFormWidth, inputFormFieldWidth,
TABLE_VIEW_MEMBER, TABLE_NAME_MEMBER,
RECORD, RECORDS,OPTIONS
 */

"use strict";

$(document).ready(function () {


    $(TABLE_VIEW_MEMBER).jtable(memberTableDef(TABLE_VIEW_MEMBER, null));
    var options = getPostData(TABLE_VIEW_MEMBER, null, TABLE_NAME_MEMBER, null, RECORDS);
    $(TABLE_VIEW_MEMBER).jtable('load', options);
    $(TABLE_VIEW_MEMBER).find('.jtable-toolbar-item-add-record').hide();
});  

function memberTableDef(tableViewId, tableTitle){
    var tableName = TABLE_NAME_MEMBER;
    var title = 'Medlemsuppgifter';
    if(tableTitle !== null)
        title = tableTitle; 

    return {
            showCloseButton: false,
            title: title,
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'FamilyName ASC, DateOfBirthr ASC', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php?X=Y',
            updateAction: function(data) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=membership',
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                _updateFields(data.Record, "MemberState", PERSON);                                                
                                _updateFields(data.Record, "VisibleInCalendar", PERSON);                                                
                                _updateFields(data.Record, "DateOfMembershipStart", PERSON);                                                
                                _updateFields(data.Record, "DateOfMembershipEnd", PERSON);                                                
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
            Id: {
                key: true,
                list: false
            },
            Name: {
                title: 'Namn',
                width: '15%',
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }       
            },
            DateOfBirth: { 
                title: 'Född',
                width: '7%',
                edit: false,
                type: 'date',
                displayFormat: DATE_FORMAT,
                display: function (data){
                    return _setClassAndValue(data, "DateOfBirth", PERSON);
                }       
            },
            PreviousCongregation: {
                title: 'Kommit från församling',
                width: '15%'
            },
            DateOfMembershipStart: {
                width: '7%',
                type: 'date',
                displayFormat: DATE_FORMAT,
                title: 'Start',
                display: function (data){
                    return _setClassAndValue(data, "DateOfMembershipStart", PERSON);
                }       
            },
            MembershipNo: {
                width: '3%',
                title: 'Nr.',
                display: function (data){
                    return _setClassAndValue(data, "MembershipNo", PERSON);
                },       
                options: function(data){
                    if(clearMembershipNoOptionCache){
                        data.clearCache();
                        clearMembershipNoOptionCache=false;
                    }
                    return '/' + SARON_URI + 'app/web-api/listPerson.php?Id=' + data.record.Id + '&selection=nextMembershipNo';
                }
            },
            DateOfMembershipEnd: {
                width: '7%',
                type: 'date',
                displayFormat: DATE_FORMAT,
                title: 'Avslut',
                display: function (data){
                    return _setClassAndValue(data, "DateOfMembershipEnd", PERSON);
                }       
            },
            NextCongregation: {
                width: '15%',
                title: 'Flyttat till församling'
            },
            MemberState:{
                width: '7',
                edit: false,
                title: 'Status'    
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
    };
}

