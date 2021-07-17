/* global DATE_FORMAT, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, 
 SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
 NO_HOME, NEW_HOME_ID,
 POS_ENABLED, POS_DISABLED,
 SUBUNIT_ENABLED, SUBUNIT_DISABLED
 */

"use strict";

function posTableDef(tableId, orgTree_FK, unitName, orgUnitType_FK){
    return {
        title: function(){
            return 'Positioner för "' + unitName + '"';
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'SortOrder', //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny position.'},
        actions: {
            listAction: '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?OrgTree_FK=' + orgTree_FK,
            createAction: '/' + SARON_URI + 'app/web-api/createOrganizationPos.php?OrgTree_FK=' + orgTree_FK,
            updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationPos.php?OrgTree_FK=' + orgTree_FK,
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationPos.php?OrgTree_FK=' + orgTree_FK
        }, 
        fields: {
            PosId: {
                key: true,
                list: false,
                create: false
            },
            RoleType:{
                sorting: false,
                width: "1%",
                edit: false,
                create: false,
                display: function (data) {
                    var src;
                    if(data.record.RoleType === '-1'){
                        src = '"/' + SARON_URI + SARON_IMAGES_URI + 'orgpos.png" title="Rollen finns på fler ställen"';
                    }
                    else{
                        switch (data.record.OrgPosStatus_FK){
                            case '1':
                                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haspos.png" title="Avstämd"';
                                break;
                            case '2':
                                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haspos_Y.png" title="Förslag"';
                                break;
                            case '4':
                                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haspos_R.png" title="Vakant"';
                                break;
                            case '6':
                                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'function.png" title="Funktionsansvar"';
                                break;
                            default:                            
                                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'pos.png" title="Tillsätts ej"';
                        }
                    }
                    var imgTag = _setImageClass(data.record, "Role", src, -1);
                    var $imgRole = $(imgTag);
                    return $imgRole;
                }                
            },
            SortOrder: {
                list: true,
                create: false,
                width: '4%',
                title: 'Sort',
                edit: false
                
            },
            OrgRole_FK: {
                width: '10%',
                title: 'Roll',
                edit: true,
                options: function(data){
                    if(data.source === 'list')
                        return '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?selection=options';
                    else{
                        data.clearCache();
                        return '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?selection=options&OrgUnitType_FK=' + orgUnitType_FK;
                        }
                    }
            },
            OrgPosStatus_FK: {
                width: '5%',
                title: 'Status',
                defaultValue: '4',
                options: function(data){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php?selection=options';
                }
            },
            Comment:{
                width: '10%',
                inputTitle: "Kort kommentar som ska vara knuten till uppdraget inte personen.",
                title: 'Kommentar',                
            },
            People_FK: {
                title: 'Ansvarig person',
                create: true,
                edit: true,
                list: false,
                options: function(data){
                    var filterDef = "&filter=true";
                    var filter = "";
                    if(data.source !== 'list'){
                        data.clearCache();
                        filter = filterDef;
                    }
                    return '/' + SARON_URI + 'app/web-api/listPeople.php?selection=options' + filter;
                }
            },
            Function_FK: {
                title: 'Alternativt funktionsansvar',
                create: true,
                edit: true,
                list: false,
                options: function(data){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?selection=options';
                }
            },
            Responsible: {
                create: false,
                edit: false,
                width: '15%',
                title: 'Förslag',
                list: true,
            },
            MemberState: {
                title: 'Medlemsstatus',
                edit: false,
                create: false,
                width: '5%'                
            },
            pCur_Mobile: {
                title: 'Mobil',
                edit: false,
                create: false,
                width: '10%'               
            },
            pCur_Email: {
                title: 'Mail',
                edit: false,
                create: false,
                width: '10%'                
            },
            PrevResponsible: {
                width: '10%',
                edit: false,
                create: false,
                title: 'Senast beslutad'
            },
            UpdaterName:{
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '5%'
            },
            LatestUpdated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: DATE_FORMAT,
                width: '5%'
            }
        },
        recordAdded: function(event, data){
            updateTreeParent(tableId, data);            
        },
        recordUpdated: function(event, data){
            updateTreeParent(tableId, data);            
        },
        recordDeleted: function(event, data){
            updateTreeParent(tableId, data);            
        },
        rowInserted: function(event, data){
            data.row.addClass("PosId_" + data.record.PosId); 
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
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        }
    };
}
