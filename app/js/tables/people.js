"use strict";

$(document).ready(function () {
    
    $('#people').jtable({
        title: 'Personuppgifter',
        paging: true, //Enable paging
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'LongHomeName ASC, DateOfBirth ASC', //Set default sorting   
        messages: {addNewRecord: 'Ny person'},
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php',
//            createAction: '/' + SARON_URI + 'app/web-api/createPerson.php',
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/createPerson.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            // set filter to latest update
                            var filter = document.getElementById("groupId");
                            filter.value = 2;
                             $('#search_people').click();
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
                        url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=person',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result !== 'ERROR'){
                                newHomeId = -1;
                                if(data.Records[0].HomeId > 0)
                                    newHomeId=data.Records[0].HomeId;

                                $dfd.resolve(data);
                                //Update Person
                                _updateLongHomeName(data.Records);
                                //_updatePhone(data.Records);
                                _updateName(data.Records);
                                _updateMemberState(data.Records);
                                _updateResidents(data.Records);
                                _updateCalendarVisability(data.Records);
                                _updateComment(data.Records);
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
            //deleteAction: 'app/web-api/deletePerson.php'
        },       
        fields: {
            Homes: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                    display: function (homeData) {
                    //Create an image that will be used to open child table
                    var $imgHome;
                    if(newHomeId>0){                    
                        homeData.record.HomeId = newHomeId;
                        newHomeId=-1;
                    }
                    if(homeData.record.HomeId>0 ){
                        $imgHome = $('<img src="/' + SARON_URI + 'app/images/home.png" title="Adressuppgifter" />');
                    }
                    else{
                        $imgHome = $('<img src="/' + SARON_URI + 'app/images/emptyHome.png"  />');
                        $('#people').jtable({}, $imgHome.closest('tr'),{});
                        return $imgHome;
                    }
                    //Open child table when user clicks the image
                    $imgHome.click(function () {
                        $('#people').jtable('openChildTable', $imgHome.closest('tr'),{
                            title: '<p class="' + _getHomeClassName(homeData.record.HomeId) + '">Hem: ' + homeData.record.LongHomeName + '</p>',                            
                            showCloseButton: false,
                            actions: {
                                listAction: '/' + SARON_URI + 'app/web-api/listHome.php?HomeId=' + homeData.record.HomeId,                                
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: '/' + SARON_URI + 'app/web-api/updateHome.php?HomeId=' + homeData.record.HomeId,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                if(data.Result !== 'ERROR'){
                                                    data.Records[0].oldHomeId=data.Records[0].HomeId;
                                                    $dfd.resolve(data);
                                                    _updateLongHomeName(data.Records);
                                                    //_updatePhone(data.Records);
                                                    _updateFields(data.Records[0], "Phone", HOME);                                                }
                                                else
                                                    $dfd.resolve(data);
                                            },
                                            error: function () {
                                                $dfd.reject();
                                            }
                                        });
                                    });
                                }
                                // createAction:
                                // deleteAction: 
                            },
                            fields: {
                                CloseChild: {
                                   title: '',
                                   width: '1%',
                                   sorting: false,
                                   edit: false,
                                   create: false,
                                   delete: false,
                                   display: function () {
                                       var $imgClose = $('<img src="/' + SARON_URI + 'app/images/cross.png" title="Stäng" />');                    
                                       $imgClose.click(function () {
                                            $('#people').jtable('closeChildTable', $($imgHome.closest('tr')));
                                       });                
                                       
                                       return $imgClose;
                                   }
                                },
                                Residents:{
                                    edit: false,
                                    title: 'Boende på adressen',
                                    width: '15%',
                                    display: function(data){
                                        return _setClass(data, "Residents", HOME);
//                                        if(data.record.Residents===null)
//                                            data.record.Residents="";
//
//                                        return '<p class="' + _getResidentsClassName(data.record.HomeId) + '">' + data.record.Residents + '</>';
                                    }
                                },
                                FamilyName: {
                                    list: false,
                                    title: 'Familjenamn'
                                },
                                Phone: {
                                    title: 'Tel.',
                                    inputTitle: 'Hemtelefon',
                                    width: '9%',
                                    display: function (data) {
                                        return _setClass(data.record, "Phone", HOME);
                                    }                       
                                },
                                Co: {
                                    title: 'Co',
                                    width: '15%'
                                },                                
                                Address: {
                                    title: 'Gatuadress',
                                    width: '20%'
                                },
                                Zip: {
                                    title: 'PA',
                                    width: '5%',
                                    display: function (data){
                                        return _formatZipCode(data.record.Zip);
                                    }
                                },
                                City: {
                                    title: 'Stad',
                                    width: '15%'
                                },
                                Country: {
                                    title: 'Land',
                                    width: '15%'
                                },
                                Letter: {
                                    inputTitle: 'Församlingspost via brev',
                                    title: 'Brev',
                                    width: '4%',
                                    options:{ 0 : '', 1 : 'Ja'}
                                } 
                            },
                            rowInserted: function(event, data){
                                if (data.record.user_role !== 'edit'){
                                    data.row.find('.jtable-edit-command-button').hide();
                                    data.row.find('.jtable-delete-command-button').hide();
                                }
                            },        
                            formCreated: function (event, data){
                                data.row[0].style.backgroundColor = "yellow";
                                data.form.css('width',inputFormWidth);
                                data.form.find('input[name=FamilyName]').css('width',inputFormFieldWidth);
                                data.form.find('input[name=Phone]').css('width',inputFormFieldWidth);
                                data.form.find('input[name=Co]').css('width',inputFormFieldWidth);
                                data.form.find('input[name=Address]').css('width',inputFormFieldWidth);
                                data.form.find('input[name=City]').css('width',inputFormFieldWidth);
                                data.form.find('input[name=Country]').css('width',inputFormFieldWidth);

                                var dbox = document.getElementsByClassName('ui-dialog-title');            
                                for(var i=0; i<dbox.length; i++)
                                    dbox[i].innerHTML='Uppdatera uppgifter för: ' + data.record.FamilyName;
                            },
                            formClosed: function (event, data){
                                clearMembershipNoOptionCache=true;
                                data.row[0].style.backgroundColor = '';
                            }
                        }, 
                        function (data) { //opened handler
                            data.childTable.jtable('load');
                        });
                    });
                    //Return image to show on the person row
                    return $imgHome;
                }
            },
//Membership            
            membership: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                display: function (memberData) {                    
                    var $imgMember = $('<img src="/' + SARON_URI + 'app/images/member.png" title="Medlemsuppgifter" />');
                    $imgMember.click(function () {
                        $('#people').jtable('openChildTable', $imgMember.closest('tr'),{
                            title: '<p class="keyValue">Medlemsuppgifter för: ' +  memberData.record.Name + '</p>',
                            showCloseButton: false,
                            actions: {
                                listAction: '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=' + memberData.record.PersonId,                                           
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=membership&PersonId=' + memberData.record.PersonId,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                if(data.Result !== 'ERROR'){                                                
                                                    $dfd.resolve(data);
                                                    var records = data['Records'];
                                                    data.Records[0].oldHomeId=data.Records[0].HomeId;
                                                    _updateMemberState(records);
                                                    _updateCalendarVisability(records);
                                                    _updateResidents(records);

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
                                //createAction: 'app/web-api/createPeopleHome.php?HomeId=' + homeData.record.Id
                                //deleteAction:
                            },
                            fields: {
                                CloseChild: {
                                    title: '',
                                    width: '1%',
                                    sorting: false,
                                    edit: false,
                                    create: false,
                                    delete: false,
                                    display: function() {
                                        var $imgClose = $('<img src="/' + SARON_URI + 'app/images/cross.png" title="Stäng" />');
                                        $imgClose.click(function() {                                            
                                            $('#people').jtable('closeChildTable', $imgMember.closest('tr'));
                                            return;
                                        });                
                                        return $imgClose;
                                   }
                                },
                                PersonId: {
                                    key: true,
                                    update: false,
                                    create: false,
                                    type: 'hidden'
                                },
                                PreviousCongregation: {
                                    title: 'Kommit från församling',
                                    width: '20%'
                                },
                                DateOfMembershipStart: {
                                    width: '7%',
                                    display: function (memberData) {
                                        return _parseDate(memberData.record.DateOfMembershipStart, false);
                                    },              
                                    type: 'date',
                                    title: 'Start'
                                },
                                MembershipNo: {
                                    width: '3%',
                                    display: function (memberData) {
                                        return _formatNumericString(memberData.record.MembershipNo);
                                    },          
                                    title: 'Nr.',
                                    options: function(data){
                                        if(clearMembershipNoOptionCache){
                                            data.clearCache();
                                            clearMembershipNoOptionCache=false;
                                        }
                                        return '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=' + memberData.record.PersonId + '&selection=nextMembershipNo';
                                    }
                                },
                                DateOfMembershipEnd: {
                                    display: function (data) {
                                        return '<p class="' + _getDateOfMembershipEndClassName(data.record.PersonId) + '">' +  _parseDate(data.record.DateOfMembershipEnd, false) + '</p>';                    
                                    },          
                                    width: '7%',
                                    type: 'date',
                                    title: 'Avslut'
                                },
                                NextCongregation: {
                                    width: '20%',
                                    title: 'Flyttat till församling'
                                },
                                VisibleInCalendar: {
                                    edit: 'true',
                                    list: false,
                                    title: 'Kalender',
                                    inputTitle: 'Synlig i adresskalendern',
                                    width: '4%',              
                                    display: function (memberData){
                                        return '<p class="' + _getCalendarVisabilityClassName(memberData.record.PersonId) + '">' +  _getVisabilityDisplayValue(memberData.record.VisibleInCalendar) + '</p>';                    
                                    },               
                                    options:{ 0: '', 1: NOT_VISIBLE, 2: VISIBLE}
                                },
                                Comment: {
                                    type: 'textarea',
                                    width: '40%',
                                    title: 'Not',
                                    display: function (data){
                                        if(data.record.Comment!==null)
                                            return '<p class="' + _getCommentClassName(data.record.PersonId) + '">' + data.record.Comment + '</p>';
                                        else
                                            return '<p class="' + _getCommentClassName(data.record.PersonId) + '"></p>';
                                    }
                                }
                            },
                            rowInserted: function(event, data){
                                if (data.record.user_role !== 'edit'){
                                    data.row.find('.jtable-edit-command-button').hide();
                                    data.row.find('.jtable-delete-command-button').hide();
                                }
                            },        
                            formCreated: function (event, data){
                                data.row[0].style.backgroundColor = "yellow";
                                data.form.css('width',inputFormWidth);
                                data.form.find('input[name=PreviousCongregation]').css('width',inputFormFieldWidth);
                                data.form.find('input[name=NextCongregation]').css('width',inputFormFieldWidth);
                                data.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);                                
                    
                                var dbox = document.getElementsByClassName('ui-dialog-title');            
                                for(var i=0; i<dbox.length; i++)
                                    dbox[i].innerHTML='Uppdatera uppgifter för: ' + data.record.FirstName + ' ' + data.record.LastName;
                            },
                            formClosed: function (event, data){
                                clearMembershipNoOptionCache=true;
                                data.row[0].style.backgroundColor = '';
                            }                        
                        },
                        function (data) { //opened handler
                            data.childTable.jtable('load');
                        });
                    });
                    //Return Membership image to show on the person row
                    return $imgMember;
                }
            }, 
//Baptism            
            baptism: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false, 
                delete: false,
                display: function (baptistData) {
                    //Create an image that will be used to open child table
                    var $imgBaptist = $('<img src="/' + SARON_URI + 'app/images/baptist.png" title="Dopuppgifter" />');
                    //Open child table when user clicks the image
                    $imgBaptist.click(function () {
                        $('#people').jtable('openChildTable', $imgBaptist.closest('tr'),{
                            title: '<p class="keyValue">Dopuppgifter för: ' +  baptistData.record.Name + '</p>',
                            showCloseButton: false,                                    
                            actions: {
                                listAction: '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=' + baptistData.record.PersonId,                                           
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=baptism&PersonId=' + baptistData.record.PersonId,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                if(data.Result !== 'ERROR'){
                                                    $dfd.resolve(data);
                                                    var records = data['Records'];
                                                    _updateMemberState(records);
                                                    _updateResidents(records);
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
                                //createAction: 'app/web-api/createPeopleHome.php?HomeId=' + homeData.record.Id
                                //deleteAction:
                            },
                            fields: {
                                CloseChild: {
                                   title: '',
                                   width: '1%',
                                   sorting: false,
                                   edit: false,
                                   create: false,
                                   delete: false,
                                   display: function (memberData) {
                                       var $imgClose = $('<img src="/' + SARON_URI + 'app/images/cross.png" title="Stäng" />');
                                       $imgClose.click(function () {
                                            $('#people').jtable('closeChildTable', $($imgBaptist.closest('tr')));
                                       });                
                                   return $imgClose;
                                   }
                                },
                                PersonId: {
                                    key: true,
                                    update: false,
                                    create: false,
                                    type: 'hidden'
                                },
                                CongregationOfBaptismThis: {
                                    list: false,
                                    with: '20%',
                                    title: 'Döpt',
                                    options: {0:'Nej', 1: 'Ja, Ange församling nedan.', 2:'Ja, ' + FullNameOfCongregation + '.'}
                                },
                                CongregationOfBaptism: {
                                    edit: true,
                                    create: false,
                                    width: '20%',
                                    title: 'Dopförsamling',
                                    display: function(data){
                                        if(data.record.CongregationOfBaptism!==null)
                                            return '<p class="' + _getBaptistConcregationClassName(data.record.PersonId) + '">' + data.record.CongregationOfBaptism + '</p>';
                                        else
                                            return '<p class="' + _getBaptistConcregationClassName(data.record.PersonId) + '"></p>';
                                    }
                                },
                                DateOfBaptism: {
                                    width: '7%',
                                    type: 'date',
                                    display: function (baptistData) {
                                        return _parseDate(baptistData.record.DateOfBaptism, false);
                                    },          
                                    title: 'Dopdatum'
                                },
                                Baptister: {
                                    width: '20%',
                                    title: 'Dopförrättare'
                                },

                                Comment: {
                                    type: 'textarea',
                                    width: '35%',
                                    title: 'Not',
                                    display: function (data){
                                        if(data.record.Comment!==null)
                                            return '<p class="' + _getCommentClassName(data.record.PersonId) + '">' + data.record.Comment + '</p>';
                                        else
                                            return '<p class="' + _getCommentClassName(data.record.PersonId) + '"></p>';
                                    }
                                }
                            }, //Baptist
                            rowInserted: function(event, data){
                                if (data.record.user_role !== 'edit'){
                                    data.row.find('.jtable-edit-command-button').hide();
                                    data.row.find('.jtable-delete-command-button').hide();
                                }
                            },        
                            formCreated: function (event, data){
                                data.row[0].style.backgroundColor = "yellow";
                                data.form.css('width',inputFormWidth);
                                data.form.find('input[name=Baptister]').css('width',inputFormFieldWidth);
                                data.form.find('input[name=CongregationOfBaptism]').css('width',inputFormFieldWidth);
                                data.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);
                                data.form.find('select[name=CongregationOfBaptismThis]').change(function () {baptistFormAuto(data, this.value)});

                                var dbox = document.getElementsByClassName('ui-dialog-title');            
                                for(var i=0; i<dbox.length; i++)
                                    dbox[i].innerHTML='Uppdatera uppgifter för: ' + data.record.FirstName + ' ' + data.record.LastName;
                                //...
                            },
                            formClosed: function (event, data){
                                data.row[0].style.backgroundColor = '';
                                clearMembershipNoOptionCache=true;

                            }                        
                        },
                        function (data) { //opened handler
                            data.childTable.jtable('load');
                        });
                    });
                    //Return image to show on the person row
                    return $imgBaptist;
                }
            },
            HomeId: {
                create: true,
                edit: true,
                list: true,
                title: 'Hem',
                inputTitle: 'Välj hem',
                display: function(data){
                    if(data.record.LongHomeName!==null)
                            return '<p class="' + _getHomeClassName(data.record.HomeId) + '">' + data.record.LongHomeName + '</p>';
                        else
                            return '<p class="' + _getHomeClassName(data.record.HomeId) + '"> Inget hem</p>';
                },
                options: function(data){
                    if(data.source !== 'list')
                        data.clearCache();
                    return '/' + SARON_URI + 'app/web-api/listHomes.php?selection=options';
                }
            },
            PersonId: {
                key: true,
                list: false,
                create: false,
                edit: false,
                update: false
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
                display: function (data) {
                    return '<p class="keyValue '+ _getNameClassName(data.record.PersonId) +'">' +  data.record.Name + '</p>';
                }          
            },
            DateOfBirth: {
                title: 'Född',
                width: '5%',
                type: 'date',
                display: function (data) {
                    return _parseDate(data.record.DateOfBirth, true);
                }          
            },
            Gender: {
                title: 'Kön',
                width: '2%',
                options:{ 0 : '-', 1 : 'Man', 2 : 'Kvinna'}
            },
            Email: {
                title: 'Mail',
                display: function (data) {
                    return _getMailLink(data.record.Email, data.record.PersonId);
                },
                width: '13%'
            },  
            Mobile: {
                title: 'Mobil',
                inputTitle: 'Mobil <BR> - Hemtelefonuppgifter matas in under "Adressuppgifter"',
                width: '7%',
                display: function (data) {
                   return _formatPhoneNumber(data.record.Mobile);
                }           
            },
            Phone: {
                title: 'Tel.',
                edit: false,
                width: '7%',
                create: false,
                display: function (data) {
                    return _setClass(data.record, "Phone", HOME);
                }                       
            },
            DateOfMembershipStart: {
                edit: false,
                list: false,
                title: 'Medlemskap start',
                width: '5%',
                type: 'date',
                display: function (data) {
                    return _parseDate(data.record.DateOfMembershipStart,false);
                }            
            }, 
            MembershipNo: {
                list: false,
                edit: false,
                title: 'Medlemsnummer',
                display: function (data) {
                    return _formatNumericString(data.record.MembershipNo);
                },          
                
                options: function (data){
                    if(clearMembershipNoOptionCache){
                        data.clearCache();
                        clearMembershipNoOptionCache=false;
                    }
                    return '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=null' + '&selection=nextMembershipNo';
                }
            },
            MemberState: {
                title: 'Status',
                edit: false,
                create: false,
                width: '4%',
                display: function (data){
                    return '<p class="' + _getMemberStateClassName(data.record.PersonId) + '">' +  data.record.MemberState + '</p>';                    
                }
            },
            VisibleInCalendar: {
                edit: false,
                title: 'Kalender',
                inputTitle: 'Synlig i adresskalendern',
                width: '4%',              
                display: function (memberData){
                    return '<p class="' + _getCalendarVisabilityClassName(memberData.record.PersonId) + '">' +  _getVisabilityDisplayValue(memberData.record.VisibleInCalendar) + '</p>';                    
                },               
                options:{ 0: '', 1: NOT_VISIBLE, 2: VISIBLE}
            },
            DateOfMembershipEnd: {
                title: 'Medlemskap slut', //hidden
                list: false,
                edit: false,
                type: 'date',
                create: false,
                display: function (data) {
                    return '<p class="' + _getDateOfMembershipEndClassName(data.record.PersonId) + '">' +  _parseDate(data.record.DateOfMembershipEnd, false) + '</p>';                    
                }            
            },
            DateOfDeath: {
                title: 'Avliden',
                list: false,
                type: 'date',
                create: false,
                edit: true,
                display: function (data) {
                    return _parseDate(data.record.DateOfDeath, false);
                }
            },
            Comment: {
                title: 'Not',
                type: 'textarea',
                list: false,
                display: function (data){
                    if(data.record.Comment!==null)
                        return '<p class="' + _getCommentClassName(data.record.PersonId) + '">' + data.record.Comment + '</p>';
                    else
                        return '<p class="' + _getCommentClassName(data.record.PersonId) + '"></p>';
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
                $('#people').find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            var headLine;
            
            if(data.formType === 'edit'){
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
    });
    //Re-load records when user click 'load records' button.
    $('#search_people').click(function (e) {
        e.preventDefault();
        filterPeople('people');
    }); 
    //Load all records when page is first shown
    $('#search_people').click();
    $('#people').find('.jtable-toolbar-item-add-record').hide();
});

function filterPeople(viewId){
    $('#' + viewId).jtable('load', {
        searchString: $('#searchString').val(),
        groupId: $('#groupId').val(),
        tableview: viewId
    });
}

    
