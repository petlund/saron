/* global DATE_FORMAT, 
saron, 
PERSON, inputFormWidth, inputFormFieldWidth 
*/

"use strict";

$(document).ready(function () {

    $(saron.table.keys.viewid).jtable(keyTableDef(saron.table.keys.viewid, null, null));
    $(saron.table.keys.viewid).jtable('load');
    $(saron.table.keys.viewid).find('.jtable-toolbar-item-add-record').hide();
});  

function keyTableDef(tableViewId, tablePath, tableTitle, parentId){
    var tableName = saron.table.keys.name; 
    var title = 'Nyckelinnehav'; 
    if(tableTitle !== null)
        title = tableTitle; 
    
    return {
        title: title,
        showCloseButton: false,        
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'news_date desc', //Set default sorting        
        actions: {
            listAction:   '/' + saron.uri.saron + 'app/web-api/listPeople.php',
            updateAction:   '/' + saron.uri.saron + 'app/web-api/updatePerson.php?selection=keyHolding'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            TablePath:{
                defaultValue: tableName,
                type: 'hidden'
            },
            Name: {
                title: 'Namn',
                width: '10%',
                list: includedIn(tableViewId, saron.table.keys.viewid),
                create: false,
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }                 
            },
            DateOfBirth: {
                title: 'Född',
                list: includedIn(tableViewId, saron.table.keys.viewid),
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
                list: includedIn(tableViewId, saron.table.keys.viewid),
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
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === saron.userrole.editor){ 
                $('#KEYS').find('.jtable-toolbar-item-add-record').show();
            }
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

