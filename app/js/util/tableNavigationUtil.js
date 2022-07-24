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
    var tablePathRoot = getRootElementFromTablePath(tablePath);
    var tablePlaceHolder = _getChildTablePlaceHolderFromImg(img, tablePathRoot);
            
    var source = saron.source.list;
    var resultType = saron.responsetype.records;        
    var postData = getPostData(id, childTableName, parentId, tablePath, source, resultType);

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
    var tableRootElement = getRootElementFromTablePath(data.record.AppCanvasPath);
    var tablePlaceHolder = _getChildTablePlaceHolderFromImg(img, tableRootElement);
    
    $(tablePlaceHolder).jtable('closeChildTable', tr, function(callBackData){
        var openChild = false;
    
        _updateAfterOpenCloseAction(tablePlaceHolder, childTableDef.parentTableDef, data, openChild, clientOnly);
    });
}



function _updateAfterOpenCloseAction(tablePlaceHolder, tableDef, data, openChild, clientOnly){
    var options = {url:tableDef.actions.listAction, clientOnly:clientOnly, animationsEnabled:false};
    options.record = getPostData(data.record.Id, tableDef.tableName, null, tableDef.tablePath, saron.source.list, saron.responsetype.record);
    options.record.OpenChildTable = openChild; 

    $(tablePlaceHolder).jtable('updateRecord', options); //update icon
}



function _getChildTablePlaceHolderFromImg(img, appCanvasName){
    if(img !== null){
        var tablePlaceHolder = img.closest('div.jtable-child-table-container');
        if(tablePlaceHolder.length > 0)
            return tablePlaceHolder;
    }
    var placeHolder = $("#" + appCanvasName); 
    return placeHolder;   
}



function getParentTablePlaceHolderFromChild(childPlaceHolder, appCanvasPath){
    if(childPlaceHolder !== null){
        var tablePlaceHolder = childPlaceHolder.closest('div.jtable-child-table-container');
        if(tablePlaceHolder.length > 0)
            return tablePlaceHolder;
    }
    var placeHolder = $("#" + getRootElementFromTablePath(appCanvasPath)); 
    return placeHolder;   
}



function updateParentRow(event, data, tableDef){
    var parentTableName = tableDef.parentTableDef.tableName;
    var parentTablePath = tableDef.parentTableDef.tablePath;
    var parentListUrl = tableDef.parentTableDef.actions.listAction;
    var parentPostData = getPostData(tableDef.parentId, parentTableName, null, parentTablePath, null, saron.responsetype.record);

    var parentPlaceHolder = getParentTablePlaceHolderFromChild(event.target, tableDef.tablePath);
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



//function _updateCurrentRow(data, table, listParentRowUri){
//    var url = '/' + saron.uri.saron + listParentRowUri;
//    var options = {record:{Id:data.record.Id, AppCanvasName:data.record.AppCanvasName}, clientOnly: false, url:url};
//
//    table.jtable('updateRecord', options);
//    
//}




//function _getImageCloseTag(data, childTableName, type){
//    var src = '"/' + saron.uri.saron + saron.uri.images + 'cross.png "title="Stäng"';
//    var imageTag = _setImageClass(data, childTableName, src, type);
//    return $(imageTag);
//}
//
//
//
//function _getAllClassNameOpenChild(data){
//    var className = _getClassNameOpenChild(data, saron.table.unit.name);
//        className+= _getClassNameOpenChild(data, saron.table.unittype.name);
//        className+= _getClassNameOpenChild(data, saron.table.unitlist.name);
//        className+= _getClassNameOpenChild(data, saron.table.unittree.name);
//        className+= _getClassNameOpenChild(data, saron.table.role.name);
//        className+= _getClassNameOpenChild(data, saron.table.pos.name);
//        className+= _getClassNameOpenChild(data, saron.table.engagement.name);
//        className+= _getClassNameOpenChild(data, saron.table.engagements.name);
//        className+= _getClassNameOpenChild(data, saron.table.people.name);
//        className+= _getClassNameOpenChild(data, saron.table.member.name);
//        className+= _getClassNameOpenChild(data, saron.table.baptist.name);
//        className+= _getClassNameOpenChild(data, saron.table.homes.name);
//        className+= _getClassNameOpenChild(data, saron.table.keys.name);
//        className+= _getClassNameOpenChild(data, saron.table.statistics.name);
//        className+= _getClassNameOpenChild(data, saron.table.statistics_detail.name);
//    return className;
//    
//}
//
//
//
//
//
//
//
//function _getClassNameOpenChild(data, tableName){
//
//    return tableName + is_open +  data.record.Id + ' ';
//}




