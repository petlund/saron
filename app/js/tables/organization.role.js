/* global SARON_URI, J_TABLE_ID, DATE_FORMAT, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";

const ORG_ROLE = "#ORG_ROLE";
    
$(document).ready(function () {

        $(ORG_ROLE).jtable(roleTableDef(ORG_ROLE, -1, null));
        $(ORG_ROLE).jtable('load');
    }
);

function roleTableDef(tableId, unitTypeId, orgName){
    return {
        title: function (){
            if(orgName !== null)
                return 'Roller för ' + orgName;
            else
                return 'Roller';
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationRole.php',
            createAction:   '/' + SARON_URI + 'app/web-api/createOrganizationRole.php',
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url:  '/' + SARON_URI + 'app/web-api/updateOrganizationRole.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationRole.php'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            Org:{
                width: '1%',
                create: false,
                edit: false,
                sorting: false,
                display: function(data){
                    var src;
                    if(data.record.HasChild === '0'){
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'child.png" title="Organisation"';
                    }
                    else{
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haschild.png" title="Organisation"';
                    }
                    
                    var imgTag = _setImageClass(data.record, "Role", src, -1);
                    var $imgChild = $(imgTag);

                    $imgChild.click(data, function (event){
                        var $tr = $imgChild.closest('tr');
                        $(tableId).jtable('openChildTable', $tr, subUnitTableDef(tableId, data.record.Id, data.record.Name), function(data){
                            data.childTable.jtable('load');
                        });
                    });
                    return $imgChild;
                }
            },
            Name: {
                title: 'Benämning',
                width: '15%'
            },
            Description: {
                title: 'Beskrivning',
                width: '40%'
            },
            RoleType:{
                title: 'Typ',
                width: '10%',
                options:  { '0' : 'Verksamhetsroll', '1' : 'Organisationsroll [Org]' }
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
        recordUpdated(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            else
                if (data.record.HasChild === '0')
                    data.row.find('.jtable-delete-command-button').show();
                else
                    data.row.find('.jtable-delete-command-button').hide();
            
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if (data.record.HasChild !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            
            addDialogDeleteListener(data);
            
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
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
        }
    };
}



function subUnitTableDef(tableId, orgRole_FK, roleName){
    return {
        title: '"' + roleName + '" finns i nedanstående typer av organisatoriska enheter',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationRole-UnitType.php?selection=unitTypes&OrgRole_FK=' + orgRole_FK,
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url:  '/' + SARON_URI + 'app/web-api/createOrganizationRole-UnitType.php?selection=unitTypes&OrgRole_FK=' + orgRole_FK,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                data.childTable.jtable('load');
                                updateRoleRecord(data, 'create', orgRole_FK);                               
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            deleteAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url:  '/' + SARON_URI + 'app/web-api/deleteOrganizationRole-UnitType.php?OrgRole_FK=' + orgRole_FK,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                updateRoleRecord(data, 'delete', orgRole_FK);                               
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },        
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            OrgUnitType_FK:{
                title: 'Benämning',
                list: true,
                edit: false,
                create: true,
                width: '20%',
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=options';
                }                
            },
            Description: {
                edit: false,
                create: false,
                title: 'Beskrivning',
                width: '50%'
            },
            UpdaterName: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '15%'
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: DATE_FORMAT,
                width: '15%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
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
    };    
}


function updateRoleRecord(data, method, orgRole_FK){
    var url = '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?Id=' + orgRole_FK;
    var options = {record:{"Id": orgRole_FK}, "clientOnly": false, "url":url};
    $(ORG_ROLE).jtable('updateRecord', options);
}