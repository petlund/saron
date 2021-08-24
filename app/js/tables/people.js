/* global DATE_FORMAT, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, 
inputFormWidth, inputFormFieldWidth, 
NO_HOME, NEW_HOME_ID, 
ORG, RECORD, OPTIONS,
saron
 */

"use strict";
$(document).ready(function () {
    localStorage.setItem(NEW_HOME_ID, -1);
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    
    var Id = -1; 
    if(urlParams.has('Id'))
        Id = urlParams.get('Id');
        
    $(saron.table.people.viewid).jtable(peopleTableDef(saron.table.people.viewid, null));
    var options = getPostData(saron.table.people.viewid, null, saron.table.people.name, null, saron.responsetype.records);
    $(saron.table.people.viewid).jtable('load', options);
    $(saron.table.people.viewid).find('.jtable-toolbar-item-add-record').hide();
});


function peopleTableDef(tableViewId, tableTitle) {
    var tableName = saron.table.homes.name;
    var title = 'Personuppgifter';
    if(tableTitle !== null)
        title = tableTitle; 
    
    return {
        showCloseButton: false,
        title: title,
        paging: true, //Enable paging
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'LongHomeName ASC, DateOfBirth ASC', //Set default sorting   
        messages: {addNewRecord: 'Ny person'},
        actions: {
            listAction:   '/' + saron.uri.saron + 'app/web-api/listPeople.php',
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + saron.uri.saron + 'app/web-api/createPerson.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result === 'OK'){
                                $dfd.resolve(data);
                                $("#groupId").val("2");
                                var pData = {searchString: "", groupId: 2, tableViewId: saron.table.people.viewid};

                                $(tableViewId).jtable('load', pData, function (){
                                    if(data.Record.HomeId > 0)
                                        _openHomeChildTable(tableViewId, data);                                    
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
                        url: '/' + saron.uri.saron + 'app/web-api/updatePerson.php?selection=person&nocache=' + Math.random(),
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result === 'OK'){
                                localStorage.setItem(NEW_HOME_ID, data.Record.HomeId);

                                $dfd.resolve(data); //Mandatory
                                var isChildRowOpen = false;
                                
                                var $selectedRow = $("[data-record-key=" + data.Record.Id + "]"); 
                                var moveToNewHome = (data.Record.HomeId > 0 && data.Record.OldHome_HomeId !== data.Record.HomeId);
                                if(!(data.Record.HomeId > 0 && data.Record.OldHome_HomeId === data.Record.HomeId)){
                                    isChildRowOpen = $(tableViewId).jtable('isChildRowOpen', $selectedRow),
                                    $(tableViewId).jtable('closeChildTable', $selectedRow, function(){
                                        _updateHomeFields(data);
                                        if(data.Record.HomeId > 0 && (isChildRowOpen || moveToNewHome))
                                            _openHomeChildTable(tableViewId, data);
                                    });
                                }
                                else{ // no move to another home
                                    _updateHomeFields(data);                                
                                }
                            }
                            else
                                $dfd.resolve(data);
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            }
        },       
        fields: {
            Homes:{
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,            
                display: function (data) {
                    var childTableTitle = 'Hem för ' + data.record.LongHomeName;
                    var childTableName = saron.table.homes.name;
                    var tooltip = 'title="Adressuppgifter"';
                    var imgFile = "home.png";
                    var listUri = 'app/web-api/listHomes.php';

                    var childTableDef = homeTableDef(tableViewId, childTableTitle);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, ORG, listUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, ORG, listUri);

                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);

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
                    var childTableTitle = 'Medlemsuppgifter för "' + data.record.Name + '"';
                    var childTableName = saron.table.member.name;
                    var tooltip = 'title="Medlemsuppgifter"';
                    var imgFile = "member.png";
                    var listUri = 'app/web-api/listPeople.php';

                    var childTableDef = memberTableDef(tableViewId, childTableTitle);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, ORG, listUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, ORG, listUri);

                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);

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
                    var childTableTitle = 'Dopuppgifter för "' + data.record.Name + '"';
                    var childTableName = saron.table.baptist.name;
                    var tooltip = 'title="Dopuppgifter"';
                    var imgFile = "baptist.png";
                    var listUri = 'app/web-api/listPeople.php';

                    var childTableDef = baptistTableDef(tableViewId, childTableTitle);
                    var $imgChild = openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, ORG, listUri);
                    var $imgClose = closeChildTable(data, tableViewId, childTableName, ORG, listUri);

                    return getChildNavIcon(data, childTableName, $imgChild, $imgClose);

                }
            },
            Id: {
                key: true,
                list: false
            },
            TablePath:{
                list: false,
                edit: false,
                create: false
            },
            HomeId: {
                create: true,
                edit: true,
                list: false,
                title: 'Välj hem',
                options: function(data){
                    if(data.source !== 'list')
                        data.clearCache();
                    return '/' + saron.uri.saron + 'app/web-api/listHomes.php?selection=options';
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
                    return _setClassAndValueAltNull(data, "LongHomeName", NO_HOME, PERSON_AND_HOME);
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
                create: false,
                edit: true,
                list: false,
                type: 'hidden',
                defaultValue: function(data){
                    return data.record.DateOfMembershipStart;
                }
            }, 
            DateOfMembershipStart_create: {
                create: true,
                edit: false,
                list: false,
                title: 'Medlemskap start',
                displayFormat: DATE_FORMAT,
                type: 'date'
            }, 
            MembershipNo: {
                list: false,
                edit: false,
                title: 'Medlemsnummer',
                display: function (data){
                    return _setClassAndValue(data, "MembershipNo", PERSON);
                },       
                options: function (data){
                    if(clearMembershipNoOptionCache){
                        data.clearCache();
                        clearMembershipNoOptionCache=false;
                    }
                    return '/' + saron.uri.saron + 'app/web-api/listPerson.php?Id=null' + '&selection=nextMembershipNo';
                }
            },
            MemberState: {
                title: 'Status',
                edit: false,
                create: false,
                width: '4%',
                display: function (data){
                    return _setClassAndValue(data, "MemberState", PERSON);
                },       
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
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit'){ 
                $(tableViewId).find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            var headLine;
            //$.datepicker.formatDate("yyyy-MM-dd");
            if(data.formType === 'edit'){
                if(data.record.HomeId === "0"){
                    data.record.HomeId = localStorage.getItem(NEW_HOME_ID);
                    $('#Edit-HomeId').val(data.record.HomeId).change();
                }                 
                data.row[0].style.backgroundColor = "yellow";
                headLine = 'Uppdatera uppgifter för: ' + data.record.FirstName + ' ' + data.record.LastName;
                //data.form.find('input[name=DateOfMembershipStart]').hide();
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
        recordsUpdated: function (event, data){
            var d = $(document).find("[aria-describedby='ui-id-1']");
            var n = d.length;
            d.addClass("saron.ui.dialog");
            d.css('width',inputFormFieldWidth);
            
        },
        formClosed: function (event, data){
            clearMembershipNoOptionCache=true;
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        }
    };
}

function filterPeople(viewId, reload){
    if(reload)
        $('#searchString').val('');

    $('#' + viewId).jtable('load', {
        searchString: $('#searchString').val(),
        groupId: $('#groupId').val(),
        tableview: viewId
    });
}



function _updateHomeFields(data){
    _updateFields(data.Record, "LongHomeName", HOME);                                                
    _updateFields(data.Record, "LongHomeName", PERSON);                                                
    _updateFields(data.Record, "Residents", HOME);                                                
    _updateFields(data.Record, "Letter", HOME);                                                
    _updateFields(data.Record, "Phone", HOME);                                                
    _updateFields(data.Record, "Name", PERSON);                                                
    _updateFields(data.Record, "DateOfBirth", PERSON);                                                
    _updateFields(data.Record, "DateOfMembershipEnd", PERSON);                                                
    _updateFields(data.Record, "MemberState", PERSON);                                                
    _updateFields(data.Record, "VisibleInCalendar", PERSON);                                                
    _updateFields(data.Record, "Comment", PERSON);                                                
    _updateFields(data.Record, "Mobile", PERSON);

    if(data.Record.HomeId !== data.Record.OldHome_HomeId && data.Record.OldHome_HomeId > 0){
        _updateFields(data.Record, "HomeId", OLD_HOME);                                                
        _updateFields(data.Record, "LongHomeName", OLD_HOME);                                                
        _updateFields(data.Record, "Residents", OLD_HOME);                                                
        _updateFields(data.Record, "Phone", OLD_HOME);            
    }
}



