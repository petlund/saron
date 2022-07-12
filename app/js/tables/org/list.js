/* global DATE_FORMAT,  
saron, 
inputFormWidth, inputFormFieldWidth, 
ORG,
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.unitlist.nameId);
    tablePlaceHolder.jtable(unitTableDef(null, saron.table.unitlist.name)); //-1 => null parent === topnode
    var options = getPostData(null, saron.table.unitlist.name, null, saron.table.unitlist.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});

