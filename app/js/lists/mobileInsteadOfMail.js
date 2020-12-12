/* global SARON_URI, DATE_FORMAT */
"use strict";

$(document).ready(function () {
    var element = document.getElementById("MOBILE_INSTEAD_OF_EMAIL");
    if(element === null)
        return;

        var head1 = '<div class="saronSmallText">Mobilnummer till personer utan mail.</div><br>';
        var head2 = '<br><br><br><br><div class="saronSmallText">Samma nummer med namn.</div><br>';

        $.get( '/' + SARON_URI + 'app/web-api/listPeople.php?selection=mobileInsteadOfMail', function(text) {
        var data = JSON.parse(text);
        var cnt = data.TotalRecordCount;
        var str = head1;
        for(var i = 0; i<cnt; i++){                
            str = str + data.Records[i].Mobile + ', ';
        }
        
        str = str + head2;
        
        for(var i = 0; i<cnt; i++){                
            str = str + data.Records[i].Name_FL + ": " + data.Records[i].Mobile + '<br>';
        }

        element.innerHTML = str;
    
    });
});

