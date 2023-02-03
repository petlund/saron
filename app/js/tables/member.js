/* global DATE_FORMAT,PERSON, inputFormWidth, inputFormFieldWidth,
saron,
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.member.nameId);
    var table = memberTableDef(null, null, null, null);
    tablePlaceHolder.jtable(table);
    var options = getPostData(null, saron.table.member.name, null, saron.table.member.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
});  

function memberTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.member.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        showCloseButton: false,
        title: 'Medlemsuppgifter',
        paging: true,
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
                                updateRelatedRows();  
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
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.member.name
            },
            DateOfAnonymization:{
                type: 'hidden'
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
            DateOfFriendshipStart:{
                displayFormat: DATE_FORMAT,
                type: 'date',
                title: 'Vänkontakt start',
                inputTitle: 'Sätt datum för start av vänkontakt - Endast för icke medlemmar.'
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
                    var parameters = getOptionsUrlParameters(data, tableName, parentId, tablePath, field);                    
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
            MemberStateName: {
                title: 'Status',
                edit: false,
                create: false,
                width: '4%',
                display: function (data){
                    return _setClassAndValue(data, "MemberStateName", PERSON);
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
            },
            Updated:{
                title: 'Uppdaterad',
                width: '5%',
                create: false,
                edit: false,
                display: function (data){
                    return getUpdateInfo(data);
                }
            }
        },
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
            addAttributeForEasyUpdate(data);
        },        
        recordUpdated(data, event){
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

    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configMemberTableDef(tableDef);
    
    return tableDef;    
}


function configMemberTableDef(tableDef, tablePath){
    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.member.name){
    }
    else{
        tableDef.fields.Name.list = false;
        tableDef.fields.DateOfBirth.list = false;
        tableDef.fields.MemberStateName.list = false;        
        tableDef.paging = false;
        tableDef.sorting = false;
    }    
    if(tablePathRoot === saron.table.statistics.name){
        tableDef.actions.updateAction = null;
    }
}


