/* global DATE_FORMAT, 
saron, 
PERSON, inputFormWidth, inputFormFieldWidth 
*/

"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.keys.nameId);
    var table = keyTableDef(null, saron.table.keys.name, null, null);
    tablePlaceHolder.jtable(table);
    var postData = getPostData(null, saron.table.keys.name, null, saron.table.keys.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
});  

function keyTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.keys.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title: 'Nyckelinnehav',
        showCloseButton: false,        
        paging: true,
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
                defaultValue: saron.table.keys.name,
                type: 'hidden'
            },
            AppCanvasPath:{
                defaultValue: saron.table.keys.name,
                type: 'hidden'
            },
            Name: {
                title: 'Namn',
                width: '10%',
                list: true,
                create: false,
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }                 
            },
            DateOfBirth: {
                title: 'Född',
                list: true,
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
                list: true,
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
            alowedToUpdateOrDelete(event, data, tableDef);
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

    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    
    configKeysTableDef(tableDef);

    return tableDef;
}
    

function configKeysTableDef(tableDef){
    var appCanvasRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(appCanvasRoot !== saron.table.keys.name){
        tableDef.fields.Name.list = false;
        tableDef.fields.DateOfBirth.list = false;
        tableDef.fields.MemberState.list = false;        
        tableDef.paging = false;
    }    
    if(appCanvasRoot === saron.table.statistics.name){
        tableDef.actions.updateAction = null;
    }
}