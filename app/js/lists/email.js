/* global saron, DATE_FORMAT */
"use strict";

$(document).ready(function () {
    var element = document.getElementById("EMAIL_LIST");
    if(element === null)
        return;

    
    
    $.get( '/' + saron.uri.saron + 'app/web-api/listPeople.php?ResultType=' + saron.responsetype.records + '&TableViewId=' + saron.list.email.viewid, function(text) {
        var data = JSON.parse(text);
        var cnt = data.Records.length;
        var head = '<div class="saronAugdText">Mailadresser att kopiera och klistra in i adressfält för hemlig kopia. (' + cnt + ' st.)</div><br>';
        var str = head;
        for(var i = 0; i<cnt; i++)                
            str = str + data.Records[i].Email + ', ';

        element.innerHTML = str;
    
    });
});

