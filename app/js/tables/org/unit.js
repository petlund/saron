/* global DATE_FORMAT,
saron, 
SUBUNIT_ENABLED, 
ORG, TABLE,
RECORD, OPTIONS,
POS_ENABLED
 */
    
"use strict";    

function unitTableDef(tableTitle, tablePath){
    var title = 'Organisatoriska enheter';
    if(tableTitle !== null)
        title = tableTitle;
    
    return {
        appCanvasName: saron.table.unit.name,
        showCloseButton: false,
        title: title,
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
                defaultValue: -1,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.unit.name
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.unit.name
            },
            SubUnitEnabled: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                list: function(data){
                    return includedIn (saron.table.unittree.name, data.record.AppCanvasPath);
                },
                display: function (data) {
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" har följande underenheter';
                    var tooltip = "";
                    var imgFile = "";
                    var type = 0;
                    var clientOnly = true;
                    var childTablePath = tablePath + "/" + saron.table.unit.name;

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

                        var childTableDef = unitTableDef(childTableTitle, childTablePath); // PersonId point to childtable unic id   
                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                        var $imgClose = getImageCloseTag(data, childTableDef, type);

                        $imgChild.click(data, function (event){
                            event.data.record.ParentId = data.record.Id;
                            _clickActionOpen(childTableDef, $imgChild, event.data, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            _clickActionClose(childTableDef, $imgClose, event.data, clientOnly);
                        });    

                        return _getClickImg(data, childTableDef, $imgChild, $imgClose);
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
                    var childTablePath = tablePath + "/" + saron.table.pos.name;
                    
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

                        var childTableDef = posTableDef(childTableTitle, childTablePath); // PersonId point to childtable unic id   

                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                        var $imgClose = getImageCloseTag(data, childTableDef, type);

                        $imgChild.click(data, function (event){
                            event.data.record.ParentId = data.record.Id;
                            _clickActionOpen(childTableDef, $imgChild, event.data, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            _clickActionClose(childTableDef, $imgClose, event.data, clientOnly);
                        });    

                        return _getClickImg(data, childTableDef, $imgChild, $imgClose);
                    }
                    return null;
                }
            },
            ParentTreeNode_FK:{
                list: false, //includedIn(mainTableViewId, saron.table.unitlist.nameId),
                edit: true, 
                create: function(data){
                    var appCanvasRoot = getRootElementFromTablePath(data.record.AppCanvasPath);
                    return !includedIn(appCanvasRoot, saron.table.unittree.name);
                },
                title: 'Överordna verksamhet',
                options: function(data) {
                    var url = saron.root.webapi + "listOrganizationUnit.php";
                    var field = "ParentTreeNode_FK";                    
                    var parameters = getOptionsUrlParameters(data, saron.table.unit.name, data.record.ParentId, data.record.AppCanvasPath, field);                    
                    return url + parameters;
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
            OrgPath: {
                title: "Sökväg",
                create: false,
                edit: false,
                list: function(data){
                    var appCanvasRoot = getRootElementFromTablePath(data.record.AppCanvasPath);
                    return includedIn(appCanvasRoot, saron.table.role.name + saron.table.unittype.name + saron.table.unitlist.name);
                }
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
                    var parameters = getOptionsUrlParameters(data, data.record.AppCanvasName, data.record.ParentId, data.record.AppCanvasPath, field);                    
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
            if(data.record.HasSubUnit !== '0' || data.record.HasPos !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            else
                data.row.find('.jtable-delete-command-button').show();
            
            if(data.record.parentNodeChange !== '0')
                $(saron.table.unittype.nameId).jtable('load');

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
            var addButton = $(event.target).find('.jtable-toolbar-item-add-record');

            if(includedIn(data.records[0].AppCanvasName, saron.table.unittree.nameId + saron.table.unitlist.nameId))
                if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org') 
                    addButton.show();
        },        
        loadingRecords: function(event, data) {
//            var addButton = $(event.target).find('.jtable-toolbar-item-add-record');
//
//            if(includedIn(data.record.AppCanvasName, saron.table.unittree.nameId + saron.table.unitlist.nameId))
//                addButton.show();
//            else
//                addButton.hide();
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
