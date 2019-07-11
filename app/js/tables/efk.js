
$(document).ready(function () {

    $('#EFK').jtable({
        title: 'EFK Statistik ' + previousYear(),
            paging: false, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'AgeInterval ASC', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/entities/statistics_EFK.php'
            //createAction: 'create.php',
            //updateAction: '/' + SARON_URI + 'app/entities/updatePersonMemberShip.php'
            //deleteAction: 'delete.php'
        },
        fields: {
            AgeInterval: {
                title: 'Åldersgrupp',
                width: '15%',
                key: true
            },
            Amount: {
                title: 'Antal medlemmar',
                width: '15%',
                display: function (data){
                    return "<p class='numericString'>" + data.record.Amount + "</p>";
                }
            },
            Dummy: {
                title: '',
                width: '70%',
                sorting:false 
            }
        }
    });
 //Re-load records when user click 'load records' button.
        $('#EFK').click(function (e) {
            e.preventDefault();
            $('#EFK').jtable('load');
        });
 
        //Load all records when page is first shown
        $('#EFK').click();
});
    
function previousYear (){
    var d = new Date();
    return d.getFullYear() - 1;
} 