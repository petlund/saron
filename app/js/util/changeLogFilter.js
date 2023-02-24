/* global saron, DATE_FORMAT 
saron 
 */
"use strict";


$.getJSON(saron.root.webapi + "listChangeLog.php?Field=User&ResultType=Options",function(data){
    var stringToAppend = "";
    $.each(data.Options,function(key,val) {

       stringToAppend += "<option value='" + val.Value + "'>" + val.DisplayText + "</option>";

    });

    $("#uid").html(stringToAppend);
});



$.getJSON(saron.root.webapi + "listChangeLog.php?Field=ChangeType&ResultType=Options",function(data){
    var stringToAppend = "";
    $.each(data.Options,function(key,val) {

       stringToAppend += "<option value='" + val.Value + "'>" + val.DisplayText + "</option>";

    });

    $("#cid").html(stringToAppend);
});

$(document).ready(function () {
    var f = $(".changeLogFilter");
    var eventType = "";
    if(f)
        for(var i = 0; i < f.length; i++){
            eventType = "change";
            
            f[i].addEventListener(eventType, () => changeLogFilter());
        }
});


function changeLogFilter(){
    var urlParams = window.location.search;
    var searchParams = new URLSearchParams(urlParams);
    var appCanvasName = searchParams.get('AppCanvasName');

    var options = {uid: $('#uid').val(), 
                    cid: $('#cid').val(), 
                    AppCanvasName: appCanvasName, 
                    AppCanvasPath: appCanvasName, 
                    ResultType: saron.responsetype.records
                };

    $('#' + appCanvasName).jtable('load', options);
}
