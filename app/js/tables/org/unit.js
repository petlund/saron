/* global DATE_FORMAT,
saron, 
SUBUNIT_ENABLED, 
ORG, ORG_UNIT, TABLE,
RECORD, OPTIONS,
POS_ENABLED
 */
    
"use strict";    

function unitTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.unit.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        showCloseButton: false,
        title: 'Organisatoriska enheter',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: "Name",        
        messages: {addNewRecord: 'Lägg till en ny organisatorisk enhet.'},
        actions: {
            listAction:   saron.root.webapi  +  'listOrganizationUnit.php',
            createAction: saron.root.webapi  +  'createOrganizationUnit.php',
            updateAction: saron.root.webapi  +  'updateOrganizationUnit.php',
            deleteAction: saron.root.webapi  +  'deleteOrganizationUnit.php'
        }, 
        fields: {
            Id: {
                key: true, 
                list: false
            },
            ParentId:{
                defaultValue: parentId,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: tableName
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: tablePath
            },
            SubUnitEnabled: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                list: true,
                display: function (data) {
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" har följande underenheter';
                    var tooltip = "";
                    var imgFile = "";
                    var type = 0;
                    var clientOnly = true;
                    
                    if(data.record.SubUnitEnabled === SUBUNIT_ENABLED){
                        if(data.record.HasSubUnit === '0' || data.record.statusSubProposal === null  || data.record.statusSubVacant === null){
                            tooltip = "Inga underorganisationer";
                            imgFile = "child.png";
                        }
                        else{
                            if(data.record.statusSubProposal > 0 && data.record.statusSubVacant > 0){
                                tooltip = 'Underorganisationer har ' + data.record.statusSubProposal + ' förslag och ' + data.record.statusSubVacant + ' vakans(er)';
                                imgFile = "haschild_YR.png";
                            }
                            else if(data.record.statusSubProposal === "0" && data.record.statusSubVacant !== "0"){
                                tooltip = 'Underorganisationer har ' + data.record.statusSubVacant + ' vakans(er)';
                                imgFile = "haschild_R.png";
                            }
                            else if(data.record.statusSubProposal !== "0" && data.record.statusSubVacant === "0"){
                                tooltip = 'Underorganisationer har ' + data.record.statusSubProposal + ' förslag';
                                imgFile = "haschild_Y.png";
                            }
                            else{
                                tooltip = "Underorganisationer";
                                imgFile =  "haschild.png";
                            }
                        }

                        var childTableDef = unitTableDef(childTableTitle, tablePath, data.record.Id, tableDef); // PersonId point to childtable unic id   
                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                        var $imgClose = getImageCloseTag(data, childTableDef, type);

                        $imgChild.click(data, function (event){
                            openChildTable(childTableDef, $imgChild, event.data, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            closeChildTable(childTableDef, $imgClose, event.data, !clientOnly);
                        });    

                        return getClickImg(data, childTableDef, $imgChild, $imgClose);
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
                    var childTableTitle = 'Den organisatoriska enheten "' + data.record.Name + '" har följande positioner';
                    var tooltip = "";
                    var imgFile = "";         
                    var type = 0;
                    var clientOnly = true;
                    
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

                        var childTableDef = posTableDef(childTableTitle, tablePath, data.record.Id, tableDef); // PersonId point to childtable unic id   

                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                        var $imgClose = getImageCloseTag(data, childTableDef, type);

                        $imgChild.click(data, function (event){
                            openChildTable(childTableDef, $imgChild, event.data, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            closeChildTable(childTableDef, $imgClose, event.data, !clientOnly);
                        });    

                        return getClickImg(data, childTableDef, $imgChild, $imgClose);
                    }
                    return null;
                }
            },
            ParentTreeNode_FK:{
                list: false,
                edit: true, 
                create: true,
                title: 'Överordna verksamhet',
                defaultValue: parentId,
                options: function(data) {
                    var url = saron.root.webapi + "listOrganizationUnit.php";
                    var field = "ParentTreeNode_FK";                    
                    var parentId = null;
                    var parameters = getOptionsUrlParameters(data, tableName, parentId, null, field);                    
                    return url + parameters;
                }                
            },
            Prefix: {
                width: '1%',
                title: 'Prefix',
                listClass: 'saron-number',
                list: true
            },
            Name: {
                width: '10%',
                title: 'Namn'
            },
            OrgPath: {
                title: "Sökväg",
                create: false,
                edit: false,
                list: false
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
                    var field = null;                    
                    var url = saron.root.webapi + 'listOrganizationUnitType.php';
                    var parameters = getOptionsUrlParameters(data, tableName, parentId, null, field);                    
                    return url + parameters;
                }
            },
            Updated:{
                title: 'Uppdaterad',
                width: '5%',
                create: false,
                edit: false,
                display: function (data){
                    return getUpdateInfo(data);
                }
            }
        },
        recordUpdated: function (event, data){
            var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

            if(tablePathRoot === saron.table.unittree.name)
                if(data.record.ParentTreeNode_FK !== parentId)
                    moveOrgUnit(data);

            alowedToUpdateOrDelete(event, data, tableDef);

            if(data.record.HasSubUnit !== '0' || data.record.HasPos !== '0')
                data.row.find('.jtable-delete-command-button').hide();                        
        },  
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
            var tr = data.row.closest("tr");
            var list = tr[0].classList;
            list.add(saron.table.unit.name + '_' + data.record.Id);
            
            if(data.record.HasSubUnit !== '0' || data.record.HasPos !== '0')
                data.row.find('.jtable-delete-command-button').hide();

            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            alowedToAddRecords(event, data, tableDef);
        },        
        loadingRecords: function(event, data) {
        },
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit){
                data.row[0].style.backgroundColor = "yellow";
                data.form.find('select[name=ParentTreeNode_FK]')[0].disabled=false;
                data.form.find('select[name=OrgUnitType_FK]')[0].disabled=true;                
            }
            else{
                data.form.find('select[name=OrgUnitType_FK]')[0].disabled=false;
                data.form.find('select[name=ParentTreeNode_FK]')[0].disabled=true;
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
    
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configUnitTableDef(tableDef);
    
    return tableDef;
}    



function configUnitTableDef(tableDef){

    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.unitlist.name 
            || tablePathRoot === saron.table.unittype.name 
            || tablePathRoot === saron.table.role.name){ 
        //tableDef.fields.ParentTreeNode_FK.list = true; 
        //tableDef.fields.OrgPath.list = true; NOT IMPLEMENTED YET
        tableDef.fields.SubUnitEnabled.list = false;
        tableDef.fields.Prefix.list = false;
        tableDef.actions.createAction = null;
        tableDef.actions.deleteAction = null;
        tableDef.actions.updateAction = null;
        tableDef.fields.Prefix.update = false;
    }    

    if(tablePathRoot === saron.table.unittype.name)
        tableDef.fields.PosEnabled.list = false;             

}


function moveOrgUnit(data){
    // clone data?
    var childTablePlaceholder_From = getChildTablePlaceHolderFromTag(data.row, data.record.AppCanvasPath);
    childTablePlaceholder_From.jtable("deleteRecord",{key: data.record.Id, clientOnly:true, animationsEnabled:true});

    var table_To;
    if(data.record.ParentTreeNode_FK !== null){
        var parentTableRow_To = $('tr.jtable-data-row.' + saron.table.unit.name + '_' + data.record.ParentTreeNode_FK);
        if(parentTableRow_To.length > 0){
            var parentTable_To = getParentTablePlaceHolderFromChild(parentTableRow_To, data.record.AppCanvasPath);
            var childOpen = parentTable_To.jtable('isChildRowOpen', parentTableRow_To);
            if(childOpen){
                var childRow_To = parentTable_To.jtable('getChildRow', parentTableRow_To);
                var tables_To = childRow_To.find("div.jtable-main-container");
                table_To = getChildTablePlaceHolderFromTag(tables_To, data.record.AppCanvasPath);
                table_To.jtable('reload');
            }
            else
                return;
            //update parent
        }
        else
            return;
    }
    else{
        table_To = getMainTablePlaceHolderFromTablePath(data.record.AppCanvasPath);
        var options = {'record': data.record, 'clientOnly': true, 'url': null, 'animationsEnabled': true };
        table_To.jtable('addRecord', options);                    
    }
}
