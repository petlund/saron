/* global DATE_FORMAT, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, 
 SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
 NO_HOME, NEW_HOME_ID,
 POS_ENABLED, POS_DISABLED,
 SUBUNIT_ENABLED, SUBUNIT_DISABLED
 */

"use strict";
    
$(document).ready(function () {
    const ORG_TREE = "#ORG_TREE";

    $(ORG_TREE).jtable(treeTableDef(ORG_TREE, -1, '')); //-1 => null parent === topnode
    $(ORG_TREE).jtable('load');
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
        messages: {addNewRecord: 'Lägg till en ny organisatorisk enhet.'},
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
                create: false,
                title: 'Överordna verksamhet',
                options: function(data) {
                    if(data.source !== 'list'){
                        data.clearCache();
                    } 
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
                    if(data.record.SubUnitEnabled === SUBUNIT_ENABLED){
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
                title: 'Namn'
            },
            Description: {
                width: '15%',
                title: 'Beskrivning'
            },
            OrgUnitType_FK:{
                title: 'Typ av enhet',
                inputTitle: 'Typ av enhet (Kan inte ändras. Vill du ändra behöver du skapa en ny organisatorisk enhet).',
                width: '5%',
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

            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
                $(tableId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit'){
                data.row[0].style.backgroundColor = "yellow";
                data.form.find('select[name=ParentTreeNode_FK]')[0].disabled=false;
                data.form.find('select[name=OrgUnitType_FK]')[0].disabled=true;                
            }
            else{
                data.form.find('select[name=OrgUnitType_FK]')[0].disabled=false;
            }

            data.form.css('width','600px');
            data.form.find('input[name=Name]').css('width','580px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        }
    };
}    


function updateTreeParent(tableId, data){
    var table;
    var treeId = data.record.OrgTree_FK;

    table = $(".TreeId_" + treeId).closest('div.jtable-child-table-container');       
    
    if(table.length === 0) 
        table = $(tableId);

    var url =  '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?selection=single_node';
    var options = {record:{"TreeId": treeId}, "clientOnly": false, "url":url};
    table.jtable('updateRecord', options);                                    
}

