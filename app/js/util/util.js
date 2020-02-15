/* global SARON_URI, FullNameOfCongregation */

"use strict";

const HOME = 1;
const OLD_HOME = 3;
const PERSON = 2;
const NO_HOME = "Inget hem";
const inputFormWidth = '500px';
const inputFormFieldWidth = '480px';
const NOT_VISIBLE = 'Ej synlig';
const VISIBLE = 'Synlig';
const SARON_IMAGES_URI = 'app/images/';


var clearMembershipNoOptionCache = true;

function refreschTableAndSetViewLatestUpdate(){
    var filter = document.getElementById("groupId");
    filter.value = 2;
    $('#search_people').click();
}
    
function _setClassAndValue(record, field, type){
    if(field === "VisibleInCalendar")
        return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), _getVisibilityOption(record[field]), '');  
    else
        return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), record[field], '');    
}


function _setClassAndValuePrefix(record, field, type, prefix){
    return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), prefix + ' ' + record[field], '');    
}


function _setClassAndValueAltNull(record, field, nullValue, type){
//    if(field === "Email" || field === "user_email"){
//        return _setMailClassAndValue(record, field, nullValue, type);
//    }
//    else    
        return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), record[field], nullValue);    
}


function _setMailClassAndValue(record, field, nullValue, type){
    var mail = record.Email;
    if(mail === null || mail === undefined)
        mail = record.user_email;
    
    if(mail === undefined)
        mail = null;
    
    var mailRef = null;
    if(mail!==null)
        mailRef = '<a href="mailto:' + mail + '">' + mail + '</a>';

    return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), mailRef, nullValue);        
}


function _setImageClass(record, field, src, type){
    var imgRef = '<img src = ' + src + '/>';

    return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), imgRef);        
}


function _getClassName_Id(record, field, type){
    if(record.HomeId === -1 && type === HOME) // new Home HomeId from DB
        return field + '_' + _getId(record, type);

    return field + '_' + _getId(record, type);
}


function _styleSaronValue(clazz, val, altValue){
    if(val === null || val === undefined)
        val = altValue;
    
    if(clazz === null)
        return val;
    
    return '<p class="' + clazz +'">' + val + '</p>';
}


function _getId(record, type){
    if(type === HOME)
        if(record.HomeId === "-1")
            return 'H' + localStorage.getItem('newHomeId')
        else
            return 'H' + record.HomeId;
    else if(type === OLD_HOME)
        return 'H' + record.OldHomeId;
    else if(type === PERSON)
        return 'P' + record.PersonId;
    else 
        return -1;
}


function _updateFields(record, field, type){
    var elementValue;
    
    if(type === OLD_HOME)
        elementValue = record["ResidentsOldHome"];
    else if(field === "VisibleInCalendar")
        elementValue = _getVisibilityOption(record[field]);  
    else if(field === "LongHomeName" && record.HomeId === null)
        elementValue = NO_HOME;  
    else
        elementValue = record[field];
 
    
    var className_Id = _getClassName_Id(record, field, type);
    var element = document.getElementsByClassName(className_Id);
    for(var i = 0; i<element.length;i++)
        element[i].innerHTML = elementValue;
}


function _closeEmptyOldHome(record, field, type){
    if(record.ResidentsOldHome !== null && record.DateOfDeath === null)
        return;
    

    var className_Id = _getClassName_Id(record, field, type);
    var element = document.getElementsByClassName(className_Id);
    for(var i = 0; i<element.length;i++){
        var $tr = $(element[i].closest('tr'));
        $('#people').jtable('closeChildTable', $tr);
    }
}


function _membershipOptions(personId){
    return '/' + SARON_URI + 'app/web-api/listPerson.php?PersonId=' + personId + '&selection=nextMembershipNo';
}


function _baptistOptions(){
    return {0:'Nej', 1: 'Ja, Ange församling nedan.', 2:'Ja, ' + FullNameOfCongregation + '.'};
}


function _getVisibilityOption(i){
    var vo = _visibilityOptions();
    return vo[i];
}


function _visibilityOptions(){
        return { 0: '', 1: 'Ej synlig', 2: 'Synlig'};
}


function _letterOptions(){
    return { 0 : '', 1 : 'Ja'};
}


function baptistFormAuto(data, selectedValue){
    var inp = data.form.find('input[name=CongregationOfBaptism]');
    if(selectedValue === '0'){
        inp[0].value = "";                                      
        inp[0].disabled=true;
    }
    else if(selectedValue === '1'){
        inp[0].value = "";                                                                              
        inp[0].disabled=false;
    }
    else{
        inp[0].value = FullNameOfCongregation; //see util/js.php
        inp[0].disabled=true;
    }
}


