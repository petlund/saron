var inputFormWidth = '500px';
var inputFormFieldWidth = '480px';

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
            listAction:   '/' + SARON_URI + 'app/entities/listPeople.php', 
            //createAction: 
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/entities/updatePersonBaptist.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result !== 'ERROR'){
                                var records = data['Records'];
                                _updateMemberState(records);
                            }
                            
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            }
            //deleteAction: 
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
                display: function(data){
                    return '<p class="keyValue">' + data.record.Name + '</p>';                    
                }
            },
            DateOfBirth: {
                title: 'Född',
                width: '7%',
                type: 'date',
                edit: false,
                display: function(data){
                    return '<p class="keyValue dateString">' + data.record.DateOfBirth + '</p>'                    
                }
            },
            CongregationOfBaptismThis: {
                list: false,
                title: 'Döpt',
                //options: '/' + SARON_URI + 'app/entities/listBaptismOptions.php'
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
                title: 'Dopdatum'
            },
            Baptister: {
                width: '15%',
                title: 'Dopförrättare'
            },
            MemberState:{
                title: 'Status',
                width: '7%',
                edit: false,
                display: function (memberData){
                    return '<p class="' + _getMemberStateClassName(memberData.record.PersonId) + '">' +  memberData.record.MemberState + '</p>';                    
                }
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
            for (i=0; i<dbox.length; i++)
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

function baptistFormAuto(data, selectedValue){
        var inp = data.form.find('input[name=CongregationOfBaptism]');
        if(selectedValue === '0'){
            inp[0].value = "";                                      
            inp[0].disabled=true;
        }
        else if(selectedValue === '1'){
            inp[0].value = "";                                                                              
            inp[0].disabled=false;
        }
        else{
            inp[0].value = FullNameOfCongregation; //see util/js.php
            inp[0].disabled=true;
        }
    }
                                
    
