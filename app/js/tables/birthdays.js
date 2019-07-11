$(document).ready(function () {

    $('#birthdays').jtable({
        title: 'Födelsedagar',
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'NextBirthday ASC', //Set default sorting        
        actions: {
            listAction:   '/' + SARON_URI + 'app/entities/listPeople.php',
        },
        
        fields: {
            PersonId: {
                title: 'id',
                list: false,
                key: true
                
            },
            Name: {
                title: 'Namn',
                width: '15%',
                display: function(data){
                    return '<p class="keyValue">' + data.record.Name + '</p>'                    
                }
            },
            DateOfBirth: {
                title: 'Född',
                width: '7%',
                type: 'date',
                display: function(data){
                    return '<p class="keyValue dateString">' + data.record.DateOfBirth + '</p>'                    
                }
            },
            Age: {
                title: 'Blir',
                width: '5%',
                display: function(data){
                    return '<p class="numericString">' + data.record.Age + '</p>'                    
                }
            },
            NextBirthday: {
                title: 'När',
                width: '7%',
                type: 'date',
                display: function(data){
                    return '<p class="dateString">' + data.record.NextBirthday + '</p>'                    
                }
            },
            MemberState: {
                title: 'Status',               
                width: '5%'
            },
            Contact:{
                title: 'Kontaktuppgifter',
                width: '50%'               
            }
        }
    });
 //Re-load records when user click 'load records' button.
        $('#search_birthdays').click(function (e) {
            e.preventDefault();
            filterPeople('birthdays');
        });
 
        //Load all records when page is first shown
        $('#search_birthdays').click();
});
    
