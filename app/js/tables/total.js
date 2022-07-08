/* global saron, 
DATE_FORMAT, PERSON, HOME, PERSON_AND_HOME, OLD_HOME,  
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
saron.table.total.name, saron.table.total.nameId
*/

"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $("#" + saron.table.total.name);
    tablePlaceHolder.jtable(totalTableDef(null, null));
    var options = getPostData(null, saron.table.total.name, null, saron.table.total.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);

});

function totalTableDef(tableTitle, tablePath){
    var tableName = saron.table.total.name;
    var title = 'Översikt per person';

    if(tableTitle !== null)
        title = tableTitle; 
    
    
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 
    
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
            listAction:   saron.root.webapi + 'listPeople.php',            
            deleteAction: saron.root.webapi + 'deletePerson.php'
        },  
        fields: {
            Id:{
                key: true,
                list: false
            },
            ParentId:{
                defaultValue: -1,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.total.name
            },
            PDF: {
                title: 'Pdf',
                width: '5%',
                sorting: false,
                display: function (data) {
                    var $imgPdf = $('<img src="' + saron.root.images + 'pdf.png" title="Skapa personakt PDF" />');
                    $imgPdf.click(function () {                        
                        window.open(saron.root.pdf + 'DossierReport.php?Id=' + data.record.Id, '_blank');
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

    
