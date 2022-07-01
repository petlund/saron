/* global saron, DATE_FORMAT */
"use strict";

$(document).ready(function () {
    var element = document.getElementById("MOBILE_INSTEAD_OF_EMAIL");
    if(element === null)
        return;

        var head1 = '<div class="saronAugdText">Mobilnummer till personer utan mail.</div><br>';
        var head2 = '<br><br><br><br><div class="saronAugdText">Samma nummer med namn.</div><br>';

        $.get(saron.root.webapi + 'listPeople.php?TableViewId=' + saron.list.mobile_instead_of_email.viewid + '&ResultType=' + saron.responsetype.records, function(text) {
        var data = JSON.parse(text);
        var cnt = data.TotalRecordCount;
        var str = head1;
        for(var i = 0; i<cnt; i++){                
            str += data.Records[i].Mobile + ', ';
        }
        
        str += head2;

        str +=  "<table>";
        for(var i = 0; i<cnt; i++){                
            str +=  "<tr>";
            str += "<td>" + data.Records[i].Name_FL + "</td><td> </td><td style='text-align: right'>"  + data.Records[i].Mobile + '</td>';
            str +=  "</tr>";
        }
        str +=  "</table>";

        element.innerHTML = str;
    
    });
});

