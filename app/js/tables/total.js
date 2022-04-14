/* global saron, 
DATE_FORMAT, PERSON, HOME, PERSON_AND_HOME, OLD_HOME,  
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
saron.table.total.name, saron.table.total.viewid
*/

"use strict";
const totalListUri = 'app/web-api/listPeople.php';
$(document).ready(function () {
    var mainTableViewId = saron.table.total.viewid;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(totalTableDef(saron.table.total.viewid, null));
    var options = getPostData(null, mainTableViewId, null, saron.table.total.name, saron.source.list, saron.responsetype.records, totalListUri);
    tablePlaceHolder.jtable('load', options);

});

function totalTableDef(tableViewId, tablePath, newTableTitle, parentId){
    var tableName = saron.table.total.name;
    var title = 'Översikt per person';
    if(newTableTitle !== null)
        title = newTableTitle; 

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
            message+='<br>Eventuellt medlemskap avslutas vid anonymisering.';
            message+='<br><B>KAN INTE ÅNGRAS.</B>';
            data.deleteConfirmMessage = message;
        },         
        actions: {
            listAction:   '/' + saron.uri.saron + totalListUri,            
            deleteAction: '/' + saron.uri.saron + 'app/web-api/updatePerson.php?selection=anonymization&TablePath=' + tableName
        },  
        fields: {
            Id:{
                key: true,
                list: false
            },
            ParentId:{
                defaultValue: parentId,
                type: 'hidden'
            },
            TablePath:{
                type: 'hidden',
                defaultValue: tableName
            },
            PDF: {
                title: 'Pdf',
                width: '5%',
                sorting: false,
                display: function (data) {
                    var $imgPdf = $('<img src="/' + saron.uri.saron + 'app/images/pdf.png" title="Skapa personakt PDF" />');
                    $imgPdf.click(function () {                        
                        window.open('/' + saron.uri.saron + 'app/pdf/DossierReport.php?Id=' + data.record.Id, '_blank');
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
            },
            Engagement: {
                title: 'Förtroendeuppdrag',
                width: '15%',
                display: function(data){
                    if(data.record.Engagement > 0)
                        return "Personen har " + data.record.Engagement + " uppdrag kopplat till sig.";

                    return null;
                }
            }
        },
        rowInserted: function(event, data){
            if (data.record.user_role !== saron.formtype.edit){
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

    
