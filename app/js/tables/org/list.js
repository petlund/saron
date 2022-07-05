/* global DATE_FORMAT,  
saron, 
inputFormWidth, inputFormFieldWidth, 
ORG,
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
    var mainTableViewId = saron.table.unitlist.nameId;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(unitTableDef(mainTableViewId, saron.table.unitlist.name,  null, null)); //-1 => null parent === topnode
    var options = getPostData(null, mainTableViewId, null, saron.table.unitlist.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});

