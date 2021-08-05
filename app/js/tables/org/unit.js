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
RECORDS, RECORD, OPTIONS
 */
    
"use strict";    

function unitTableDef(tableViewId, parentTablePath, parentId, childTableTitle){
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
            listAction:   '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?ParentId=' + parentId + '&TablePath=' + tablePath + "&ResultType=" + RECORDS,
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
            UnitId: {
                key: true,
                list: false
            },
            ParentTreeNode_FK:{
                list: includedIn(tableViewId, TABLE_NAME_UNITLIST),
                edit: true, 
                create: true,
                title: 'Överordna verksamhet',
                options: function(data) {
                    var optionTablePath = tablePath + "/" + OPTIONS;
                    var parameters = '?ParentId=' + parentId + '&TablePath=' + optionTablePath + "&ResultType=" + OPTIONS;
                    if(data.source !== 'list'){
                        data.clearCache();
                    } 
                    return '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php' + parameters;
                }                
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
                    if(data.record.SubUnitEnabled === SUBUNIT_ENABLED){
                        var $imgChild;
                        var title = "";
                        if(data.record.HasSubUnit === '0' || data.record.statusSubProposal === null  || data.record.statusSubVacant === null){
                            $imgChild = getImageTag(data, "child.png", "Underorganisation", TABLE_NAME_UNIT);
                        }
                        else{
                            if(data.record.statusSubProposal > 0 && data.record.statusSubVacant > 0){
                                title = '"Underorganisation med ' + data.record.statusSubProposal + ' förslag och ' + data.record.statusSubVacant + ' vakans(er)"';
                                $imgChild = getImageTag(data, "haschild_YR.png", title, TABLE_NAME_UNIT);
                            }
                            else if(data.record.statusSubProposal === "0" && data.record.statusSubVacant !== "0"){
                                title = '"Underorganisation med ' + data.record.statusSubVacant + ' vakans(er)"';
                                $imgChild = getImageTag(data, "haschild_R.png", title, TABLE_NAME_UNIT);
                            }
                            else if(data.record.statusSubProposal !== "0" && data.record.statusSubVacant === "0"){
                                title = '"Underorganisation med ' + data.record.statusSubProposal + ' förslag"';
                                $imgChild = getImageTag(data, "haschild_Y.png", title, TABLE_NAME_UNIT);
                            }
                            else{
                                title = '"Underorganisation"';
                                $imgChild = getImageTag(data, "haschild.png", title, TABLE_NAME_UNIT);
                            }
                        }
                        //var allOpenClasses = getChildOpenClassName(data, TABLE_NAME_UNIT) + getChildOpenClassName(data, TABLE_NAME_POS);
                        var allOpenClasses = getUnitOpenClassName(data.record.UnitId) + getChildOpenClassName(data, TABLE_NAME_POS);
                        var currentOpenClass = getUnitOpenClassName(data.record.UnitId);
                        //var currentOpenClass = getChildOpenClassName(data, TABLE_NAME_UNIT);

                        $imgChild.click(data, function (event){
                            var $tr = $imgChild.closest('tr');
                            $tr.removeClass(allOpenClasses);
                            $tr.addClass(currentOpenClass);

                            var childTableTitle = "Underenheter till: " + data.record.Name;
                            $(tableViewId).jtable('openChildTable', $tr, unitTableDef(tableViewId, tablePath, data.record.UnitId, childTableTitle), function(data){
                                data.childTable.jtable('load');
                                updateParentUnit(tableViewId, event.data);
                            });
                        });
                    var $imgClose = getImageCloseTag(data, TABLE_NAME_ROLE);

                    $imgClose.click(data, function(event) {
                        var $tr = $imgClose.closest('tr'); 
                        $tr.removeClass(allOpenClasses);
                        var $currentRow = $(tableViewId).jtable('getRowByKey', data.record.UnitId);
                        $(tableViewId).jtable('closeChildTable', $currentRow, function(data){  
                            updateParentUnit(tableViewId, event.data);
                        });
                    });     

                    var isChildRowOpen = $("." + currentOpenClass).length > 0;
                    if(isChildRowOpen)
                        return $imgClose;
                    else
                        return $imgChild;
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
                display: function(data){
                    childTableTitle = data.record.Name + " har följande positioner";
                    return getAggregatedPosIcon(tableViewId, tablePath, data.record.UnitId, childTableTitle, data);
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
                title: 'Typ av enhet',
                inputTitle: 'Typ av enhet (Kan inte ändras. Vill du ändra behöver du skapa en ny organisatorisk enhet).',
                width: '5%',
                options: function (data){
                    var optionTablePath = tablePath + "/" + OPTIONS;
                    var parameters = '?ParentId=' + parentId + '&TablePath=' + optionTablePath + "&ResultType=" + OPTIONS;

                    if(data.source !== 'list'){
                        data.clearCache();
                    } 
                    return '/' + SARON_URI + 'app/web-api/listOrganizationUnitType.php' + parameters;
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
            var UnitId = data.record.UnitId;
            var parentUnitId = data.record.ParentTreeNode_FK;
            
            if(parentUnitId > 0){
                table = $(".UnitId_" + parentUnitId).closest('div.jtable-child-table-container');
    
                if(table.length === 0) 
                    table = $(tableViewId);
    
                var url =  '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php?selection=single_node';
                var options = {record:{"UnitId": parentUnitId}, "clientOnly": false, "url":url};
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
            data.row.addClass("UnitId_" + data.record.UnitId); 
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
    var id = data.record.UnitId;
    
    var url =  '/' + SARON_URI + 'app/web-api/listOrganizationUnit.php';
    var options = {record:{"UnitId": id}, "clientOnly": false, "url":url};
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