/* global J_TABLE_ID, PERSON, DATE_FORMAT, HOME, PERSON_AND_HOME, OLD_HOME, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
ORG, TABLE, POS_ENABLED, 
saron,
RECORD, OPTIONS
*/
  
"use strict";
$(document).ready(function () {

    var tablePlaceHolder = $(saron.table.engagement.nameId);
    tablePlaceHolder.jtable(peopleEngagementTableDef(null, null, null, null));
    var postData = getPostData(null, saron.table.engagement.name, null, saron.table.engagement.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);

    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();

});



function peopleEngagementTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.engagement.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        showCloseButton: false,
        title: 'Ansvarsuppgifter per person',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction: saron.root.webapi + 'listEngagement.php'
        },
        fields: {
            Id: {
                key: true,
                list: false,
                edit: false,
                create: false
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.engagement.name
            }, 
            Role:{
                width: '1%',
                sorting: false,
                display: function(data){
                    var childTableTitle = data.record.Name + ' har nedanstående uppdrag';
                    var tooltip = "";
                    var imgFile = "";
                    var clientOnly = true;
                    var type = 0;
                    
                    if(data.record.Engagement ===  null){
                        tooltip = 'Inga uppdrag';
                        imgFile = "pos.png";
                    }
                    else{
                        tooltip = 'Uppdragslista';
                        imgFile = "haspos.png";
                    }                    

                    var childTableDef = engagementsTableDef(childTableTitle, tablePath, data.record.Id, childTableDef);
                    childTableDef.parentTableDef = tableDef;
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                    var $imgClose = getImageCloseTag(data, childTableDef, type);
                        
                    $imgChild.click(data, function (event){
                        openChildTable(childTableDef, $imgChild, event.data, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        closeChildTable(childTableDef, $imgClose, event.data, clientOnly);
                    });    

                    return getClickImg(data, childTableDef, $imgChild, $imgClose);

                }
            },
            Name: {
                title: 'Namn',
                width: '15%'
            },
            MemberState: {
                title: 'Status'
            },
            DateOfMembershipStart: {
                title: 'Medlemskap Start'
            },
            Email: {
                title: 'Mail',
                display: function (data){
                    return _setMailClassAndValue(data, "Email", '', PERSON);
                }       
            },
            Mobile: {
                title: 'Mobil'
            },
            Hosted:{
                title: 'Bostadsort'
            },
            Cnt: {
                title: 'Antal',
                width: '5%'
            },
            Engagement: {
                title: 'Uppdragsöversikt',
                width: '50%'
            }
        },
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
            if(data.record.OrgRole_FK !==  null)
                data.row.find('.jtable-delete-command-button').hide();        
        },        
        recordsLoaded: function(event, data) {
            alowedToAddRecords(event, data, tableDef);
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };  
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configEngagementsTableDef(tableDef);
    
    return tableDef;    
}


function configEngagementsTableDef(tableDef){
    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot !== saron.table.engagement.name){
        
    }
}


function engagementsTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.engagements.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        showCloseButton: false,
        title: "Ansvarsuppgifter",        
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Tilldela ett vakant uppdrag'},
        actions: {
            listAction:   saron.root.webapi + 'listOrganizationPos.php',
            createAction: saron.root.webapi + 'createOrganizationPos.php',
            updateAction: saron.root.webapi + 'updateOrganizationPos.php'
        },
        fields: {
            Id: {
                title: 'Position',
                width: '25%',                
                create: true,
                key: true,
                options: function (data){
                    var url = saron.root.webapi + 'listOrganizationPos.php';
                    var field = "Id";
                        var parameters = getOptionsUrlParameters(data, saron.table.engagements.name, parentId, tableDef.tablePath, field);
                    return  url + parameters;
                }
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: tableName
            },    
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: tablePath
            },    
            People_FK:{
                type: 'hidden',
                defaultValue: -1
            },
            ParentId:{
                type: 'hidden',
                defaultValue: parentId
            },
            OrgTree_FK:{
                type: 'hidden'
            },
            OrgSuperPos_FK:{
                type: 'hidden'
            },
            OrgPosStatus_FK: {
                title: 'Status',
                width: '10%',
                defaultValue: 2,
                options: function (data){
                    var url = saron.root.webapi + 'listOrganizationPosStatus.php';
                    var field = "OrgPosStatus_FK";
                    var parameters = getOptionsUrlParameters(data, saron.table.engagement.name, parentId, tableDef.tablePath, field);
                    return  url + parameters;
                }
            },            
            Comment:{
                create: function(data){
                    return !data.record.AppCanvasPath.includes(saron.table.people.name)
                },
                title: 'Kommentar',
                inputTitle: 'Kommentar kopplat till uppdraget ej person'
            },
            UpdaterName: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '5%'
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
        recordUpdated: function(event, data){
            updateParentRow(event, data, tableDef);
            
            if(data.record.OrgPosStatus_FK > 3){ // set vacancy
                var childTable = event.target.closest('div.jtable-child-table-container');
                $(childTable).jtable("deleteRecord",{key: data.record.Id, clientOnly:true, animationsEnabled:true});
            }
        },
        recordsLoaded: function(event, data) {            
            updateParentRow(event, data, tableDef);
            alowedToAddRecords(event, data, tableDef);
        },        
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
            addDialogDeleteListener(data);            
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit){
                data.row[0].style.backgroundColor = "yellow";
            }

            data.form.css('width','600px');
            data.form.find('input[name=Comment]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        },
    };    
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configEngagementstTableDef(tableDef);
    
    return tableDef;    
}


function configEngagementstTableDef(tableDef){
    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.statistics.name){
        tableDef.actions.updateAction  = null;
        tableDef.actions.createAction  = null;        
    }
}



function updateParentRow(event, data, tableDef){
    var parentTableName = tableDef.parentTableDef.tableName;
    var parentTablePath = tableDef.parentTableDef.tablePath;
    var parentListUrl = tableDef.parentTableDef.actions.listAction;
    var parentPostData = getPostData(tableDef.parentId, parentTableName, null, parentTablePath, null, saron.responsetype.record);

    var parentPlaceHolder = getParentTablePlaceHolderFromChild(event.target, tableDef.tablePath);
    var record = parentPostData;
    record.OpenChildTable = tableDef.tableName;
    
    var options = {record, "clientOnly": false, "url":parentListUrl};
    parentPlaceHolder.jtable('updateRecord', options);
}

