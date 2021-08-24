/* global DATE_FORMAT,
SUBUNIT_ENABLED, 
ORG, POS_ENABLED, 
saron, 
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
        $(saron.table.pos.viewid).jtable(posTableDef(saron.table.pos.viewid, null));
        var postData = getPostData(saron.table.pos.viewid, null, saron.table.pos.name, null, saron.responsetype.records);
        $(saron.table.pos.viewid).jtable('load', postData);
    }
);


function posTableDef(tableViewId, tableTitle){
    const tableName = saron.table.pos.name;
    const listUri = 'app/web-api/listOrganizationPos.php';
    
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
            listAction: '/' + saron.uri.saron + listUri,
            createAction: function(){
                if(tableViewId === saron.table.pos.viewid)
                    return null;
                else
                    return '/' + saron.uri.saron + 'app/web-api/createOrganizationPos.php';
                },
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateOrganizationPos.php',
            deleteAction: '/' + saron.uri.saron + 'app/web-api/deleteOrganizationPos.php'
        }, 
        fields: {
            TablePath:{
                list: false,
                edit: false,
                create: false,
            },
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
                options: function(data){
                    var optionUri = 'app/web-api/listOrganizationUnit.php';
                    var parameters = getURLParameters(tableViewId, data.record.ParentId, data.record.TablePath, data.source, saron.responsetype.options);
                    
                    if(data.source !== saron.source.list)
                        data.clearCache();

                    return '/' + saron.uri.saron + optionUri + parameters;
                    }
            },            
            OrgRole_FK: {
                width: '10%',
                title: 'Roll',
                edit: true,
                options: function(data){
                    var parameters = getURLParameters(tableViewId, data.record.ParentId, data.record.TablePath, data.source, saron.responsetype.options);
                    var optionUri = 'app/web-api/listOrganizationRole.php';
                    
                    if(data.source !== 'list')
                        data.clearCache();

                    return '/' + saron.uri.saron + optionUri + parameters;
                    }
            },
            OrgPosStatus_FK: {
                width: '5%',
                title: 'Status',
                defaultValue: '4',
                options: function(data){                    
                    var parameters = getURLParameters(tableViewId, data.record.ParentId, data.record.TablePath, data.source, saron.responsetype.options);
                    var optionUri = 'app/web-api/listOrganizationPosStatus.php';
                    
                    return '/' + saron.uri.saron + optionUri + parameters;
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
                    return '/' + saron.uri.saron + 'app/web-api/listPeople.php?selection=options' + filter;
                }
            },
            Function_FK: {
                title: 'Alternativt funktionsansvar',
                create: true,
                edit: true,
                list: false,
                options: function(data){                    
                    var parameters = getURLParameters(tableViewId, data.record.ParentId, data.record.TablePath, data.source, saron.responsetype.options);
                    var optionUri = 'app/web-api/listOrganizationUnit.php';
                    
                    return '/' + saron.uri.saron + optionUri + parameters;
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
            updateParentUnit(tableViewId, data);            
        },
        recordUpdated: function(event, data){
            updateParentUnit(tableViewId, data);            
        },
        recordDeleted: function(event, data){
            updateParentUnit(tableViewId, data);            
        },
        rowInserted: function(event, data){
            data.row.addClass("Id_" + data.record.Id); 
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            $('#jtable-edit-form').append('<input type="hidden" name="OrgTree_FK" value="' + parentId + '" />');
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

//function getAggregatedPosIcon(tableViewId, tablePath, parentId, childTableTitle, data){
//    if(data.record.PosEnabled === POS_ENABLED){
//        var src;
//        if(data.record.HasPos === '0')
//            src = '"/' + saron.uri.saron + saron.uri.images + 'unit_empty.png" title="Inga positioner"';
//        else{
//            if(data.record.statusProposal !== "0" && data.record.statusVacant !== "0")
//                src = '"/' + saron.uri.saron + saron.uri.images + 'unit_YR.png" title="' + data.record.statusProposal + ' Förslag och ' + data.record.statusVacant + ' vakans(er) på position(er)"';
//            else if(data.record.statusProposal === "0" && data.record.statusVacant !== "0")
//                src = '"/' + saron.uri.saron + saron.uri.images + 'unit_R.png" title="' + data.record.statusVacant + ' Vakans(er) på position(er)"';
//            else if(data.record.statusProposal !== "0" && data.record.statusVacant === "0")
//                src = '"/' + saron.uri.saron + saron.uri.images + 'unit_Y.png" title="' + data.record.statusProposal + ' Förslag på position(er)"';
//            else
//                src = '"/' + saron.uri.saron + saron.uri.images + 'unit.png" title="Bemannade positioner"';
//        }
//
//        var imgTag = _setImageClass(data, saron.table.pos.name, src, ORG);
//        var $imgRole = $(imgTag);
//
//        $imgRole.click(data, function (event){
//            var $tr = $imgRole.closest('tr');
//            $(tableViewId).jtable('openChildTable', $tr, posTableDef(tableViewId, tablePath, parentId, childTableTitle), function(data){
//                data.childTable.jtable('load');
//            });
//        });
//        return $imgRole;
//        }
//    else{
//        return null;
//    }
// }
//


function getDefaultPosSorting(tableViewId){
    switch(tableViewId) {
        case saron.table.pos.viewid:
            return "OrgTree_FK, SortOrder";
        default:
            return "SortOrder";
    }
}