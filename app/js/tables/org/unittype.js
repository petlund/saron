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
    tablePlaceHolder.jtable(unitTypeTableDef(null, saron.table.unittype.name));
    var postData = getPostData(null, saron.table.unittype.name, null, saron.table.unittype.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});


function unitTypeTableDef(tableTitle, tablePath){
    var title = 'Organisatoriska enhetertyper'; 
    if(tableTitle !== null)
        title = tableTitle;
    
    
    return {
        appCanvasName: saron.table.unittype.name,
        showCloseButton: false,
        title: title,        
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
                list: function(data){
                    return includedIn (saron.table.unittype.name, data.record.AppCanvasPath);
                },
                display: function(data){
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" används för nedanstående organisatoriska enheter';                            
                    var tooltip = "Enhetstypen används inom följande organisatoriska enheter";
                    var imgFile = "unit.png";
                    var url = 'listOrganizationUnitType.php';
                    var type = 0;
                    var clientOnly = true;
                    var childTablePath = tablePath + "/" + saron.table.unit.name;
                    
                    if(data.record.UsedInUnit ===  "1"){                        
                        var childTableDef = unitTableDef(childTableTitle, childTablePath); // PersonId point to childtable unic id   
                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableDef, type);
                        var $imgClose = getImageCloseTag(data, childTableDef, type);

                        $imgChild.click(data, function (event){
                            event.data.record.ParentId = data.record.Id;
                            _clickActionOpen(childTableDef, $imgChild, event.data, url.data, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            _clickActionClose(childTableDef, $imgClose, event.data, url, clientOnly);
                        });    

                        return _getClickImg(data, childTableDef, $imgChild, $imgClose);
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
                list: function(data){
                    return includedIn (saron.table.unittype.name, data.record.AppCanvasPath);
                },
                display: function(data){
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" har följande roller';
                    var tooltip = "";
                    var imgFile = "";
                    var type = 0;
                    var clientOnly = true;
                    var childTablePath = tablePath + "/" + saron.table.role_unittype.name;

                    if(data.record.PosEnabled ===  POS_ENABLED){

                        if(data.record.HasPos === '0'){
                            imgFile = "pos.png";
                            tooltip = "Inga roller";
                        }
                        else{
                            imgFile = "haspos.png";
                            tooltip = "Enhetstypen har roller";
                        }
                        
                        var childTableDef = role_role_unitType_TableDef(childTableTitle, childTablePath);    
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
            var addButton = $(event.target).find('.jtable-toolbar-item-add-record');

            if(data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org'){ 
                addButton.show();
            }
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
}



