/* global saron, DATE_FORMAT */
"use strict";

var mobilegroups ={parameters:[
        {
            head:"Mobilnummer till medlemmar utan mail", 
            listname: saron.list.mobile_member.name, 
            listid: saron.list.mobile_member.nameId
        },        
        {
            head:"Mobilnummer till församlingens vänner utan mail", 
            listname: saron.list.mobile_friendship.name, 
            listid: saron.list.mobile_friendship.nameId
        },        
        {
            head:"Namn på församlingens vänner där kontakten pågått i mer än ett år. Uppdatera medlemsregistret!", 
            listname: saron.list.mobile_ending_friendship.name,
            listid: saron.list.mobile_ending_friendship.nameId
        }        
    ]
};


$(document).ready(function () {
    var mainPlaceholder = document.getElementById(saron.list.mobile_instead_of_email.name);

    if(mainPlaceholder === null)
        return;

    addPlaceholders(mainPlaceholder, mobilegroups);

    for(var j=0; j<mobilegroups.parameters.length; j++){
        requestPhoneNumber(mainPlaceholder, mobilegroups.parameters[j]);
    }  
        

});



function addPlaceholders(mainPlaceholder, mobilegroups){
    var div;
    for(var i = 0; i<mobilegroups.parameters.length; i++){
        div = document.createElement('div');
        div.setAttribute("class", mobilegroups.parameters[i].listname);
        div.setAttribute("Id", mobilegroups.parameters[i].listname);
        mainPlaceholder.appendChild(div);
    }
}



function requestPhoneNumber(mainPlaceholder, mobilegroup){
        var head1 = '<div class="saronAugdText">' +  mobilegroup.head + '</div><br>';
        var head2 = '<br><br><div class="saronAugdText">Samma nummer med namn.</div><br>';

        var url = {url: saron.root.webapi + 'listPeople.php'};
        var postData = getPostData(null, mobilegroup.listname, null, mobilegroup.listname, saron.source.list, saron.responsetype.records);
        $.post(url, postData
        ).then(function(json) {    
            var data = JSON.parse(json);
            var cnt = data.TotalRecordCount;
            var str = head1;
            for(var i = 0; i<cnt; i++){                
                str += data.Records[i].Mobile + ', ';
        }
        
        str += head2;

        str +=  "<table>";
        for(var i = 0; i<cnt; i++){                
            str +=  "<tr>";
            str += "<td>" + data.Records[i].Name_FL + "</td><td> </td><td style='text-align: right'>"  + data.Records[i].Mobile + '</td>';
            str +=  "</tr>";
        }
        str +=  "</table><br><br><br>";

        var listPlaceholder = document.getElementById(mobilegroup.listname);
        listPlaceholder.innerHTML = str;
    
    });    
}

