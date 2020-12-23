/* global DATE_FORMAT, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";
    
$(document).ready(function () {
    const ORG_STRUCT = "#ORG_STRUCT";

    $(ORG_STRUCT).jtable(treeTableDef(ORG_STRUCT, -1, '')); //-1 => null parent === topnode
    $(ORG_STRUCT).jtable('load');
    //$(TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
});


function treeTableDef(tableId, parentTreeNode_FK, parentName){
    return {
        title: function(data){
            if(parentName.length > 0)
                return 'Organisation under ' + parentName;
            else
                return 'Organisation';
                
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Prefix, Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?ParentTreeNode_FK=' + parentTreeNode_FK,
            createAction: '/' + SARON_URI + 'app/web-api/createOrganizationStructure.php?ParentTreeNode_FK=' + parentTreeNode_FK,
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url:  '/' + SARON_URI + 'app/web-api/updateOrganizationStructure.php?ParentTreeNode_FK=' + parentTreeNode_FK,
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
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationStructure.php'
        }, 
        fields: {
            TreeId: {
                key: true,
                list: false
            },
            ParentTreeNode_FK:{
                list: false,
                edit: true, 
                title: 'Överordna verksamhet',
                options: function(data) {
                    data.clearCache();
                    return '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?selection=options&filter=yes&TreeId=' + data.record.TreeId;
                }                
            },
            SubUnitEnabled: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                
                display: function (data) {
                    if(data.record.SubUnitEnabled === '1'){
                        var src;
                        if(data.record.HasSubUnit === '0' || data.record.statusSubProposal === null  || data.record.statusSubVacant === null){
                            src = '"/' + SARON_URI + SARON_IMAGES_URI + 'child.png" title="Under organisation"';                            
                        }
                        else{
                            if(data.record.statusSubProposal > 0 && data.record.statusSubVacant > 0)
                                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haschild_YR.png" title="Underorganisation med ' + data.record.statusSubProposal + ' förslag och ' + data.record.statusSubVacant + ' vakans(er)"';
                            else if(data.record.statusSubProposal === "0" && data.record.statusSubVacant !== "0")
                                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haschild_R.png" title="Underorganisation med ' + data.record.statusSubVacant + ' vakans(er)"';
                            else if(data.record.statusSubProposal !== "0" && data.record.statusSubVacant === "0")
                                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haschild_Y.png" title="Underorganisation med ' + data.record.statusSubProposal + ' förslag"';
                            else
                                src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haschild.png" title="Under organisation"';
                        }
                        var imgTag = _setImageClass(data.record, "Org", src, -1);
                        var $imgChild = $(imgTag);

                        $imgChild.click(data, function (event){
                            var $tr = $imgChild.closest('tr');
                            $(tableId).jtable('openChildTable', $tr, treeTableDef(tableId, data.record.TreeId, data.record.Name), function(data){
                                data.childTable.jtable('load');
                            });
                        });
                        return $imgChild;
                    }
                }
            },
            PosEnabled: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                display: function (data) {
                    if(data.record.PosEnabled === '1'){
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
                        
                        var imgTag = _setImageClass(data.record, "Role", src, -1);
                        var $imgRole = $(imgTag);

                        $imgRole.click(data, function (event){
                            var $tr = $imgRole.closest('tr');
                            $(tableId).jtable('openChildTable', $tr, posTableDef(tableId, data.record.TreeId, data.record.Name, data.record.OrgUnitType_FK), function(data){
                                data.childTable.jtable('load');
                            });
                        });
                        return $imgRole;
                        }
                    else{
                        return null;
                    }
                }
            },
            Prefix: {
                width: '1%',
                title: 'Prefix',
                listClass: 'saron-number'
                
            },
            Name: {
                width: '10%',
                title: 'Namn',
            },
            Description: {
                width: '15%',
                title: 'Beskrivning'
            },
            OrgUnitType_FK:{
                create: true,
                edit: true,
                title: 'Typ av enhet',
                width: '5%'
                ,
                options: function (data){
                    if(data.source !== 'list'){
                        data.clearCache();
                    } 
                    return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=options';
                }
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
        recordUpdated: function (event, data){
            var table;
            var treeId = data.record.TreeId;
            var parentTreeId = data.record.ParentTreeNode_FK;
            
            if(parentTreeId > 0){
                table = $(".TreeId_" + parentTreeId).closest('div.jtable-child-table-container');
    
                if(table.length === 0) 
                    table = $(tableId);
    
                var url =  '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?selection=single_node';
                var options = {record:{"TreeId": parentTreeId}, "clientOnly": false, "url":url};
                table.jtable('updateRecord', options);                                
            }        
            if(data.record.HasSubUnit !== '0' || data.record.HasPos !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            else
                data.row.find('.jtable-delete-command-button').show();
            
            if(data.record.parentNodeChange !== '0')
                $(tableId).jtable('load');

        },  
        rowInserted: function(event, data){
            data.row.addClass("TreeId_" + data.record.TreeId); 
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.HasSubUnit !== '0' || data.record.HasPos !== '0')
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
            data.form.find('input[name=Name]').css('width','580px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        },
        deleteFormCreated: function (event, data){
        },
        deleteFormClosed: function (event, data){
        }
    };
}    

function posTableDef(tableId, orgTree_FK, unitName, orgUnitType_FK){
    return {
        title: function(){
            return 'Roller inom ' + unitName;
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'SortOrder', //Set default sorting        
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
                    else
                        return '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?selection=options&OrgUnitType_FK=' + orgUnitType_FK;
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
                title: 'Ansvar',
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
            parentsUpdate(tableId, data);            
        },
        recordUpdated: function(event, data){
            parentsUpdate(tableId, data);            
        },
        recordDeleted: function(event, data){
            parentsUpdate(tableId, data);            
        },
        rowInserted: function(event, data){
            data.row.addClass("PosId_" + data.record.PosId); 
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

function parentsUpdate(tableId, data){
    var table;
    var treeId = data.record.OrgTree_FK;

    table = $(".TreeId_" + treeId).closest('div.jtable-child-table-container');       
    
    if(table.length === 0) 
        table = $(tableId);

    var url =  '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?selection=single_node';
    var options = {record:{"TreeId": treeId}, "clientOnly": false, "url":url};
    table.jtable('updateRecord', options);                                    
}

