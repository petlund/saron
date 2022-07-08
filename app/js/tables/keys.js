/* global DATE_FORMAT, 
saron, 
PERSON, inputFormWidth, inputFormFieldWidth 
*/

"use strict";

$(document).ready(function () {
    var mainTableViewId = saron.table.keys.nameId;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(keyTableDef(mainTableViewId, null, null, null));
    tablePlaceHolder.jtable('load');
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});  

function keyTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    var title = 'Nyckelinnehav'; 
    if(newTableTitle !== null)
        title = newTableTitle; 
    
    var tableName = saron.table.keys.name; 
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
    
    return {
        title: title,
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),            
        showCloseButton: false,        
        paging: mainTableViewId[0].includes(saron.table.keys.nameId), //Enable paging
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
                defaultValue: parentId,
                type: 'hidden'
            },
            AppCanvasName:{
                defaultValue: tableName,
                type: 'hidden'
            },
            Name: {
                title: 'Namn',
                width: '10%',
                list: includedIn(mainTableViewId, saron.table.keys.nameId),
                create: false,
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }                 
            },
            DateOfBirth: {
                title: 'Född',
                list: includedIn(mainTableViewId, saron.table.keys.nameId),
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
                list: includedIn(mainTableViewId, saron.table.keys.nameId),
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

