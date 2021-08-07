/* global 
SARON_URI, SARON_IMAGES_URI,
TABLE_NAME_UNIT, TABLE_NAME_UNITTYPE, TABLE_NAME_UNITLIST, TABLE_NAME_UNITTREE, TABLE_NAME_ROLE, TABLE_NAME_POS,
ORG
*/

"use strict";
const is_open = "_is_open_";

function openChildTable(data, tableViewId, childTableDef, imgFile, tooltip, childTableName, listParentRowUrl){
    var $imgChild = getImageTag(data, imgFile, tooltip, childTableName);

    $imgChild.click(data, function (event){
        var $tr = $imgChild.closest('tr');
        $tr.removeClass(getAllClassNameOpenChild(data));
        $tr.addClass(getClassNameOpenChild(data, childTableName ));

        $(tableViewId).jtable('openChildTable', $tr, childTableDef, function(data){
            data.childTable.jtable('load');
            updateParentRow(event, tableViewId, listParentRowUrl);        
        });
    });
    return $imgChild;
    
}



function closeChildTable(data, tableViewId, childTableName, listParentRowUrl){
    var $imgClose = getImageCloseTag(data, childTableName);
    $imgClose.click(data, function(event) {
        var $tr = $imgClose.closest('tr'); 
        $tr.removeClass(getAllClassNameOpenChild(data));
        var $currentRow = $(tableViewId).jtable('getRowByKey', data.record.Id);
        $(tableViewId).jtable('closeChildTable', $currentRow, function(data){  
            updateParentRow(event, tableViewId, listParentRowUrl);        
        });
    });    
    return $imgClose;
}



function updateParentRow(event, tableViewId, listParentRowUrl){
        var url = '/' + SARON_URI + listParentRowUrl;
        var options = {record:{"Id": event.data.record.Id}, "clientOnly": false, "url":url};
        $(tableViewId).jtable('updateRecord', options);    
}


function getImageCloseTag(data, childTableName){
    var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'cross.png "title="Stäng"';
    var imageTag = _setImageClass(data, childTableName, src, ORG);
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


