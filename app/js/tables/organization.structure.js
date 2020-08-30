/* global DATE_FORMAT, SARON_URI, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";
    
$(document).ready(function () {
    const TABLE_ID = "#ORG_STRUCT";

    $(TABLE_ID).jtable(treeTableDef(TABLE_ID, -1, '')); //-1 => null parent === topnode
    $(TABLE_ID).jtable('load');
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
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?ParentTreeNode_FK=' + parentTreeNode_FK,
            createAction: '/' + SARON_URI + 'app/web-api/createOrganizationStructure.php?ParentTreeNode_FK=' + parentTreeNode_FK,
            updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationStructure.php?ParentTreeNode_FK=' + parentTreeNode_FK,
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationStructure.php'
        }, 
        fields: {
            TreeId: {
                key: true,
                list: false
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
                        if(data.record.HasSubUnit === '0')
                            src = '"/' + SARON_URI + SARON_IMAGES_URI + 'child.png" title="Under organisation"';
                        else
                            src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haschild.png" title="Under organisation"';
                           
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
                    else{
                        return null;
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
                            src = '"/' + SARON_URI + SARON_IMAGES_URI + 'pos.png" title="Positioner"';
                        else
                            src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haspos.png" title="Positioner"';
                        
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
            SortOrder:{
                list: false,
                title: 'Nr',
                inputTitle: 'Nr (Värde för sorteringsordning)', 
                width: '1%',
                listClass: 'saron-number',
                defaultValue: 10,
                input: function (data) {
                    if (data.record) {
                        return '<input type="number" name="SortOrder" style="width:200px" value="' + data.record.SortOrder + '" />';
                    } 
                    else {
                        return '<input type="number" name="SortOrder" style="width:200px" value="" />';
                    }
                }
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
                create: true,
                edit: true,
                title: 'Typ av enhet',
                width: '5%'
                ,
                options: function (data){
                    if(data.source !== 'list')
                        data.clearCache();

                    return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=options';
                }
            },
            Updater: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '5%',
                options: function (data){
//                    if(data.source !== 'list')
//                        data.clearCache();

                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php';        
                }
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                //displayFormat: DATE_FORMAT,
                width: '5%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.HasSubUnit !== '0' || data.record.HasPos !== '0')
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
        },
        deleteFormClosed: function (event, data){
        }
    };
}    

function posTableDef(tableId, orgTreeNode_FK, unitName, orgUnitType_FK){
    return {
        title: function(){
            return 'Roller inom ' + unitName;
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'OrgRole_FK', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?Org_Tree_FK=' + orgTreeNode_FK,
            createAction:   '/' + SARON_URI + 'app/web-api/createOrganizationPos.php?Org_Tree_FK=' + orgTreeNode_FK,
            //updateAction:   '/' + SARON_URI + 'app/web-api/updateNews.php'
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updateOrganizationPos.php?Org_Tree_FK=' + orgTreeNode_FK,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result !== 'ERROR'){
                                var records = data['Records'];
                                _updateOrganizationUnitTypeRecord(records);
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationPos.php?Org_Tree_FK=' + orgTreeNode_FK
        }, 
        fields: {
            PosId: {
                key: true,
                list: false
            },
            PosTreeId:{                
                list: false,
                edit: false,
                create: false,
                type: 'hidden'
            },
            MultiPos:{
                sorting: false,
                width: "1%",
                display: function (data) {
                    if(data.record.MultiPos === '1'){
                        var src;
                            src = '"/' + SARON_URI + SARON_IMAGES_URI + 'multipos.png" title="Rollen finns på fler ställen"';
                        
                        var imgTag = _setImageClass(data.record, "Role", src, -1);
                        var $imgRole = $(imgTag);

                        $imgRole.click(data, function (event){
                            var $tr = $imgRole.closest('tr');
                            $(tableId).jtable('openChildTable', $tr, treeListTableDef(tableId, data.record.PosId, data.record.Name), function(data){
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
            OrgRole_FK: {
                width: '10%',
                title: 'Roll',
                edit: false,
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
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php?selection=options';
                }
            },
            People_FK: {
                width: '15%',
                title: 'Innehavare',
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listPeople.php?selection=options';
                }
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
            PrevPerson: {
                width: '10%',
                edit: false,
                create: false,
                title: 'Föregående'
            },
            Updater: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '5%',
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
                width: '5%'
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
    
    
function treeListTableDef(tableId, Org_Pos_FK, parentName){
        return {
            title: parentName + " finns på nedanstående platser i organisationen.",
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'Name', //Set default sorting        
            actions: {
                listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationUnitMember.php?Org_Pos_FK=' + Org_Pos_FK,
                createAction: '/' + SARON_URI + 'app/web-api/createOrganizationUnitMember.php?Org_Pos_FK=' + Org_Pos_FK,
                updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationUnitMember.php?Org_Pos_FK=' + Org_Pos_FK,
                deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationUnitMember.php'
            }, 
            fields: {
                Org_Tree_FK: {
                    title: 'Organisatorsik enhet',
                    key: true,
                    options: function(){
                       return '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?selection=options';
                    } 
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
    };
}
    
