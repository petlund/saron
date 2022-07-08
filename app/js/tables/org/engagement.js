/* global J_TABLE_ID, PERSON, DATE_FORMAT, HOME, PERSON_AND_HOME, OLD_HOME, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
ORG, TABLE, POS_ENABLED, 
saron,
RECORD, OPTIONS
*/
  
"use strict";
$(document).ready(function () {

    var tablePlaceHolder = $("#" + saron.table.engagement.name);
    tablePlaceHolder.jtable(peopleEngagementTableDef(null, null));
    var postData = getPostData(null, saron.table.engagement.name, null, saron.table.engagement.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});



function peopleEngagementTableDef(tableTitle, tablePath){
    var title = 'Ansvarsuppgifter per person';
    if(tableTitle !== null)
        title = tableTitle; 
    
    var tableName = saron.table.engagement.name; 
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
 
    return {
        showCloseButton: false,
        title: title,
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
                defaultValue: tableName
            }, 
            Role:{
                width: '1%',
                sorting: false,
                display: function(data){
                    var childTableName = saron.table.engagements.name;
                    var childTableTitle = data.record.Name + '" har nedanstående uppdrag';
                    var tooltip = "";
                    var imgFile = "";
                    var url = saron.root.webapi + 'listOrganizationPos.php';
                    var clientOnly = true;
                    var parentId = data.record.Id;
                    var type = 0;
                    
                    if(data.record.Engagement ===  null){
                        tooltip = 'Inga uppdrag';
                        imgFile = "pos.png";
                    }
                    else{
                        tooltip = 'Uppdragslista';
                        imgFile = "haspos.png";
                    }                    

                    var childTableDef = engagementTableDef(childTableTitle, tablePath); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event.data, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event.data, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose);

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
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.OrgRole_FK !==  null)
                data.row.find('.jtable-delete-command-button').hide();
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org'){ 
                "#" + tableName.find('.jtable-toolbar-item-add-record').show();
            }
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
}



function engagementTableDef(tableTitle, tablePath){
    
    var title = "Ansvarsuppgifter";
    if(tableTitle !== null)
        title = tableTitle; 
    
    var tableName = saron.table.engagements.name; 
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
 

    return {
        showCloseButton: false,
        title: title,        
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Tilldela ett vakant uppdrag'},
        actions: {
            listAction:   saron.root.webapi + 'listOrganizationPos.php',
            createAction: saron.root.webapi + 'addPersonToOrganizationPos.php',
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
                    var parentId = data.record.ParentId;
                    var parameters = getOptionsUrlParameters(data, tableName, parentId, tablePath, field);
                    return  url + parameters;
                }
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: tableName
            },    
            People_FK:{
                type: 'hidden',
                defaultValue: -1
            },
            ParentId:{
                type: 'hidden',
                defaultValue: -1
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
                    var parentId = data.record.ParentId;
                    var parameters = getOptionsUrlParameters(data, tableName, parentId, tablePath, field);
                    return  url + parameters;
                }
            },            
            Comment:{
                create: !tableName.includes(tablePath),
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
            updatePersonEngagementRecord(data.record.ParentId);
            if(data.record.OrgPosStatus_FK > 3){ // set vacancy
                var childTable = event.target.closest('div.jtable-child-table-container');
                $(childTable).jtable("deleteRecord",{key: data.record.Id, clientOnly:true, animationsEnabled:true});
            }
        },
        recordAdded(event, data){
            updatePersonEngagementRecord(data.record.ParentId);            
        },
        recordsLoaded: function(event, data) {
            if(!tablePath.includes(saron.table.people.name)){
                if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org'){ 
                    $("#" + tableName).find('.jtable-toolbar-item-add-record').show();
                }
            }
            else{
                $("#" + tableName).find('.jtable-toolbar-item-add-record').hide();
            }
        },        
        rowInserted: function(event, data){
            data.row.find('.jtable-delete-command-button').hide();
            if ((data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org') || tablePath.includes(saron.table.people.name)){
                data.row.find('.jtable-edit-command-button').hide();
            }
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
}



function updatePersonEngagementRecord(Id){
    var url = saron.root.webapi + 'listEngagement.php';
    var options = {record:{"Id": Id}, "clientOnly": false, "url":url};
    $(saron.table.engagement.nameId).jtable('updateRecord', options);
}

