/* global DATE_FORMAT, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, 
inputFormWidth, inputFormFieldWidth, 
NO_HOME, NEW_HOME_ID, 
TABLE, ORG, RECORD, OPTIONS,
saron,
homesListUri
 */

"use strict";

$(document).ready(function () {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    var tablePlaceHolder = $(saron.table.people.nameId);
    var table = peopleTableDef(null, null, null, null);
    table.paging = true;
    tablePlaceHolder.jtable(table);
    
    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();
    
    var options = getPostData(null, saron.table.people.name, null, saron.table.people.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
});

function peopleTableDef(tableTitle, parentTablePath, parentId, parentTableDef) {
    var tableName = saron.table.people.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title: 'Personuppgifter',
        showCloseButton: false,
        paging: false, //Enable paging
        pageList: 'minimal',
        sorting: true,
        multiSorting: true,
        defaultSorting: 'LongHomeName ASC, DateOfBirth ASC', //Set default sorting    //Set default sorting   
        messages: {addNewRecord: 'Ny person'},
        actions: {
            listAction:  saron.root.webapi + 'listPeople.php',     
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $dfd.done(function(data){
                        if(data.Record){
                            var options = {Id: data.Record.Id, AppCanvasName: tableName, AppCanvasPath: tableName, "Source":"create"};
                            $(saron.table.people.nameId).jtable('load', options);              
                        }
                    });
                    $.ajax({
                        url: saron.root.webapi + 'createPerson.php',    
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                var groupId = 20;
                                $("#groupId").val(groupId);
                                $("#searchString").val(data.Record.LastName);
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: saron.root.webapi + 'updatePerson.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (successData) {
                            $dfd.resolve(successData); //Mandatory
                            if(successData.Result === 'OK'){
                                updateRelatedRows();                            
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            }
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
            OldHomeId:{
                defaultValue: -1,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.people.name
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.people.name
            },
            Homes:{
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,            
                display: function (data) {
                    var childTableTitle = _setClassAndValueHeadline(data, 'LongHomeName', HOME, 'Hem', 'Hem för ', '');;
                    var tooltip = 'Adressuppgifter';
                    var imgFile = "home.png";
                    var clientOnly = false;
                    var type = 0;

                    var childTableDef = homeTableDef(childTableTitle, tablePath, data.record.HomeId, tableDef); // PersonId point to childtable unic id

                    if(data.record.HomeId > 0)
                        imgFile = "home.png";
                    else{
                        var $imgEmpty = getImageTag(data, "empty.png", tooltip, childTableDef, -1);
                        return $imgEmpty;
                    }

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
            },
            MemberShip:{
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,            
                display: function (data) {
                    var childTableTitle = _setClassAndValueHeadline(data, 'Name', PERSON, 'Medlemsuppgifter', 'Medlemsuppgifter för ', '');;
                    var tooltip = 'Ej medlem';
                    var imgFile = "notmember.png";
                    var clientOnly = false;
                    var type = 0;

                    if(data.record.MemberStateId === "7"){ //Not Member
                        tooltip = 'Vänkontakt';
                        imgFile = "friendship.png";                        
                    }
                    else if(data.record.MemberStateId === "2"){ //Not Member
                        tooltip = 'Medlem';
                        imgFile = "member.png";                        
                    }


                    var childTableDef = memberTableDef(childTableTitle, tablePath, data.record.Id, tableDef); // PersonId point to childtable unic id   
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
            },
            Baptism:{ 
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,            
                display: function (data) {
                    var childTableTitle = _setClassAndValueHeadline(data, 'Name', PERSON, 'Dopuppgifter', 'Dopuppgifter för ', '');;
                    var tooltip = 'Dopuppgifter saknas';
                    var imgFile = "not_baptist.png";
                    var clientOnly = false;
                    var type = 0;
                    
                    if(data.record.DateOfBaptism !== null || data.record.CongregationOfBaptismThis > 0){
                        imgFile = "baptist.png";
                        tooltip = 'Dopuppgifter saknas';
                    }
                    
                    var childTableDef = baptistTableDef(childTableTitle, tablePath, data.record.Id, tableDef); // PersonId point to childtable unic id   
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
            },
            Key:{ 
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,            
                display: function (data) {
                    var childTableTitle = _setClassAndValueHeadline(data, 'Name', PERSON, 'Nyckelinnehav', 'Nyckelinnehav för ', '');;
                    var tooltip = 'NyckelInnehav';
                    var imgFile = "no_key.png";

                    if(data.record.KeyToChurch > 1 || data.record.KeyToExp > 1)
                        imgFile = "key.png";
                    
                    var clientOnly = false;
                    var type = 0;

                    var childTableDef = keyTableDef(childTableTitle, tablePath, data.record.Id, tableDef); // PersonId point to childtable unic id   
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
            },            
            Engagements:{
                edit: false,
                create: false,
                width: '1%',
                sorting: false,
                display: function(data){
                    var childTableTitle = data.record.Name + ' har nedanstående uppdrag';
                    var tooltip = "Inga uppdrag";
                    var imgFile = "";
                    var clientOnly = true;
                    var type = 0;

                    if(data.record.Engagements ===  '0'){
                        tooltip = 'Inga uppdrag';
                        imgFile = "pos.png";
                    }
                    else{
                        tooltip = 'Har ' + data.record.Engagements + ' förtroendeuppdrag';
                        imgFile = "haspos.png";
                    }  
                    
                    var childTableDef = engagementsTableDef(childTableTitle, tablePath, data.record.Id, tableDef);  
                    childTableDef.parentTableDef = tableDef;
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
            },
            PDF: {
                create: false,
                edit: false,
                title: '',
                width: '1%',
                sorting: false,
                display: function (data) {
                    var tooltip = "Skapa personakt PDF";
                    var $imgPdf = getImageTag(data, "pdf.png", tooltip, null, -1);
                    $imgPdf.click(function () {                        
                        window.open(saron.root.reports + 'DossierReport.php?Id=' + data.record.Id, '_blank');
                    });                
                return $imgPdf;
                }
            },
            HomeId: {
                create: true,
                edit: true,
                list: false,
                title: 'Hem',
                inputTitle: 'Välj hem',
                optionsSorting: "text",
                options: function(data){
                    var url = saron.root.webapi + 'listHomes.php';
                    var field = 'HomeId';
                    var parameters = getOptionsUrlParameters(data, tableName, parentId, tablePath, field);                    
                    return url + parameters;
                }
            },
            LongHomeName: {
                create: false,
                edit: false,
                list: true,
                title: 'Hem'
            },
            DateOfMembershipEnd:{
                type: 'hidden'            
            },
            DateOfMembershipStart:{
                type: 'hidden'            
            },
            DateOfAnonymization:{
                type: 'hidden'
            },
            LastName: {
                title: 'Efternamn',
                inputTitle: 'Obligatorisk: Efternamn',
                list: false,
                edit: true,
                create: true,
                listClass: "LastName"
            },
            FirstName: {
                title: 'Förnamn',
                inputTitle: 'Obligatorisk: Förnamn',
                list: false,
                edit: true,
                create: true,
                listClass: "FirstName" 
            },
            Name: {
                title: 'Namn',
                width: '10%',
                list: true,
                create: false,
                edit: false,
                listClass: "Name" 
            },
            DateOfBirth: {
                title: 'Född',
                inputTitle: 'Obligatorisk: Född',
                width: '5%',
                displayFormat: DATE_FORMAT,
                type: 'date',
                listClass: "DateOfBirth" 
            },
            Gender: {
                title: 'Kön',
                width: '2%',
                options:{ 0 : '-', 1 : 'Man', 2 : 'Kvinna'}
            },
            Email: {
                width: '13%',
                title: 'Mail',
                display: function (data){
                    return _setMailClassAndValue(data, "Email", '', PERSON);
                }       
            },  
            Mobile: {
                title: 'Mobil',
                inputTitle: 'Mobil <BR> - Hemtelefonuppgifter matas in under "Adressuppgifter"',
                width: '7%',
                listClass: "Mobile" 
            },
            Phone: {
                title: 'Tel.',
                edit: false,
                width: '7%',
                create: false,
                listClass: "Phone" 
            },
            MemberStateName: {
                title: 'Status',
                edit: false,
                create: false,
                width: '4%',
                listClass: "MemberStateName" 
            },
            DateOfDeath: {
                title: 'Avliden',
                list: false,
                displayFormat: DATE_FORMAT,
                type: 'date',
                create: false,
                edit: true,
                listClass: "DateOfDeath" 
            },
            Comment: {
                title: 'Not',
                type: 'textarea',
                list: false,
                listClass: "DateOfDeath" 
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
        rowInserted: function(event, data){
            addAttributeForEasyUpdate(data);
            if (data.record.user_role !== saron.userrole.editor){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
        },        
        recordsLoaded: function(event, data) {
            alowedToAddRecords(event, data, tableDef);     
        },        
        formCreated: function (event, data){
            var headLine;

            if(data.formType === saron.formtype.edit){
                data.row[0].style.backgroundColor = "yellow";
                headLine = 'Uppdatera uppgifter för: ' + data.record.FirstName + ' ' + data.record.LastName;
            }
            else{
                headLine = 'Ange uppgifter för ny person';                
            }
            var dbox = document.getElementsByClassName('ui-dialog-title');            
            for(var i=0; i<dbox.length; i++){
                dbox[i].innerHTML=headLine;
                data.form.css('width',inputFormWidth);
            }

            data.form.css('width',inputFormWidth);
            data.form.find('input[name=FirstName]').css('width',inputFormFieldWidth);
            data.form.find('input[name=LastName]').css('width',inputFormFieldWidth);
            data.form.find('input[name=Email]').css('width',inputFormFieldWidth);
            data.form.find('input[name=Mobile]').css('width',inputFormFieldWidth);
            data.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);
      
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configPeopleTableDef(tableDef);
    
    return tableDef;
}


function configPeopleTableDef(tableDef){

    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.unittree.name){
    }
    if(tablePathRoot === saron.table.unitlist.name 
            || tablePathRoot === saron.table.unittype.name 
            || tablePathRoot === saron.table.role.name 
            || tablePathRoot === saron.table.statistics.name){ 
        tableDef.actions.updateAction  = null;
        tableDef.actions.createAction  = null;
    }    
}


