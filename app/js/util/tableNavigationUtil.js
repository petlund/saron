/* global 
saron,
ORG, TABLE
*/

"use strict";

const is_open = "_is_open_";

function openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, type, listParentRowUrl){
    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);

    $imgChild.click(data, function (event){
        _openChildAndUpdateParentIcon(data, $imgChild, childTableDef, childTableName, listParentRowUrl);
    });
    return $imgChild;
    
}


function closeChildTable(data, tableViewId, childTableName, type, listParentRowUri){
    var $imgClose = _getImageCloseTag(data, childTableName, type);
    
    $imgClose.click(data, function(event) {
        _closeChildAndUpdateParentIcon(data, $imgClose, childTableName, listParentRowUri)
    });    
    return $imgClose;
}



function getChildNavIcon(data, childTableName, $imgChild, $imgClose){
    var openClassName = _getClassNameOpenChild(data, childTableName);
    var isChildRowOpen = $("." + openClassName).length > 0;
    if(isChildRowOpen)
        return $imgClose;
    else
        return $imgChild;    
}


//********************* prvate methods *********************


function _openChildAndUpdateParentIcon(data, $imgChild, childTableDef, childTableName, listParentRowUri){
    var tr = $imgChild.closest('tr');
    tr.removeClass(_getAllClassNameOpenChild(data));
    tr.addClass(_getClassNameOpenChild(data, childTableName ));

    var table = _findTableByElement(data, tr, childTableName);
    _updateParentRow(data, table, listParentRowUri);
    _openChildTable(data, tr, table, childTableDef, childTableName);

}




function _closeChildAndUpdateParentIcon(data, $imgClose, childTableName, listParentRowUri){
        var tr = $imgClose.closest('tr'); 
        tr.removeClass(_getAllClassNameOpenChild(data));
                
        var table = _findTableByElement(data, tr, childTableName); 
        _updateParentRow(data, table, listParentRowUri);

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



function _updateParentRow(data, table, listParentRowUri){
    var url = '/' + saron.uri.saron + listParentRowUri;
    var options = {record:{Id:data.record.Id, TablePath:data.record.TablePath}, clientOnly: false, url:url};

    table.jtable('updateRecord', options);    
    
}



function _openChildTable(data, tr, parentTable, childTableDef, childTableName){
    parentTable.jtable('openChildTable', tr, childTableDef, function(childTablePlaceHolder){
        var tablePath = _getTablePath(data, childTableName);
        var id = data.record.Id;
        if(data.record.PersonId > 0) // used in statistic table. personId is not unic, id = rowId
            id = data.record.PersonId;

        var postData = getPostData(null, 'childPlaceholder', id, tablePath, saron.source.list, saron.responsetype.records, childTableName);
        childTablePlaceHolder.childTable.jtable('load', postData, function(data){
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
