/* global J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */
"use strict";
    
$(document).ready(function () {
    const TABLE_ID = "#ORG_UNIT";

    $(TABLE_ID).jtable(orgTableDef(TABLE_ID, -1, null));
    $(TABLE_ID).jtable('load');
    $(TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
});

function orgTableDef(tableId, roleId, roleName){
    return {
        title: 'Organisatoriska enheter',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?RoleId=' + roleId,
            createAction:   '/' + SARON_URI + 'app/web-api/createOrganizationUnit.php',
            //updateAction:   '/' + SARON_URI + 'app/web-api/updateNews.php'
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updateOrganizationUnit.php',
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
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationUnit.php'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            Role:{
                width: '1%',
                sorting: false,
                display: function(data){
                    if(data.record.BusinessRole_FK !==  null){
                        var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'roles.png" title="Roller"';
                        var imgTag = _setImageClass(data.record, "Role", src, -1);
                        var $imgChild = $(imgTag);

                        $imgChild.click(data, function (event){
                            var $tr = $imgChild.closest('tr');
                            $(tableId).jtable('openChildTable', $tr, roleTableDef(tableId, data.record.Id, data.record.Name), function(data){
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
            HasSubUnit: {
                title: 'Har underenheter',
                width: '10%',
                options: {"0":"", "1":"Ja"}
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
                    return '/' + SARON_URI + 'app/web-api/listUsersAsOptions.php'           
                }
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: 'yy-mm-dd',
                width: '15%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.BusinessRole_FK !==  null)
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
    }    
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
