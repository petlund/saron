/* global J_TABLE_ID, PERSON, DATE_FORMAT, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
ORG, POS_ENABLED, 
TABLE_VIEW_ROLE, TABLE_NAME_ROLE, 
TABLE_VIEW_UNITTYPE, TABLE_NAME_UNITTYPE,
TABLE_VIEW_ROLE_UNITTYPE, TABLE_NAME_ROLE_UNITTYPE,
TABLE_VIEW_UNIT, TABLE_NAME_UNIT,
TABLE_VIEW_ORG, TABLE_NAME_ORG,
TABLE_VIEW_UNITLIST, TABLE_NAME_UNITLIST,
TABLE_VIEW_UNITTREE, TABLE_NAME_UNITTREE,
TABLE_VIEW_ENGAGEMENT, TABLE_NAME_ENGAGEMENT
TABLE_VIEW_POS, TABLE_NAME_POS,
RECORDS, RECORD, OPTIONS, SOURCE_LIST, SOURCE_CREATE, SOURCE_EDIT
*/
 
"use strict";
    
$(document).ready(function () {

    $(TABLE_VIEW_ENGAGEMENT).jtable(peopleEngagementTableDef(TABLE_VIEW_ENGAGEMENT, null, -1, null));
    $(TABLE_VIEW_ENGAGEMENT).jtable('load');
    $(TABLE_VIEW_ENGAGEMENT).find('.jtable-toolbar-item-add-record').hide();
});


function filterPeople(viewId){
    $('#' + viewId).jtable('load', {
        searchString: $('#searchString').val(),
        groupId: $('#groupId').val(),
        tableview: viewId
    });
}


function peopleEngagementTableDef(tableViewId, parentTablePath, parentId,  childTableTitle){
    const tableName = TABLE_NAME_ENGAGEMENT;
    var tablePath = tableName;
    if(parentTablePath !== null)
        tablePath = parentTablePath + "/" + tableName;
 
    return {
        title: 'Personer',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Tilldela personen ett vakant uppdrag'},
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listEngagement.php'
        },
        fields: {
            TablePath:{
                list: true,
                edit: false,
                create: false
            },            
            Id: {
                key: true,
                list: false
            },
            Role:{
                width: '1%',
                sorting: false,
                display: function(data){
                    var src;
                    if(data.record.Engagement ===  null)
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'pos.png" title="Inga upppdrag"';
                    else
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haspos.png" title="Uppdragslista"';
                    
                    var imgTag = _setImageClass(data.record, TABLE_NAME_ENGAGEMENT, src, -1);
                    var $imgChild = $(imgTag);

                    $imgChild.click(data, function (event){
                        var $tr = $imgChild.closest('tr');
                        var childTableTitle = data.record.Name + '" har nedanstående uppdrag';
                        var parentId = data.record.Id;
                        
                        $(tableViewId).jtable('openChildTable', $tr, engagementTableDef(tableViewId, tablePath, parentId,  childTableTitle), function(data){
                            data.childTable.jtable('load');
                        });
                    });
                    return $imgChild;
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
                    return _setMailClassAndValue(data.record, "Email", '', PERSON);
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
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.OrgRole_FK !==  null)
                data.row.find('.jtable-delete-command-button').hide();
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        }
    }    
}

function engagementTableDef(tableViewId, parentTablePath, parentId,  childTableTitle){
    const tableName = TABLE_NAME_POS;
    var tablePath = tableName;
    if(parentTablePath !== null)
        tablePath = parentTablePath + "/" + tableName;
 
    return {
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
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationPos.php' + getURLParameter(parentId, tablePath, null, RECORDS),
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/addPersonToOrganizationPos.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                updatePersonEngagementRecord(parentId);
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
                        url: '/' + SARON_URI + 'app/web-api/updateOrganizationPos.php?Source=EngagementView&People_FK=' + PersonId,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                updatePersonEngagementRecord(parentId);
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
            PosId: {
                title: 'Position',
                width: '25%',                
                create: true,
                key: true,
                options: function (data){
                    if(data.source === 'list'){
                        var parameters = getURLParameter(parentId, tablePath, SOURCE_LIST, OPTIONS);
                        return '/' + SARON_URI + 'app/web-api/listOrganizationPos.php' + parameters;
                    }
                    else{
                        data.clearCache();
                        var parameters = getURLParameter(parentId, tablePath, null, OPTIONS);
                        return '/' + SARON_URI + 'app/web-api/listOrganizationPos.php' + parameters;
                    }
                }
            },
            OrgPosStatus_FK: {
                title: 'Status',
                width: '10%',
                defaultValue: 2,
                options: function (data){
                    if(data.source === 'create'){
                        var parameters = getURLParameter(parentId, tablePath, SOURCE_CREATE, OPTIONS);
                        return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php' + parameters;
                    }
                    else if(data.source === 'edit'){
                        var parameters = getURLParameter(parentId, tablePath, SOURCE_EDIT, OPTIONS);
                        return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php' + parameters;
                    }
                    else{
                        var parameters = getURLParameter(parentId, tablePath, null, OPTIONS);
                        return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php' + parameters; 
                    }
                }
            },
            
            Comment:{
                title: 'Kommentar'
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
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
            }
            addDialogDeleteListener(data);            
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit'){
                data.row[0].style.backgroundColor = "yellow";
                $('#jtable-edit-form').append('<input type="hidden" name="OrgTree_FK" value="' + data.record.Id + '" />');
            }
            data.form.css('width','600px');
            data.form.find('input[name=Comment]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        },
    };    
}



function updatePersonEngagementRecord(PersonId){
    var url = '/' + SARON_URI + 'app/web-api/listEngagement.php?Id=' + PersonId;
    var options = {record:{"Id": PersonId}, "clientOnly": false, "url":url};
    $(TABLE_VIEW_ENGAGEMENT).jtable('updateRecord', options);
}
  


function updateChildRecord(PersonName, PersonId,  Cnt){
    var $selectedRow = $("[data-record-key=" + PersonId + "]"); 
    $(TABLE_VIEW_ENGAGEMENT).jtable('closeChildTable', $selectedRow, function(){
        var $selectedRow = $("[data-record-key=" + PersonId + "]"); 
        if(Cnt > 1){
            $(TABLE_VIEW_ENGAGEMENT).jtable('openChildTable', $selectedRow, engagementTableDef(PEOPLE_ENG, PersonName, PersonId, Cnt), function(data){
                data.childTable.jtable('load');            
            });
        }
    });
}
