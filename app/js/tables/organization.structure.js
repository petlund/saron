/* global J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";
    
$(document).ready(function () {
    const TABLE_ID = "#ORG_STRUCT";

    $(TABLE_ID).jtable(treeTableDef(TABLE_ID, -1, '')); //-1 => null parent === topnode
    $(TABLE_ID).jtable('load');
    $(TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
});
function treeTableDef(tableId, parentId, parentName){
    return {
        title: function(data){
            if(parentName.length > 0)
                return 'Organisatorisk struktur under ' + parentName;
            else
                return 'Organisatorisk struktur';
                
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationStructure.php?ParentId=' + parentId,
            createAction:   '/' + SARON_URI + 'app/web-api/createOrganizationStructure.php' + parentId,
            //updateAction:   '/' + SARON_URI + 'app/web-api/updateNews.php'
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updateOrganizationStructure.php',
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
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationStructure.php'
        }, 
        fields: {
            Id: {
                key: true,
                list: false
            },
            ParentUnitId_FK: {
              list: false,  
              title: 'Parent'  
            },
            HasSubUnit: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                display: function (data) {
                    if(data.record.HasSubUnit !== '0'){
                        var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'child.png" title="Under organisation"';
                        var imgTag = _setImageClass(data.record, "Org", src, -1);
                        var $imgChild = $(imgTag);

                        $imgChild.click(data, function (event){
                            var $tr = $imgChild.closest('tr');
                            $(tableId).jtable('openChildTable', $tr, treeTableDef(tableId, data.record.TreeId, data.record.UnitName), function(data){
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
            HasSubRole: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                display: function (data) {
                    if(data.record.HasSubRole !== '0'){
                        var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'roles.png" title="Roller"';
                        var imgTag = _setImageClass(data.record, "Role", src, -1);
                        var $imgRole = $(imgTag);

                        $imgRole.click(data, function (event){
                            var $tr = $imgRole.closest('tr');
                            $(tableId).jtable('openChildTable', $tr, posTableDef(tableId, data.record.TreeId, data.record.UnitName), function(data){
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
            UnitType_FK:{
                create: true,
                edit: true,
                title: 'Typ av enhet',
                width: '5%',
                options: function (data){
                    if(data.source === 'list')
                        return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=options';
                    else
                        return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=options';
                }
            },
            UnitName: {
                width: '5%',
                title: 'Organisatorisk enhet'
            },
            Description: {
                width: '15%',
                title: 'Beskrivning'
            },
            Updater: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '10%',
                options: function (){
                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php'           
                }
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: 'yy-mm-dd',
                width: '10%'
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
    }
}    

function posTableDef(tableId, nodeId, unitName){
    return {
        title: function(){
            return 'Roller inom ' + unitName;
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationPos.php?NodeId=' + nodeId,
            createAction:   '/' + SARON_URI + 'app/web-api/createOrganizationPos.php?NodeId=' + nodeId,
            //updateAction:   '/' + SARON_URI + 'app/web-api/updateNews.php'
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updateOrganizationPos.php',
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
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationPos.php'
        }, 
        fields: {
            Id: {
                key: true,
                list: false
            },
            BusinessPosRole_FK: {
                width: '10%',
                title: 'Roll',
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?selection=options';
                }
            },
            BusinessPosStatus_FK: {
                width: '5%',
                title: 'Status',
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listOrganizationStatus.php?selection=options';
                }
            },
            PersonId: {
                width: '15%',
                title: 'Innehavare',
                options: function(){
                    return '/' + SARON_URI + 'app/web-api/listPeople.php?selection=options';
                }
            },
            PersonId2: {
                width: '5%',
                create: false,
                edit: false,
                title: 'Innehavare',
                display: function(data){
                    return data.record.PersonId;
                }
            },
            MemberState: {
                title: 'Medlemsstatus',
                edit: false,
                create: false,
                width: '5%',                
            },
            pCur_Mobile: {
                title: 'Mobil',
                edit: false,
                create: false,
                width: '10%',                
            },
            pCur_Email: {
                title: 'Mail',
                edit: false,
                create: false,
                width: '10%',                
            },
            PrevPerson: {
                width: '10%',
                edit: false,
                create: false,
                title: 'Föregående innehavare'
            },
            Updater: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '5%',
                options: function (){
                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php'           
                }
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: 'yy-mm-dd',
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
    }
}    

function _updateOrganizationUnitTypeRecord(records){
    var key = document.getElementsByClassName("jtable-data-row");
    if(key===null)
        return;
    
    for(var i = 0; i<key.length;i++){
        if(key[i].dataset.recordKey === records[0].Id){ 
            //key[i].cells[5].innerHTML = (records[0].Updated).substring(0,10);                                              
        }
    }
}
