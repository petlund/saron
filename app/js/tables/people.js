/* global PERSON, HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME */

"use strict";

$(document).ready(function () {
    localStorage.clear();
    localStorage.setItem('newHomeId', -1);
    
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
            createAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/createPerson.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            refreschTableAndSetViewLatestUpdate();                            
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
                                localStorage.setItem('newHomeId', data.Records[0].HomeId);

                                _closeEmptyOldHome(data.Records[0], "Home", OLD_HOME);

                                $dfd.resolve(data);

                                _updateFields(data.Records[0], "LongHomeName", HOME);                                                
                                _updateFields(data.Records[0], "Residents", HOME);                                                
                                _updateFields(data.Records[0], "Phone", HOME);                                                
                                _updateFields(data.Records[0], "Residents", OLD_HOME);                                                
                                _updateFields(data.Records[0], "Name", PERSON);                                                
                                _updateFields(data.Records[0], "DateOfBirth", PERSON);                                                
                                _updateFields(data.Records[0], "DateOfMembershipEnd", PERSON);                                                
                                _updateFields(data.Records[0], "MemberState", PERSON);                                                
                                _updateFields(data.Records[0], "VisibleInCalendar", PERSON);                                                
                                _updateFields(data.Records[0], "Comment", PERSON);                                                
                                _updateFields(data.Records[0], "Mobile", PERSON);
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
            Home: {
                title: '',
                width: '1%',
                sorting: false,
                edit: false,
                create: false,
                delete: false,
                display: function (data) {
                    var src = '"/' + SARON_URI + 'app/images/home.png" title="Adressuppgifter"';
                    var imgTag = _setImageClass(data.record, "Home", src, HOME);
                    var imgHome = $(imgTag);
                    var newHomeId = -1;
                    
                    if(data.record.HomeId === "-1")
                        newHomeId = localStorage.getItem('newHomeId');
                    else
                        newHomeId = data.record.HomeId;
                    
                    imgHome.click(function () {
                        $('#people').jtable('openChildTable', imgHome.closest('tr'),{
                            title: _setClassAndValue(data.record, "LongHomeName", HOME),                            
                            showCloseButton: false,
                            actions: {
                                listAction: '/' + SARON_URI + 'app/web-api/listHome.php?HomeId=' + newHomeId,                                
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: '/' + SARON_URI + 'app/web-api/updateHome.php?HomeId=' + newHomeId,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                if(data.Result !== 'ERROR'){

                                                    $dfd.resolve(data);
                                                    for(var field in data.Records[0])
                                                        _updateFields(data.Records[0], field, HOME);                                                
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
                                CloseChild: {
                                    title: '',
                                    width: '1%',
                                    sorting: false,
                                    edit: false,
                                    create: false,
                                    delete: false,
                                    display: function () {
                                        var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'cross.png" title="Stäng"';
                                        var imgTag = _setImageClass(data.record, "CloseChild", src, HOME);
                                        var imgObj = $(imgTag);                    
                                        
                                        imgObj.click(function () {
                                             $('#people').jtable('closeChildTable', $(imgHome.closest('tr')));
                                        });                
                                        return imgObj;
                                    }
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
                                    options: _letterOptions()
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
                    if(Math.abs(data.record.HomeId) > 0){
                        return imgHome;
                    }
                    else{
                        return null; 
                    }
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
                display: function (data) {                    
                    var $imgMember = $('<img src="/' + SARON_URI + 'app/images/member.png" title="Medlemsuppgifter" />');
                    $imgMember.click(function () {
                        $('#people').jtable('openChildTable', $imgMember.closest('tr'),{
                            title: _setClassAndValuePrefix(data.record, "Name", PERSON, "Medlemsuppgifter för: "),
                            showCloseButton: false,
                            actions: {
                                listAction: '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=' + data.record.PersonId,                                           
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=membership&PersonId=' + data.record.PersonId,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                if(data.Result !== 'ERROR'){                                                
                                                    $dfd.resolve(data);
                                                    _updateFields(data.Records[0], "MemberState", PERSON);                                                
                                                    _updateFields(data.Records[0], "VisibleInCalendar", PERSON);                                                
                                                    _updateFields(data.Records[0], "DateOfMembershipStart", PERSON);                                                
                                                    _updateFields(data.Records[0], "DateOfMembershipEnd", PERSON);                                                
                                                    _updateFields(data.Records[0], "Residents", HOME);                                                
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
                                    width: '20%',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "PreviousCongregation", PERSON);
                                    }
                                },
                                DateOfMembershipStart: {
                                    width: '7%',     
                                    type: 'date',
                                    title: 'Start',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "DateOfMembershipStart", PERSON);
                                    }
                                },
                                MembershipNo: {
                                    title: 'Nr.',
                                    width: '3%',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "MembershipNo", PERSON);
                                    }, 
                                    options: function(data){
                                        if(clearMembershipNoOptionCache){
                                            data.clearCache();
                                            clearMembershipNoOptionCache=false;
                                        }
                                        return '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=' + data.record.PersonId + '&selection=nextMembershipNo';
                                    }
                                },
                                DateOfMembershipEnd: {
                                    width: '7%',
                                    type: 'date',
                                    title: 'Avslut',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "DateOfMembershipEnd", PERSON);
                                    } 
                                },
                                NextCongregation: {
                                    width: '20%',
                                    title: 'Flyttat till församling',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "NextCongregation", PERSON);
                                    } 
                                },
                                VisibleInCalendar: {
                                    edit: 'true',
                                    list: true,
                                    title: 'Kalender',
                                    inputTitle: 'Synlig i adresskalendern',
                                    width: '4%',
                                    inputClass: function(data){
                                        return _setClassAndValue(data.record, "VisibleInCalendar", PERSON);
                                    },
                                    options:_visibilityOptions()
                                },
                                Comment: {
                                    type: 'textarea',
                                    width: '40%',
                                    title: 'Not',
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
                display: function (data) {
                    //Create an image that will be used to open child table
                    var $imgBaptist = $('<img src="/' + SARON_URI + 'app/images/baptist.png" title="Dopuppgifter" />');
                    //Open child table when user clicks the image
                    $imgBaptist.click(function () {
                        $('#people').jtable('openChildTable', $imgBaptist.closest('tr'),{
                            title: _setClassAndValuePrefix(data.record, "Name", PERSON, "Dopuppgifter för: "),
                            showCloseButton: false,                                    
                            actions: {
                                listAction: '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=' + data.record.PersonId,                                           
                                updateAction: function(postData) {
                                    return $.Deferred(function ($dfd) {
                                        $.ajax({
                                            url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=baptism&PersonId=' + data.record.PersonId,
                                            type: 'POST',
                                            dataType: 'json',
                                            data: postData,
                                            success: function (data) {
                                                if(data.Result !== 'ERROR'){
                                                    
                                                    $dfd.resolve(data);
                                                    _updateFields(data.Records[0], "DateOfBaptism", PERSON);                                                
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
                                CloseChild: {
                                   title: '',
                                   width: '1%',
                                   sorting: false,
                                   edit: false,
                                   create: false,
                                   delete: false,
                                   display: function (data) {
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
                                    display: function (data){
                                        return _setClassAndValue(data.record, "CongregationOfBaptism", PERSON);
                                    }       
                                },
                                DateOfBaptism: {
                                    width: '7%',
                                    type: 'date',       
                                    title: 'Dopdatum',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "DateOfBaptism", PERSON);
                                    }
                                },
                                Baptister: {
                                    width: '20%',
                                    title: 'Dopförrättare',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "Baptister", PERSON);
                                    }       
                                },
                                Comment: {
                                    type: 'textarea',
                                    width: '35%',
                                    title: 'Not',
                                    display: function (data){
                                        return _setClassAndValue(data.record, "Comment", PERSON);
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
                                data.form.find('select[name=CongregationOfBaptismThis]').change(function () {
                                    baptistFormAuto(data, this.value);
                                });

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
                display: function (data){
                    return _setClassAndValueAltNull(data.record, "LongHomeName", NO_HOME, HOME);
                },
                options: function(data){
                    if(data.source !== 'list')
                        data.clearCache();
                    return '/' + SARON_URI + 'app/web-api/listHomes.php?selection=options';
                }
            },
            OldHomeId: {
                list: false,
                create: false,
                edit: true,
                type: 'hidden',
                defaultValue: function (data){
                    if(data.record.HomeId > 0 && data.record.HomeId < 0)
                       return data.record.HomeId;
                   
                    return "0";
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
                display: function (data){
                    return _setClassAndValue(data.record, "Name", PERSON);
                }                 
            },
            DateOfBirth: {
                title: 'Född',
                width: '5%',
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
                    return '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=null' + '&selection=nextMembershipNo';
                }
            },
            MemberState: {
                title: 'Status',
                edit: false,
                create: false,
                width: '4%',
                display: function (data){
                    return _setClassAndValue(data.record, "MemberState", PERSON);
                }       
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
                $('#people').find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            var headLine;
            if(data.formType === 'edit'){
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

    
