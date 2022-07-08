/* global saron, DATE_FORMAT */
"use strict";

$(document).ready(function () {
    var element = document.getElementById(saron.list.email.name);
    if(element === null)
        return;

    
    var url = {url: saron.root.webapi + 'listStatistics.php'};
    var postData = getPostData(null, saron.list.email.name, null, saron.graph.timeseries.name, saron.source.list, saron.responsetype.records);
    $.post(url, postData
    ).then(function(json) {    
        var data = JSON.parse(json);
        var cnt = data.Records.length;
        var head = '<div class="saronAugdText">Mailadresser att kopiera och klistra in i adressfält för hemlig kopia. (' + cnt + ' st.)</div><br>';
        var str = head;
        for(var i = 0; i<cnt; i++)                
            str = str + data.Records[i].Email + ', ';

        element.innerHTML = str;
    
    });
});

