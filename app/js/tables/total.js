/* global DATE_FORMAT, PERSON, HOME, PERSON_AND_HOME, OLD_HOME, SARON_URI, SARON_IMAGES_URI, inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID
RECORD, RECORDS, OPTIONS,
TABLE_NAME_TOTAL, TABLE_VIEW_TOTAL
*/

"use strict";

$(document).ready(function () {

    $(TABLE_VIEW_TOTAL).jtable(totalTableDef(TABLE_VIEW_TOTAL, null));
    var options = getPostData(TABLE_VIEW_TOTAL, null, TABLE_NAME_TOTAL, null, RECORDS);
    $(TABLE_VIEW_TOTAL).jtable('load', options);
    $(TABLE_VIEW_TOTAL).find('.jtable-toolbar-item-add-record').hide();

});

function totalTableDef(tableViewId, tableTitle){
    var tableName = TABLE_NAME_HOMES;
    var title = 'Översikt per person';
    if(tableTitle !== null)
        title = tableTitle; 
    
    return{
        title: title,
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
            listAction:   '/' + SARON_URI + 'app/web-api/listPeople.php',            
            deleteAction: '/' + SARON_URI + 'app/web-api/updatePerson.php?selection=anonymization'
        },  
        fields: {
            Id:{
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
                        window.open('/' + SARON_URI + 'app/pdf/DossierReport.php?Id=' + data.record.Id, '_blank');
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
    

    var deleteButtons = document.getElementsByClassName('jtable-delete-command-button');
    for(var i=0; i<deleteButtons.length; i++){
        deleteButtons[i].addEventListener("click", deleteButtonClicked(this),false);
        deleteButtons[i].attr('onclick', 'deleteButtonClicked(this);');
    }

    
