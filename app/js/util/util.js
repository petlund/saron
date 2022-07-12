/* global saron, 
ORG_ROLE, ORG_LIST, ORG_UNIT_TYPE, ORG_TREE,
saron,
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
const EVENT_TYPE = 200;
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

function _setClassAndValueHeadline(data, field, type, defaultHead, preHead, postHead){
    var value = data.record[field];
    if(value === undefined)
        return defaultHead;

    if(value === null)
        return defaultHead;
    
    if(preHead === null)
        preHead = '';
    
    if(postHead === null)
        postHead = '';
    
    var headLine = preHead + _styleSaronValueInline(field + ' ' + _getClassName_Id(data, field, type), data.record[field], '', true)  + postHead;    
     
   return headLine; 
    
    
}

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
    var returnVal = _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type), prefix + ' ' + data.record[field], '');    
    return returnVal;
}



function _setClassAndValueWidthEventType(data, field, type){
    
    var eventType = "_event_type_id_" + data.record.event_type_id;
    var returnVal = _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type) + eventType, data.record[field], '');    
    return returnVal;
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
    return _styleSaronValueInline(clazz, val, altValue, false);
}



function _styleSaronValueInline(clazz, val, altValue, inline){
    if(val === null || val === undefined)
        val = altValue;
    
    if(clazz === null)
        return val;
    var inlineStyle = '';
    if(inline)
        inlineStyle = 'style="display:inline"';
    
    return '<p class="' + clazz + '" ' + inlineStyle + '>' + val + '</p>';
}




function _getId(data, type){
    if(type === HOME)
        if(data.record.AppCanvasName.includes(saron.table.homes.name))
            return 'H' + data.record.Id;
        else
            return 'H' + data.record.HomeId;
    else if(type === OLD_HOME)
        return 'H' + data.record[OLD_HOME_PREFIX + 'HomeId'];
    else if(type === PERSON)
        if(data.record.AppCanvasName.includes(saron.table.people.name))
            return 'P' + data.record.Id;
        else
            return 'X' + data.record.Id;
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
//            elementValue = data.serverResponse.Record[field];
  
    var className_Id = _getClassName_Id(data, field, type);
    var element = document.getElementsByClassName(className_Id);
    for(var i = 0; i<element.length;i++)
        element[i].innerHTML = elementValue;
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


function filterPeople(viewId, reloaded, tableName){
    if(reloaded)
        $('#searchString').val('');

    var options = {searchString: $('#searchString').val(), 
                    groupId: $('#groupId').val(), 
                    TableViewId: viewId, 
                    AppCanvasName: tableName, 
                    ResultType: saron.responsetype.records
                };

    $('#' + viewId).jtable('load', options);
}



function includedIn(currentTableId, requiredTableId){
    if(requiredTableId.includes(currentTableId))
        return true;

    return false;
}



function getURLParameters(id, appCanvasName, parentId, appCanvasPath, source, resultType, field ){
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
    
    if(appCanvasPath !== null){        
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";

        parameter+= 'AppCanvasPath=' + appCanvasPath;
    }
    
    if(appCanvasName !== null){
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";
        
        parameter+= 'AppCanvasName=' + appCanvasName;
//        parameter+= 'TableView=' + getTableView(tableViewId);
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
    
        
    if(field !== null){
        if(first){
            parameter = "?";
            first = false;
        }
        else
            parameter+= "&";
    
        parameter+= 'Field=' + field;
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
            
//    console.log(url + " => " + parameter);

    return parameter;
}
    


    function getPostData(id, appCanvasName, parentId, appCanvasPath, source, resultType){
        if(parentId === null){        
            parentId = -1;
        }

        if(id === null){        
            id = -1;
        }

        if(resultType === null){
            resultType = saron.responsetype.records;
        }

        if(source === null){
            source = saron.source.list;
        }
        
        var options = {Id:id, 
                        ParentId:parentId, 
                        AppCanvasName:appCanvasName, 
                        AppCanvasPath:appCanvasPath, 
                        Source:source, 
                        ResultType:resultType
                    };

        return options;
    }



function getInitParametes(mainTableViewId, tablePath, parentId){
    var initParameters = {MainTableViewId:mainTableViewId, 
                            TablePath:tablePath, 
                            ParentId:parentId
                        };
    return initParameters;
    
}


function getOptionsUrlParameters(data, appCanvasName, parentId, appCanvasPath, field){
    var parameters = "";
    
    if(data.source === saron.source.list){
        parameters = getURLParameters(null, appCanvasName, parentId, appCanvasPath, data.source, saron.responsetype.options, field);
    }
    if(data.source === saron.source.edit){
        parameters = getURLParameters(data.record.Id, appCanvasName, parentId, appCanvasPath, data.source, saron.responsetype.options, field);                        
        data.clearCache();
    }
    if(data.source === saron.source.create){
        parameters = getURLParameters(null, appCanvasName, parentId, appCanvasPath, data.source, saron.responsetype.options, field);                        
        data.clearCache();                        
    }    
    return parameters;
}



function getImageTag(data, imgFile, title, childTableDef, type){
    var src = '"' + saron.root.images + imgFile + '" title="' + title + '"';
    var imageTag = _setImageClass(data, childTableDef.appCanvasName, src, type);
    return $(imageTag);
}



function getImageCloseTag(data, childTableName, type){
    return getImageTag(data, "cross.png", "Stäng", childTableName, type);
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
