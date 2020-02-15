"use strict";
    
$(document).ready(function () {

    $('#NEWS').jtable({
        title: 'Nyheter',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'news_date desc', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listNews.php',
            createAction:   '/' + SARON_URI + 'app/web-api/createNews.php',
            //updateAction:   '/' + SARON_URI + 'app/web-api/updateNews.php'
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: '/' + SARON_URI + 'app/web-api/updateNews.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result !== 'ERROR'){
                                var records = data['Records'];
                                //Update news
                                _updateNewsRecord(records);
                            }
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            deleteAction: '/' + SARON_URI + 'app/web-api/deleteNews.php',
        },
        fields: {
            id: {
                key: true,
                list: false
            },
            news_date: {
                edit: false,
                create: false, 
                title: 'Datum',
                width: '15%',
                type: 'date',
                displayFormat: 'yy-mm-dd'
            },
            information: {
                title: 'Information',
                width: '70%'
            },
            writer: {
                edit: false,
                create: false, 
                title: 'Skribent',
                width: '15%'
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
                $('#NEWS').find('.jtable-toolbar-item-add-record').show();
            }
        },        
        formCreated: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = "yellow";

            data.form.css('width','600px');
            data.form.find('input[name=information]').css('width','580px');
        },
        formClosed: function (event, data){
            if(data.formType === 'edit')
                data.row[0].style.backgroundColor = '';
        },
        deleteFormCreated: function (event, data){
            data.row[0].style.backgroundColor = 'red';
        },
        deleteFormClosed: function (event, data){
            data.row[0].style.backgroundColor = '';
        }
    });
    $('#NEWS').jtable('load');
    $('#NEWS').find('.jtable-toolbar-item-add-record').hide();
});
    
function _updateNewsRecord(records){
    var key = document.getElementsByClassName("jtable-data-row");
    if(key===null)
        return;
    
    for(var i = 0; i<key.length;i++){
        if(key[i].dataset.recordKey === records[0].id){ 
            key[i].cells[0].innerHTML = (records[0].news_date).substring(0,10);                                              
            key[i].cells[2].innerHTML = records[0].writer;          
        }
    }
}
