var inputFormWidth = '500px';
var inputFormFieldWidth = '480px';
var NOT_VISIBLE = 'Ej synlig';
var VISIBLE = 'Synlig';
var newHomeId = -1;
var clearMembershipNoOptionCache = true;

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
            listAction:   '/' + SARON_URI + 'app/entities/listPeople.php',
//            createAction: '/' + SARON_URI + 'app/entities/createPerson.php',
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/entities/createPerson.php',
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
                        url: '/' + SARON_URI + 'app/entities/updatePerson.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            if(data.Result !== 'ERROR'){
                                newHomeId = -1;
                                if(data.Records[0].HomeId > 0)
                                    newHomeId=data.Records[0].HomeId;

                                $dfd.resolve(data);
                                var records = data['Records'];
                                //Update Person
                                _updateHome(records);
                                _updatePhone(records);
                                _updateName(records);
                                _updateMemberState(records);
                                _updateResidents(records);
                                _updateCalendarVisability(records);
                                _updateComment(records);
                            }
                            else
                                $dfd.resolve(data);

                        },
                        error: function () {
                            //alert('Error in updateAction\r\n' + $dfd.toString());
                            //window.location.href = 'http://localhost/' + SARON_URI + 'app/';
                            $dfd.reject();
                        }
                    });
                });
            }
            //deleteAction: 'app/entities/deletePerson.php'
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
                                listAction: '/' + SARON_URI + 'app/entities/listPeopleHome.php?HomeId=' + homeData.record.HomeId,                                
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: '/' + SARON_URI + 'app/entities/updatePeopleHome.php?HomeId=' + homeData.record.HomeId,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                if(data.Result !== 'ERROR'){
                                                    data.Records[0].oldHomeId=data.Records[0].HomeId;
                                                    $dfd.resolve(data);
                                                    _updateHome(data.Records);
                                                    _updatePhone(data.Records);
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
                                        if(data.record.Residents===null)
                                            data.record.Residents="";

                                        return '<p class="' + _getResidentsClassName(data.record.HomeId) + '">' + data.record.Residents + '</>';
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
                                        if(data.record.Phone!==null)
                                            return '<p class="numericString">' +  data.record.Phone + '</p>';
                                        else
                                            return '<p class="numericString"></p>';
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
                                    display: function (homeData){
                                        if(homeData.record.Zip!==null)
                                            return '<p class="numericString">' + homeData.record.Zip + '</p>';
                                        else
                                            return '<p class="numericString"></p>';
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
                                data.form.find('input[name=Address]').css('width',inputFormFieldWidth);

                                var dbox = document.getElementsByClassName('ui-dialog-title');            
                                for (i=0; i<dbox.length; i++)
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
                                listAction: '/' + SARON_URI + 'app/entities/listPeopleMembership.php?PersonId=' + memberData.record.PersonId,                                           
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: '/' + SARON_URI + 'app/entities/updatePersonMembership.php?PersonId=' + memberData.record.PersonId,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                if(data.Result !== 'ERROR'){                                                
                                                    $dfd.resolve(data);
                                                    var records = data['Records'];
                                                    _updateMemberState(records);
                                                    _updateCalendarVisability(records);                                               
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
                                //createAction: 'app/entities/createPeopleHome.php?HomeId=' + homeData.record.Id
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
                                        if(memberData.record.MembershipNo>0)
                                            return '<p class="numericString">' + memberData.record.MembershipNo + '</p>';
                                        else
                                            return '<p class="numericString"></p>';
                                    },          
                                    title: 'Nr.',
                                    options: function(data){
                                        if(clearMembershipNoOptionCache){
                                            data.clearCache();
                                            clearMembershipNoOptionCache=false;
                                        }
                                        return '/' + SARON_URI + 'app/entities/listNextMembershipNo.php?PersonId=' + memberData.record.PersonId;
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
                                for (i=0; i<dbox.length; i++)
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
                                listAction: '/' + SARON_URI + 'app/entities/listPeopleBaptist.php?PersonId=' + baptistData.record.PersonId,                                           
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: '/' + SARON_URI + 'app/entities/updatePersonBaptist.php?PersonId=' + baptistData.record.PersonId,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                if(data.Result !== 'ERROR'){
                                                    $dfd.resolve(data);
                                                    var records = data['Records'];
                                                    _updateMemberState(records);
                                                    _updateBaptistCongregation(records);
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
                                //createAction: 'app/entities/createPeopleHome.php?HomeId=' + homeData.record.Id
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
                                    options: {0:'Nej', 1: 'Ja, Ange församling nedan.', 2:'Ja, i Korskyrkan.'}
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
                                for (i=0; i<dbox.length; i++)
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
                    return '/' + SARON_URI + 'app/entities/listHomesOptions.php';
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
                display: function (data) {
                    return '<p class="keyValue">' +  data.record.LastName + '</p>';
                }          
            },
            FirstName: {
                title: 'Förnamn',
                list: false,
                display: function (data) {
                    return '<p class="keyValue">' +  data.record.FirstName + '</p>';
                }          
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
                    return _parseDate  (data.record.DateOfBirth, true);
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
                    if(data.record.Mobile!==null)
                        return '<p class="numericString">' +  data.record.Mobile + '</p>';
                    else
                        return '<p class="numericString"></p>';
                }           
            },
            Phone: {
                title: 'Tel.',
                edit: false,
                width: '7%',
                create: false,
                display: function (data) {
                    if(data.record.Phone!==null)
                        return '<p class="numericString ' + _getPhoneClassName(data.record.HomeId) + '">' +  data.record.Phone + '</p>';
                    else
                        return '<p class="numericString ' + _getPhoneClassName(data.record.HomeId) + '"></p>';
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
                    if(data.record.MembershipNo>0)
                        return '<p class="numericString">' + data.record.MembershipNo + '</p>';
                    else
                        return '<p class="numericString"></p>';
                },          
                
                options: function (data){
                    if(clearMembershipNoOptionCache){
                        data.clearCache();
                        clearMembershipNoOptionCache=false;
                    }
                    return '/' + SARON_URI + 'app/entities/listNextMembershipNo.php?PersonId=null'
                }
            },
            MemberState: {
                title: 'Status',
                edit: false,
                create: false,
                width: '4%',
                display: function (memberData){
                    return '<p class="' + _getMemberStateClassName(memberData.record.PersonId) + '">' +  memberData.record.MemberState + '</p>';                    
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
            for (i=0; i<dbox.length; i++){
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
function _updateComment(records){
    var commentClassName = _getCommentClassName(records[0].PersonId);
    var classComment = document.getElementsByClassName(commentClassName);
    for(i = 0; i<classComment.length;i++)
        classComment[i].innerHTML = records[0].Comment;
}


function _updateBaptistCongregation(records){
    var baptistConcregationClassName = _getBaptistConcregationClassName(records[0].PersonId);
    var classBaptistConcregation = document.getElementsByClassName(baptistConcregationClassName);
    for(i = 0; i<classBaptistConcregation.length;i++)
        classBaptistConcregation[i].innerHTML = records[0].CongregationOfBaptism;
}

function _updatePhone(records){
    var phoneClassName = _getPhoneClassName(records[0].oldHomeId);
    var classPhone = document.getElementsByClassName(phoneClassName);
    for(i = 0; i<classPhone.length;i++)
        if(records[0].HomeId > 0)
            classPhone[i].innerHTML = records[0].Phone;                                                                                                    
        else
            classPhone[i].innerHTML = "";
}

function _updateName(records){
    var nameClassName = _getNameClassName(records[0].PersonId);
    var className = document.getElementsByClassName(nameClassName);
    for(i = 0; i<className.length;i++)
        className[i].innerHTML = records[0].Name;                                                                                                        
}

function _updateHome(records){
    var homeClassName = _getHomeClassName(records[0].oldHomeId);
    var classHomes = document.getElementsByClassName(homeClassName);
    for(i = 0; i<classHomes.length;i++){
        if(classHomes[i].parentNode.getAttribute("class")==='jtable-title-text')
            classHomes[i].innerHTML = 'Hem: ' + records[0].LongHomeName;
        else
            if(records[0].HomeId>0)
                classHomes[i].innerHTML = records[0].LongHomeName;
            else
                classHomes[i].innerHTML = ' Inget hem';
    }                                                                                        
}

function _updateCalendarVisability(records){
    var calendarVisabilityClassName = _getCalendarVisabilityClassName(records[0].PersonId);
    var classCalendarVisability = document.getElementsByClassName(calendarVisabilityClassName);
    for(i = 0; i<classCalendarVisability.length;i++)
        classCalendarVisability[i].innerHTML = _getVisabilityDisplayValue(records[0].VisibleInCalendar);
}

function _updateDateOfMembershipEnd(records){
    var dateOfMembershipEndClassName = _getDateOfMembershipEndClassName(records[0].PersonId);
    var classDateOfMembershipEnd = document.getElementsByClassName(dateOfMembershipEndClassName);
    for(i = 0; i<classDateOfMembershipEnd.length;i++)
        classDateOfMembershipEnd[i].innerHTML = _parseDate(records[0].DateOfMembershipEnd);
}

function _getVisabilityDisplayValue(id){
    if(id==='1')
        return NOT_VISIBLE;
    else if(id==='2')
        return VISIBLE;
    else
        return '';                      
}

function _updateResidents(records){
    var residentsClassName = _getResidentsClassName(records[0].oldHomeId);
    var classResidents = document.getElementsByClassName(residentsClassName);
    for(i = 0; i<classResidents.length;i++)
        classResidents[i].innerHTML = records[0].Residents;
}

function _updateMemberState(records){
    var memberStateClassName = _getMemberStateClassName(records[0].PersonId);
    var classMemeberState = document.getElementsByClassName(memberStateClassName);
    for(i = 0; i<classMemeberState.length;i++)
        classMemeberState[i].innerHTML = records[0].MemberState;                                                
}

function _getCommentClassName(PersonId){
    return 'people Comment_' + PersonId;
}

function _getDateOfMembershipEndClassName(PersonId){
    return 'people DateOfMembershipEnd_' + PersonId;
}

function _getHomePersonClassName(HomeId, PersonId){
    return 'home home_' + HomeId + ' PersonId_' + PersonId;
}

function _getCalendarVisabilityClassName(PersonId){
    return 'people visible_' + PersonId;
}

function _getEmailClassName(PersonId){
    return 'email_' + PersonId;
}

function _getPersonClassName(PersonId){
    return 'home personid_' + PersonId;
}

function _getNameClassName(PersonId){
    return 'home name_' + PersonId;
}

function _getMemberStateClassName(PersonId){
    return 'member memberstate_' + PersonId;
}

function _getHomeClassName(HomeId){
    return 'home homeid_' + HomeId;
}

function _getResidentsClassName(HomeId){
    return 'home residents_' + HomeId;
}

function _getPhoneClassName(HomeId){
    return 'home phone_' + HomeId;
}

function _getBaptistConcregationClassName(PersonId){
    return 'baptism congregationofbaptism_' + PersonId;
}

function _getMailLink(mail, PersonId){
    if(mail!==null)
        return '<p class="mailLink"><a href="mailto:' + mail + '">' + mail + '</a></p>';
    else
        return '<p class="mailLink ' + _getEmailClassName(PersonId) + '"></p>';
}


function _formatNumericString(number) {
    if(number!==null)
        return '<p class="numericString">' +  number + '</p>';
    else
        return '';
}

function _formatKeyValue(str) {
    if(str!==null)
        return '<p class="keyValue">' +  str + '</p>';
    else
        return '';
}


function _parseDate (dateString, keyValue) {
if(keyValue)
    classNames ="dateString keyValue";
else
    classNames ="dateString";

if (dateString===null){
    return '';
}
if (typeof dateString === 'undefined'){ 
    return '';
}
if (dateString.indexOf('Date') >= 0) { //Format: /Date(1320259705710)/
    return '<p class="' + classNames + '">' + $.datepicker.formatDate("yy-mm-dd", new Date(parseInt(dateString.substr(6), 10))) + '</p>';
} 
else if (dateString.length === 10) { //Format: 2011-01-01
//    return new Date(parseInt(dateString.substr(0, 4), 10),parseInt(dateString.substr(5, 2), 10) - 1,parseInt(dateString.substr(8, 2), 10));
    return '<p class="' + classNames + '">' + dateString + '</p>';
} 
else if (dateString.length === 19) { //Format: 2011-01-01 20:32:42
    return new Date(parseInt(dateString.substr(0, 4), 10),parseInt(dateString.substr(5, 2), 10) - 1,parseInt(dateString.substr(8, 2, 10)),parseInt(dateString.substr(11, 2), 10),parseInt(dateString.substr(14, 2), 10),parseInt(dateString.substr(17, 2), 10));
} 
else {
    this._logWarn('Given date is not properly formatted: ' + dateString);
    return 'format error!';
    }
}
    
