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



function changeLogFilter(appCanvasName, reloaded){
    if(reloaded)
        $('#searchString').val('');

    var options = {uid: $('#uid').val(), 
                    cid: $('#cid').val(), 
                    AppCanvasName: appCanvasName, 
                    AppCanvasPath: appCanvasName, 
                    ResultType: saron.responsetype.records
                };

    $('#' + appCanvasName).jtable('load', options);
}
