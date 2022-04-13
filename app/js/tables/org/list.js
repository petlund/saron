/* global DATE_FORMAT,  
saron, 
inputFormWidth, inputFormFieldWidth, 
unitListUri,
ORG,
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
    var mainTableViewId = saron.table.unitlist.viewid;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(unitTableDef(mainTableViewId, null,  null, null)); //-1 => null parent === topnode
    var options = getPostData(null, mainTableViewId, null, saron.table.unitlist.name, saron.source.list, saron.responsetype.records, unitListUri);
    tablePlaceHolder.jtable('load', options);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});

