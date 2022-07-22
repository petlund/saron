/* global DATE_FORMAT,
SUBUNIT_ENABLED, 
ORG, POS_ENABLED, 
saron, 
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.pos.nameId);
    var table = posTableDef(null, saron.table.pos.name, null, null);
    table.defaultSorting = "OrgTree_FK, SortOrder";
    tablePlaceHolder.jtable(table);

    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();

    var postData = getPostData(null, saron.table.pos.name, null, saron.table.pos.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', postData);

});


function posTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.pos.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);
    
    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        showCloseButton: false,
        title: 'Alla positioner',
        paging: true, //Enable paging§§
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
//        defaultSorting: "OrgRole_FK",        
        messages: {addNewRecord: 'Lägg till en ny position.'},
        actions: {
            listAction:   saron.root.webapi + 'listOrganizationPos.php',
            createAction: saron.root.webapi + 'createOrganizationPos.php',
            updateAction: saron.root.webapi + 'updateOrganizationPos.php',
            deleteAction: saron.root.webapi + 'deleteOrganizationPos.php'
        }, 
        fields: {
            Id: {
                key: true,
                list: false,
                create: false,
                edit: false
            },         
            ParentId:{
                defaultValue: -1,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.pos.name
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.pos.name
            },
            RoleType:{
                sorting: false,
                width: "1%",
                edit: false,
                create: false,
                display: function (data) {
                    var src;
                    if(data.record.RoleType === '-1'){
                        src = getImageTag(data, 'orgpos.png', "Rollen finns på fler ställen", saron.table.role.name, -1)
                    }
                    else{
                        switch (data.record.OrgPosStatus_FK){
                            case '1':
                                src = getImageTag(data, 'haspos.png', "Avstämd", saron.table.role.name, -1);
                                break;
                            case '2':
                                src = getImageTag(data, 'haspos_Y.png', "Förslag", saron.table.role.name, -1);
                                break;
                            case '4':
                                src = getImageTag(data, 'haspos_R.png', "Vakant", saron.table.role.name, -1);
                                break;
                            default:                            
                                src = getImageTag(data, 'pos.png', "Tillsätts ej", saron.table.role.name, -1);
                        }
                    }
                    var $imgRole = $(src);
                    return $imgRole;
                }                
            },
            SortOrder: {
                list: false,
                create: false,
                width: '4%',
                title: 'Sort',
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "SortOrder", 0);
                }, 
                options: {0:'0',1:'1',2:'2',3:'3',4:'4',5:'5',6:'6',7:'7',8:'8',9:'9'}
                
            },
            OrgTree_FK:{                
                create: false,
                edit: false,
                list: true,
                title: "Organisatorisk enhet",
                optionsSorting: 'text',
                options: function(data){
                    var url = saron.root.webapi + 'listOrganizationUnit.php';
                    var field = "OrgTree_FK";
                    var parameters = getOptionsUrlParameters(data, saron.table.pos.name,  parentId, tableDef.tablePath, field);
                    return url + parameters;
                }                
            },            
            OrgRole_FK: {
                width: '10%',
                title: 'Roll',
                options: function(data){
                    var url = saron.root.webapi + 'listOrganizationRole.php';      
                    var field = 'OrgRole_FK';
                    var parameters = getOptionsUrlParameters(data, saron.table.pos.name, parentId, tableDef.tablePath, field);                    
                    return url + parameters;
                }
            },
            OrgPosStatus_FK: {
                width: '5%',
                title: 'Status',
                defaultValue: '4',
                options: function(data){                    
                    var url = saron.root.webapi + 'listOrganizationPosStatus.php';
                    var field = "OrgPosStatus_FK";
                    var parameters = getOptionsUrlParameters(data, saron.table.pos.name,  parentId, tableDef.tablePath, field);                    
                    return url + parameters;
                }
            },
            Comment:{
                width: '10%',
                inputTitle: "Kort kommentar som ska vara knuten till uppdraget inte personen.",
                title: 'Kommentar'                
            },           
            ResourceType: {
                title: 'Resurstyp',
                create: true,
                edit: true,
                list: false,
                options: function(data){                    
                    return {'1':'Ansvarig person','2':'Organisationsroll', '3':'Alternativt funktion' };
                }
            },
            People_FK: {
                title: 'Ansvarig person',
                inputTitle: 'Resurstyp: Ansvarig person',
                create: true,
                edit: true,
                list: false,
                options: function(data){
                    var url = saron.root.webapi + 'listPeople.php';
                    var field = "People_FK";
                    var parameters = getOptionsUrlParameters(data, saron.table.pos.name,  parentId, tableDef.tablePath, field);                    
                    return url + parameters;
                }
            },
            OrgSuperPos_FK: {
                title: 'Organisationsroll',
                inputTitle: 'Resurstyp: Organisationsroll',
                create: true,
                edit: true,
                list: false,
                options: function(data){                    
                    var url = saron.root.webapi + 'listOrganizationPos.php';
                    var field = 'OrgSuperPos_FK';
                    var parameters = getOptionsUrlParameters(data, saron.table.pos.name,  parentId, tableDef.tablePath, field);                    
                    return url + parameters;
                }
            },
            Function_FK: {
                title: 'Alternativt funktionsansvar',
                inputTitle: 'Resurstyp: Alternativt funktionsansvar',
                create: true,
                edit: true,
                list: false,
                options: function(data){                    
                    var url = saron.root.webapi + 'listOrganizationUnit.php';
                    var field = null;
                    var parameters = getOptionsUrlParameters(data, saron.table.pos.name,  parentId, tableDef.tablePath, field);                    
                    return url + parameters;
                }
            },
            Responsible: {
                create: false,
                edit: false,
                width: '15%',
                title: 'Förslag',
                list: true,
            },
            MemberState: {
                title: 'Medlemsstatus',
                edit: false,
                create: false,
                width: '5%'                
            },
            pCur_Mobile: {
                title: 'Mobil',
                edit: false,
                create: false,
                width: '10%'               
            },
            pCur_Email: {
                title: 'Mail',
                edit: false,
                create: false,
                width: '10%'                
            },
            PrevResponsible: {
                width: '10%',
                edit: false,
                create: false,
                title: 'Senast beslutad'
            },
            UpdaterName:{
                edit: false,
                create: false, 
                title: 'Uppdaterare',
                width: '5%'
            },
            LatestUpdated: {
                edit: false,
                create: false, 
                title: 'Uppdaterad',
                type: 'date',
                displayFormat: DATE_FORMAT,
                width: '5%'
            }
        },
        recordAdded: function(event, data){
        },
        recordUpdated: function(event, data){
        },
        recordDeleted: function(event, data){
        },
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            alowedToAddRecords(event, data, tableDef);
        },        
        formCreated: function (event, data){
            if(data.formType === saron.formtype.edit){
                data.form.find('select[name=OrgRole_FK]')[0].disabled=true;
                data.row[0].style.backgroundColor = "yellow";
            }
            data.form.css('width','600px');
            
            if(data.record !== undefined)
                posFormAuto(data, data.record.ResourceType);
            else
                posFormAuto(data, 1);
            
            data.form.find('select[name=ResourceType]').change(function () {posFormAuto(data, this.value)});
            
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };

    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configPosTableDef(tableDef);
   
    return tableDef;    
 }


function configPosTableDef(tableDef){

    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.pos.name){ 
        tableDef.fields.SortOrder.list = false;
        tableDef.actions.createAction = null;
    } 
    else{
        tableDef.defaultSorting = 'SortOrder';        
        tableDef.fields.OrgTree_FK.list = false;
    }
}

