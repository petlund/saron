/* global DATE_FORMAT, SARON_URI, J_TABLE_ID, 
 PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, 
 inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
ORG, 
 POS_ENABLED, POS_DISABLED,
 SUBUNIT_ENABLED, SUBUNIT_DISABLED,
TABLE_VIEW_ROLE, TABLE_NAME_ROLE, 
TABLE_VIEW_UNITTYPE, TABLE_NAME_UNITTYPE,
RECORDS, RECORD, OPTIONS, SOURCE_LIST, SOURCE_CREATE, SOURCE_EDIT
*/
"use strict";

$(document).ready(function () {
    $(TABLE_VIEW_UNITTYPE).jtable(unitTypeTableDef(TABLE_VIEW_UNITTYPE, null, -1, null));
    $(TABLE_VIEW_UNITTYPE).jtable('load');
});



function unitTypeTableDef(tableViewId, parentTablePath, parentId, childTableTitle){
    const tableName = TABLE_NAME_UNITTYPE;
    var tablePath = tableName;
    if(parentTablePath !== null)
        tablePath = parentTablePath + "/" + tableName;
 
    return {
        title: function (){
            if(childTableTitle !== null)
                return childTableTitle;
            else
                return 'Organisatoriska enhetertyper';
        },        
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny typ av organisatorisk enhet.'},
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationUnitType.php?ParentId=' + parentId + '&TablePath=' + tablePath + "&ResultType=" + RECORDS,
            createAction: '/' + SARON_URI + 'app/web-api/createOrganizationUnitType.php',
            updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationUnitType.php',
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationUnitType.php'
        },
        fields: {
            TablePath:{
                list: true,
                edit: false,
                create: false
            },
            Id: {
                key: true,
                list: false
            },
            UsedInUnit: {
                title: 'Används',
                width: '3%',
                edit: false,
                create: false,
                list: includedIn(tableViewId, TABLE_VIEW_UNITTYPE),
                display: function(data){
                    var src;
                    if(data.record.UsedInUnit ===  "1"){
                        src= '"/' + SARON_URI + SARON_IMAGES_URI + 'unit.png" title="Används på följande ställen"';
                        
                        var imgTag = _setImageClass(data.record, TABLE_NAME_UNITTYPE, src, -1);
                        var $imgChild = $(imgTag);

                        $imgChild.click(data, function (event){
                            var $tr = $imgChild.closest('tr');
                            var childTableTitleUsedInUnit = 'Enhetstypen "' + data.record.Name + '" används för nedanstående organisatoriska enheter';
                            
                            $(tableViewId).jtable('openChildTable', $tr, unitTableDef(tableViewId, tablePath,  data.record.Id, childTableTitleUsedInUnit), function(data){
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
            HasPos:{
                width: '3%',
                title: 'Roller',
                create: false,
                edit: false,   
                list: includedIn(tableViewId, TABLE_VIEW_UNITTYPE),
                display: function(data){
                    if(data.record.PosEnabled ===  POS_ENABLED){
                        var src;
                        if(data.record.HasPos === '0')
                            src= '"/' + SARON_URI + SARON_IMAGES_URI + 'pos.png" title="Inga roller"';
                        else
                            src = '"/' + SARON_URI + SARON_IMAGES_URI + 'haspos.png" title="Roller"';
                        
                        var imgTag = _setImageClass(data.record, "UnitType", src, -1);
                        var $imgChild = $(imgTag);

                        $imgChild.click(data, function (event){
                            var $tr = $imgChild.closest('tr');
                            var childTableTitleIncludedRole = 'Enhetstypen "' + data.record.Name + '" har följande roller';
                            $(tableViewId).jtable('openChildTable', $tr, roleTableDef(tableViewId, tablePath, data.record.Id,childTableTitleIncludedRole ), function(data){
                                data.childTable.jtable('load');
                            });
                        });
                        return $imgChild;
                    }
                    else
                        return null;
                }
            },
            Name: {
                title: 'Benämning',
                width: '15%'
            },
            SubUnitEnabled: {
                title: 'Kan ha underenheter',
                width: '15%',                
                options: function(data){
                    
                    if(data.source !== "create"){    
                        var val = data.record.UseChild;
                        if(val === null)
                            val = 0;
                        return {"1":"Nej", "2":"Ja (" + val + " underenheter)"};
                    }
                    return {"1":"Nej", "2":"Ja"};
                }
            },
            PosEnabled: {
                title: 'Kan ha bemanning',
                width: '15%',
                options: function(data){
                   
                    if(data.source !== "create"){
                        var val = data.record.UseRole;
                        if(val === null)
                            val = 0;
                        return {"1":"Nej", "2":"Ja (" + val + " positioner)"};
                    }
                    return {"1":"Nej", "2":"Ja"};
                }
            },
            Description: {
                title: 'Beskrivning',
                width: '50%'
            },
            UpdaterName: {
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '15%'
            },
            Updated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: DATE_FORMAT,
                width: '15%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.UsedInUnit > 0 || data.record.HasPos > 0)
                data.row.find('.jtable-delete-command-button').hide();

            addDialogDeleteListener(data);
                        
        },        
        rowUpdated: function(event, data){
            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            else
                if(data.record.UsedInUnit !== '0')
                    data.row.find('.jtable-delete-command-button').hide();
                else
                    data.row.find('.jtable-delete-command-button').show();
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = "yellow";

            if(data.formType !== "create"){
                if(data.record.UseChild > 0){
                    var inp = data.form.find('select[name=SubUnitEnabled]');            
                    inp[0].disabled=true;            
                }

                if(data.record.UseRole > 0){
                    var inp = data.form.find('select[name=PosEnabled]');            
                    inp[0].disabled=true;            
                }
            }

            data.form.css('width','600px');
            data.form.find('input[name=Description]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        }
    };    
}



function updateUnitRecord(tableViewId, data){
    var url = '/' + SARON_URI + 'app/web-api/listOrganizationUnitType.php?Id=' + data.record.orgUnitType_FK;
    var options = {record:{"Id": data.record.orgUnitType_FK}, "clientOnly": false, "url":url};
    $(tableViewId).jtable('updateRecord', options);
}



//function unitType_roleTableDef(tableId, orgUnitType_FK, childTableTitle){
//    return {
//        title: function (){
//            if(childTableTitle !== null)
//                return childTableTitle;
//            else
//                return 'Roller';
//        },
//        paging: true, //Enable paging
//        pageSize: 10, //Set page size (default: 10)
//        pageList: 'minimal',
//        sorting: true, //Enable sorting
//        multiSorting: true,
//        defaultSorting: 'SortOrder', //Set default sorting        
//        messages: {addNewRecord: 'Lägg till en ny koppling till en roll.'},
//        actions: {
//            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationRole-UnitType.php?selection=role&OrgUnitType_FK=' + orgUnitType_FK,
//            createAction: function(postData) {
//                return $.Deferred(function ($dfd) {
//                    $.ajax({
//                        url:  '/' + SARON_URI + 'app/web-api/createOrganizationRole-UnitType.php?selection=role&OrgUnitType_FK=' + orgUnitType_FK,
//                        type: 'POST',
//                        dataType: 'json',
//                        data: postData,
//                        success: function (data) {
//                            $dfd.resolve(data);
//                            if(data.Result === 'OK'){
//                                updateUnitRecord(data, 'create', orgUnitType_FK);                               
//                            }
//                        },
//                        error: function () {
//                            $dfd.reject();
//                        }
//                    });
//                });
//            },
//            updateAction:   '/' + SARON_URI + 'app/web-api/updateOrganizationRole-UnitType.php?selection=role',
//            deleteAction: function(postData) {
//                return $.Deferred(function ($dfd) {
//                    $.ajax({
//                        url:  '/' + SARON_URI + 'app/web-api/deleteOrganizationRole-UnitType.php?OrgUnitType_FK=' + orgUnitType_FK,
//                        type: 'POST',
//                        dataType: 'json',
//                        data: postData,
//                        success: function (data) {
//                            $dfd.resolve(data);
//                            if(data.Result === 'OK'){
//                                updateUnitRecord(data, 'delete', orgUnitType_FK);                               
//                            }
//                        },
//                        error: function () {
//                            $dfd.reject();
//                        }
//                    });
//                });
//            },        
//        },
//        fields: {
//            TablePath:{
//                list: false,
//                edit: false,
//                create: false,
//                display: function(){
//                    return 'related_unittype';
//                }
//            },
//            Id: {
//                key: true,
//                list: false
//            },
//            OrgRole_FK: {
//                list: true,
//                edit: false,
//                width: '20%',
//                title: 'Rollbenämning',
//                options: function(){
//                    return '/' + SARON_URI + 'app/web-api/listOrganizationRole.php?selection=options';
//                }
//            },
//            PosOccurrency:{
//                edit: false,
//                create: false,
//                title: "Antal positioner",
//                width: "10%",
//                display: function (data){
//                    return _setClassAndValue(data.record, "PosOccurrency", -1);
//                }                  
//            },
//            Description: {
//                edit: false,
//                create: false,
//                title: 'Beskrivning',
//                width: '50%'
//            },
//            SortOrder: {
//                edit: true,
//                create: true,
//                title: 'Sortering',
//                options: {"0": "-", "1": "Nivå 1", "2": "Nivå 2", "3":"Nivå 3", "4": "Nivå 4", "5":"Nivå 5","6":"Nivå 6"},
//                width: '10%'
//            },
//            UpdaterName: {
//                edit: false,
//                create: false, 
//                title: 'Uppdaterare',
//                width: '15%'
//            },
//            Updated: {
//                edit: false,
//                create: false, 
//                title: 'Uppdaterad',
//                type: 'date',
//                displayFormat: DATE_FORMAT,
//                width: '15%'
//            }
//        },
//        rowInserted: function(event, data){
//            if (data.record.user_role !== 'edit' && data.record.user_role !== 'org'){
//                data.row.find('.jtable-edit-command-button').hide();
//                data.row.find('.jtable-delete-command-button').hide();
//            }
//            
//            if(data.record.PosOccurrency > 0){ // Pos exist
//                data.row.find('.jtable-delete-command-button').hide();
//
//            }
//
//            addDialogDeleteListener(data);
//        },        
//        recordsLoaded: function(event, data) {
//            if(data.serverResponse.user_role === 'edit' || data.serverResponse.user_role === 'org'){ 
//                $(tableId).find('.jtable-toolbar-item-add-record').show();
//            }
//        },        
//        formCreated: function (event, data){
//            if(data.formType === 'edit')
//                data.row[0].style.backgroundColor = "yellow";
//                        
//            data.form.css('width','600px');
//            data.form.find('input[name=Description]').css('width','580px');
//        },
//        formClosed: function (event, data){
//            if(data.formType === 'edit')
//                data.row[0].style.backgroundColor = '';
//        }
//    };
//}
