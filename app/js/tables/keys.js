/* global DATE_FORMAT, 
saron, 
PERSON, inputFormWidth, inputFormFieldWidth 
*/

"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $("#" + saron.table.keys.name);
    tablePlaceHolder.jtable(keyTableDef(null, null));
    tablePlaceHolder.jtable('load');
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});  

function keyTableDef(tableTitle, tablePath){
    var title = 'Nyckelinnehav'; 
    if(tableTitle !== null)
        title = tableTitle; 
    
    var tableName = saron.table.keys.name; 
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
    
    return {
        title: title,
        showCloseButton: false,        
        paging: tablePath.startsWith(saron.table.keys.name), //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'news_date desc', //Set default sorting        
        actions: {
            listAction:   saron.root.webapi + 'listPeople.php',
            updateAction: saron.root.webapi + 'updatePerson.php'
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
            AppCanvasName:{
                defaultValue: tableName,
                type: 'hidden'
            },
            AppCanvasPath:{
                defaultValue: tableName,
                type: 'hidden'
            },
            Name: {
                title: 'Namn',
                width: '10%',
                list: includedIn(saron.table.keys.name, tablePath),
                create: false,
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }                 
            },
            DateOfBirth: {
                title: 'Född',
                list: includedIn(saron.table.keys.name, tablePath),
                edit: false,
                width: '5%',
                type: 'date',
                displayFormat: DATE_FORMAT,
                display: function (data){
                    return _setClassAndValue(data, "DateOfBirth", PERSON);
                }       
            },
            MemberState:{
                edit: false,
                create: false,
                list: includedIn(saron.table.keys.name, tablePath),
                title: 'Status',
                width: '5%',                
            },
            KeyToChurch: {
                edit: true,
                create: true, 
                title: 'Kodad nyckel',
                width: '5%',
                 display: function (data){
                    return _setClassAndValue(data, "KeyToChurch", PERSON);
                },                  
               options: _keyOptions()
            },
            KeyToExp: {
                edit: true, 
                create: true, 
                title: 'Vanlig nyckel',
                width: '5%',
                display: function (data){
                    return _setClassAndValue(data, "KeyToExp", PERSON);
                },                  
                options: _keyOptions()
            },
            CommentKey: {
                create: false,
                list: true,
                title: 'Not - Nycklar',
                width: '30%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== saron.userrole.editor)
                data.row.find('.jtable-edit-command-button').hide();
            else
                data.row.find('.jtable-delete-command-button').hide();
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit){
                data.row[0].style.backgroundColor = "yellow";

                data.form.css('width',inputFormWidth);
                data.form.find('input[name=CommentKey]').css('width',inputFormFieldWidth);
            }
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };
}

