/* global DATE_FORMAT, SARON_URI, J_TABLE_ID, 
 PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, 
 inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
 POS_ENABLED, POS_DISABLED,
 SUBUNIT_ENABLED, SUBUNIT_DISABLED
*/
"use strict";
    
const ORG_UNIT = "#ORG_UNIT";

$(document).ready(function () {

    $(ORG_UNIT).jtable(orgUnitTableDef(ORG_UNIT));
    $(ORG_UNIT).jtable('load');
});

function orgUnitTableDef(tableId){
    return {
        title: 'Organisatoriska enhetertyper',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny typ av organisatorisk enhet.'},
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
            InUse: {
                title: 'Används',
                width: '3%',
                edit: false,
                create: false,
                display: function(data){
                    var src;
                    if(data.record.InUse ===  "1"){
                        src= '"/' + SARON_URI + SARON_IMAGES_URI + 'unit.png" title="Används på följande ställen"';
                        
                        var imgTag = _setImageClass(data.record, "Role", src, -1);
                        var $imgChild = $(imgTag);

                        $imgChild.click(data, function (event){
                            var $tr = $imgChild.closest('tr');
                            $(tableId).jtable('openChildTable', $tr, listTableDef(tableId, data.record.Name, "Enhetstypen", "unittype", "OrgUnitType_FK", data.record.Id), function(data){
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
            HasPos:{
                width: '3%',
                title: 'Roller',
                create: false,
                edit: false,    
                display: function(data){
                    if(data.record.PosEnabled ===  POS_ENABLED){
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
                    else
                        return null;
                }
            },
            Name: {
                title: 'Benämning',
                width: '15%'
            },
            SubUnitEnabled: {
                title: 'Kan ha underenheter',
                width: '15%',                
                options: function(data){
                    
                    if(data.source !== "create"){    
                        var val = data.record.UseChild;
                        if(val === null)
                            val = 0;
                        return {"1":"Nej", "2":"Ja (" + val + " underenheter)"};
                    }
                    return {"1":"Nej", "2":"Ja"};
                }
            },
            PosEnabled: {
                title: 'Kan ha bemanning',
                width: '15%',
                options: function(data){
                   
                    if(data.source !== "create"){
                        var val = data.record.UseRole;
                        if(val === null)
                            val = 0;
                        return {"1":"Nej", "2":"Ja (" + val + " positioner)"};
                    }
                    return {"1":"Nej", "2":"Ja"};
                }
            },
            Description: {
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
            if(data.record.InUse > 0 || data.record.HasPos > 0)
                data.row.find('.jtable-delete-command-button').hide();

            addDialogDeleteListener(data);
                        
        },        
        rowUpdated: function(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            else
                if(data.record.InUse !== '0')
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

            if(data.formType !== "create"){
                if(data.record.UseChild > 0){
                    var inp = data.form.find('select[name=SubUnitEnabled]');            
                    inp[0].disabled=true;            
                }

                if(data.record.UseRole > 0){
                    var inp = data.form.find('select[name=PosEnabled]');            
                    inp[0].disabled=true;            
                }
            }

            data.form.css('width','600px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        }
    };    
}



function unitTypeTableDef(tableId, orgRole_FK, roleName){
    return {
        title: '"' + roleName + '" kan ingå i nedanstående typer av organisatoriska enheter',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'OrgUnitType_FK', //Set default sorting        
        messages: {addNewRecord: 'Koppla roll till typ av organisatorisk enhet.'},
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
            }        
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            OrgUnitType_FK:{
                title: 'Organisatorisk enhetstyp',
                list: true,
                edit: false,
                create: true,
                width: '15%',
                options: function(data){
                    if(data.source === 'list')
                        return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=options';
    
                    data.clearCache();                    
                    return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=options&OrgRole_FK=' + orgRole_FK + "&source='organization.role.js'";
                }                
            },
            PosOccurrency:{
                title: "Antal positioner"
            },
            RoleList:{
                width: '30%',
                title: "Samtliga roller inom enhetstypen"
            },
            Description: {
                edit: false,
                create: false,
                title: 'Beskrivning',
                width: '40%'
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
            if(data.record.Occurrency !== null)
                if (data.record.Occurrency.length > 0)
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




function updateUnitRecord(data, method, orgUnitType_FK){
    var url = '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?Id=' + orgUnitType_FK;
    var options = {record:{"Id": orgUnitType_FK}, "clientOnly": false, "url":url};
    $(ORG_UNIT).jtable('updateRecord', options);
}