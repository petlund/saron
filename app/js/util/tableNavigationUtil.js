/* global 
SARON_URI, SARON_IMAGES_URI,
TABLE_NAME_UNIT, TABLE_NAME_UNITTYPE, TABLE_NAME_UNITLIST, TABLE_NAME_UNITTREE, TABLE_NAME_ROLE, TABLE_NAME_POS
*/

"use strict";
const is_open = "_is_open_";

function closeChildFunction(tableViewId, Id){
    var $imgClose = getImageCloseTag(data, TABLE_NAME_UNITTYPE);
    $imgClose.click(data, function(event) {
        var $tr = $imgClose.closest('tr'); 
        $tr.removeClass(getAllClassNameOpenChild(Id));
        var $currentRow = $(tableViewId).jtable('getRowByKey', data.record.Id);
        $(tableViewId).jtable('closeChildTable', $currentRow, function(data){  
            updateUnitTypeRecord(tableViewId, event.data);
        });
    });    
    return $imgClose;
}





function getImageCloseTag(data, childTableName){
    var src = '"/' + SARON_URI + SARON_IMAGES_URI + 'cross.png "title="Stäng"';
    var imageTag = _setImageClass(data.record, childTableName, src, data.record.Id);
    return $(imageTag);
}



function getChildNavIcon(childTableName, Id, $imgChild, $imgClose){
    var openClassName = getClassNameOpenChild(childTableName, Id);
    var isChildRowOpen = $("." + openClassName).length > 0;
    if(isChildRowOpen)
        return $imgClose;
    else
        return $imgChild;    
}



function getClassNameOpenChild(childTableName, Id){
    return childTableName + is_open +  Id + ' ';
}



function getAllClassNameOpenChild(Id){
    var className = getClassNameOpenChild(TABLE_NAME_UNIT, Id);
        className+= getClassNameOpenChild(TABLE_NAME_UNITTYPE, Id);
        className+= getClassNameOpenChild(TABLE_NAME_UNITLIST, Id);
        className+= getClassNameOpenChild(TABLE_NAME_UNITTREE, Id);
        className+= getClassNameOpenChild(TABLE_NAME_ROLE, Id);
        className+= getClassNameOpenChild(TABLE_NAME_POS, Id);
    return className;
    
}


