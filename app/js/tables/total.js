/* global DATE_FORMAT, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID */

"use strict";

$(document).ready(function () {
    const J_TABLE_ID = '#total';

    $(J_TABLE_ID).jtable(totalTableDef(J_TABLE_ID));
    $(J_TABLE_ID).jtable('load');
    $(J_TABLE_ID).find('.jtable-toolbar-item-add-record').hide();

});

function totalTableDef(placeHolder){
    return{
        title: 'Översikt per person',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'Person ASC', //Set default sorting        
        deleteConfirmation: function(data) {
            var message = ' Vill du anonymisera data om:<br>' + data.record.Person + '?';
            message+='<br><br><b>OBS!</b>';
            message+='<br>Om du kommit överens med medlemmen ';
            message+='om att lagra dopuppgifter i pärmverket ';
            message+='för dopuppgifter behöver du göra en utskrift för underskrift ';
            message+='innan anonymisering!';
            message+='<br>Medlemskapet avslutas vid anonymisering.';
            message+='<br><B>KAN INTE ÅNGRAS.</B>';
            data.deleteConfirmMessage = message;
        },         
        actions: {
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php?tableview=total',            
            deleteAction: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=anonymization'
        },  
        fields: {
            PersonId:{
                key: true,
                list: false
            },
            PDF: {
                title: 'Pdf',
                width: '5%',
                sorting: false,
                display: function (data) {
                    var $imgPdf = $('<img src="/' + SARON_URI + 'app/images/pdf.png" title="Skapa personakt PDF" />');
                    $imgPdf.click(function () {                        
                        window.open('/' + SARON_URI + 'app/pdf/DossierReport.php?PersonId=' + data.record.PersonId, '_blank');
                    });                
                return $imgPdf;
                }
            },
            Person: {
                title: 'Personuppgifter',
                width: '10%'
            },
            Membership: {
                title: 'Medlemsuppgifter',
                width: '15%'
            },
            Baptist: { 
                title: 'Dopuppgifter',
                width: '15%'
            },
            Contact: {
                title: 'Kontaktuppgifter',
                width: '15%'
            },
            Other: {
                title: 'Övriga uppgifter',
                width: '15%'
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== 'edit'){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
            addDialogDeleteListener(data);            
        }
    };
}
    
// //Re-load records when user click 'load records' button.
//        $('#search_total').click(function (e) {
//            e.preventDefault();            
//            $('#total').jtable('load', {
//                searchString: $('#searchString').val(),
//                groupId: $('#groupId').val(),
//                tableview: "total"
//            });
//        });
//        //Load all records when page is first shown
//        $('#search_total').click();

    var deleteButtons = document.getElementsByClassName('jtable-delete-command-button');
    for(var i=0; i<deleteButtons.length; i++){
        deleteButtons[i].addEventListener("click", deleteButtonClicked(this),false);
        deleteButtons[i].attr('onclick', 'deleteButtonClicked(this);');
    }

    
