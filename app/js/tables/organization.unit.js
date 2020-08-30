/* global DATE_FORMAT, SARON_URI, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";
    
$(document).ready(function () {
    const TABLE_ID = "#ORG_UNIT";

    $(TABLE_ID).jtable(orgUnitTableDef(TABLE_ID));
    $(TABLE_ID).jtable('load');
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
            updateAction:   '/' + SARON_URI + 'app/web-api/updateOrganizationUnit.php',
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
                            $(tableId).jtable('openChildTable', $tr, unitRoleTableDef(tableId, data.record.Id, data.record.Name), function(data){
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
                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php';           
                }
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                //displayFormat: DATE_FORMAT,
                width: '15%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.HasPos !== '0')
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


function _updateOrganizationUnitTypeRecord(records){
    var key = document.getElementsByClassName("jtable-data-row");
    if(key===null)
        return;
    
    for(var i = 0; i<key.length;i++){
        if(key[i].dataset.recordKey === records[0].Id){ 
            key[i].cells[3].innerHTML = (records[0].Updated).substring(0,10);                                              
        }
    }
}

function unitRoleTableDef(tableId, orgUnitType_FK, orgName){
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
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationRole-UnitType.php?selection=role&OrgUnitType_FK=' + orgUnitType_FK,
            createAction:   '/' + SARON_URI + 'app/web-api/createOrganizationRole-UnitType.php?OrgUnitType_FK=' + orgUnitType_FK,
            deleteAction:   '/' + SARON_URI + 'app/web-api/deleteOrganizationRole-UnitType.php'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            OrgRole_FK: {
                list: false,
                edit: false,
                title: 'Lägg till roll',
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?selection=options';
                }
            },
            Name: {
                create: false,
                title: 'Benämning',
                width: '15%'
            },
            Description: {
                create: false,
                title: 'Beskrivning',
                width: '50%'
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
                //displayFormat: DATE_FORMAT,
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
