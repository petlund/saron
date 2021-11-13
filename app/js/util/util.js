/* global saron, 
ORG_ROLE, ORG_LIST, ORG_UNIT_TYPE, ORG_TREE,
saron,
saron.table.unit.name
*/
"use strict";

//const J_TABLE_ID = "#people";
const HOME = 1;
const OLD_HOME = 3;
const PERSON = 2;
const PERSON_AND_HOME = 4;
const NEWS = 5;
const ORG = 10;
const TABLE = 100;
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
    
function _setClassAndValue(data, field, type){
    if(field === "VisibleInCalendar")
        return _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type), _getVisibilityOption(data.record[field]), '');  
    else if(field === "KeyToChurch" || field === "KeyToExp")
        return _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type), _getKeyOption(data.record[field]), '');  
    else if(field === "Letter")
        return _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type), _getLetterOption(data.record[field]), '');  
    else
        return _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type), data.record[field], '');    

}

function _setClassAndValuePrefix(data, field, type, prefix){
    return _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type), prefix + ' ' + data.record[field], '');    
}



function _setClassAndValueAltNull(data, field, nullValue, type){
        if(type === PERSON_AND_HOME){
            var classNames = field + ' ' + _getClassName_Id(data, field, PERSON) + ' ' + _getClassName_Id(data, field, HOME);
            return _styleSaronValue(classNames, data.record[field], nullValue);
        }
        else    
            return _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type), data.record[field], nullValue);    
}


function _setMailClassAndValue(data, field, nullValue, type){
    var mail = data.record.Email;
    if(mail === null || mail === undefined)
        mail = data.record.user_email;
    
    if(mail === undefined)
        mail = null;
    
    var mailRef = null;
    if(mail!==null)
        mailRef = '<a href="mailto:' + mail + '">' + mail + '</a>';

    return _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type), mailRef, nullValue);        
}


function _setImageClass(data, field, src, type){
    var imgRef = '<img class="saron_table_icon" src = ' + src + '/>';

    return _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type), imgRef);        
}


function _getClassName_Id(data, field, type){
    return field + '_' + _getId(data, type);
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
    
    return '<div class="' + clazz + '">' + val + '</p>';
}


function _getId(data, type){
    if(type === HOME)
        if(data.record.HomeId === "0")
            return 'H' + localStorage.getItem('newHomeId');
        else
            return 'H' + data.record.HomeId;
    else if(type === OLD_HOME)
        return 'H' + data.record[OLD_HOME_PREFIX + 'HomeId'];
    else if(type === PERSON)
        return 'P' + data.record.Id;
    else if(type === NEWS)
        return 'N' + data.record.Id;
    else if(type === ORG)
        return 'Org_' + data.record.Id;
    else if(type === TABLE)
        return '_' + data.record.Id;
    else 
        return data.record.Id;
}


function _updateFields(data, field, type){
    var elementValue;
    
    if(field === "VisibleInCalendar")
        elementValue = _getVisibilityOption(data.record[field]);  
    else if(field === "Letter")
        elementValue = _getLetterOption(data.record[field]);  
    else if(field === "KeyToChurch" || field === "KeyToExp")
        elementValue = _getKeyOption(data.record[field]);  
    else
        if(type === OLD_HOME)
            elementValue = data.record[OLD_HOME_PREFIX + field];
        else
            elementValue = data.record[field];
  
    var className_Id = _getClassName_Id(data, field, type);
    var element = document.getElementsByClassName(className_Id);
    for(var i = 0; i<element.length;i++)
        element[i].innerHTML = elementValue;
}


function _membershipOptions(data){
    return '/' + saron.uri.saron + 'app/web-api/listPerson.php?Id=' + data.record.Id + '&selection=nextMembershipNo';
}


function _baptistOptions(){
    return {0:'Nej', 1: 'Ja, Ange församling nedan.', 2:'Ja, ' + saron.name.full_name + '.'};
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
        inp[0].value = saron.name.full_name; 
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


function filterPeople(viewId, reloaded){
    if(reloaded)
        $('#searchString').val('');

    var options = {searchString: $('#searchString').val(), groupId: $('#groupId').val(), TableView: viewId};

    $('#' + viewId).jtable('load', options);
}




function includedIn (currentTableId, requiredTableId){
    if(requiredTableId.includes(currentTableId))
        return true;

    return false;
}



function getURLParameters(id, tableViewId, parentId, tablePath, source, resultType){
    var first = true;
    var parameter = "";

    if(parentId === null){        
        parentId = -1;
    }

    if(id === null){        
        id = -1;
    }


    if(id !== null){        
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";

        parameter+= 'Id=' + id;
    }
    
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
    }
    
    if(tableViewId !== null){
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";
        
        parameter+= 'TableView=' + getTableView(tableViewId);
    }
    
    
    if(source !== null){
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";
    
        parameter+= 'Source=' + source;
    }
    
        
    if(resultType !== null){
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";

        parameter+= 'ResultType=' + resultType;
    }
    else
        parameter+= 'ResultType=' + saron.responsetype.records;
            
    console.log(parameter);

    return parameter;
}
    


    function getPostData(id, tableViewId, parentId, tablePath, source, resultType){
        if(parentId === null){        
            parentId = -1;
        }

        if(id === null){        
            id = -1;
        }


        if(resultType === null){
            resultType = saron.responsetype.records;
        }
        var options = {Id:id, TableView:getTableView(tableViewId), ParentId:parentId, TablePath:tablePath, Source:source, ResultType:resultType};
        console.log(options);
        return options;
    }



function getImageTag(data, imgFile, title, childTableName, type){
    var src = '"/' + saron.uri.saron + saron.uri.images + imgFile + '" title="' + title + '"';
    var imageTag = _setImageClass(data, childTableName, src, type);
    return $(imageTag);
}



function getChildOpenClassName(data, childTableName){
    return childTableName + '_is_open_' +  data.record.Id + ' ';
}



async function post(url, options, transport){
    var response = await $.post( url, options)
        .done(function(data){
            this.data = data;
    }.bind(this), "json"); 
    return response;
}


function getRespons(data){
    return data;    
}


function getTableView(tableViewId){
    return tableViewId.substring(1, tableViewId.length);
}