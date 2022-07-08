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

    var mainTableViewId = saron.table.people.nameId;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(peopleTableDef(mainTableViewId, null, null, null));
    var options = getPostData(null, saron.table.people.name, null, saron.table.people.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});

function peopleTableDef(mainTableViewId, tablePath, newTableTitle, parentId) {
    var title = 'Personuppgifter2';
    if(newTableTitle !== null)
        title = newTableTitle;
    
    var tableName = saron.table.people.name;
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 

    return {
        title: function(data){
            return title;
        },
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),
        showCloseButton: false,
        paging: mainTableViewId.includes(saron.table.people.nameId), //Enable paging
        pageList: 'minimal',
        sorting: true,
        multiSorting: true,
        defaultSorting: 'LongHomeName ASC, DateOfBirth ASC', //Set default sorting   
        messages: {addNewRecord: 'Ny person'},
        actions: {
            listAction:  saron.root.webapi + 'listPeople.php',
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: saron.root.webapi + 'createPerson.php',    
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result === 'OK'){
                                $dfd.resolve(data);
                                var groupId = 12;
                                $("#groupId").val(groupId);
                                $("#searchString").val(data.Record.LastName);
                                var options = {searchString: data.Record.LastName, groupId:groupId, TableView: getTableView(saron.table.people.nameId), AppCanvasName: tableName};

                                $(mainTableViewId).jtable('load', options, function (){
//                                    if(data.Record.HomeId > 0)
//                                        _openHomeChildTable(tableViewId, tablePath, data);                                    
                                });
                            }
                            else
                                $dfd.resolve(data);
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
                            if(successData.Result === 'OK'){
                                $dfd.resolve(successData); //Mandatory
                                var data = {record: successData.Record};                                
                                                                
                                var $selectedRow = $("[data-record-key=" + data.record.Id + "]"); 
                                var moveToNewHome = (data.record.HomeId > 0 && data.record.OldHome_HomeId !== data.record.HomeId);
                                var moveToNoHome = (data.record.HomeId === null);
                                var move = (moveToNewHome || moveToNoHome);
                                var isChildRowOpen = $(mainTableViewId).jtable('isChildRowOpen', $selectedRow);
                                var tablePath = saron.table.people.name + '/' + saron.table.homes.name;
                                var parentId = data.record.HomeId;
                                
                                var childTableTitle = _setClassAndValueHeadline(data, 'LongHomeName', HOME, 'Hem', 'Hem för ', '');;
                                
                                if(move && isChildRowOpen){
                                    var options = getPostData(null, mainTableViewId, parentId, tablePath, saron.source.list, saron.responsetype.records);
                                    var clientOnly = true;
                                    var url = saron.root.webapi + 'listPeople.php';
                                    var childTableDef = homeTableDef(mainTableViewId, tablePath, childTableTitle, parentId);

                                    $(mainTableViewId).jtable('closeChildTable', $selectedRow, function(){

                                        var tablePathOpenChild = false;                                
                                        _updateAfterClickAction(mainTableViewId, data, tablePathOpenChild, url, clientOnly);            

                                        if(parentId > 0){
                                            $(mainTableViewId).jtable('openChildTable', $selectedRow, childTableDef, function(callBackData){
                                                callBackData.childTable.jtable('load', options, function(){
                                                    var tablePathOpenChild = _getClassNameOpenChild(data, tablePath);                                
                                                    _updateAfterClickAction(mainTableViewId, data, tablePathOpenChild, url, clientOnly);                                                   
                                                });
                                            });
                                        }
                                    });
                                }
                            }
                            else
                                $dfd.resolve(successData);
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
                defaultValue: parentId,
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
                    var childTableName = saron.table.homes.name;
                    var tooltip = 'Adressuppgifter';
                    var imgFile = "home.png";
                    var clientOnly = false;
                    var parentId = data.record.HomeId;
                    var url = saron.root.webapi + 'listPeople.php';
                    var type = 0;

                    if(parentId > 0)
                        imgFile = "home.png";
                    else{
                        var $imgEmpty = getImageTag(data, "empty.png", tooltip, childTableName, -1);
                        return $imgEmpty;
                    }
                    var childTableDef = homeTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event.data, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event.data, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose);
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
                    var childTableName = saron.table.member.name;
                    var tooltip = 'Medlemsuppgifter';
                    var imgFile = "member.png";
                    var clientOnly = true;
                    var parentId = data.record.Id;
                    var url = saron.root.webapi + 'listPeople.php';
                    var type = 0;

                    var childTableDef = memberTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event.data, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event.data, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose);
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
                    var childTableName = saron.table.baptist.name;
                    var tooltip = 'Dopuppgifter';
                    var imgFile = "baptist.png";
                    var clientOnly = true;
                    var parentId = data.record.Id;
                    var url = saron.root.webapi + 'listPeople.php';
                    var type = 0;

                    var childTableDef = baptistTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event.data, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event.data, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose);

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
                    var childTableName = saron.table.keys.name;
                    var tooltip = 'NyckelInnehav';
                    var imgFile = "no_key.png";
                    if(data.record.KeyToChurch > 1 || data.record.KeyToExp > 1)
                        imgFile = "key.png";
                    
                    var clientOnly = false;
                    var parentId = data.record.Id;
                    var url = saron.root.webapi + 'listPeople.php';
                    var type = 0;

                    var childTableDef = keyTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event.data, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event.data, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose); 
                }
            },            
            Engagements:{
                edit: false,
                create: false,
                width: '1%',
                sorting: false,
                display: function(data){
                    var childTableName = saron.table.engagements.name;
                    var childTableTitle = data.record.Name + '" har nedanstående uppdrag';
                    var tooltip = "";
                    var imgFile = "";

                    if(data.record.Engagement ===  '0'){
                        var $imgEmpty = getImageTag(data, "empty.png", tooltip, childTableName, -1);
                        return $imgEmpty;
                    }
                    else{
                        tooltip = 'Har ' + data.record.Engagement + ' förtroendeuppdrag';
                        imgFile = "haspos.png";
                    }                    

                    var clientOnly = true;
                    var parentId = data.record.Id;
                    var url = null;
                    var type = 0;

                    var childTableDef = engagementTableDef(mainTableViewId, tablePath, childTableTitle, parentId); // PersonId point to childtable unic id   
                    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
                    var $imgClose = getImageCloseTag(data, childTableName, type);
                        
                    $imgChild.click(data, function (event){
                        _clickActionOpen(childTableDef, $imgChild, event.data, url, clientOnly);
                    });

                    $imgClose.click(data, function (event){
                        _clickActionClose(childTableDef, $imgClose, event.data, url, clientOnly);
                    });    

                    return _getClickImg(data, childTableDef, $imgChild, $imgClose);
                }
            },
            HomeId: {
                create: true,
                edit: true,
                list: false,
                title: 'Välj hem',
                options: function(data){
                    var url = saron.root.webapi + 'listHomes.php';
                    var field = null;
                    var parameters = getOptionsUrlParameters(data, saron.table.people.name, parentId, tablePath, field);                    
                    return url + parameters;
                }
            },
            OldHomeId: { // for requests
                list: true,
                create: false,
                edit: true,
                type: 'hidden',
                defaultValue: function (data){
                    if(data.record.HomeId > 0 || data.record.HomeId < 0)
                       return data.record.HomeId;
                   
                    return "0";
                },
                display: function(data){
                    return "OldHomeId: " + data.record.OldHomeId + " HomeId: " + data.record.HomeId;
                }
            },
            LongHomeName: {
                create: false,
                edit: false,
                list: true,
                title: 'Hem',
                display: function (data){
                    data.record.OldHomeId = data.record.HomeId;
                    return _setClassAndValueAltNull(data, "LongHomeName", NO_HOME, HOME);
                }
            },
            LastName: {
                title: 'Efternamn',
                list: false,
                edit: true,
                create: true
            },
            FirstName: {
                title: 'Förnamn',
                list: false,
                edit: true,
                create: true 
            },
            Name: {
                title: 'Namn',
                width: '10%',
                list: true,
                create: false,
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }                 
            },
            DateOfBirth: {
                title: 'Född',
                width: '5%',
                displayFormat: DATE_FORMAT,
                type: 'date',
                display: function (data){
                    return _setClassAndValue(data, "DateOfBirth", PERSON);
                }       
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
                display: function (data){
                    return _setClassAndValue(data, "Mobile", PERSON);
                }       
            },
            Phone: {
                title: 'Tel.',
                edit: false,
                width: '7%',
                create: false,
                display: function (data){
                    return _setClassAndValue(data, "Phone", HOME);
                }                  
            },
            DateOfMembershipStart: {
                create: true,
                edit: true,
                list: false,
                type: 'date',
                displayFormat: DATE_FORMAT,
                title: 'Medlemskap start'
            }, 
            MembershipNo: {
                list: false, 
                edit: false,
                create: true, 
                title: 'Medlemsnummer',
                display: function (data){
                    return _setClassAndValue(data, "MembershipNo", PERSON);
                },       
                options: function (data){
                    var url = saron.root.webapi + 'listPeople.php';
                    var field = "MembershipNo";
                    var parameters = getOptionsUrlParameters(data, saron.table.people.name, parentId, tablePath, field, url);
                    return url + parameters;
                }
            },
            MemberState: {
                title: 'Status',
                edit: false,
                create: false,
                width: '4%',
                display: function (data){
                    return _setClassAndValue(data, "MemberState", PERSON);
                }       
            },
            VisibleInCalendar: {
                edit: false,
                title: 'Kalender',
                inputTitle: 'Synlig i adresskalendern',
                width: '4%',             
                display: function (data){
                    return _setClassAndValue(data, "VisibleInCalendar", PERSON);
                },       
                options:_visibilityOptions()
            },
            DateOfMembershipEnd: {
                list: false,
                edit: true,
                type: 'hidden',
                create: false,
                defaultValue: function (data){
                    return data.record.DateOfMembershipEnd;
                }       
            },
            DateOfDeath: {
                title: 'Avliden',
                list: false,
                displayFormat: DATE_FORMAT,
                type: 'date',
                create: false,
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "DateOfDeath", PERSON);
                }       
            },
            Comment: {
                title: 'Not',
                type: 'textarea',
                list: false,
                display: function (data){
                    return _setClassAndValue(data, "Comment", PERSON);
                }       
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== saron.userrole.editor){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
        },        
        recordsLoaded: function(event, data) {
            var addButton = $(event.target).find('.jtable-toolbar-item-add-record');

            if(data.serverResponse.user_role === saron.userrole.editor){
                if(mainTableViewId !== saron.table.statistics.nameId){
                    addButton.show();
                }
            }
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
            }

            data.form.css('width',inputFormWidth);
            data.form.find('input[name=FirstName]').css('width',inputFormFieldWidth);
            data.form.find('input[name=LastName]').css('width',inputFormFieldWidth);
            data.form.find('input[name=Email]').css('width',inputFormFieldWidth);
            data.form.find('input[name=Mobile]').css('width',inputFormFieldWidth);
            data.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);
      
        },
        recordUpdated: function (event, data){
            _updatePeopleFields(data);
            
        },
        formClosed: function (event, data){
            if(data.formType === saron.formtype.edit)
                data.row[0].style.backgroundColor = '';
        }
    };
}


function _updatePeopleFields(data){
//    _updateFields(data, "LongHomeName", HOME);                                                
    _updateFields(data, "LongHomeName", PERSON);                                                
    _updateFields(data, "Residents", HOME);                                                
    _updateFields(data, "Residents", OLD_HOME);                                                
//    _updateFields(data, "Letter", HOME);                                                
//    _updateFields(data, "Phone", HOME);                                                
    _updateFields(data, "Name", PERSON);                                                
    _updateFields(data, "DateOfBirth", PERSON);                                                
    _updateFields(data, "DateOfMembershipEnd", PERSON);                                                
    _updateFields(data, "MemberState", PERSON);                                                
    _updateFields(data, "VisibleInCalendar", PERSON);                                                
    _updateFields(data, "Comment", PERSON);                                                
    _updateFields(data, "Mobile", PERSON);
}



