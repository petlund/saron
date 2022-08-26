/* global saron, DATE_FORMAT 
saron 
 */
"use strict";

var mailgroups ={parameters:[
        {
            head:"Mailadresser till medlemmar", 
            listname: saron.list.email_member.name, 
            listid: saron.list.email_member.nameId
        },        
        {
            head:"Mailadresser till församlingens vänner", 
            listname: saron.list.email_friendship.name, 
            listid: saron.list.email_friendship.nameId
        },        
        {
            head:"Namn på församlingens vänner där kontakten pågått i mer än ett år. Uppdatera medlemsregistret!", 
            listname: saron.list.email_ending_friendship.name,
            listid: saron.list.email_ending_friendship.nameId
        }        
    ]
};

var head = '<div class="saronAugdText">Mailadresser att kopiera och klistra in i adressfält för hemlig kopia.</div><br>';

$(document).ready(function () {
    var mainPlaceholder = document.getElementById(saron.list.email.name);
    if(mainPlaceholder === null)
        return;
    else{
        mainPlaceholder.innerHTML = head;
        addPlaceholders(mainPlaceholder, mailgroups);
        for(var j=0; j<mailgroups.parameters.length; j++){
            request(mailgroups.parameters[j]);
        }  
    }    
});


function addPlaceholders(mainPlaceholder, mailgroups){
    var div;
    for(var i = 0; i<mailgroups.parameters.length; i++){
        div = document.createElement('div');
        div.setAttribute("class", mailgroups.parameters[i].listname);
        div.setAttribute("Id", mailgroups.parameters[i].listname);
        mainPlaceholder.appendChild(div);
    }
}


function request(parameter){
    var url = {url: saron.root.webapi + 'listPeople.php'};
    var postData = getPostData(null, parameter.listname, null,  parameter.listname, saron.source.list, saron.responsetype.records);
    $.post(url, postData, function(json) { 
        var data = JSON.parse(json);    
        var cnt = data.Records.length;
        var str = '<div><b>' + parameter.head + "</b></div>";
        str +='<div class="saronSmallText"> - ' + cnt + " st. adresser</div><br>";

        for(var i = 0; i<cnt; i++)                
            str += data.Records[i].entry + ', ';
        
        str += '<BR><BR>';
        
        var listPlaceholder = document.getElementById(parameter.listname);
        listPlaceholder.innerHTML = str;
    });
}