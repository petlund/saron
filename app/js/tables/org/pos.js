/* global DATE_FORMAT,
SUBUNIT_ENABLED, 
ORG, POS_ENABLED, 
saron, 
RECORD, OPTIONS
 */

"use strict";
const posListUri = 'app/web-api/listOrganizationPos.php';

$(document).ready(function () {
        $(saron.table.pos.viewid).jtable(posTableDef(saron.table.pos.viewid, saron.table.pos.name, null));
        var postData = getPostData(null, saron.table.pos.viewid, null, saron.table.pos.name, saron.source.list, saron.responsetype.records, posListUri);
        $(saron.table.pos.viewid).jtable('load', postData);
        $(saron.table.pos.viewid).find('.jtable-toolbar-item-add-record').hide();
    }
);


function posTableDef(tableViewId, tablePath, tableTitle){
    const tableName = saron.table.pos.name;
    
    return {
        showCloseButton: false,
        title: function (){
            if(tableTitle !== null)
                return tableTitle;
            else
                return 'Positioner';
        },
        paging: true, //Enable paging§§
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: getDefaultPosSorting(tableViewId), //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny position.'},
        actions: {
            listAction: '/' + saron.uri.saron + posListUri,
            createAction: '/' + saron.uri.saron + 'app/web-api/createOrganizationPos.php',
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateOrganizationPos.php',
            deleteAction: '/' + saron.uri.saron + 'app/web-api/deleteOrganizationPos.php'
        }, 
        fields: {
            Id: {
                key: true,
                list: false,
                create: false,
                edit: false
            },         
            RoleType:{
                sorting: false,
                width: "1%",
                edit: false,
                create: false,
                display: function (data) {
                    var src;
                    if(data.record.RoleType === '-1'){
                        src = '"/' + saron.uri.saron + saron.uri.images + 'orgpos.png" title="Rollen finns på fler ställen"';
                    }
                    else{
                        switch (data.record.OrgPosStatus_FK){
                            case '1':
                                src = '"/' + saron.uri.saron + saron.uri.images + 'haspos.png" title="Avstämd"';
                                break;
                            case '2':
                                src = '"/' + saron.uri.saron + saron.uri.images + 'haspos_Y.png" title="Förslag"';
                                break;
                            case '4':
                                src = '"/' + saron.uri.saron + saron.uri.images + 'haspos_R.png" title="Vakant"';
                                break;
                            case '6':
                                src = '"/' + saron.uri.saron + saron.uri.images + 'function.png" title="Funktionsansvar"';
                                break;
                            default:                            
                                src = '"/' + saron.uri.saron + saron.uri.images + 'pos.png" title="Tillsätts ej"';
                        }
                    }
                    var imgTag = _setImageClass(data, saron.table.role.name, src, -1);
                    var $imgRole = $(imgTag);
                    return $imgRole;
                }                
            },
            SortOrder: {
                list: !includedIn(tableViewId, saron.table.pos.viewid),
                create: false,
                width: '4%',
                title: 'Sort',
                edit: false
                
            },
            OrgTree_FK:{                
                create: false,
                edit: false,
                list: includedIn(tableViewId, saron.table.pos.viewid),
                title: "Organisatorisk enhet",
                dependsOn: 'Id',
                options: function(data){
                    var uri = 'app/web-api/listOrganizationUnit.php';
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, uri);
                    return '/' + saron.uri.saron + uri + parameters;
                }
            },            
            OrgRole_FK: {
                width: '10%',
                title: 'Roll',
                options: function(data){
                    var uri = 'app/web-api/listOrganizationRole.php';                    
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
                }
            },
            OrgPosStatus_FK: {
                width: '5%',
                title: 'Status',
                defaultValue: '4',
                options: function(data){                    
                    var uri = 'app/web-api/listOrganizationPosStatus.php';
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
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
                    var uri = 'app/web-api/listPeople.php';
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
                }
            },
            Function_FK: {
                title: 'Alternativt funktionsansvar',
                create: true,
                edit: true,
                list: false,
                options: function(data){                    
                    var uri = 'app/web-api/listOrganizationUnit.php';
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
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
            if(saron.table.pos.viewid !== tableViewId)
                updateParentUnit(tableViewId, data);            
        },
        recordUpdated: function(event, data){
            if(saron.table.pos.viewid !== tableViewId)
                updateParentUnit(tableViewId, data);            
        },
        recordDeleted: function(event, data){
            if(saron.table.pos.viewid !== tableViewId)
                updateParentUnit(tableViewId, data);            
        },
        rowInserted: function(event, data){
            data.row.addClass("Id_" + data.record.Id); 
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            if(data.records.length > 0){
                var showCreateButton = (data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org') && data.records[0].TablePath !== tableName; 
                if(showCreateButton){ 
                    $(tableViewId).find('.jtable-toolbar-item-add-record').show();
                }
            }
        },        
        formCreated: function (event, data){
//            $('#jtable-edit-form').append('<input type="hidden" name="OrgTree_FK" value="' + data.record.ParentTreeNode_FK + '" />');
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };
}


function getDefaultPosSorting(tableViewId){
    switch(tableViewId) {
        case saron.table.pos.viewid:
            return "OrgTree_FK, SortOrder";
        default:
            return "SortOrder";
    }
}