/* global DATE_FORMAT,PERSON, inputFormWidth, inputFormFieldWidth,
saron,
RECORD, OPTIONS
 */

"use strict";

const memberListUri = 'app/web-api/listPeople.php';
$(document).ready(function () {

    $(saron.table.member.viewid).jtable(memberTableDef(saron.table.member.viewid, saron.table.member.name, null));
    var options = getPostData(null, saron.table.member.viewid, null, saron.table.member.name, saron.source.list, saron.responsetype.records, memberListUri);
    $(saron.table.member.viewid).jtable('load', options);
    $(saron.table.member.viewid).find('.jtable-toolbar-item-add-record').hide();
});  

function memberTableDef(tableViewId, tablePath, tableTitle){
    var tableName = saron.table.member.name;
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
            listAction:   '/' + saron.uri.saron + memberListUri,
            updateAction: function(data) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + saron.uri.saron + 'app/web-api/updatePerson.php?selection=membership',
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                            
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            }
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
                list: includedIn (tableViewId, saron.table.member.viewid),
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }       
            },
            TablePath:{
                type: 'hidden',
                defaultValue: tableName
            },
            DateOfBirth: { 
                title: 'Född',
                width: '7%',
                edit: false,
                type: 'date',
                list: includedIn (tableViewId, saron.table.member.viewid),
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
                options: function (data){
                    var uri =  'app/web-api/listPerson.php';
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, uri);
                    return '/' + saron.uri.saron + uri + parameters;
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
            if (data.record.user_role !== saron.userrole.editor){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
        },       
        recordUpdated(data, event){
            _updateFields(event, "MemberState", PERSON);                                                
            _updateFields(event, "VisibleInCalendar", PERSON);                                                
            _updateFields(event, "DateOfMembershipStart", PERSON);                                                
            _updateFields(event, "DateOfMembershipEnd", PERSON);                
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

