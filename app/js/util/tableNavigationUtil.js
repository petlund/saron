/* global 
saron,
ORG, TABLE
*/

"use strict";

const is_open = "_is_open_";

// new childOpenFunction

function _getClickImg(data, childTableDef, $imgChild, $imgClose){
    var openChildTableServer = data.record.OpenChildTable;
    var openChildTable = _getClassNameOpenChild(data, data.record.AppCanvasName);
    if(openChildTableServer !== false && openChildTable === openChildTableServer)
        return $imgClose;
    else
        return $imgChild;      
}    
    
    
function _clickActionOpen(childTableDef, img, data, url, clientOnly){
    var tr = img.closest('.jtable-data-row');
    var appCanvasRoot = getRootElementFromTablePath(data.record.AppCanvasPath);
    var tablePlaceHolder = _getChildTablePlaceHolderFromImg(img, appCanvasRoot);
    
    tablePlaceHolder.jtable('openChildTable', tr, childTableDef, function(callBackData){
        var id = null;
        var parentId = data.record.ParentId;
        var appCanvasPath = data.record.AppCanvasPath;
        var appCanvasName = data.record.AppCanvasName;
        var source = saron.source.list;
        var resultType = saron.responsetype.records;        
        var options = getPostData(id, appCanvasName, parentId, appCanvasPath, source, resultType);

        callBackData.childTable.jtable('load', options, function(childData){
        });
        
        var classNameOpenChild = _getClassNameOpenChild(data, appCanvasName);                                
        _updateAfterClickAction(tablePlaceHolder, data, classNameOpenChild, url, clientOnly);            
    });
    $(tr).find('.jtable-toolbar-item-add-record').hide();
}



function _clickActionClose(childTableDef, img, data, url, clientOnly){
    var tr = img.closest('.jtable-data-row');
    var mainTablePlaceHolder = childTableDef.initParameters.MainTableViewId;
    var tablePlaceHolder = _getChildTablePlaceHolderFromImg(img, mainTablePlaceHolder);
    
    $(tablePlaceHolder).jtable('closeChildTable', tr, function(callBackData){
        var classNameOpenChild = false;
    
        _updateAfterClickAction(tablePlaceHolder, data, classNameOpenChild, url, clientOnly);
    });
}



function _updateAfterClickAction(tablePlaceHolder, data, tablePathOpenChild, url, clientOnly){
    var options = {url:url, clientOnly:clientOnly, animationsEnabled:false};
    options.record = {Id: data.record.Id, OpenChildTable: tablePathOpenChild}; 

    if(tablePlaceHolder !== null)
        $(tablePlaceHolder).jtable('updateRecord', options); //update icon
}



function _getChildTablePlaceHolderFromImg(img, tableName){
    if(img !== null){
        var tablePlaceHolder = img.closest('div.jtable-child-table-container');
        if(tablePlaceHolder.length > 0)
            return tablePlaceHolder;
    }
    return $("#" + tableName);
        
}



//********************* private methods *********************


function getRootElementFromTablePath(tablePath){
    var p = tablePath.indexOf("/");
    if(p < 1)
        return tablePath;
    else
        return tablePath.substring(0,p);
}


function _openChildAndUpdateParentIcon(data, $imgChild, childTableDef, childTableName, listParentRowUri){
    var tr = $imgChild.closest('.jtable-data-row');
    tr.removeClass(_getAllClassNameOpenChild(data));
    tr.addClass(_getClassNameOpenChild(data, childTableName ));

    var tablePlaceholder = _findTableByElement(data, tr, childTableName);
    _updateCurrentRow(data, tablePlaceholder, listParentRowUri);
    _openChildTable(data, tr, tablePlaceholder, childTableDef, childTableName);

}




function _closeChildAndUpdateParentIcon(data, $imgClose, childTableName, listParentRowUri){
        var tr = $imgClose.closest('tr'); 
        tr.removeClass(_getAllClassNameOpenChild(data));
                
        var table = _findTableByElement(data, tr, childTableName); 
        _updateCurrentRow(data, table, listParentRowUri);

        table.jtable('closeChildTable', tr, function(){});
}



function _findTableByElement(data, element, childTableName){

    var _table = element.closest('div.jtable-main-container');
    var table = null;
    for(var t = 0; t<_table.length;t++){
        var parentDiv = _table[t].parentElement;
        
        if(parentDiv.id.length === 0)
            parentDiv.setAttribute('id', childTableName + '_' + data.record.Id);

        table = $("#" + parentDiv.id);
    }
    return table;
}



function _updateCurrentRow(data, table, listParentRowUri){
    var url = '/' + saron.uri.saron + listParentRowUri;
    var options = {record:{Id:data.record.Id, AppCanvasName:data.record.AppCanvasName}, clientOnly: false, url:url};

    table.jtable('updateRecord', options);
    
}



function _openChildTable(data, tr, tablePlaceHolder, childTableDef, childTableName){
    tablePlaceHolder.jtable('openChildTable', tr, childTableDef, function(childData){
        var tablePath = _getTablePath(data, childTableName);
        var id = data.record.Id;
        if(data.record.PersonId > 0) // used in statistic table. personId is not unic, id = rowId
            id = data.record.PersonId;

        var options = getPostData(null, 'childPlaceholder', id, tablePath, saron.source.list, saron.responsetype.records, childTableName);
        childData.childTable.jtable('load', options, function(data){
        });
    });    
}





function _getImageCloseTag(data, childTableName, type){
    var src = '"/' + saron.uri.saron + saron.uri.images + 'cross.png "title="Stäng"';
    var imageTag = _setImageClass(data, childTableName, src, type);
    return $(imageTag);
}



function _getAllClassNameOpenChild(data){
    var className = _getClassNameOpenChild(data, saron.table.unit.name);
        className+= _getClassNameOpenChild(data, saron.table.unittype.name);
        className+= _getClassNameOpenChild(data, saron.table.unitlist.name);
        className+= _getClassNameOpenChild(data, saron.table.unittree.name);
        className+= _getClassNameOpenChild(data, saron.table.role.name);
        className+= _getClassNameOpenChild(data, saron.table.pos.name);
        className+= _getClassNameOpenChild(data, saron.table.engagement.name);
        className+= _getClassNameOpenChild(data, saron.table.engagements.name);
        className+= _getClassNameOpenChild(data, saron.table.people.name);
        className+= _getClassNameOpenChild(data, saron.table.member.name);
        className+= _getClassNameOpenChild(data, saron.table.baptist.name);
        className+= _getClassNameOpenChild(data, saron.table.homes.name);
        className+= _getClassNameOpenChild(data, saron.table.keys.name);
        className+= _getClassNameOpenChild(data, saron.table.statistics.name);
        className+= _getClassNameOpenChild(data, saron.table.statistics_detail.name);
    return className;
    
}



function _getClassNameOpenChild(data, tableName){

    return tableName + is_open +  data.record.Id + ' ';
}



function _getTablePath(data, tableName){
    var parentTablePath = data.record.AppCanvasName;
    if(tableName === saron.table.unittree.name && parentTablePath === saron.table.unittree.name + "/" + saron.table.unittree.name)
        return saron.table.unittree.name + "/" + saron.table.unittree.name;
    else
        if(parentTablePath !== null)
            return parentTablePath + "/" + tableName;
        else
            return tableName;    
}


