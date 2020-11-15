/* global DATE_FORMAT, SARON_URI, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";
    
const ORG_UNIT = "#ORG_UNIT";

$(document).ready(function () {0

    $(ORG_UNIT).jtable(orgUnitTableDef(ORG_UNIT));
    $(ORG_UNIT).jtable('load');
});

function orgUnitTableDef(tableId){
    return {
        title: 'Typer av organisatoriska enheter',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php',
            createAction:   '/' + SARON_URI + 'app/web-api/createOrganizationUnit.php',
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url:  '/' + SARON_URI + 'app/web-api/updateOrganizationUnit.php',
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
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationUnit.php'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            HasPos:{
                width: '10%',
                title: 'Har roller',
                create: false,
                edit: false,
                sorting: false,
                display: function(data){
                    if(data.record.PosEnabled ===  "1"){
                        var src;
                        if(data.record.HasPos === '0')
                            src= '"/' + SARON_URI + SARON_IMAGES_URI + 'pos.png" title="Inga roller"';
                        else
                            src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haspos.png" title="Roller"';
                        
                        var imgTag = _setImageClass(data.record, "Role", src, -1);
                        var $imgChild = $(imgTag);

                        $imgChild.click(data, function (event){
                            var $tr = $imgChild.closest('tr');
                            $(tableId).jtable('openChildTable', $tr, subRoleTableDef(tableId, data.record.Id, data.record.Name), function(data){
                                data.childTable.jtable('load');
                            });
                        });
                        return $imgChild;
                    }
                    else{
                        return null;
                    }
                }
            },
            Name: {
                title: 'Benämning',
                width: '15%'
            },
            SubUnitEnabled: {
                title: 'Kan ha underenheter',
                width: '15%',
                options: {"0":"Nej", "1":"Ja"}
            },
            PosEnabled: {
                title: 'Kan ha bemanning',
                width: '15%',
                options: {"0":"Nej", "1":"Ja"}
            },
            Description: {
                title: 'Beskrivning',
                width: '50%'
            },
            Updater: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '15%',
                options: function (){
                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php?selection=unit';           
                }
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
            if(data.record.HasPos !== '0')
                data.row.find('.jtable-delete-command-button').hide();
        },        
        rowUpdated: function(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            else
                if(data.record.HasPos !== '0')
                    data.row.find('.jtable-delete-command-button').hide();
                else
                    data.row.find('.jtable-delete-command-button').show();
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



function subRoleTableDef(tableId, orgUnitType_FK, orgName){
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
        defaultSorting: 'SortOrder', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationRole-UnitType.php?selection=role&OrgUnitType_FK=' + orgUnitType_FK,
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url:  '/' + SARON_URI + 'app/web-api/createOrganizationRole-UnitType.php?selection=role&OrgUnitType_FK=' + orgUnitType_FK,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                updateUnitRecord(data, 'create', orgUnitType_FK);                               
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            updateAction:   '/' + SARON_URI + 'app/web-api/updateOrganizationRole-UnitType.php?selection=role',
            deleteAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url:  '/' + SARON_URI + 'app/web-api/deleteOrganizationRole-UnitType.php?OrgUnitType_FK=' + orgUnitType_FK,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                updateUnitRecord(data, 'delete', orgUnitType_FK);                               
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
            OrgRole_FK: {
                list: true,
                edit: false,
                width: '20%',
                title: 'Benämning',
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?selection=options';
                }
            },
            Description: {
                edit: false,
                create: false,
                title: 'Beskrivning',
                width: '50%'
            },
            SortOrder: {
                edit: true,
                create: true,
                title: 'Sortering',
                options: {"0": "-", "1": "Nivå 1", "2": "Nivå 2", "3":"Nivå 3", "4": "Nivå 4", "5":"Nivå 5","6":"Nivå 6"},
                width: '10%'
            },
            Updater: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '15%',
                options: function (){
                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php';           
                }
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
function updateUnitRecord(data, method, orgUnitType_FK){
    var url = '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?Id=' + orgUnitType_FK;
    var options = {record:{"Id": orgUnitType_FK}, "clientOnly": false, "url":url};
    $(ORG_UNIT).jtable('updateRecord', options);
}