var inputFormWidth = '500px';
var inputFormFieldWidth = '480px';

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
                edit: true
            },
            Co: {
                title: 'Co',
                width: '10%',
                edit: true
            },
            Address: {
                title: 'Adress',
                width: '10%',
                edit: true
            },
            Zip: {
                title: 'PA',
                width: '5%',
                edit: true
            },
            City: {
                title: 'Stad',
                width: '10%',
                edit: true
            },
            Country: {
                title: 'Land',
                width: '10%',
                edit: true
            },
            Residents: {
                title: 'Boende på adressen',
                width: '15%',
                edit: false
            },
            Phone: {
                title: 'Telefon',
                width: '10%',
                edit: true,
                display: function (data) {
                    if(data.record.Phone!==null)
                        return '<p class="numericString">' +  data.record.Phone + '</p>';
                    else
                        return '<p class="numericString"></p>';
                }                       
            },
            Letter: {
                title: 'Brevutskick',
                inputTitle: 'Församlingspost via brev',
                edit: true,
                options:{ 0 : '', 1 : 'Ja'},
                width: '5%'
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
            for (i=0; i<dbox.length; i++)
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