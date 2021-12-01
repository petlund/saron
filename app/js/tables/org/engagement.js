/* global J_TABLE_ID, PERSON, DATE_FORMAT, HOME, PERSON_AND_HOME, OLD_HOME, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
ORG, TABLE, POS_ENABLED, 
saron,
RECORD, OPTIONS
*/
 
"use strict";
const engagementListUri = 'app/web-api/listEngagement.php';    
$(document).ready(function () {

    $(saron.table.engagement.viewid).jtable(peopleEngagementTableDef(saron.table.engagement.viewid, saron.table.engagement.name));
    var postData = getPostData(null, saron.table.engagement.viewid, null, saron.table.engagement.name, saron.source.list, saron.responsetype.records, engagementListUri);
    $(saron.table.engagement.viewid).jtable('load', postData);
    $(saron.table.engagement.viewid).find('.jtable-toolbar-item-add-record').hide();
});



function peopleEngagementTableDef(tableViewId, tablePath){
    const tableName = saron.table.engagement.name;
 
    return {
        showCloseButton: false,
        title: 'Personer',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + saron.uri.saron + engagementListUri,
        },
        fields: {
            TablePath:{
                list: false,
                edit: false,
                create: false
            },            
            Id: {
                key: true,
                list: false,
                edit: false,
                create: false
            },
            Role:{
                width: '1%',
                sorting: false,
                display: function(data){
                    var childTableName = saron.table.engagements.name;
                    var childTablePath = tablePath + "/" + childTableName;
                    var childTableTitle = data.record.Name + '" har nedanstående uppdrag';
                    var tooltip = "";
                    var imgFile = "";
                    var childUri = 'app/web-api/listOrganizationPos.php';

                    if(data.record.Engagement ===  null){
                        tooltip = 'Inga uppdrag';
                        imgFile = "pos.png";
                    }
                    else{
                        tooltip = 'Uppdragslista';
                        imgFile = "haspos.png";
                    }                    

                    var childTableDef = engagementTableDef(tableViewId, childTablePath, childTableTitle);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, TABLE, childUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, TABLE, engagementListUri);

                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                }
            },
            Name: {
                title: 'Namn',
                width: '15%'
            },
            MemberState: {
                title: 'Status'
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
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
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



function engagementTableDef(tableViewId, tablePath, childTableTitle){
    const uri = 'app/web-api/listOrganizationPos.php';
    return {
        showCloseButton: false,
        title: function (){
            if(childTableTitle !== null)
                return childTableTitle;
            else
                return 'Positioner';
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Tilldela personen ett vakant uppdrag'},
        actions: {
            listAction:   '/' + saron.uri.saron + uri,
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + saron.uri.saron + 'app/web-api/addPersonToOrganizationPos.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                updatePersonEngagementRecord(data.Record.ParentId);
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + saron.uri.saron + 'app/web-api/updateOrganizationPos.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                updatePersonEngagementRecord(data.Record.ParentId);
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
                title: 'Position',
                width: '25%',                
                create: true,
                key: true,
                options: function (data){
                    var uri = 'app/web-api/listOrganizationPos.php';
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, uri);
                    return  '/' + saron.uri.saron + uri + parameters;
                }
            },
            ParentId:{
                type: 'hidden'                
            },
            People_FK:{
                type: 'hidden'                
            },
            TablePath:{
                type: 'hidden'                
            },            
            OrgPosStatus_FK: {
                title: 'Status',
                width: '10%',
                defaultValue: 2,
                options: function (data){
                    var uri = 'app/web-api/listOrganizationPosStatus.php'
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, uri);
                    return  '/' + saron.uri.saron + uri + parameters;
                }
            },
            
            Comment:{
                title: 'Kommentar kopplat till uppdraget',
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
            if(data.record.OrgPosStatus_FK > 3){ // set vacancy
                updateChildRecord(PersonName, parentId, data.record.Cnt);
            }
        },
        rowInserted: function(event, data){
            data.row.find('.jtable-delete-command-button').hide();
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
            }
            addDialogDeleteListener(data);            
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org'){ 
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit){
                data.row[0].style.backgroundColor = "yellow";
                $('#jtable-edit-form').append('<input type="hidden" name="OrgTree_FK" value="' + data.record.Id + '" />');
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
    var url = '/' + saron.uri.saron + 'app/web-api/listEngagement.php?Id=' + Id;
    var options = {record:{"Id": Id}, "clientOnly": false, "url":url};
    $(saron.table.engagement.viewid).jtable('updateRecord', options);
}
  


function updateChildRecord(PersonName, Id,  Cnt){
    var $selectedRow = $("[data-record-key=" + Id + "]"); 
    $(saron.table.engagement.viewid).jtable('closeChildTable', $selectedRow, function(){
        var $selectedRow = $("[data-record-key=" + Id + "]"); 
        if(Cnt > 1){
            $(saron.table.engagement.viewid).jtable('openChildTable', $selectedRow, engagementTableDef(PEOPLE_ENG, PersonName, Id, Cnt), function(data){
                data.childTable.jtable('load');            
            });
        }
    });
}
