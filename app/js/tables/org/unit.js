/* global DATE_FORMAT,
saron, 
SUBUNIT_ENABLED, 
ORG, TABLE,
RECORD, OPTIONS,
POS_ENABLED
 */
    
"use strict";    
const unitListUri = 'app/web-api/listOrganizationUnit.php';

function unitTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    var title = 'Organisatoriska enheter';
    if(newTableTitle !== null)
        title = newTableTitle;
    
    var tableName = saron.table.unit.name;

    const maxUnits = saron.table.unittree.name + '/' + saron.table.unit.name;
        if(tablePath !== maxUnits){
        if(tablePath === null)
            tablePath = tableName;
        else
            tablePath+= '/' + tableName; 
    }    
    return {
        showCloseButton: false,
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),
        title: title,
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: getDefaultUnitSorting(mainTableViewId), //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny organisatorisk enhet.'},
        actions: {
            listAction:   '/' + saron.uri.saron + unitListUri,
            createAction: '/' + saron.uri.saron + 'app/web-api/createOrganizationUnit.php?ParentId=' + parentId,
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateOrganizationUnit.php',
            deleteAction: '/' + saron.uri.saron + 'app/web-api/deleteOrganizationUnit.php'
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
            SubUnitEnabled: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                list: includedIn(mainTableViewId, saron.table.unittree.viewid),
                
                display: function (data) {
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" har följande underenheter';
                    var childTableName  = ""; // no adding of tablePath on requrssion 
                    var tooltip = "";
                    var imgFile = "";
                    var parentId = data.record.Id;
                    var url = unitListUri;
                    var type = 0;
                    var clientOnly = false;

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

                        var childTableDef = unitTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                        var $imgClose = getImageCloseTag(data, childTableName, type);

                        $imgChild.click(data, function (event){
                            _clickActionOpen(childTableDef, $imgChild, event, url, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            _clickActionClose(childTableDef, $imgClose, event, url, clientOnly);
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
                    var childTableName = saron.table.pos.name;
                    var childTableTitle = 'Den organisatoriska enheten "' + data.record.Name + '" har följande positioner';
                    var parentId = data.record.Id;
                    var tooltip = "";
                    var imgFile = "";         
                    var url = unitListUri;
                    var type = 0;
                    var clientOnly = false;
                    
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

                        var childTableDef = posTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   

                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                        var $imgClose = getImageCloseTag(data, childTableName, type);

                        $imgChild.click(data, function (event){
                            _clickActionOpen(childTableDef, $imgChild, event, url, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            _clickActionClose(childTableDef, $imgClose, event, url, clientOnly);
                        });    

                        return _getClickImg(data, childTableDef, $imgChild, $imgClose);
                    }
                    return null;
                }
            },
            ParentTreeNode_FK:{
                list: false, //includedIn(mainTableViewId, saron.table.unitlist.viewid),
                edit: true, 
                create: !includedIn(mainTableViewId, saron.table.unittree.viewid),
                title: 'Överordna verksamhet',
                options: function(data) {
//                    if(includedIn(mainTableViewId, saron.table.unitlist.viewid))
//                        data.record.ParentId=null; //using cache
                    var field = null;                    
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, unitListUri);                    
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
                list: includedIn(mainTableViewId, saron.table.role.viewid + saron.table.unittype.viewid + saron.table.unitlist.viewid)
            },
            SubUnits: {
                title: "Underenheter",
                create: false,
                edit: false,
                list: includedIn(mainTableViewId, saron.table.role.viewid + saron.table.unittype.viewid + saron.table.unitlist.viewid)
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
//                    if(includedIn(mainTableViewId, saron.table.unitlist.viewid))
//                        data.record.ParentId=null; //using cache
                    var field = null;                    
                    var uri = 'app/web-api/listOrganizationUnitType.php';
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, uri);                    
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
            if(data.record.HasSubUnit !== '0' || data.record.HasPos !== '0')
                data.row.find('.jtable-delete-command-button').hide();
            else
                data.row.find('.jtable-delete-command-button').show();
            
            if(data.record.parentNodeChange !== '0')
                $(mainTableViewId).jtable('load');

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

            if(includedIn(mainTableViewId, saron.table.unittree.viewid + saron.table.unitlist.viewid))
                if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org') 
                    addButton.show();
        },        
        loadingRecords: function(event, data) {
            var addButton = $(event.target).find('.jtable-toolbar-item-add-record');

            if(includedIn(mainTableViewId, saron.table.unittree.viewid + saron.table.unitlist.viewid))
                addButton.show();
            else
                addButton.hide();
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
        case saron.table.unittree.viewid:
            return "Prefix, Name";
        default:
            return "Name";
    }
}