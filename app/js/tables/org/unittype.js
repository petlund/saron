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
const unitTypeListUri = 'app/web-api/listOrganizationUnitType.php';

$(document).ready(function () {
    var mainTableViewId = saron.table.unittype.viewid;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(unitTypeTableDef(mainTableViewId, null, null, null));
    var postData = getPostData(null, mainTableViewId, null, saron.table.unittype.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});



function unitTypeTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    var title = 'Organisatoriska enhetertyper'; 
    if(newTableTitle !== null)
        title = newTableTitle;
    
    const tableName = saron.table.unittype.name;
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
    
    return {
        showCloseButton: false,
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),
        title: title,        
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Name', //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny typ av organisatorisk enhet.'},
        actions: {
            listAction:   '/' + saron.uri.saron + unitTypeListUri,
            createAction: '/' + saron.uri.saron + 'app/web-api/createOrganizationUnitType.php',
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateOrganizationUnitType.php',
            deleteAction: '/' + saron.uri.saron + 'app/web-api/deleteOrganizationUnitType.php'
        },
        fields:{
            TablePath:{
                list: false,
                edit: false,
                create: false
            },
            Id: {
                key: true,
                list: false
            },
            ParentId:{
                defaultValue: parentId,
                type: 'hidden'
            },
            UsedInUnit: {
                title: 'Används',
                width: '3%',
                edit: false,
                sorting: false,
                create: false,
                list: includedIn(mainTableViewId, saron.table.unittype.viewid),
                display: function(data){
                    var childTableName = saron.table.unit.name;
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" används för nedanstående organisatoriska enheter';                            
                    var tooltip = "Enhetstypen används inom följande organisatoriska enheter";
                    var imgFile = "unit.png";
                    var url = 'app/web-api/listOrganizationUnitType.php';
                    var parentId = data.record.Id;
                    var type = 0;
                    var clientOnly = true;
                    
                    if(data.record.UsedInUnit ===  "1"){                        
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
            HasPos:{
                width: '3%',
                title: 'Roller',
                create: false,
                sorting: false,
                edit: false,   
                list: includedIn(mainTableViewId, saron.table.unittype.viewid),
                display: function(data){
                    var childTableName = saron.table.role.name;
                    var childTableTitle = 'Enhetstypen "' + data.record.Name + '" har följande roller';
                    var tooltip = "";
                    var imgFile = "";
                    var parentId = data.record.Id;
                    var url = 'app/web-api/listOrganizationUnitType.php';
                    var parentId = data.record.Id;
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
                        
                        var childTableDef = role_role_unitType_TableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
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



