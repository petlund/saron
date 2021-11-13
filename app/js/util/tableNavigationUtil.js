/* global 
saron,
ORG, TABLE
*/

"use strict";

const is_open = "_is_open_";

function openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, type, listParentRowUrl){
    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);

    $imgChild.click(data, function (event){
        _openChild(data, $imgChild, tableViewId, childTableDef, childTableName, listParentRowUrl)
    });
    return $imgChild;
    
}


function _openChild(data, $imgChild, tableViewId, childTableDef, childTableName, listParentRowUrl){
    var $tr = $imgChild.closest('tr');
    $tr.removeClass(getAllClassNameOpenChild(data));
    $tr.addClass(getClassNameOpenChild(data, childTableName ));

    $(tableViewId).jtable('openChildTable', $tr, childTableDef, function(placeholder){
        var tablePath = getTablePath(data, childTableName);
        var postData = getPostData(null, tableViewId, data.record.Id, tablePath, 'list', saron.responsetype.records);
        updateParentRow(data, tableViewId, childTableName, listParentRowUrl);        
        placeholder.childTable.jtable('load', postData, function(){
        });
    });    
}



function closeChildTable(data, tableViewId, childTableName, type, listParentRowUri){
    var $imgClose = getImageCloseTag(data, childTableName, type);
    $imgClose.click(data, function(event) {
        var $tr = $imgClose.closest('tr'); 
        $tr.removeClass(getAllClassNameOpenChild(data));
        var table = getTableById(data, tableViewId, childTableName);        
        table.jtable('closeChildTable', $tr, function(){  
            updateParentRow(event.data, tableViewId, childTableName, listParentRowUri);        
        });
    });    
    return $imgClose;
}



function updateParentRow(parentData, tableViewId, childTableName, listParentRowUri){
    //updateParentTable(parentData, tableViewId, listParentRowUri);
    var url = '/' + saron.uri.saron + listParentRowUri;
    var options = {record:{Id: parentData.record.Id}, clientOnly: false, url:url};
    var table = getTableById(parentData, tableViewId, childTableName);
    table.jtable('updateRecord', options);    
}



function updateParentTable(data, tableViewId, listParentRowUri){
    var parentId = data.record.Id;
    if(parentId > 0){
        var table = getParentTableById(tableViewId, parentId);
        if(table.length === 0) 
            table = $(tableViewId, parentId);

        var url =  '/' + saron.uri.saron + listParentRowUri;
        var postData = {record:{"Id": parentId}, "clientOnly": false, "url":url};
        table.jtable('updateRecord', postData);                                
    }  
}


function getParentTableById(tableViewId, parentId){
    return $(tableViewId).jtable('getRowByKey', parentId).closest('div.jtable-child-table-container');
}


function getTableById(data, tableViewId, childTableName){
    var classNameId = "." + _getClassName_Id(data, childTableName, TABLE);
    var table = $(classNameId).closest('div.jtable-child-table-container');
    if(table.length === 0) 
        table = $(tableViewId);

    return table;
}



function getImageCloseTag(data, childTableName, type){
    var src = '"/' + saron.uri.saron + saron.uri.images + 'cross.png "title="Stäng"';
    var imageTag = _setImageClass(data, childTableName, src, type);
    return $(imageTag);
}



function getChildNavIcon(data, childTableName, $imgChild, $imgClose){
    var openClassName = getClassNameOpenChild(data, childTableName);
    var isChildRowOpen = $("." + openClassName).length > 0;
    if(isChildRowOpen)
        return $imgClose;
    else
        return $imgChild;    
}



function getClassNameOpenChild(data, childTableName){
    return childTableName + is_open +  data.record.Id + ' ';
}



function getAllClassNameOpenChild(data){
    var className = getClassNameOpenChild(data, saron.table.unit.name);
        className+= getClassNameOpenChild(data, saron.table.unittype.name);
        className+= getClassNameOpenChild(data, saron.table.unitlist.name);
        className+= getClassNameOpenChild(data, saron.table.unittree.name);
        className+= getClassNameOpenChild(data, saron.table.role.name);
        className+= getClassNameOpenChild(data, saron.table.pos.name);
        className+= getClassNameOpenChild(data, saron.table.people.name);
        className+= getClassNameOpenChild(data, saron.table.member.name);
        className+= getClassNameOpenChild(data, saron.table.baptist.name);
        className+= getClassNameOpenChild(data, saron.table.homes.name);
        className+= getClassNameOpenChild(data, saron.table.keys.name);
        className+= getClassNameOpenChild(data, saron.table.statistics.name);
        className+= getClassNameOpenChild(data, saron.table.statistics_detail.name);
    return className;
    
}

function getTablePath(data, tableName){
    var parentTablePath = data.record.TablePath;
    if(tableName === saron.table.unittree.name && parentTablePath === saron.table.unittree.name + "/" + saron.table.unittree.name)
        return saron.table.unittree.name + "/" + saron.table.unittree.name;
    else
        if(parentTablePath !== null)
            return parentTablePath + "/" + tableName;
        else
            return tableName;    
}
