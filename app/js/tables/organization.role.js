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
        messages: {addNewRecord: 'Lägg till ny roll'},
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
            UsedIn:{
                width: '5%',
                create: false,
                title: "Används",
                edit: false,
                sorting: false,
                display: function(data){
                    var src;
                    if(data.record.HasChild === '0'){
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit_empty.png" title="Organisation"';
                    }
                    else{
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit.png" title="Organisation"';
                    }
                    
                    var imgTag = _setImageClass(data.record, "Role", src, data.record.Id);
                    var $imgChild = $(imgTag);
                    var isChildRowOpen=false;
                    
                    $imgChild.click(data, function (event){
                        var $tr = $imgChild.closest('tr');
                        $(tableId).jtable('openChildTable', $tr, listTableDef(tableId, data.record.Name, "Rollen", "role", "OrgRole_FK", data.record.Id), function(data){
                            var $currentRow = $(tableId).jtable('getRowByKey', event.data.record.Id);
                            isChildRowOpen = $(tableId).jtable('isChildRowOpen', $currentRow);

                            data.childTable.jtable('load');
                        });
                    });
                    if(isChildRowOpen)
                        return $imgChild;
                    else
                        return $imgChild;

                }               
            },
            Org:{
                width: '5%',
                create: false,
                title: "Ingår i",
                edit: false,
                sorting: false,
                display: function(data){
                    var src;
                    if(data.record.HasChild === '0'){
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'pos.png" title="Organisation"';
                    }
                    else{
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haspos.png" title="Organisation"';
                    }
                    
                    var imgTag = _setImageClass(data.record, "Role", src, -1);
                    var $imgChild = $(imgTag);
                    
                    $imgChild.click(data, function (event){
                        var $tr = $imgChild.closest('tr');
                        $(tableId).jtable('openChildTable', $tr, unitTypeTableDef(tableId, data.record.Id, data.record.Name), function(data){
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
                edit: false,
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

function subRoleTableDef(tableId, orgUnitType_FK, orgName){
    return {
        title: function (){
            if(orgName !== null)
                return 'Tillhörande roller för enhetstyp "' + orgName + '"';
            else
                return 'Roller';
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'SortOrder', //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny koppling till en roll.'},
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
                title: 'Rollbenämning',
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?selection=options';
                }
            },
            PosOccurrency:{
                edit: false,
                create: false,
                title: "Antal positioner",
                width: "10%",
                display: function (data){
                    return _setClassAndValue(data.record, "PosOccurrency", -1);
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
            
            if(data.record.PosOccurrency > 0){ // Pos exist
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
        }
    };
}



function updateRoleRecord(data, method, orgRole_FK){
    var url = '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?Id=' + orgRole_FK;
    var options = {record:{"Id": orgRole_FK}, "clientOnly": false, "url":url};
    $(ORG_ROLE).jtable('updateRecord', options);
}
