/* global saron, DATE_FORMAT */
"use strict";

$(document).ready(function () {
    var element = document.getElementById(saron.list.email.name);
    if(element === null)
        return;

    
    
    $.get( saron.root.webapi + 'listPeople.php?ResultType=' + saron.responsetype.records + '&TableViewId=' + saron.list.email.nameId, function(text) {
        var data = JSON.parse(text);
        var cnt = data.Records.length;
        var head = '<div class="saronAugdText">Mailadresser att kopiera och klistra in i adressfält för hemlig kopia. (' + cnt + ' st.)</div><br>';
        var str = head;
        for(var i = 0; i<cnt; i++)                
            str = str + data.Records[i].Email + ', ';

        element.innerHTML = str;
    
    });
});

