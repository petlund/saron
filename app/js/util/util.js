/* global saron, 
ORG_ROLE, ORG_LIST, ORG_UNIT_TYPE, ORG_TREE,
saron,
*/
"use strict";
//Util.js
//const J_TABLE_ID = "#people";
const HOME = 1;
const OLD_HOME = 3;
const PERSON = 2;
const PERSON_AND_HOME = 4;
const NEWS = 5;
const ORG = 10;
const ORG_UNIT = 11;
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
const APPCANVASPATH = "AppCanvasPath";

function addAttributeForEasyUpdate(data){
    $(data.row[0]).attr("HomeId", data.record.HomeId); // Update home related data on several rows
    $(data.row[0]).attr(APPCANVASPATH, data.record.AppCanvasPath); // Update home related data on several rows
}



function updateRelatedRows(){
    var placeHolder = getMainTablePlaceHolderFromTablePath(saron.table.people.name);
    var className_Id = "jtable-data-row";
    var dataRecordKey = "data-record-key";
    var openRows = [];
    var parentId = -1;
    
    var jTableDataRows = placeHolder.find("." + className_Id);

    if(jTableDataRows.length === 0)
        return;

    for(var i = 0; i < jTableDataRows.length;i++){
        var jTableDataRow = jTableDataRows[i];
        var id = jTableDataRow.getAttribute(dataRecordKey);
        var tablepath = jTableDataRow.getAttribute(APPCANVASPATH);
        
        if(tablepath.includes("/")){
            var tableName = getLastElementFromTablePath(tablepath);
            var pTag = "p." + tableName + "_" + parentId;
            var row = {pTag: pTag};
            openRows.push(row);
        }
        else{
            parentId = id;            
        }
    }

    $(saron.table.people.nameId).jtable('reload', function(){
        for(var i = 0; i < openRows.length;i++){
            var openRow = openRows[i];
            var subTable = document.querySelectorAll(openRow.pTag);
            if(subTable)
                if(subTable.length > 0){
                    var img = subTable[0].firstChild;
                    img.click();
                }
        }
    });
}



function getMainTablePlaceHolderFromTag(tag, appCanvasPath){
    if(tag !== null){
        var tablePlaceHolder = tag.closest("div.jtable-main-container");
        if(tablePlaceHolder.length > 0)
            return tablePlaceHolder;
    }
}



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



function _setClassAndValueWidthEventType(data, field, type){
    
    var eventType = "_event_type_id_" + data.record.event_type_id;
    var returnVal = _styleSaronValue(field + ' ' + _getClassName_Id(data, field, type) + eventType, data.record[field], '');    
    return returnVal;
}




function _setMailClassAndValue(data, field, nullValue, type){
    var mail = null;
    if(data.record[field])
        mail = data.record[field];
    
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
        if(data.record.AppCanvasPath.includes(saron.table.people.name))
            return 'P' + data.record.Id;
        else
            return 'X' + data.record.Id;
    else if(type === NEWS)
        return 'N' + data.record.Id;
    else if(type === ORG)
        return 'Org_' + data.record.Id;
    else if(type === ORG_UNIT)
        return 'Org_Unit_' + data.record.Id;
    else if(type === TABLE)
        return '_' + data.record.Id;
    else 
        return data.record.Id;
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


function urlParamToJson(postData){
    var params = postData.split("&");

    // Create the destination object.
    var obj = {};

    // iterate the splitted String and assign the key and values into the obj.
    for (var i in params) {
      var keys = params[i].split("=");
      obj[keys[0]] = keys[1];
    }

    return obj;
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
    


    function getPostData(id, appCanvasName, parentId, appCanvasPath, source, resultType, searchString){
        if(parentId === null){        
            parentId = -1;
        }

        if(id === null){        
            id = -1;
        }

        if(resultType){
            resultType = saron.responsetype.records;
        }

        if(source){
            source = saron.source.list;
        }
        
        
        var options = {Id:id, 
                        ParentId:parentId, 
                        AppCanvasName:appCanvasName, 
                        AppCanvasPath:appCanvasPath, 
                        Source:source, 
                        ResultType:resultType,
                        searchString:searchString
                    };

        return options;
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
    var imageTag; 
    if(childTableDef !== null)
        imageTag = _setImageClass(data, childTableDef.tableName, src, type);
    else
        imageTag = _setImageClass(data, "report", src, type);
    return $(imageTag);
}



function getImageCloseTag(data, childTableName, type){
    return getImageTag(data, "cross.png", "Stäng", childTableName, type);
}



//function getChildOpenClassName(data, childTableName){
//    return childTableName + '_is_open_' +  data.record.Id + ' ';
//}



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

function getUpdateInfo(data){
    var updater = "";
    var inserter = "";
    var updated = "";
    var inserted = "";
    var br ="";
    
    if(data.record.Inserted){
        if(data.record.InserterName)
            inserter = 'Skapat av:<br> - ' + data.record.InserterName + '<br> - ';
        
        if(data.record.Inserted)
            inserted = data.record.Inserted + '<br>';

        br = '<br>';
    }
    if(data.record.Updated){
        if(data.record.UpdaterName)
            updater = br + 'Senast uppdaterat av:<br> - ' + data.record.UpdaterName + '<br> - ';
        
        if(data.record.Updated)
            updated = data.record.Updated;
    }


    var tooltiptext = inserter + inserted + updater + updated;
    
    
    var cellText = updated.substring(0, 10);
    if(cellText.length === 0)
        cellText = inserted.substring(0, 10);
    
    
    var tooltip = '<div class="Updated"><div class="tooltip">';
    tooltip+= cellText;
    tooltip+= '<span class="tooltiptext">';
    tooltip+= tooltiptext;
    tooltip+= '</span>';
    tooltip+= '</div></div>';
     
    return tooltip;
    
}