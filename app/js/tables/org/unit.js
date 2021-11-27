/* global DATE_FORMAT,
saron, 
SUBUNIT_ENABLED, 
ORG, TABLE,
RECORD, OPTIONS,
POS_ENABLED
 */
    
"use strict";    
const unitListUri = 'app/web-api/listOrganizationUnit.php';

function unitTableDef(tableViewId, tablePath, childTableTitle){

    var tableName = "";
    if(tableViewId === saron.table.unittree.viewid)
        tableName = saron.table.unittree.name;
    else if(tableViewId === saron.table.unitlist.viewid)
        tableName = saron.table.unitlist.name;
    else
        tableName = saron.table.unit.name;
    
    return {
        showCloseButton: false,
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
            listAction:   '/' + saron.uri.saron + unitListUri,
            createAction: '/' + saron.uri.saron + 'app/web-api/createOrganizationUnit.php',
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateOrganizationUnit.php',
            deleteAction: '/' + saron.uri.saron + 'app/web-api/deleteOrganizationUnit.php'
        }, 
        fields: {
            Id: {
                key: true, 
                list: false
            },
            ParentId:{
                list: false,
                edit: false,
                create: false    
            },
            SubUnitEnabled: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                list: includedIn(tableViewId, saron.table.unittree.viewid),
                
                display: function (data) {
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" har följande roller';
                    var childTableName  = ""; // no adding of tablePath on requrssion 
                    var childTablePath = tablePath + "/" + childTableName;
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
                        var childTableDef = unitTableDef(tableViewId, childTablePath, childTableTitle);
                        var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, tableName, TABLE, unitListUri);
                        var $imgClose = closeChildTable(data, tableViewId, tableName, TABLE, unitListUri);
                        
                        return getChildNavIcon(data, tableName, $imgChild, $imgClose);
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
                    var childTableName = saron.table.pos.name;
                    var childTablePath = tablePath + "/" + childTableName;
                    var childTableTitle = data.record.Name + " har följande positioner";
                    var tooltip = "";
                    var imgFile = "";         
                    
                    if(data.record.PosEnabled === POS_ENABLED){
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

                        var childTableDef = posTableDef(tableViewId, childTablePath, childTableTitle);
                        var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, TABLE, unitListUri);
                        var $imgClose = closeChildTable(data, tableViewId, childTableName, TABLE, unitListUri);
                        
                        return getChildNavIcon(data, childTableName, $imgChild, $imgClose);
                    }
                    return null;
                }
            },
            ParentTreeNode_FK:{
                list: includedIn(tableViewId, saron.table.unitlist.viewid),
                edit: true, 
                create: !includedIn(tableViewId, saron.table.unittree.viewid),
                title: 'Överordna verksamhet',
                options: function(data) {
                    if(includedIn(tableViewId, saron.table.unitlist.viewid))
                        data.record.ParentId=null; //using cache
                    
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, unitListUri);                    
                    return '/' + saron.uri.saron + unitListUri + parameters;
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
                list: includedIn(tableViewId, saron.table.role.viewid + saron.table.unittype.viewid + saron.table.unitlist.viewid)
            },
            SubUnits: {
                title: "Underenheter",
                create: false,
                edit: false,
                list: includedIn(tableViewId, saron.table.role.viewid + saron.table.unittype.viewid + saron.table.unitlist.viewid)
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
                    if(includedIn(tableViewId, saron.table.unitlist.viewid))
                        data.record.ParentId=null; //using cache
                    
                    var uri = 'app/web-api/listOrganizationUnitType.php';
                    var parameters = getOptionsUrlParameters(data, tableViewId, tablePath, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
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
            //updateParentTable(data, tableViewId, listUri);
                    
            if(data.record.HasSubUnit !== '0' || data.record.HasPos !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            else
                data.row.find('.jtable-delete-command-button').show();
            
            if(data.record.parentNodeChange !== '0')
                $(tableViewId).jtable('load');

        },  
        rowInserted: function(event, data){
            data.row.addClass("Id_" + data.record.Id); 
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
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
            if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org'){ 
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit){
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
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };
}    


function getDefaultUnitSorting(currentTableId){
    switch(currentTableId) {
        case saron.table.unitlist.viewid:
            return "Name";
        case saron.table.unittree.viewid:
            return "Prefix, Name";
        case saron.table.role.viewid:
            return "Name";
        case saron.table.role.viewid_UNITTYPE:
            return "Name";
        default:
            return "Name";
    }
}