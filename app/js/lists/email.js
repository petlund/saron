/* global SARON_URI, DATE_FORMAT */
"use strict";

$(document).ready(function () {
    var element = document.getElementById("EMAIL_LIST");
    if(element === null)
        return;

    var head = '<div class="saronSmallText">Mailadresser att kopiera och klistra in i adressfält för hemlig kopia.</div><br>';
    
    
    $.get( '/' + SARON_URI + 'app/web-api/listPeople.php?selection=email', function(text) {
        var data = JSON.parse(text);
        var cnt = data.TotalRecordCount;
        var str = head;
        for(var i = 0; i<cnt; i++){                
            if(data.Records[i].Email !== null)
                if(data.Records[i].Email.includes('@')){
                    str = str + data.Records[i].Email + ', ';
                }
        }
        element.innerHTML = str;
    
    });
});

