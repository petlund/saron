/* global saron, 
DATE_FORMAT, PERSON, HOME, PERSON_AND_HOME, OLD_HOME,  
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, NO_HOME, NEW_HOME_ID,
saron.table.total.name, saron.table.total.nameId
*/

"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.total.nameId);
    tablePlaceHolder.jtable(totalTableDef(null, saron.table.total.name, null, null));
    var options = getPostData(null, saron.table.total.name, null, saron.table.total.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);

});

function totalTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.total.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);
    
    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
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
                width: '1%',
                sorting: false,
                display: function (data) {
                    var $imgPdf = $('<img src="' + saron.root.images + 'pdf.png" title="Skapa personakt PDF" />');
                    $imgPdf.click(function () {                        
                        window.open(saron.root.reports + 'DossierReport.php?Id=' + data.record.Id, '_blank');
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
                create: false,
                edit: false,
                display: function(data){
                    if(data.record.Engagement > 0)
                        return "Personen har " + data.record.Engagement + " uppdrag kopplat till sig.";

                    return null;
                }
            },
            KeyToChurch: {
                type: 'hidden'
            },
            KeyToExp: {
                type: 'hidden'
            },
            CommentKey: {
                type: 'hidden'
            },
            Updated:{
                title: 'Uppdaterad',
                width: '5%',
                display: function (data){
                    return getUpdateInfo(data);
                }
            }
        },
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
            addDialogDeleteListener(data);            
        }
    };
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    return tableDef;
}
    

    
