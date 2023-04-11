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
    var people_FK = data.form.find('select[name=People_FK]')[0];
    var orgSuperPos_FK = data.form.find('select[name=OrgSuperPos_FK]')[0];
    var function_FK = data.form.find('select[name=Function_FK]')[0];

    if(selectedValue === '3'){
        people_FK.value = null;
        orgSuperPos_FK.value = null;
        if(data.record !== undefined)
            function_FK.value = data.record.Function_FK;
        else
            function_FK.value = null;
        
        people_FK.disabled=true;
        orgSuperPos_FK.disabled=true;
        function_FK.disabled=false;

    }
    else if(selectedValue === '2'){
        people_FK.value = null;
        
        if(data.record !== undefined)
            orgSuperPos_FK.value = data.record.OrgSuperPos_FK;
        else
            orgSuperPos_FK.value = null;

        function_FK.value = null;

        people_FK.disabled=true;
        orgSuperPos_FK.disabled=false;
        function_FK.disabled=true;

    }
    else{
        if(data.record !== undefined)
            people_FK.value = data.record.People_FK;
        else
            people_FK.value = null;

        orgSuperPos_FK.value = null;
        function_FK.value = null;

        people_FK.disabled=false;
        orgSuperPos_FK.disabled=true;
        function_FK.disabled=true;

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



function filter(){
    var urlParams = window.location.search;
    var searchParams = new URLSearchParams(urlParams);
    var appCanvasName = searchParams.get('AppCanvasName');

    var options = {searchString: $('#searchString').val(), 
                    groupId: $('#groupId').val(), 
                    AppCanvasName: appCanvasName, 
                    AppCanvasPath: appCanvasName, 
                    ResultType: saron.responsetype.records
                };

    $('#' + appCanvasName).jtable('load', options);
}



