/* global 
saron,
ORG, TABLE
*/

"use strict";

function getClickImg(data, childTableDef, $imgChild, $imgClose){
    var openChildTable = data.record.OpenChildTable;
    var currentChildTable = childTableDef.tableName;
    if(openChildTable !== false && currentChildTable === openChildTable)
        return $imgClose;
    else
        return $imgChild;      
}    
    
    
function openChildTable(childTableDef, img, data, clientOnly){

    var tr = img.closest('.jtable-data-row');

    var id = null;
    var parentId = childTableDef.parentId;
    var childTableName = childTableDef.tableName;
    var tablePath = childTableDef.tablePath;;
    var tablePlaceHolder = getSurroundingTableTag(img, tablePath);
            
    var source = saron.source.list;
    var resultType = saron.responsetype.records;        
    
    var searchString = "";
    if(data.record.searchString)
        searchString = data.record.searchString;
        
    var postData = getPostData(id, childTableName, parentId, tablePath, source, resultType, searchString);

    tablePlaceHolder.jtable('openChildTable', tr, childTableDef, function(callBackData){
        var addButton = callBackData.childTable.find('.jtable-toolbar-item-add-record');
        addButton.hide();

        callBackData.childTable.jtable('load', postData, function(childData){
        });
        _updateAfterOpenCloseAction(tablePlaceHolder, childTableDef.parentTableDef, data, childTableName, clientOnly);            
    });
}



function closeChildTable(childTableDef, img, data, clientOnly){
    var tr = img.closest('.jtable-data-row');
    var tablePlaceHolder = getSurroundingTableTag(img, data.record.AppCanvasPath);
    
    $(tablePlaceHolder).jtable('closeChildTable', tr, function(callBackData){
        var openChild = false;
    
        _updateAfterOpenCloseAction(tablePlaceHolder, childTableDef.parentTableDef, data, openChild, clientOnly);
    });
}



function _updateAfterOpenCloseAction(tablePlaceHolder, tableDef, data, openChild, clientOnly){
    var options = {url:tableDef.actions.listAction, clientOnly:clientOnly, animationsEnabled:false};
    options.record = getPostData(data.record.Id, tableDef.tableName, null, tableDef.tablePath, saron.source.list, saron.responsetype.record);
    options.record.OpenChildTable = openChild; 

    var searchString = "";
    if(data.record.searchString)
        searchString = data.record.searchString;

    options.record.searchString = searchString; 

    $(tablePlaceHolder).jtable('updateRecord', options); //update icon
}



function getSurroundingTableTag(tag, appCanvasPath){
    if(tag !== null){
        var tablePlaceHolder = tag.closest('div.jtable-child-table-container');
        
        if(tablePlaceHolder !== null)
            if(tablePlaceHolder.length > 0)
                return tablePlaceHolder;
    }

    var placeHolder = getMainTablePlaceHolderFromTablePath(appCanvasPath)
    return placeHolder;   
}


function getMainTablePlaceHolderFromTablePath(appCanvasPath){
    return $("#" + getRootElementFromTablePath(appCanvasPath));
}



function updateParentRow(event, data, tableDef){
    var parentTableName = tableDef.parentTableDef.tableName;
    var parentTablePath = tableDef.parentTableDef.tablePath;
    var parentListUrl = tableDef.parentTableDef.actions.listAction;
    var parentPostData = getPostData(tableDef.parentId, parentTableName, null, parentTablePath, null, saron.responsetype.record);

    var parentPlaceHolder = getSurroundingTableTag(event.target, tableDef.tablePath);
    var record = parentPostData;
    record.OpenChildTable = tableDef.tableName;
    record.user_role = data.user_role;
    
    var options = {record, "clientOnly": false, "url":parentListUrl, animationsEnabled:false};
    parentPlaceHolder.jtable('updateRecord', options);
}


function getChildTablePath(parentTablePath, childTableName){
    const maxUnits = saron.table.unittree.name + '/' + saron.table.unit.name;

    var childTablePath;
    if(!(parentTablePath === maxUnits && childTableName === saron.table.unit.name)){
        if(parentTablePath === null){
            childTablePath = childTableName;
        }
        else{
            childTablePath = parentTablePath + "/" + childTableName;
        }
        return childTablePath;
    }
    return parentTablePath;
}



function getRootElementFromTablePath(tablePath){
    var p = tablePath.indexOf("/");
    if(p < 1)
        return tablePath;
    else
        return tablePath.substring(0,p);
}


function getLastElementFromTablePath(appCanvasPath){
    var p = appCanvasPath.lastIndexOf("/");
    if(p < 1)
        return appCanvasPath;
    else
        return appCanvasPath.substring(p+1, appCanvasPath.length);
}





