/* global J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";

const PEOPLE_ENG = "#PEOPLE_ENG";
const CHILD_TABLE_PREFIX = 'child-to-parent-';
    
$(document).ready(function () {

    $(PEOPLE_ENG).jtable(peopleEngagementTableDef(PEOPLE_ENG, -1, null));
    $(PEOPLE_ENG).jtable('load');
    $(PEOPLE_ENG).find('.jtable-toolbar-item-add-record').hide();
});


function filterPeople(viewId){
    $('#' + viewId).jtable('load', {
        searchString: $('#searchString').val(),
        groupId: $('#groupId').val(),
        tableview: viewId
    });
}


function peopleEngagementTableDef(tableId, roleId, roleName){
    return {
        title: 'Personer',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listEngagement.php'
        },
        fields: {
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
                    
                    var imgTag = _setImageClass(data.record, "Role", src, -1);
                    var $imgChild = $(imgTag);

                    $imgChild.click(data, function (event){
                        var $tr = $imgChild.closest('tr');
                        var childTableId = CHILD_TABLE_PREFIX + data.record.Id;
                        $(tableId).jtable('openChildTable', $tr, engagementTableDef(tableId, childTableId, data.record.Id, data.record.Name), function(data){
                            data.childTable.jtable('load');
                            var childTable = data.childTable;
                            childTable[0].className += ' ' + childTableId;
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
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.OrgRole_FK !==  null)
                data.row.find('.jtable-delete-command-button').hide();
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit'){ 
                $(tableId).find('.jtable-toolbar-item-add-record').show();
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
        },
        deleteFormCreated: function (event, data){
            data.row[0].style.backgroundColor = 'red';
        },
        deleteFormClosed: function (event, data){
            data.row[0].style.backgroundColor = '';
        }
    }    
}

function engagementTableDef(tableId, childTableRef, people_FK, personName){
    return {
        title: 'Uppdrag för ' + personName,
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?selection=engagement&People_FK=' + people_FK,
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/addPersonToOrganizationPos.php?People_FK=' + people_FK,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                updatePersonEngagementRecord(people_FK);
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
                        url: '/' + SARON_URI + 'app/web-api/updateOrganizationPos.php?Source=EngagementView&People_FK=' + people_FK,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                if(data.Record.OrgPosStatus_FK > 3){
                                    var $selectedRow = $("[data-record-key=" + data.Record.PosId + "]");
                                    $('.' + childTableRef).jtable('deleteRows', $selectedRow);
                                }
                                updatePersonEngagementRecord(people_FK);
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationPos.php'
        },
        fields: {
            PosId: {
                title: 'Position',
                width: '25%',                
                create: true,
                key: true,
                options: function (data){
                    if(data.source === 'list')
                        return '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?selection=positionAsOptions';
                    else{
                        data.clearCache();
                        return '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?selection=vacantPositionsAsOptions';
                    }
                }
            },
            OrgPosStatus_FK: {
                title: 'Status',
                width: '10%',
                defaultValue: 2,
                options: function (data){
                    if(data.source === 'create')
                        return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php?selection=options&statusfilter=engagement_create';
                    else if(data.source === 'edit')
                        return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php?selection=options&statusfilter=engagement_edit';
                    else
                        return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php?selection=options&statusfilter=no';
            
                }
            }
        },
        rowInserted: function(event, data){
            data.row.find('.jtable-delete-command-button').hide();
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
            }
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit'){ 
                $(tableId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit'){
                data.row[0].style.backgroundColor = "yellow";
                $('#jtable-edit-form').append('<input type="hidden" name="OrgTree_FK" value="' + data.record.OrgTree_FK + '" />');
            }
            data.form.css('width','600px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        },
        deleteFormCreated: function (event, data){
            data.row[0].style.backgroundColor = 'red';
        },
        deleteFormClosed: function (event, data){
            data.row[0].style.backgroundColor = '';
        }
    };    
}
function updatePersonEngagementRecord(people_FK){
    var url = '/' + SARON_URI + 'app/web-api/listEngagement.php?Id=' + people_FK;
    var options = {record:{"Id": people_FK}, "clientOnly": false, "url":url};
    $(PEOPLE_ENG).jtable('updateRecord', options);
}

function updateRecord(people_FK){
    var url = '/' + SARON_URI + 'app/web-api/listEngagement.php?Id=' + people_FK;
    var options = {record:{"Id": people_FK}, "clientOnly": false, "url":url};
    $(PEOPLE_ENG).jtable('deleteRecord', options);
}