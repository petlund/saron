/* global DATE_FORMAT, SARON_URI, J_TABLE_ID, 
 PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, 
 inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
ORG, 
 POS_ENABLED, POS_DISABLED,
 SUBUNIT_ENABLED, SUBUNIT_DISABLED,
TABLE_VIEW_ROLE, TABLE_NAME_ROLE, 
TABLE_VIEW_UNITTYPE, TABLE_NAME_UNITTYPE,
TABLE_VIEW_UNIT, TABLE_NAME_UNIT,
RECORDS, RECORD, OPTIONS, SOURCE_LIST, SOURCE_CREATE, SOURCE_EDIT
*/
"use strict";

$(document).ready(function () {
    $(TABLE_VIEW_UNITTYPE).jtable(unitTypeTableDef(TABLE_VIEW_UNITTYPE, null, -1, null));
    $(TABLE_VIEW_UNITTYPE).jtable('load');
});



function unitTypeTableDef(tableViewId, parentTablePath, parentId, childTableTitle){
    const listUri = 'app/web-api/listOrganizationUnitType.php';
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
            listAction:   '/' + SARON_URI + listUri + getURLParameter(parentId, tablePath, SOURCE_LIST, RECORDS),
            createAction: '/' + SARON_URI + 'app/web-api/createOrganizationUnitType.php',
            updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationUnitType.php',
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationUnitType.php'
        },
        fields:{
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
                sorting: false,
                create: false,
                list: includedIn(tableViewId, TABLE_VIEW_UNITTYPE),
                display: function(data){
                    var childTableName = TABLE_NAME_UNIT;
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" används för nedanstående organisatoriska enheter';                            
                    var tooltip = "Enhetstypen används inom följande organisatoriska enheter";
                    var imgFile = "unit.png";
                    
                    if(data.record.UsedInUnit ===  "1"){
                        var childTable = unitTableDef(tableViewId, parentTablePath, parentId, childTableTitle);
                        var $imgChild = openChildTable(data, tableViewId, childTable, listUri, imgFile, tooltip, childTableName);
                        var $imgClose = closeChildTable(data, tableViewId, childTableName);
                        
                        return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                    }
                    return null;
                }
            },            
            HasPos:{
                width: '3%',
                title: 'Roller',
                create: false,
                sorting: false,
                edit: false,   
                list: includedIn(tableViewId, TABLE_VIEW_UNITTYPE),
                display: function(data){
                    var childTableName = TABLE_NAME_ROLE;
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" har följande roller';
                    var tooltip = "";
                    var imgFile = "";

                    if(data.record.PosEnabled ===  POS_ENABLED){

                        if(data.record.HasPos === '0'){
                            imgFile = "pos.png";
                            tooltip = "Inga roller";
                        }
                        else{
                            imgFile = "haspos.png";
                            tooltip = "Enhetstypen har roller";
                        }
                        
                        var childTable = roleTableDef(tableViewId, parentTablePath, parentId, childTableTitle);
                        var $imgChild = openChildTable(data, tableViewId, childTable, listUri, imgFile, tooltip, childTableName);
                        var $imgClose = closeChildTable(data, tableViewId, childTableName);
                        
                        return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                    }
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



function updateUnitTypeRecord(tableViewId, data){
    var url = '/' + SARON_URI + 'app/web-api/listOrganizationUnitType.php';
    var options = {record:{"Id": data.record.Id}, "clientOnly": false, "url":url};
    $(tableViewId).jtable('updateRecord', options);
}


