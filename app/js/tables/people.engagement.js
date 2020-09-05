/* global J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";
    
$(document).ready(function () {
    const TABLE_ID = "#PEOPLE_ENG";

    $(TABLE_ID).jtable(peopleEngagementTableDef(TABLE_ID, -1, null));
    $(TABLE_ID).jtable('load');
    $(TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
});

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
            listAction:   '/' + SARON_URI + 'app/web-api/listPeopleEngagement.php'
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
                        $(tableId).jtable('openChildTable', $tr, engagementTableDef(tableId, data.record.People_FK, data.record.Name), function(data){
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

function engagementTableDef(tableId, personId, personName){
    return {
        title: 'Uppdrag för ' + personName,
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?selection=pos&People_FK=' + personId,
            createAction:   '/' + SARON_URI + 'app/web-api/updateOrganizationPos.php?selection=pos&People_FK=' + personId,
            //updateAction:   '/' + SARON_URI + 'app/web-api/updateOrganizationPos.php',
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updateOrganizationPos.php?People_FK=' + personId,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result === 'OK'){
                                $dfd.resolve(data);
                                //_updateOrganizationUnitTypeRecord(data);
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
                    if(data.source !== 'list'){
                        data.clearCache();
                        return '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?selection=options&pos&People_FK=-1';
                    }
                    return '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?selection=options&pos&People_FK=' + personId;
                }
            },
            OrgRole_FK:{
                list: false,
                create: true,
                type: 'hidden'
                
            },
            Org_Tree_FK:{
                list: false,
                type: 'hidden'
            },
            OrgPosStatus_FK: {
                title: 'Status',
                width: '10%',
                defaultValue: 2,
                options: function (data){
                    if(data.source === 'list')
                        return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php?selection=options&statusfilter=no';
                    else
                        return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php?selection=options&statusfilter=engagement';
                }
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
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
                $('#jtable-edit-form').append('<input type="hidden" name="Org_Tree_FK" value="' + data.record.Org_Tree_FK + '" />');
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
