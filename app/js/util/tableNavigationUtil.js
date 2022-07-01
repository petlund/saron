/* global 
saron,
ORG, TABLE
*/

"use strict";

const is_open = "_is_open_";

// new childOpenFunction

function _getClickImg(data, childTableDef, $imgChild, $imgClose){
    var tablePath = childTableDef.initParameters.TablePath;
    var openChildTableServer = data.record.OpenChildTable;
    var openChildTable = _getClassNameOpenChild(data, tablePath);
    if(openChildTableServer !== false && openChildTable === openChildTableServer)
        return $imgClose;
    else
        return $imgChild;      
}    
    
    
function _clickActionOpen(childTableDef, img, data, url, clientOnly){
    var tr = img.closest('.jtable-data-row');
    var mainTablePlaceHolder = childTableDef.initParameters.MainTableViewId;
    
    var tablePlaceHolder = _getChildTablePlaceHolderFromImg(img, mainTablePlaceHolder);
    
    tablePlaceHolder.jtable('openChildTable', tr, childTableDef, function(callBackData){
        var id = null;
        var parentId = childTableDef.initParameters.ParentId;
        var tablePath = childTableDef.initParameters.TablePath;
        var source = saron.source.list;
        var resultType = saron.responsetype.records;        
        var options = getPostData(id, mainTablePlaceHolder, parentId, tablePath, source, resultType);

        callBackData.childTable.jtable('load', options, function(childData){
        });
        var tablePathOpenChild = _getClassNameOpenChild(data, tablePath);                                
        _updateAfterClickAction(tablePlaceHolder, data, tablePathOpenChild, url, clientOnly);            
    });
    $(tr).find('.jtable-toolbar-item-add-record').hide();
}



function _clickActionClose(childTableDef, img, data, url, clientOnly){
    var tr = img.closest('.jtable-data-row');
    var mainTablePlaceHolder = childTableDef.initParameters.MainTableViewId;
    var tablePlaceHolder = _getChildTablePlaceHolderFromImg(img, mainTablePlaceHolder);
    
    $(tablePlaceHolder).jtable('closeChildTable', tr, function(callBackData){
        var tablePathOpenChild = false;
    
        _updateAfterClickAction(tablePlaceHolder, data, tablePathOpenChild, url, clientOnly);
    });
}



function _updateAfterClickAction(tablePlaceHolder, data, tablePathOpenChild, url, clientOnly){
    var options = {url:url, clientOnly:clientOnly, animationsEnabled:false};
    options.record = {Id: data.record.Id, OpenChildTable: tablePathOpenChild}; 

    if(tablePlaceHolder !== null)
        $(tablePlaceHolder).jtable('updateRecord', options); //update icon
}



function _getChildTablePlaceHolderFromImg(img, mainTablePlaceHolder){
    if(img !== null){
        var tablePlaceHolder = img.closest('div.jtable-child-table-container');
        if(tablePlaceHolder.length > 0)
            return tablePlaceHolder;
    }
    return $(mainTablePlaceHolder);
        
}

// end  new childOpenFunction 
//function openCloseChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, type, listParentRowUrl){
//    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
//    var $imgClose = _getImageCloseTag(data, childTableName, type);
//
//    $imgChild.click(data, function (event){
//        _openChildAndUpdateParentIcon(data, $imgChild, childTableDef, childTableName, listParentRowUrl);
//    });
//    
//    $imgClose.click(data, function (event){
//        closeChildTable(data, tableViewId, childTableName, type, listParentRowUrl);
//    });    
//
//    
//    var openClassName = _getClassNameOpenChild(data, childTableName);
//    var tr = $imgChild.closest('tr');
//    var cName = tr.attr('class');
//    var isChildRowOpen = false;
//    if(isChildRowOpen)
//        return $imgClose;
//    else
//        return $imgChild;    
//}
//
//
//function openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, type, listParentRowUrl){
//    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);
//
//    $imgChild.click(data, function (event){
//        _openChildAndUpdateParentIcon(data, $imgChild, childTableDef, childTableName, listParentRowUrl);
//        return _getImageCloseTag(data, childTableName, type);
//    });
//    return $imgChild;
//    
//}
//
//
//function closeChildTable(data, tableViewId, childTableName, type, listParentRowUri){
//    var $imgClose = _getImageCloseTag(data, childTableName, type);
//    
//    $imgClose.click(data, function(event) {
//        _closeChildAndUpdateParentIcon(data, $imgClose, childTableName, listParentRowUri);
//    });    
//    return $imgClose;
//}
//
//
//
//function getIcon(data, placeHolder, childTableName, $imgChild, $imgClose){
//    var row = "[data-record-key=" + data.record.Id + "]"; 
//    var tr = placeHolder.jtable('getRowByKey', row);
//    var isChildRowOpen = false;
//
//    
//    if(isChildRowOpen)
//        return $imgClose;
//    else
//        return $imgChild;    
//}
//
//
//
//function getChildNavIcon(data, childTableName, $imgChild, $imgClose){
//    var table = _findTableByElement(data, $imgChild, childTableName);
//    if(table !== null)
//        var className = table.attr('class');
//
//    var tr = $imgChild.closest('.jtable-data-row');
// 
//    var openClassName = _getClassNameOpenChild(data, childTableName);
//    var openRows = $("." + openClassName);
//    var isChildRowOpen = openRows.length > 0;
//    if(isChildRowOpen)
//        return $imgClose;
//    else
//        return $imgChild;    
//}


//********************* private methods *********************


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
    var options = {record:{Id:data.record.Id, TablePath:data.record.TablePath}, clientOnly: false, url:url};

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



function _getClassNameOpenChild(data, childTableName){
//    if(data.record.PersonId > 0)
//        return childTableName + is_open +  data.record.PersonId + ' ';

    return childTableName + is_open +  data.record.Id + ' ';
}



function _getTablePath(data, tableName){
    var parentTablePath = data.record.TablePath;
    if(tableName === saron.table.unittree.name && parentTablePath === saron.table.unittree.name + "/" + saron.table.unittree.name)
        return saron.table.unittree.name + "/" + saron.table.unittree.name;
    else
        if(parentTablePath !== null)
            return parentTablePath + "/" + tableName;
        else
            return tableName;    
}


