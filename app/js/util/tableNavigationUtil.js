/* global 
SARON_URI, SARON_IMAGES_URI,
TABLE_NAME_UNIT, TABLE_NAME_UNITTYPE, TABLE_NAME_UNITLIST, TABLE_NAME_UNITTREE, TABLE_NAME_ROLE, TABLE_NAME_POS,
ORG,
RECORDS
*/

"use strict";
const is_open = "_is_open_";

function openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, type, listParentRowUrl){
    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, type);

    $imgChild.click(data, function (event){
        var $tr = $imgChild.closest('tr');
        $tr.removeClass(getAllClassNameOpenChild(data));
        $tr.addClass(getClassNameOpenChild(data, childTableName ));

        $(tableViewId).jtable('openChildTable', $tr, childTableDef, function(placeholder){
            var tablePath = getTablePath(data, childTableName);
            var postData = getPostData(tableViewId, data.record.Id, tablePath, null, RECORDS);
            updateParentRow(data, tableViewId, childTableName, listParentRowUrl);        
            placeholder.childTable.jtable('load', postData, function(){
            });
        });
    });
    return $imgChild;
    
}



function closeChildTable(data, tableViewId, childTableName, type, listParentRowUrl){
    var $imgClose = getImageCloseTag(data, childTableName, type);
    $imgClose.click(data, function(event) {
        var $tr = $imgClose.closest('tr'); 
        $tr.removeClass(getAllClassNameOpenChild(data));
        var table = getTableById(data, tableViewId, childTableName);        
        table.jtable('closeChildTable', $tr, function(){  
            updateParentRow(event.data, tableViewId, childTableName, listParentRowUrl);        
        });
    });    
    return $imgClose;
}



function updateParentRow(parentData, tableViewId, childTableName, listParentRowUrl){
    var url = '/' + SARON_URI + listParentRowUrl;
    var options = {record:{Id: parentData.record.Id}, clientOnly: false, url:url};
    var table = getTableById(parentData, tableViewId, childTableName);
    table.jtable('updateRecord', options);    
}



function getTableById(data, tableViewId, childTableName){
//    var table = $(".Id_" + data.record.Id).closest('div.jtable-child-table-container');
    var table = $("." + _getClassName_Id(data, childTableName, ORG)).closest('div.jtable-child-table-container');
    if(table.length === 0) 
        table = $(tableViewId);

    return table;
}



function getImageCloseTag(data, childTableName, type){
    var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'cross.png "title="Stäng"';
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
    var className = getClassNameOpenChild(data, TABLE_NAME_UNIT);
        className+= getClassNameOpenChild(data, TABLE_NAME_UNITTYPE);
        className+= getClassNameOpenChild(data, TABLE_NAME_UNITLIST);
        className+= getClassNameOpenChild(data, TABLE_NAME_UNITTREE);
        className+= getClassNameOpenChild(data, TABLE_NAME_ROLE);
        className+= getClassNameOpenChild(data, TABLE_NAME_POS);
    return className;
    
}

function getTablePath(data, tableName){
    var parentTablePath = data.record.TablePath;
    if(tableName === TABLE_NAME_UNITTREE && parentTablePath === TABLE_NAME_UNITTREE + "/" + TABLE_NAME_UNITTREE)
        return TABLE_NAME_UNITTREE + "/" + TABLE_NAME_UNITTREE;
    else
        if(parentTablePath !== null)
            return parentTablePath + "/" + tableName;
        else
            return tableName;    
}
