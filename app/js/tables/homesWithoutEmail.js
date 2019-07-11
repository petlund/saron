
$(document).ready(function () {

    $('#Homes').jtable({
        title: 'Hem utan mailutskick',
        paging: true, //Enable paging
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: "LongHomeName ASC",
        actions: {
            listAction:   '/' + SARON_URI + 'app/entities/listHomesWithoutEmail.php',
            //createAction: 'create.php',
            updateAction: '/' + SARON_URI + 'app/entities/updateHomesWithoutEmail.php'
            //deleteAction: 'delete.php'
        },
        fields: {
            HomeId: {
                list: false,
                edit: false,
                key: true
            },
            LongHomeName: {
                title: 'Hem',
                width: '15%',
                edit: false
            },
            Residents: {
                title: 'Boende på adressen',
                width: '25%',
                edit: false
            },
            Phone: {
                title: 'Telefon',
                width: '10%',
                edit: false,
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
                width: '10%'
            },
            Dummy: {
                title: '',
                edit: false,
                width: '40%'
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
         },
         formClosed: function (event, data){
             data.row[0].style.backgroundColor = '';
         }                        

    });
 //Re-load records when user click 'load records' button.
        $('#Homes2').click(function (e) {
            e.preventDefault();
            $('#Homes').jtable('load');
        });
 
        //Load all records when page is first shown
        $('#Homes2').click();
});
