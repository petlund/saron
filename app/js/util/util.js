/* global SARON_URI, FullNameOfCongregation, 
ORG_ROLE, ORG_LIST, ORG_UNIT_TYPE, ORG_TREE,
SARON_IMAGES_URI,
RECORDS,
TABLE_NAME_UNIT
*/
"use strict";

const J_TABLE_ID = "#people";
const HOME = 1;
const OLD_HOME = 3;
const PERSON = 2;
const PERSON_AND_HOME = 4;
const NEWS = 5;
const ORG = 10;
const NEW_HOME_ID = 'newHomeId';
const OLD_HOME_PREFIX = "OldHome_";
const NO_HOME = "Inget hem";
const inputFormWidth = '500px';
const inputFormFieldWidth = '480px';
const NOT_VISIBLE = 'Ej synlig';
const VISIBLE = 'Synlig';
const DATE_FORMAT = 'yy-mm-dd';
const CHILD_TABLE_PREFIX = 'child-to-parent-';
const POS_ENABLED = "2";
const POS_DISABLED = "1";
const SUBUNIT_ENABLED = "2";
const SUBUNIT_DISABLED = "1";

var clearMembershipNoOptionCache = true;
    
function _setClassAndValue(record, field, type){
    if(field === "VisibleInCalendar")
        return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), _getVisibilityOption(record[field]), '');  
    else if(field === "KeyToChurch" || field === "KeyToExp")
        return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), _getKeyOption(record[field]), '');  
    else if(field === "Letter")
        return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), _getLetterOption(record[field]), '');  
    else
        return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), record[field], '');    
}


function _setClassAndValuePrefix(record, field, type, prefix){
    return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), prefix + ' ' + record[field], '');    
}



function _setClassAndValueAltNull(record, field, nullValue, type){
        if(type === PERSON_AND_HOME){
            var classNames = field + ' ' + _getClassName_Id(record, field, PERSON) + ' ' + _getClassName_Id(record, field, HOME);
            return _styleSaronValue(classNames, record[field], nullValue);
        }
        else    
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
    var imgRef = '<img class="saron_table_icon" src = ' + src + '/>';

    return _styleSaronValue(field + ' ' + _getClassName_Id(record, field, type), imgRef);        
}


function _getClassName_Id(record, field, type){
    return field + '_' + _getId(record, type);
}


function getShortFieldName(field){
    var pos = field.indexOf("_");
    if(pos > 0)
        return  field.substring(pos + 1);
    
    return field;
}


function _styleSaronValue(clazz, val, altValue){
    if(val === null || val === undefined)
        val = altValue;
    
    if(clazz === null)
        return val;
    
    return '<p class="' + clazz + '">' + val + '</p>';
}


function _getId(record, type){
    if(type === HOME)
        if(record.HomeId === "0")
            return 'H' + localStorage.getItem('newHomeId');
        else
            return 'H' + record.HomeId;
    else if(type === OLD_HOME)
        return 'H' + record[OLD_HOME_PREFIX + 'HomeId'];
    else if(type === PERSON)
        return 'P' + record.PersonId;
    else if(type === NEWS)
        return 'N' + record.Id;
    else if(type === ORG)
        return 'Org_' + record.Id;
    else 
        return record.Id;
}


function _updateFields(record, field, type){
    var elementValue;
    
    if(field === "VisibleInCalendar")
        elementValue = _getVisibilityOption(record[field]);  
    else if(field === "Letter")
        elementValue = _getLetterOption(record[field]);  
    else if(field === "KeyToChurch" || field === "KeyToExp")
        elementValue = _getKeyOption(record[field]);  
    else
        if(type === OLD_HOME)
            elementValue = record[OLD_HOME_PREFIX + field];
        else
            elementValue = record[field];
  
    var className_Id = _getClassName_Id(record, field, type);
    var element = document.getElementsByClassName(className_Id);
    for(var i = 0; i<element.length;i++)
        element[i].innerHTML = elementValue;
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


function _getLetterOption(i){
    var lo = _letterOptions();
    return lo[i];
}


function _letterOptions(){
    return { 0 : '', 1 : 'Ja'};
}

function _getKeyOption(i){
    var ko = _keyOptions();
    return ko[i];
}

function _keyOptions(){
    return {1 : '', 2 : 'Ja'};
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


function addDialogDeleteListener(data){
    data.row.find('.jtable-delete-command-button').click(data, function (event){
        data.row[0].style.backgroundColor = "red";
        data.row[0].style.color = "white";
        $( ".ui-dialog" ).on( "dialogbeforeclose", function( event, ui ) {
            data.row[0].style.backgroundColor = "";
            data.row[0].style.color = "black";
        });

   });
}



function includedIn (currentTableId, requiredTableId){
    if(requiredTableId.includes(currentTableId))
        return true;

    return false;
}



function getURLParameter(parentId, tablePath, source, reultType){
    var first = true;
    var parameter = "";
    if(parentId !== null){
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";
        
        parameter+= 'ParentId=' + parentId;
    }
    
    if(tablePath !== null){
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";
        
        parameter+= 'TablePath=' + tablePath;

        if(source !== null){
            parameter+=  "/"  + source;
        }
    }
    if(reultType !== null){
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";
        
        parameter+= 'ResultType=' + reultType;
    }
    else
        parameter+= 'ResultType=' + RECORDS;

    return parameter;
}



function getImageTag(data, imgFile, title, childTableName){
    var src = '"/' + SARON_URI + SARON_IMAGES_URI + imgFile + '" title="' + title + '"';
    var imageTag = _setImageClass(data.record, childTableName, src, data.record.Id);
    return $(imageTag);
}



function getImageCloseTag(data, childTableName){
    var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'cross.png "title="Stäng"';
    var imageTag = _setImageClass(data.record, childTableName, src, data.record.Id);
    return $(imageTag);
}


function getChildOpenClassName(data, childTableName){
    return childTableName + '_is_open_' +  data.record.Id + ' ';
}


function getUnitOpenClassName(id){
    return TABLE_NAME_UNIT + '_is_open_' +  id + ' ';
}