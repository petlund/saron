/* global DATE_FORMAT, J_TABLE_ID, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */

"use strict";
$(document).ready(function () {
    localStorage.setItem(NEW_HOME_ID, -1);
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    
    var Id = -1; 
    if(urlParams.has('Id'))
        Id = urlParams.get('Id');
        
    $(J_TABLE_ID).jtable(peopleTableDef(J_TABLE_ID, Id));
    $(J_TABLE_ID).jtable('load');
    $(J_TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
});


function peopleTableDef(placeHolder, Id) {
    
    return {
        title: 'Personuppgifter',
        paging: true, //Enable paging
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'LongHomeName ASC, DateOfBirth ASC', //Set default sorting   
        messages: {addNewRecord: 'Ny person'},
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php?tableview=people&Id=' + Id,
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/createPerson.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result === 'OK'){
                                $dfd.resolve(data);
                                $("#groupId").val("2");
                                var pData = {searchString: "", groupId: 2, tableview: "people"};

                                $(placeHolder).jtable('load', pData, function (){
                                    if(data.Record.HomeId > 0)
                                        _openHomeChildTable(placeHolder, data);                                    
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
                        url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=person&nocache=' + Math.random(),
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
                                    isChildRowOpen = $(placeHolder).jtable('isChildRowOpen', $selectedRow),
                                    $(placeHolder).jtable('closeChildTable', $selectedRow, function(){
                                        _updateHomeFields(data);
                                        if(data.Record.HomeId > 0 && (isChildRowOpen || moveToNewHome))
                                            _openHomeChildTable(placeHolder, data);
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
            HomeDetails: childTableHome(placeHolder),
            MemberShip: memberTableDef(placeHolder), 
            Baptism: childTableBaptism(placeHolder), 
            Id: {
                key: true,
                list: false
            },
            HomeId: {
                create: true,
                edit: true,
                list: false,
                title: 'Välj hem',
                options: function(data){
                    if(data.source !== 'list')
                        data.clearCache();
                    return '/' + SARON_URI + 'app/web-api/listHomes.php?selection=options';
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
                    return _setClassAndValueAltNull(data.record, "LongHomeName", NO_HOME, PERSON_AND_HOME);
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
                    return _setClassAndValue(data.record, "Name", PERSON);
                }                 
            },
            DateOfBirth: {
                title: 'Född',
                width: '5%',
                displayFormat: DATE_FORMAT,
                type: 'date',
                display: function (data){
                    return _setClassAndValue(data.record, "DateOfBirth", PERSON);
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
                    return _setMailClassAndValue(data.record, "Email", '', PERSON);
                }       
            },  
            Mobile: {
                title: 'Mobil',
                inputTitle: 'Mobil <BR> - Hemtelefonuppgifter matas in under "Adressuppgifter"',
                width: '7%',
                display: function (data){
                    return _setClassAndValue(data.record, "Mobile", PERSON);
                }       
            },
            Phone: {
                title: 'Tel.',
                edit: false,
                width: '7%',
                create: false,
                display: function (data){
                    return _setClassAndValue(data.record, "Phone", HOME);
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
                    return _setClassAndValue(data.record, "MembershipNo", PERSON);
                },       
                options: function (data){
                    if(clearMembershipNoOptionCache){
                        data.clearCache();
                        clearMembershipNoOptionCache=false;
                    }
                    return '/' + SARON_URI + 'app/web-api/listPerson.php?Id=null' + '&selection=nextMembershipNo';
                }
            },
            MemberState: {
                title: 'Status',
                edit: false,
                create: false,
                width: '4%',
                display: function (data){
                    return _setClassAndValue(data.record, "MemberState", PERSON);
                },       
            },
            VisibleInCalendar: {
                edit: false,
                title: 'Kalender',
                inputTitle: 'Synlig i adresskalendern',
                width: '4%',             
                display: function (data){
                    return _setClassAndValue(data.record, "VisibleInCalendar", PERSON);
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
                    return _setClassAndValue(data.record, "DateOfDeath", PERSON);
                }       
            },
            Comment: {
                title: 'Not',
                type: 'textarea',
                list: false,
                display: function (data){
                    return _setClassAndValue(data.record, "Comment", PERSON);
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
                $(placeHolder).find('.jtable-toolbar-item-add-record').show();
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


// *********************************************************
// SUBTABLE HOME
// *********************************************************

function childTableHome(placeHolder) {
    return {
        title: '',
        width: '1%',
        sorting: false,
        edit: false,
        create: false,
        delete: false,
        display: function (homeData) {
            var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'home.png" title="Adressuppgifter"';
            var imgTag = _setImageClass(homeData.record, "Home", src, HOME);
            var $imgHome = $(imgTag);

            $imgHome.click(homeData, function (event){
                var newHomeId = -1;
                if(event.data.record.HomeId === "0") // zero is replaced by new index from backend
                    newHomeId = localStorage.getItem(NEW_HOME_ID);
                else
                    newHomeId = event.data.record.HomeId;
    
                var $tr = $('.Name_P' + event.data.record.Id).closest('tr');
                $(placeHolder).jtable('openChildTable', $tr, homeChildTableDef(placeHolder, event.data, newHomeId), function(data){
                    data.childTable.jtable('load');
                });
            });
            if(homeData.record.HomeId === null)
                return null;
            
            if(homeData.record.HomeId >= 0){
                return $imgHome;
            }
            else{
                return null; 
            }
        }
    };
}


function homeChildTableDef(placeHolder, homeData, newHomeId){
    return {
        title: _setClassAndValue(homeData.record, "LongHomeName", HOME),                            
        showCloseButton: false,
        actions: {
            listAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/listHome.php?HomeId=' + newHomeId,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result === 'OK'){

                                $dfd.resolve(data);
                                for(var field in data.Record){
                                    _updateFields(data.Record, field, HOME);
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
            },
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updateHome.php?HomeId=' + newHomeId,
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result === 'OK'){

                                $dfd.resolve(data);
                                for(var field in data.Record){
                                    _updateFields(data.Record, field, HOME);
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
        fields: homeFields(placeHolder, homeData),
        rowInserted: function(event, homeData){
            if (homeData.record.user_role !== 'edit'){
                homeData.row.find('.jtable-edit-command-button').hide();
                homeData.row.find('.jtable-delete-command-button').hide();
            }
        },        
        formCreated: function (event, homeData){
            homeData.row[0].style.backgroundColor = "yellow";
            homeData.form.css('width',inputFormWidth);
            homeData.form.find('input[name=FamilyName]').css('width',inputFormFieldWidth);
            homeData.form.find('input[name=Phone]').css('width',inputFormFieldWidth);
            homeData.form.find('input[name=Co]').css('width',inputFormFieldWidth);
            homeData.form.find('input[name=Address]').css('width',inputFormFieldWidth);
            homeData.form.find('input[name=City]').css('width',inputFormFieldWidth);
            homeData.form.find('input[name=Country]').css('width',inputFormFieldWidth);

            var dbox = document.getElementsByClassName('ui-dialog-title');            
            for(var i=0; i<dbox.length; i++)
                dbox[i].innerHTML='Uppdatera uppgifter för: ' + homeData.record.FamilyName;
        },
        formClosed: function (event, homeData){
            clearMembershipNoOptionCache=true;
            homeData.row[0].style.backgroundColor = '';
        }
    };
}


function homeFields(placeHolder, homeData) {
    return {
        CloseChild: fieldCloseChildTable(placeHolder, homeData.record.Id),
        Id: {
            key: true,
            list: false,
            defaultValue: homeData.record.Id
        },
        Residents:{
            edit: false,
            title: 'Boende på adressen',
            width: '15%',
            display: function(data){
                return _setClassAndValue(data.record, "Residents", HOME);
            }
        },
        FamilyName: {
            list: false,
            title: 'Familjenamn',
            display: function (data) {
                return _setClassAndValue(data.record, "FamilyName", HOME);
            }          
        },
        Phone: {
            title: 'Tel.',
            inputTitle: 'Hemtelefon',
            width: '9%',
            display: function (data) {
                return _setClassAndValue(data.record, "Phone", HOME);
            }                       
        },
        Co: {
            title: 'Co',
            width: '15%',
            display: function (data){
                return _setClassAndValue(data.record, "Co", HOME);
            }
        },                                
        Address: {
            title: 'Gatuadress',
            width: '20%',
            display: function (data){
                return _setClassAndValue(data.record, "Address", HOME);
            }
        },
        Zip: {
            title: 'PA',
            width: '5%',
            display: function (data){
                return _setClassAndValue(data.record, "Zip", HOME);
            }
        },
        City: {
            title: 'Stad',
            width: '15%',
            display: function (data){
                return _setClassAndValue(data.record, "City", HOME);
            }
        },
        Country: {
            title: 'Land',
            width: '15%',
            display: function (data){
                return _setClassAndValue(data.record, "Country", HOME);
            }
        },
        Letter: {
            inputTitle: 'Församlingspost via brev',
            title: 'Brev',
            width: '4%',
            display: function (data){
                return _setClassAndValue(data.record, "Letter", HOME);
            },
            options: _letterOptions()
        }
    };
}

// *********************************************************
// SUBTABLE MEMBERSHIP
// *********************************************************

function childTableMembership(placeHolder){
    return {
        title: '',
        width: '1%',
        sorting: false,
        edit: false,
        create: false,
        delete: false,
        display: function (memberData) {                    
            var $imgMember = $('<img src="/' + SARON_URI + SARON_IMAGES_URI + 'member.png" title="Medlemsuppgifter" />');
            $imgMember.click(function () {
                $(placeHolder).jtable('openChildTable', $imgMember.closest('tr'),{
                    title: _setClassAndValuePrefix(memberData.record, "Name", PERSON, "Medlemsuppgifter för: "),
                    showCloseButton: false,
                    actions: {
                        listAction: '/' + SARON_URI + 'app/web-api/listPerson.php?Id=' + memberData.record.Id,                                           
                        updateAction: function(postData) {
                            return $.Deferred(function ($dfd) {
                                $.ajax({
                                    url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=membership&Id=' + memberData.record.Id,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: postData,
                                    success: function (data) {
                                        if(data.Result !== 'ERROR'){                                                
                                            $dfd.resolve(data);
                                            _updateFields(data.Record, "MemberState", PERSON);                                                
                                            _updateFields(data.Record, "VisibleInCalendar", PERSON);                                                
                                            _updateFields(data.Record, "DateOfMembershipStart", PERSON);                                                
                                            _updateFields(data.Record, "DateOfMembershipEnd", PERSON);                                                
                                            _updateFields(data.Record, "Residents", HOME);                                                
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
                        CloseChild: fieldCloseChildTable(placeHolder, memberData.record.Id),
                        Id: {
                            key: true,
                            list: false,
                            defaultValue: memberData.record.Id
                        },
                        PreviousCongregation: {
                            title: 'Kommit från församling',
                            width: '20%',
                            display: function (memberData){
                                return _setClassAndValue(memberData.record, "PreviousCongregation", PERSON);
                            }
                        },
                        DateOfMembershipStart: {
                            width: '7%',     
                            displayFormat: DATE_FORMAT,
                            type: 'date',
                            title: 'Start',
                            display: function (memberData){
                                return _setClassAndValue(memberData.record, "DateOfMembershipStart", PERSON);
                            }
                        },
                        MembershipNo: {
                            title: 'Nr.',
                            width: '3%',
                            display: function (memberData){
                                return _setClassAndValue(memberData.record, "MembershipNo", PERSON);
                            }, 
                            options: function(memberData){
                                if(clearMembershipNoOptionCache){
                                    memberData.clearCache();
                                    clearMembershipNoOptionCache=false;
                                }
                                return '/' + SARON_URI + 'app/web-api/listPerson.php?Id=' + memberData.record.Id + '&selection=nextMembershipNo';
                            }
                        },
                        DateOfMembershipEnd: {
                            width: '7%',
                            type: 'date',
                            displayFormat: DATE_FORMAT,
                            title: 'Avslut',
                            display: function (memberData){
                                return _setClassAndValue(memberData.record, "DateOfMembershipEnd", PERSON);
                            } 
                        },
                        NextCongregation: {
                            width: '20%',
                            title: 'Flyttat till församling',
                            display: function (meberData){
                                return _setClassAndValue(meberData.record, "NextCongregation", PERSON);
                            } 
                        },
                        VisibleInCalendar: {
                            edit: 'true',
                            list: true,
                            title: 'Kalender',
                            inputTitle: 'Synlig i adresskalendern',
                            width: '4%',
                            inputClass: function(memberData){
                                return _setClassAndValue(memberData.record, "VisibleInCalendar", PERSON);
                            },
                            options: _visibilityOptions()
                        }, 
                        Comment: {
                            type: 'textarea',
                            width: '40%',
                            title: 'Not',
                            display: function (memberData){
                                return _setClassAndValue(memberData.record, "Comment", PERSON);
                            }       
                        }
                    },
                    rowInserted: function(event, memberData){
                        if (memberData.record.user_role !== 'edit'){
                            memberData.row.find('.jtable-edit-command-button').hide();
                            memberData.row.find('.jtable-delete-command-button').hide();
                        }
                    },        
                    formCreated: function (event, memberData){
                        memberData.row[0].style.backgroundColor = "yellow";
                        memberData.form.css('width',inputFormWidth);
                        memberData.form.find('input[name=PreviousCongregation]').css('width',inputFormFieldWidth);
                        memberData.form.find('input[name=NextCongregation]').css('width',inputFormFieldWidth);
                        memberData.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);                                

                        var dbox = document.getElementsByClassName('ui-dialog-title');            
                        for(var i=0; i<dbox.length; i++)
                            dbox[i].innerHTML='Uppdatera uppgifter för: ' + memberData.record.FirstName + ' ' + memberData.record.LastName;
                    },
                    formClosed: function (event, memberData){
                        clearMembershipNoOptionCache=true;
                        memberData.row[0].style.backgroundColor = '';
                    }                        
                },
                function (memberData) { //opened handler
                    memberData.childTable.jtable('load');
                });
            });
            //Return Membership image to show on the person row
            return $imgMember;
        }
    };
}

// *********************************************************
// SUBTABLE BAPTISM
// *********************************************************

function childTableBaptism(placeHolder){
    return {
        title: '',
        width: '1%',
        sorting: false,
        edit: false,
        create: false, 
        delete: false,
        display: function (baptistData) {
            var $imgBaptist = $('<img src="/' + SARON_URI + SARON_IMAGES_URI + 'baptist.png" title="Dopuppgifter" />');
            
            $imgBaptist.click(function () {
                    $(placeHolder).jtable('openChildTable', $imgBaptist.closest('tr'),{
                    title: _setClassAndValuePrefix(baptistData.record, "Name", PERSON, "Dopuppgifter för: "),
                    showCloseButton: false,                                    
                    actions: {
                        listAction: '/' + SARON_URI + 'app/web-api/listPerson.php?Id=' + baptistData.record.Id,                                           
                        updateAction: function(postData) {
                            return $.Deferred(function ($dfd) {
                                $.ajax({
                                    url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=baptism&Id=' + baptistData.record.Id,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: postData,
                                    success: function (data) {
                                        if(data.Result !== 'ERROR'){

                                            $dfd.resolve(data);
                                            _updateFields(data.Record, "DateOfBaptism", PERSON);                                                
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
                        CloseChild: fieldCloseChildTable(placeHolder, baptistData.record.Id),
                        Id: {
                            key: true,
                            list: false,
                            defaultValue: baptistData.record.Id
                        },
                        CongregationOfBaptismThis: {
                            list: false,
                            title: 'Döpt',
                            options: _baptistOptions()
                        },
                        CongregationOfBaptism: {
                            edit: true,
                            create: false,
                            width: '20%',
                            title: 'Dopförsamling',
                            display: function (baptisData){
                                return _setClassAndValue(baptisData.record, "CongregationOfBaptism", PERSON);
                            } 
                        },
                        DateOfBaptism: {
                            title: 'Dopdatum',
                            width: '7%',
                            type: 'date',
                            displayFormat: DATE_FORMAT,
                            display: function (baptistData){
                                return _setClassAndValue(baptistData.record, "DateOfBaptism", PERSON);
                            } 
                        },
                        Baptister: {
                            width: '20%',
                            title: 'Dopförrättare'
                        },

                        Comment: {
                            type: 'textarea',
                            width: '35%',
                            title: 'Not',
                            display: function (baptisData){
                                return _setClassAndValue(baptisData.record, "Comment", PERSON);
                            } 
                        }
                    },
                    rowInserted: function(event, baptisData){
                        if (baptisData.record.user_role !== 'edit'){
                            baptisData.row.find('.jtable-edit-command-button').hide();
                            baptisData.row.find('.jtable-delete-command-button').hide();
                        }
                    },        
                    formCreated: function (event, baptisData){
                        baptisData.row[0].style.backgroundColor = "yellow";
                        baptisData.form.css('width',inputFormWidth);
                        baptisData.form.find('input[name=Baptister]').css('width',inputFormFieldWidth);
                        baptisData.form.find('input[name=CongregationOfBaptism]').css('width',inputFormFieldWidth);
                        baptisData.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);
                        baptisData.form.find('select[name=CongregationOfBaptismThis]').change(function () {
                            baptistFormAuto(baptisData, this.value);
                        });

                        var dbox = document.getElementsByClassName('ui-dialog-title');            
                        for(var i=0; i<dbox.length; i++)
                            dbox[i].innerHTML='Uppdatera uppgifter för: ' + baptisData.record.FirstName + ' ' + baptisData.record.LastName;
                    },
                    formClosed: function (event, baptisData){
                        baptisData.row[0].style.backgroundColor = '';
                        clearMembershipNoOptionCache=true;

                    }                        
                },
                function (baptisData) { //opened handler
                    baptisData.childTable.jtable('load');
                });
            });
            return $imgBaptist;
        }
    };
}

function fieldCloseChildTable(placeHolder, Id){
    return {
        title: '',
        width: '1%',
        sorting: false,
        edit: false,
        create: false,
        delete: false,
        display: function() {
            var $imgClose = $('<img src="/' + SARON_URI + SARON_IMAGES_URI + 'cross.png" title="Stäng" />');
            $imgClose.click(function () {
                //closeChildTable(Id);
                var $selectedRow = $("[data-record-key=" + Id + "]"); 
                $(placeHolder).jtable('closeChildTable', $selectedRow);  
            });                
            return $imgClose;
       }
    };
}



function _openHomeChildTable(placeHolder, data){
    var newData = {record: data.Record}
    var rowRef = "[data-record-key=" + data.Record.Id + "]";
    var $selectedRow = $(rowRef);
    $(placeHolder).jtable('openChildTable', $selectedRow, homeChildTableDef(newData, data.Record.HomeId), function(data){
        data.childTable.jtable('load');
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



