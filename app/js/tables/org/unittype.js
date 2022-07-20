    /* global DATE_FORMAT, 
 PERSON, HOME, PERSON_AND_HOME, OLD_HOME,  
 inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
ORG, TABLE, 
 POS_ENABLED, POS_DISABLED,
 SUBUNIT_ENABLED, SUBUNIT_DISABLED,
saron,
RECORD, OPTIONS
*/
"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.unittype.nameId);
    tablePlaceHolder.jtable(unitTypeTableDef(null, null, null, null));
    var postData = getPostData(null, saron.table.unittype.name, null, saron.table.unittype.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);

    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();    
});


function unitTypeTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.unittype.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);
    
    var tableDef = {
        parentId: parentId,
        tablePath: tablePath,
        tableName: tableName,
        parentTableDef: parentTableDef,
        showCloseButton: false,
        title: 'Organisatoriska enhetertyper',        
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny typ av organisatorisk enhet.'},
        actions: {
            listAction:   saron.root.webapi + 'listOrganizationUnitType.php',
            createAction: saron.root.webapi + 'createOrganizationUnitType.php',
            updateAction: saron.root.webapi + 'updateOrganizationUnitType.php',
            deleteAction: saron.root.webapi + 'deleteOrganizationUnitType.php'
        },
        fields:{
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
                defaultValue: saron.table.unittype.name
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.unittype.name
            },
            UsedInUnit: {
                title: 'Används',
                width: '3%',
                edit: false,
                sorting: false,
                create: false,
                list: true,
                display: function(data){
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" används för nedanstående organisatoriska enheter';                            
                    var tooltip = "Enhetstypen används inom följande organisatoriska enheter";
                    var imgFile = "unit.png";
                    var type = 0;
                    var clientOnly = true;
                    
                    if(data.record.UsedInUnit ===  "1"){                        
                        var childTableDef = unitTableDef(childTableTitle, tablePath, data.record.Id, childTableDef); // PersonId point to childtable unic id   
                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                        var $imgClose = getImageCloseTag(data, childTableDef, type);

                        $imgChild.click(data, function (event){
                            openChildTable(childTableDef, $imgChild, event.data, url.data, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            closeChildTable(childTableDef, $imgClose, event.data, url, clientOnly);
                        });    

                        return getClickImg(data, childTableDef, $imgChild, $imgClose);
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
                list: true,
                display: function(data){
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" har följande roller';
                    var tooltip = "";
                    var imgFile = "";
                    var type = 0;
                    var clientOnly = true;

                    if(data.record.PosEnabled ===  POS_ENABLED){

                        if(data.record.HasPos === '0'){
                            imgFile = "pos.png";
                            tooltip = "Inga roller";
                        }
                        else{
                            imgFile = "haspos.png";
                            tooltip = "Enhetstypen har roller";
                        }
                        
                        var childTableDef = role_role_unitType_TableDef(childTableTitle, tablePath, data.record.Id, childTableDef);    
                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                        var $imgClose = getImageCloseTag(data, childTableDef, type);

                        $imgChild.click(data, function (event){
                            openChildTable(childTableDef, $imgChild, event.data, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            closeChildTable(childTableDef, $imgClose, event.data, clientOnly);
                        });    

                        return getClickImg(data, childTableDef, $imgChild, $imgClose);
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
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(data.record.UsedInUnit > 0 || data.record.HasPos > 0)
                data.row.find('.jtable-delete-command-button').hide();

            addDialogDeleteListener(data);
                        
        },        
        rowUpdated: function(event, data){
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org'){
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
            alowedToAddRecords(event, data, tableDef);
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit)
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
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };    
    var title = "Alla roller";

    if(tableTitle !== null)
        title = tableTitle;

    tableDef.title = title;
    
    
    configUnitTypeTableDef(tableDef, parentTablePath);

    return tableDef;
}



function  configUnitTypeTableDef(parentTablePath){
    
}