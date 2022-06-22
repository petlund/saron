/* global DATE_FORMAT,
SUBUNIT_ENABLED, 
ORG, POS_ENABLED, 
saron, 
RECORD, OPTIONS
 */

"use strict";
const posListUri = 'app/web-api/listOrganizationPos.php';

$(document).ready(function () {
    var mainTableViewId = saron.table.pos.viewid;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(posTableDef(mainTableViewId, saron.table.pos.name, null, null));
    var postData = getPostData(null, mainTableViewId, null, saron.table.pos.name, saron.source.list, saron.responsetype.records, posListUri);
    tablePlaceHolder.jtable('load', postData);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});


function posTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    var title =  'Alla positioner';
    if(newTableTitle !== null)
        title = newTableTitle;
    
    const tableName = saron.table.pos.name;
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 

    return {
        showCloseButton: false,
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),
        title: title,
        paging: true, //Enable paging§§
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: getDefaultPosSorting(mainTableViewId), //Set default sorting        
        messages: {addNewRecord: 'Lägg till en ny position.'},
        actions: {
            listAction: '/' + saron.uri.saron + posListUri,
            createAction: '/' + saron.uri.saron + 'app/web-api/createOrganizationPos.php',
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateOrganizationPos.php',
            deleteAction: '/' + saron.uri.saron + 'app/web-api/deleteOrganizationPos.php'
        }, 
        fields: {
            Id: {
                key: true,
                list: false,
                create: false,
                edit: false
            },         
            ParentId:{
                defaultValue: parentId,
                type: 'hidden'
            },
            RoleType:{
                sorting: false,
                width: "1%",
                edit: false,
                create: false,
                display: function (data) {
                    var src;
                    if(data.record.RoleType === '-1'){
                        src = '"/' + saron.uri.saron + saron.uri.images + 'orgpos.png" title="Rollen finns på fler ställen"';
                    }
                    else{
                        switch (data.record.OrgPosStatus_FK){
                            case '1':
                                src = '"/' + saron.uri.saron + saron.uri.images + 'haspos.png" title="Avstämd"';
                                break;
                            case '2':
                                src = '"/' + saron.uri.saron + saron.uri.images + 'haspos_Y.png" title="Förslag"';
                                break;
                            case '4':
                                src = '"/' + saron.uri.saron + saron.uri.images + 'haspos_R.png" title="Vakant"';
                                break;
                            default:                            
                                src = '"/' + saron.uri.saron + saron.uri.images + 'pos.png" title="Tillsätts ej"';
                        }
                    }
                    var imgTag = _setImageClass(data, saron.table.role.name, src, -1);
                    var $imgRole = $(imgTag);
                    return $imgRole;
                }                
            },
            SortOrder: {
                list: !includedIn(mainTableViewId, saron.table.pos.viewid),
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
                list: includedIn(mainTableViewId, saron.table.pos.viewid),
                title: "Organisatorisk enhet",
                options: function(data){
                    var uri = 'app/web-api/listOrganizationUnit.php';
                    var field = null;
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, uri);
                    return '/' + saron.uri.saron + uri + parameters;
                }                
            },            
            OrgRole_FK: {
                width: '10%',
                title: 'Roll',
                options: function(data){
                    var uri = 'app/web-api/listOrganizationRole.php';      
                    var field = 'OrgRole_FK';
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
                }
            },
            OrgPosStatus_FK: {
                width: '5%',
                title: 'Status',
                defaultValue: '4',
                options: function(data){                    
                    var uri = 'app/web-api/listOrganizationPosStatus.php';
                    var field = null;
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
                }
            },
            Comment:{
                width: '10%',
                inputTitle: "Kort kommentar som ska vara knuten till uppdraget inte personen.",
                title: 'Kommentar',                
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
                    var uri = 'app/web-api/listPeople.php';
                    var field = "People_FK";
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
                }
            },
            OrgSuperPos_FK: {
                title: 'Organisationsroll',
                inputTitle: 'Resurstyp: Organisationsroll',
                create: true,
                edit: true,
                list: false,
                options: function(data){                    
                    var uri = 'app/web-api/listOrganizationPos.php';
                    var field = 'OrgSuperPos_FK';
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
                }
            },
            Function_FK: {
                title: 'Alternativt funktionsansvar',
                inputTitle: 'Resurstyp: Alternativt funktionsansvar',
                create: true,
                edit: true,
                list: false,
                options: function(data){                    
                    var uri = 'app/web-api/listOrganizationUnit.php';
                    var field = null;
                    var parameters = getOptionsUrlParameters(data, mainTableViewId, parentId, tablePath, field, uri);                    
                    return '/' + saron.uri.saron + uri + parameters;
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
//            if(saron.table.pos.viewid !== tableViewId)
//                updateParentUnit(tableViewId, data);            
        },
        recordUpdated: function(event, data){
//            if(saron.table.pos.viewid !== tableViewId)
//                updateParentUnit(tableViewId, data);            
        },
        recordDeleted: function(event, data){
//            if(saron.table.pos.viewid !== tableViewId)
//                updateParentUnit(tableViewId, data);            
        },
        rowInserted: function(event, data){
            data.row.addClass("Id_" + data.record.Id); 
            if (data.record.user_role !== saron.userrole.editor && data.record.user_role !== 'org' ){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            if(!includedIn(mainTableViewId, saron.table.unittree.viewid + saron.table.unitlist.viewid)){
                data.row.find('.jtable-delete-command-button').hide();                
            }
            addDialogDeleteListener(data);
        },        
        recordsLoaded: function(event, data) {
            var addButton = $(event.target).find('.jtable-toolbar-item-add-record');
            if(addButton === null)
                addButton = $(mainTableViewId).find('.jtable-toolbar-item-add-record');

            var showAddButton = (data.serverResponse.user_role === saron.userrole.editor || data.serverResponse.user_role === 'org') && data.records[0].TablePath !== tableName; 
            if(showAddButton) 
                addButton.show();
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
}


function getDefaultPosSorting(tableViewId){
    switch(tableViewId) {
        case saron.table.pos.viewid:
            return "OrgTree_FK, SortOrder";
        default:
            return "SortOrder";
    }
}