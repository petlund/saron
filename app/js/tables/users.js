
$(document).ready(function () {

    $('#USERS').jtable({
        title: 'Användare av Saron',
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: false,
            defaultSorting: 'AgeInterval ASC', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/entities/listUsers.php'
            //createAction: 'create.php',
            //updateAction: '/' + SARON_URI + 'app/entities/updatePersonMemberShip.php'
            //deleteAction: 'delete.php'
        },
        fields: {
            id: {
                key: true,
                list: false
            },
            display_name: {
                title: 'Namn',
                width: '25%',
            },
            user_login: {
                title: 'Användare',
                width: '25%',
            },
            user_email: {
                title: 'Mail',
                width: '25%',
                display: function (data) {
                    return _getMailLink(data.record.user_email, 0);
                }
            },
            saron_reader: {
                title: 'Läsanvändare',
                width: '15%',
                display: function (data){
                    if(data.record.saron_reader === 1)
                        return "Ja";
                    else
                        return "";
                }
            },
            saron_updater: {
                title: 'Uppdaterare',
                width: '15%',
                display: function (data){
                    if(data.record.saron_updater === 1)
                        return "Ja";
                    else
                        return "";
                }
            },
            Dummy: {
                title: '',
                width: '50%',
                sorting:false 
            }
        }
    });
 //Re-load records when user click 'load records' button.
        $('#USERS').click(function (e) {
            e.preventDefault();
            $('#USERS').jtable('load');
        });
 
        //Load all records when page is first shown
        $('#USERS').click();
});
    
