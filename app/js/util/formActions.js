/* global saron, 
*/
"use strict";

function baptistFormAuto(data, selectedValue){
    var DateOfBaptism = data.form.find('input[name=DateOfBaptism]');
    var CongregationOfBaptism = data.form.find('input[name=CongregationOfBaptism]');
    var Baptister = data.form.find('input[name=Baptister]');
    
    if(!selectedValue)
        selectedValue = data.record.CongregationOfBaptismThis;

    if(selectedValue === '0'){
        CongregationOfBaptism[0].value = "";                                      
        CongregationOfBaptism[0].disabled=true;
        DateOfBaptism[0].value = "";
        DateOfBaptism[0].disabled=true;
        Baptister[0].value = "";
        Baptister[0].disabled=true;
    }
    else if(selectedValue === '1'){
        CongregationOfBaptism[0].disabled=false;
        DateOfBaptism[0].disabled=false;
        Baptister[0].disabled=false;
    }
    else{
        CongregationOfBaptism[0].value = saron.name.full_name; 
        CongregationOfBaptism[0].disabled=true;
        DateOfBaptism[0].disabled=false;
        Baptister[0].disabled=false;
    }
}


function posFormAuto(data, selectedValue){
    var dp1 = data.form.find('select[name=People_FK]')[0];
    var dp2 = data.form.find('select[name=OrgSuperPos_FK]')[0];
    var dp3 = data.form.find('select[name=Function_FK]')[0];

    if(selectedValue === '3'){
        dp1.value = null;
        dp2.value = null;
        if(data.record !== undefined)
            dp3.value = data.record.Function_FK;
        else
            dp3.value = null;
        
        dp1.disabled=true;
        dp2.disabled=true;
        dp3.disabled=false;

    }
    else if(selectedValue === '2'){
        dp1.value = null;
        
        if(data.record !== undefined)
            dp2.value = data.record.OrgSuperPos_FK;
        else
            dp2.value = null;

        dp3.value = null;

        dp1.disabled=true;
        dp2.disabled=false;
        dp3.disabled=true;

    }
    else{
        if(data.record !== undefined)
            dp1.value = data.record.People_FK;
        else
            dp1.value = null;

        dp2.value = null;
        dp3.value = null;

        dp1.disabled=false;
        dp2.disabled=true;
        dp3.disabled=true;

    }
}


$(document).ready(function () {
    var f = $(".filter");
    var eventType = "";
    if(f)
        for(var i = 0; i < f.length; i++){
            if(f[i].tagName === "INPUT")
                eventType = "keyup";
            else
                eventType = "change";
            
            f[i].addEventListener(eventType, () => filter());
        }
        
            
});



function filter(e){
    var urlParams = window.location.search;
    var searchParams = new URLSearchParams(urlParams);
    var appCanvasName = searchParams.get('AppCanvasName');
    var reloaded = false; 

//    if(appCanvasName === saron.table.homes.name)
//        reloaded = true;
//    
//    if(reloaded)
//        $('#searchString').val('');

    var options = {searchString: $('#searchString').val(), 
                    groupId: $('#groupId').val(), 
                    AppCanvasName: appCanvasName, 
                    AppCanvasPath: appCanvasName, 
                    ResultType: saron.responsetype.records
                };

    $('#' + appCanvasName).jtable('load', options);
}



