/* global DATE_FORMAT,PERSON, inputFormWidth, inputFormFieldWidth,
saron,
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $("#" + saron.table.member.name);
    tablePlaceHolder.jtable(memberTableDef(null, null));
    var options = getPostData(null, saron.table.member.name, null, saron.table.member.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
    //tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});  

function memberTableDef(tableTitle){
    var title = 'Medlemsuppgifter';
    if(tableTitle !== null)
        title = tableTitle; 
    

    return {
        appCanvasName: saron.table.member.name,
        showCloseButton: false,
        title: title,
        paging: function (data){
            return data.record.AppCanvasPath.startsWith(saron.table.member.name)
        }, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'FamilyName ASC, DateOfBirthr ASC', //Set default sorting        
        actions: {
            listAction:   saron.root.webapi + 'listPeople.php',
            updateAction: function(data) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: saron.root.webapi + 'updatePerson.php',
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
            ParentId:{
                defaultValue: -1,
                type: 'hidden'
            },
            Name: {
                title: 'Namn',
                width: '15%',
                edit: false,
                list: function(data){
                    return includedIn (saron.table.member.name, data.record.AppCanvasPath);
                },
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }       
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.member.name
            },
            DateOfBirth: { 
                title: 'Född',
                width: '7%',
                edit: false,
                type: 'date',
                list: function(data){
                    return includedIn (saron.table.member.name, data.record.AppCanvasPath);
                },
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
                    var url =  saron.root.webapi + 'listPeople.php';
                    var field = "MembershipNo";
                    var parameters = getOptionsUrlParameters(data, saron.table.member.name, data.record.ParentId, tablePath, field);
                    return url + parameters;
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

