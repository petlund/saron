/* global J_TABLE_ID, PERSON, DATE_FORMAT, HOME, PERSON_AND_HOME, OLD_HOME, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
ORG, TABLE, POS_ENABLED, 
saron,
RECORD, OPTIONS
*/
  
"use strict";
const engagementListUri = 'app/web-api/listEngagement.php';    
$(document).ready(function () {

    var mainTableViewId = saron.table.engagement.viewid;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(peopleEngagementTableDef(mainTableViewId, null, null, null));
    var postData = getPostData(null, mainTableViewId, null, saron.table.engagement.name, saron.source.list, saron.responsetype.records, engagementListUri);
    tablePlaceHolder.jtable('load', postData);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});



function peopleEngagementTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    var title = 'Ansvarsuppgifter per person';
    if(newTableTitle !== null)
        title = newTableTitle; 
    
    var tableName = saron.table.engagement.name; 
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
 
    return {
        showCloseButton: false,
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),            
        title: title,
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + saron.uri.saron + engagementListUri
        },
        fields: {
            Id: {
                key: true,
                list: false,
                edit: false,
                create: false
            },
            TablePath:{
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
                    var parentId = data.record.Id;
                    var url = 'app/web-api/listOrganizationPos.php';
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

                    var childTableDef = engagementTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event, url, clientOnly);
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
                $(mainTableViewId).find('.jtable-toolbar-item-add-record').show();
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



function engagementTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    const uri = 'app/web-api/listOrganizationPos.php';
    
    var title = "Ansvarsuppgifter";
    if(newTableTitle !== null)
        title = newTableTitle; 
    
    var tableName = saron.table.engagements.name; 
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
 

    return {
        showCloseButton: false,
        title: title,        
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Tilldela ett vakant uppdrag'},
        actions: {
            listAction:   '/' + saron.uri.saron + uri,
            createAction: '/' + saron.uri.saron + 'app/web-api/addPersonToOrganizationPos.php',
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateOrganizationPos.php'
        },
        fields: {
            Id: {
                title: 'Position',
                width: '25%',                
                create: true,
                key: true,
                options: function (data){
                    var uri = 'app/web-api/listOrganizationPos.php';
                    var field = "Id";
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, uri);
                    return  '/' + saron.uri.saron + uri + parameters;
                }
            },
            TablePath:{
                type: 'hidden',
                defaultValue: tableName
            },    
            People_FK:{
                type: 'hidden',
                defaultValue: parentId
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
                    var uri = 'app/web-api/listOrganizationPosStatus.php';
                    var field = "OrgPosStatus_FK";
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, uri);
                    return  '/' + saron.uri.saron + uri + parameters;
                }
            },            
            Comment:{
                create: !mainTableViewId.includes(saron.table.engagement.viewid),
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
                    $(mainTableViewId).find('.jtable-toolbar-item-add-record').show();
                }
            }
            else{
                $(mainTableViewId).find('.jtable-toolbar-item-add-record').hide();
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
    var url = '/' + saron.uri.saron + engagementListUri;
    var options = {record:{"Id": Id}, "clientOnly": false, "url":url};
    $(saron.table.engagement.viewid).jtable('updateRecord', options);
}

