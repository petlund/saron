/* global DATE_FORMAT,
SARON_URI,SARON_IMAGES_URI, 
SUBUNIT_ENABLED, 
ORG, POS_ENABLED, 
TABLE_VIEW_ROLE, TABLE_NAME_ROLE, 
TABLE_VIEW_UNITTYPE, TABLE_NAME_UNITTYPE,
TABLE_VIEW_ROLE_UNITTYPE, TABLE_NAME_ROLE_UNITTYPE,
TABLE_VIEW_UNIT, TABLE_NAME_UNIT,
TABLE_VIEW_ORG, TABLE_NAME_ORG,
TABLE_VIEW_UNITLIST, TABLE_NAME_UNITLIST,
TABLE_VIEW_UNITTREE, TABLE_NAME_UNITTREE,
TABLE_VIEW_POS, TABLE_NAME_POS,
RECORDS, RECORD, OPTIONS, SOURCE_LIST, SOURCE_CREATE, SOURCE_EDIT
 */

"use strict";

$(document).ready(function () {
        $(TABLE_VIEW_POS).jtable(posTableDef(TABLE_VIEW_POS, null, -1, null));
        $(TABLE_VIEW_POS).jtable('load');
    }
);


function posTableDef(tableViewId, parentTablePath, parentId,  childTableTitle){
    const tableName = TABLE_NAME_POS;
    var tablePath = tableName;
    if(parentTablePath !== null)
        tablePath = parentTablePath + "/" + tableName;
    
    return {
        title: function (){
            if(childTableTitle !== null)
                return childTableTitle;
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
            listAction: '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?ParentId=' + parentId + '&TablePath=' + tablePath + "&ResultType=" + RECORDS,
            createAction: function(){
                if(tableViewId === TABLE_VIEW_POS)
                    return null;
                else
                    return '/' + SARON_URI + 'app/web-api/createOrganizationPos.php';
                },
            updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationPos.php',
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationPos.php'
        }, 
        fields: {
            TablePath:{
                list: true,
                edit: false,
                create: false,
            },
            Id: {
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
                    var imgTag = _setImageClass(data.record, TABLE_NAME_ROLE, src, -1);
                    var $imgRole = $(imgTag);
                    return $imgRole;
                }                
            },
            SortOrder: {
                list: !includedIn(tableViewId, TABLE_VIEW_POS),
                create: false,
                width: '4%',
                title: 'Sort',
                edit: false
                
            },
            OrgTree_FK:{                
                create: false,
                edit: false,
                list: includedIn(tableViewId, TABLE_VIEW_POS),
                title: "Organisatorisk enhet",
                options: function(data){
                    var optionTablePath = tablePath + "/" + OPTIONS;
                    var parameters = '?ParentId=' + parentId + '&TablePath=' + optionTablePath + "&ResultType=" + OPTIONS;
                    
                    if(data.source !== 'list')
                        data.clearCache();

                    return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php' + parameters;
                    }
            },            
            OrgRole_FK: {
                width: '10%',
                title: 'Roll',
                edit: true,
                options: function(data){
                    var optionTablePath = tablePath + "/" + OPTIONS;
                    var parameters = '?ParentId=' + parentId + '&TablePath=' + optionTablePath + "&ResultType=" + OPTIONS;

                    if(data.source !== 'list')
                        data.clearCache();

                    return '/' + SARON_URI + 'app/web-api/listOrganizationRole.php' + parameters;
                    }
            },
            OrgPosStatus_FK: {
                width: '5%',
                title: 'Status',
                defaultValue: '4',
                options: function(data){
                    var optionTablePath = tablePath + "/" + OPTIONS;
                    var parameters = '?ParentId=' + parentId + '&TablePath=' + optionTablePath + "&ResultType=" + OPTIONS;

                    return '/' + SARON_URI + 'app/web-api/listOrganizationPosStatus.php' + parameters;
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
                options: function(){
                    var optionTablePath = tablePath + "/" + OPTIONS;
                    var parameters = '?ParentId=' + parentId + '&TablePath=' + optionTablePath + "&ResultType=" + OPTIONS;
                    return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php' + parameters;
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

function getAggregatedPosIcon(tableViewId, tablePath, parentId, childTableTitle, data){
    if(data.record.PosEnabled === POS_ENABLED){
        var src;
        if(data.record.HasPos === '0')
            src = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit_empty.png" title="Inga positioner"';
        else{
            if(data.record.statusProposal !== "0" && data.record.statusVacant !== "0")
                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit_YR.png" title="' + data.record.statusProposal + ' Förslag och ' + data.record.statusVacant + ' vakans(er) på position(er)"';
            else if(data.record.statusProposal === "0" && data.record.statusVacant !== "0")
                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit_R.png" title="' + data.record.statusVacant + ' Vakans(er) på position(er)"';
            else if(data.record.statusProposal !== "0" && data.record.statusVacant === "0")
                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit_Y.png" title="' + data.record.statusProposal + ' Förslag på position(er)"';
            else
                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'unit.png" title="Bemannade positioner"';
        }

        var imgTag = _setImageClass(data.record, TABLE_NAME_POS, src, -1);
        var $imgRole = $(imgTag);

        $imgRole.click(data, function (event){
            var $tr = $imgRole.closest('tr');
            $(tableViewId).jtable('openChildTable', $tr, posTableDef(tableViewId, tablePath, parentId, childTableTitle), function(data){
                data.childTable.jtable('load');
            });
        });
        return $imgRole;
        }
    else{
        return null;
    }
 }



function getDefaultPosSorting(tableViewId){
    switch(tableViewId) {
        case TABLE_VIEW_POS:
            return "OrgTree_FK, SortOrder";
        default:
            return "SortOrder";
    }
}