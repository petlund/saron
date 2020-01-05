var inputFormWidth = '500px';
var inputFormFieldWidth = '480px';

$(document).ready(function () {

    $('#member').jtable({
        title: 'Medlemsuppgifter',
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'FamilyName ASC, DateOfBirthr ASC', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php',
            //createAction: 'create.php',
            //updateAction: '/' + SARON_URI + 'app/web-api/updatePersonMemberShip.php'
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updatePersonMembership.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result !== 'ERROR'){
                                var records = data['Records'];
                                _updateMemberState(records);
                                _updateCalendarVisability(records);                                               
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            }
            //deleteAction: 'delete.php'
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
                    return '<p class="keyValue">' + data.record.Name + '</p>'                    
                }
            },
            DateOfBirth: { 
                title: 'Född',
                width: '7%',
                edit: false,
                type: 'date',
                display: function(data){
                    return '<p class="keyValue dateString">' + data.record.DateOfBirth + '</p>'                    
                },
            },
            PreviousCongregation: {
                title: 'Kommit från församling',
                width: '15%'
            },
            DateOfMembershipStart: {
                width: '7%',
                display: function (data) {
                    return _parseDate(data.record.DateOfMembershipStart);
                },              
                type: 'date',
                title: 'Start'
            },
            MembershipNo: {
                width: '3%',
                display: function (data) {
                    if(data.record.MembershipNo>0)
                        return '<p class="numericString">' + data.record.MembershipNo + '</p>';
                    else
                        return '<p class="numericString"></p>';
                },          
                options: function (data){
                    return '/' + SARON_URI + 'app/web-api/listNextMembershipNo.php?PersonId=' + data.record.PersonId;
                },                                            
                title: 'Nr.'
            },
            DateOfMembershipEnd: {
                display: function (data) {
                    return _parseDate(data.record.DateOfMembershipEnd);
                },  
                options: {0: 'A', 1: 'B'}, 
                width: '7%',
                type: 'date',
                title: 'Avslut'
            },
            NextCongregation: {
                width: '15%',
                title: 'Flyttat till församling'
            },
            MemberState:{
                width: '7',
                edit: false,
                title: 'Status',
                display: function (memberData){
                    return '<p class="' + _getMemberStateClassName(memberData.record.PersonId) + '">' +  memberData.record.MemberState + '</p>';                    
                }
            },
            VisibleInCalendar: {
                edit: 'true',
                title: 'Kalender',
                inputTitle: 'Synlig i adresskalendern',
                width: '4%',
                options:{ 0: '', 1: 'Ej synlig', 2: 'Synlig'}
            },
            Comment: {
                type: 'textarea',
                width: '46%',
                title: 'Not'
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
            data.row[0].style.backgroundColor = '';
        }    
    });
    //Re-load records when user click 'load records' button.
    $('#search_member').click(function (e) {
        e.preventDefault();
        filterPeople('member');
    });

    //Load all records when page is first shown
    $('#search_member').click();
});
    
