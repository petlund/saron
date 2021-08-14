/* global DATE_FORMAT,
SARON_URI,SARON_IMAGES_URI, 
SUBUNIT_ENABLED, 
ORG,
TABLE_VIEW_ROLE, TABLE_NAME_ROLE, 
TABLE_VIEW_UNITTYPE, TABLE_NAME_UNITTYPE,
TABLE_VIEW_ROLE_UNITTYPE, TABLE_NAME_ROLE_UNITTYPE,
TABLE_VIEW_UNIT, TABLE_NAME_UNIT,
TABLE_VIEW_POS, TABLE_NAME_POS,
TABLE_VIEW_UNITLIST, TABLE_NAME_UNITLIST,
TABLE_VIEW_UNITTREE, TABLE_NAME_UNITTREE,
RECORDS, RECORD, OPTIONS,
SOURCE_LIST,
POS_ENABLED
 */
    
"use strict";    

function unitTableDef(tableViewId, parentTablePath, childTableTitle){
    const listUri = 'app/web-api/listOrganizationUnit.php';
    //var options = getPostData(tableViewId, )
    var tableName = "";
    if(tableViewId === TABLE_VIEW_UNITTREE)
        tableName = TABLE_NAME_UNITTREE;
    else if(tableViewId === TABLE_VIEW_UNITLIST)
        tableName = TABLE_NAME_UNITLIST;
    else
        tableName = TABLE_NAME_UNIT;
    
    var tablePath = tableName;
    if(tableName === TABLE_NAME_UNITTREE && parentTablePath === TABLE_NAME_UNITTREE + "/" + TABLE_NAME_UNITTREE)
        tablePath = TABLE_NAME_UNITTREE + "/" + TABLE_NAME_UNITTREE;
    else
        if(parentTablePath !== null){
            tablePath = parentTablePath + "/" + tableName;
        }
            
    return {
        title: function(){
            if(childTableTitle !== null)
                return childTableTitle;
            else
                return 'Organisatoriska enheter';
                
        },
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: getDefaultUnitSorting(tableViewId), //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny organisatorisk enhet.'},
        actions: {
            listAction:   '/' + SARON_URI + listUri,
            createAction: '/' + SARON_URI + 'app/web-api/createOrganizationUnit.php',
            updateAction: '/' + SARON_URI + 'app/web-api/updateOrganizationUnit.php',
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteOrganizationUnit.php'
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
            ParentId:{
            },
            ParentTreeNode_FK:{
                list: includedIn(tableViewId, TABLE_VIEW_UNITLIST),
                edit: true, 
                create: true,
                title: 'Överordna verksamhet'
//                ,
//                options: function(data) {
//                    var url =  '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php';
// 
//                    if(data.source !== 'list'){
//                        data.clearCache();
//                    } 
//                    var options = getURLParameter(null, tablePath, data.source, OPTIONS);
//
//                    return post(url, options);
//                }                
            },
            SubUnitEnabled: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                list: includedIn(tableViewId, TABLE_VIEW_UNITTREE),
                
                display: function (data) {
                    var childTableName = TABLE_NAME_UNITTREE;
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" har följande roller';
                    var tooltip = "";
                    var imgFile = "";

                    if(data.record.SubUnitEnabled === SUBUNIT_ENABLED){
                        if(data.record.HasSubUnit === '0' || data.record.statusSubProposal === null  || data.record.statusSubVacant === null){
                            tooltip = "Underorganisation";
                            imgFile = "child.png";
                        }
                        else{
                            if(data.record.statusSubProposal > 0 && data.record.statusSubVacant > 0){
                                tooltip = 'Underorganisation med ' + data.record.statusSubProposal + ' förslag och ' + data.record.statusSubVacant + ' vakans(er)';
                                imgFile = "haschild_YR.png";
                            }
                            else if(data.record.statusSubProposal === "0" && data.record.statusSubVacant !== "0"){
                                tooltip = 'Underorganisation med ' + data.record.statusSubVacant + ' vakans(er)';
                                imgFile = "haschild_R.png";
                            }
                            else if(data.record.statusSubProposal !== "0" && data.record.statusSubVacant === "0"){
                                tooltip = 'Underorganisation med ' + data.record.statusSubProposal + ' förslag';
                                imgFile = "haschild_Y.png";
                            }
                            else{
                                tooltip = "Underorganisation";
                                imgFile =  "haschild.png";
                            }
                        }
                        var childTableDef = unitTableDef(tableViewId, tablePath, childTableTitle);
                        var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, ORG, listUri);
                        var $imgClose = closeChildTable(data, tableViewId, childTableName, ORG, listUri);
                        
                        return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                    }
                    return null;
                }
            },
            PosEnabled: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                display: function(data){
                    var childTableName = TABLE_NAME_POS;
                    var childTableTitle = data.record.Name + " har följande positioner";
                    var tooltip = "";
                    var imgFile = "";         
                    
                    if(data.record.PosEnabled === POS_ENABLED){
                        var src;
                        if(data.record.HasPos === '0'){
                            imgFile = 'unit_empty.png';
                            tooltip = "Inga positioner";
                        }
                        else{
                            if(data.record.statusProposal !== "0" && data.record.statusVacant !== "0"){
                                imgFile = 'unit_YR.png';
                                tooltip = data.record.statusProposal + ' Förslag och ' + data.record.statusVacant + ' vakans(er) på position(er)';
                            }
                            else if(data.record.statusProposal === "0" && data.record.statusVacant !== "0"){
                                imgFile = 'unit_R.png'; 
                                tooltip = data.record.statusVacant + ' Vakans(er) på position(er)';
                            }
                            else if(data.record.statusProposal !== "0" && data.record.statusVacant === "0"){
                                imgFile = 'unit_Y.png'; 
                                tooltip = data.record.statusProposal + ' Förslag på position(er)';
                            }
                            else{
                                imgFile = 'unit.png" title="Bemannade positioner"';
                            }
                        }

                        var childTableDef = posTableDef(tableViewId, tablePath, data.record.Id, childTableTitle);
                        var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, ORG, listUri);
                        var $imgClose = closeChildTable(data, tableViewId, childTableName, ORG, listUri);
                        
                        return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                    }
                    return null;
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
            Path: {
                title: "Sökväg",
                create: false,
                edit: false,
                list: includedIn(tableViewId, TABLE_VIEW_ROLE + TABLE_VIEW_UNITTYPE + TABLE_VIEW_UNITLIST)
            },
            SubUnits: {
                title: "Underenheter",
                create: false,
                edit: false,
                list: includedIn(tableViewId, TABLE_VIEW_ROLE + TABLE_VIEW_UNITTYPE + TABLE_VIEW_UNITLIST)
            },
            Description: {
                width: '15%',
                title: 'Beskrivning'
            },
            OrgUnitType_FK:{
                list: true,
                title: 'Typ av enhet',
                inputTitle: 'Typ av enhet (Kan inte ändras. Vill du ändra behöver du skapa en ny organisatorisk enhet).',
                width: '5%',
                options: function (data){
                    var url = '/' + SARON_URI + 'app/web-api/listOrganizationUnitType.php';
                    var parameters = getURLParameter(data.record.ParentId, tablePath, data.source, OPTIONS);

                    if(data.source !== 'list'){
                        data.clearCache();
                    } 
                    return url + parameters;
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
            var parentId = data.record.ParentId;

            
            if(parentId > 0){
                table = $(".Id_" + parentId).closest('div.jtable-child-table-container');
                if(table.length === 0) 
                    table = $(tableViewId);

                var url =  '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=single_node';
                var options = {record:{"Id": parentId}, "clientOnly": false, "url":url};
                table.jtable('updateRecord', options);                                
            }  
                
                    
            if(data.record.HasSubUnit !== '0' || data.record.HasPos !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            else
                data.row.find('.jtable-delete-command-button').show();
            
            if(data.record.parentNodeChange !== '0')
                $(tableViewId).jtable('load');

        },  
        rowInserted: function(event, data){
            data.row.addClass("Id_" + data.record.Id); 
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
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
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


function updateParentUnit(tableViewId, data){
    var id = data.record.Id;
    
    var url =  '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php';
    var options = {record:{"Id": id}, "clientOnly": false, "url":url};
    $(tableViewId).jtable('updateRecord', options);                                    
}



function getDefaultUnitSorting(currentTableId){
    switch(currentTableId) {
        case TABLE_VIEW_UNITLIST:
            return "Name";
        case TABLE_VIEW_UNITTREE:
            return "Prefix, Name";
        case TABLE_VIEW_ROLE:
            return "Name";
        case TABLE_VIEW_ROLE_UNITTYPE:
            return "Name";
        default:
            return "Name";
    }
}