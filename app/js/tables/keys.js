var inputFormWidth = '500px';
var inputFormFieldWidth = '480px';

$(document).ready(function () {

    $('#keys').jtable({
        title: 'Nyckelinnehav',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'news_date desc', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php',
            updateAction:   '/' + SARON_URI + 'app/web-api/updateKeyOwner.php'
        },
        fields: {
            PersonId: {
                key: true,
                list: false
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
                edit: false,
                width: '5%',
                type: 'date',
                display: function (data) {
                    return _parseDate  (data.record.DateOfBirth, true);
                }          
            },
            MemberState:{
                edit: false,
                create: false,
                list: true,
                title: 'Status',
                width: '5%',                
            },
            KeyToChurch: {
                edit: true,
                create: true, 
                title: 'Kyrkan',
                width: '5%',
                display: function (data){
                    if(data.record.KeyToChurch === '2')
                        return "<p class='numericString'>Ja</p>";
                    else
                        return '';
                },
                options: {0: '-', 2:'Ja'}
            },
            KeyToExp: {
                edit: true, 
                create: true, 
                title: 'Expedition',
                width: '5%',
                style: 'align: right',
                display: function (data){
                    if(data.record.KeyToExp === '2')
                        return "<p class='numericString'>Ja</p>";
                    else
                        return '';
                },
                options: {0: '-', 2:'Ja'}
            },
            CommentKey: {
                create: false,
                list: true,
                title: 'Not - Nycklar',
                width: '30%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit')
                data.row.find('.jtable-edit-command-button').hide();
            else
                data.row.find('.jtable-delete-command-button').hide();
        },        
        recordsLoaded: function(event, data) {
            if(data.serverResponse.user_role === 'edit'){ 
                $('#KEYS').find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit'){
                data.row[0].style.backgroundColor = "yellow";

                data.form.css('width',inputFormWidth);
                data.form.find('input[name=CommentKey]').css('width',inputFormFieldWidth);
            }
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        }
    });
        //Re-load records when user click 'load records' button.
    $('#search_keys').click(function (e) {
        e.preventDefault();
        filterPeople('keys');
    });

    //Load all records when page is first shown
    $('#search_keys').click();
//    $('#KEYS').jtable('load');
//    $('#KEYS').find('.jtable-toolbar-item-add-record').hide();
});
    
