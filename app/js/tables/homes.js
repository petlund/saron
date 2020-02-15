"use strict";

$(document).ready(function () {

    $('#homes').jtable({
        title: 'Hem',
        paging: true, //Enable paging
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: "FamilyName ASC",
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listHomes.php',
            //createAction: 'create.php',
            updateAction: '/' + SARON_URI + 'app/web-api/updateHome.php'
            //deleteAction: 'delete.php'
        },
        fields: {
            HomeId: {
                list: false,
                key: true
            },
            FamilyName: {
                title: 'Hem',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data.record, "FamilyName", PERSON);
                }       
            },
            Co: {
                title: 'Co',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data.record, "Co", PERSON);
                }       
            },
            Address: {
                title: 'Adress',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data.record, "Address", PERSON);
                }       
            },
            Zip: {
                title: 'PA',
                width: '5%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data.record, "Zip", PERSON);
                }       
            },
            Country: {
                title: 'Stad',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data.record, "Country", PERSON);
                }       
            },
            Residents: {
                title: 'Boende på adressen',
                width: '15%',
                edit: false,
                display: function (data){
                    return _setClassAndValue(data.record, "Residents", PERSON);
                }       
            },
            Phone: {
                title: 'Telefon',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data.record, "Phone", PERSON);
                }                   
            },
            Letter: {
                title: 'Brevutskick',
                inputTitle: 'Församlingspost via brev',
                edit: true,
                width: '5%',
                options:_letterOptions()
            },
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
             data.row[0].style.backgroundColor = '';
         }                        

    });
    //Re-load records when user click 'load records' button.
    $('#search_homes').click(function (e) {
        e.preventDefault();
        filterHomes('homes');
    });
    //Load all records when page is first shown
    $('#search_homes').click();
    $('#homes').find('.jtable-toolbar-item-add-record').hide();
});


function filterHomes(viewId){
    $('#' + viewId).jtable('load', {
        searchString: $('#searchString').val(),
        groupId: $('#groupId').val(),
        tableview: viewId
    });
}