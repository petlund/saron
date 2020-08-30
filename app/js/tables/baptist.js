/* global DATE_FORMAT, PERSON, SARON_URI, inputFormWidth, inputFormFieldWidth */
"use strict";

$(document).ready(function () {
  
    $('#baptist').jtable({
        title: 'Dopuppgifter',
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'FamilyName ASC, DateOfBirthr ASC', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php', 
            updateAction: function(data) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=baptism',
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result !== 'ERROR'){
                                _updateFields(data.Record[0], "DateOfBaptism", PERSON);                                                
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
            PersonId: {
                key: true,
                list: false,
                update: false
            },
            Name: {
                title: 'Namn',
                width: '15%',
                edit: false,
                display: function (data){
                    return _setClassAndValue(data.record, "Name", PERSON);
                }       
            },
            DateOfBirth: {
                title: 'Född',
                width: '7%',
                type: 'date',
                //displayFormat: DATE_FORMAT,
                edit: false,
                display: function (data){
                    return _setClassAndValue(data.record, "DateOfBirth", PERSON);
                }       
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
                title: 'Dopförsamling'
            },
            DateOfBaptism: {
                width: '7%',
                //displayFormat: DATE_FORMAT,
                type: 'date',
                title: 'Dopdatum',
                display: function (data){
                    return _setClassAndValue(data.record, "DateOfBaptism", PERSON);
                }       
            },
            Baptister: {
                width: '15%',
                title: 'Dopförrättare'
            },
            MemberState:{
                title: 'Status',
                width: '7%',
                edit: false
            },
            Comment: {
                width: '34%',
                title: 'Not',
                type: 'textarea'
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
            data.form.css('width', inputFormWidth);
            data.form.find('input[name=Baptister]').css('width',inputFormFieldWidth);
            data.form.find('input[name=CongregationOfBaptism]').css('width',inputFormFieldWidth);
            data.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);
            data.form.find('select[name=CongregationOfBaptismThis]').change(function () {baptistFormAuto(data, this.value)});

            var dbox = document.getElementsByClassName('ui-dialog-title');            
            for(var i=0; i<dbox.length; i++)
                dbox[i].innerHTML='Uppdatera uppgifter för: ' + data.record.FirstName + ' ' + data.record.LastName;
        },
        formClosed: function (event, data){
            data.row[0].style.backgroundColor = '';
        }
    });
    //Re-load records when user click 'load records' button.
    $('#search_baptist').click(function (e) {
        e.preventDefault();
        filterPeople('baptist');
    });

    //Load all records when page is first shown
    $('#search_baptist').click();
});

                                
    
