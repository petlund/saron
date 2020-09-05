/* global SARON_URI, J_TABLE_ID, DATE_FORMAT, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";
    
$(document).ready(function () {
    const TABLE_ID = "#ORG_ROLE";

    $(TABLE_ID).jtable(roleTableDef(TABLE_ID, -1, null));
    $(TABLE_ID).jtable('load');
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
//            updateAction:   '/' + SARON_URI + 'app/web-api/updateOrganizationRole.php',
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url:  '/' + SARON_URI + 'app/web-api/updateOrganizationRole.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result === 'OK')
                                $dfd.resolve(data);
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
                    if(data.record.HasChild === '0')
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'child.png" title="Organisation"';
                    else
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haschild.png" title="Organisation"';
                    
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
                width: '50%'
            },
            MultiPos:{
                title: 'MultiPos',
                width: '2%',
                type: 'checkbox',
                formText: 'Aktivera om rollens bemanning ska finnas på flera ställe i organisationen.',
                values:  { '0' : 'Nej', '1' : 'Ja' }
            },
            Updater: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '15%',
                options: function (){
                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php?selection=role';           
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
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if (data.record.HasChild !== '0')
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
    };
}



function subUnitTableDef(tableId, orgRole_FK, roleName){
    return {
        title: '"' + roleName + '" finns i nedanstående organisatoriska enheter',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationRole-UnitType.php?selection=unitTypes&OrgRole_FK=' + orgRole_FK,
            createAction:   '/' + SARON_URI + 'app/web-api/createOrganizationRole-UnitType.php?OrgRole_FK=' + orgRole_FK,
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationRole-UnitType.php'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            OrgUnitType_FK:{
                titel: 'Lägg till organisatorisk enhet',
                list: true,
                edit: false,
                create: true,
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=options';
                }                
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
