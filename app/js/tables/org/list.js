/* global DATE_FORMAT,  
saron, 
inputFormWidth, inputFormFieldWidth, 
ORG,
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
    $(saron.table.unitlist.viewid).jtable(unitTableDef(saron.table.unitlist.viewid, null,  null)); //-1 => null parent === topnode
    var options = getPostData(saron.table.unitlist.viewid, null, saron.table.unitlist.name, null, saron.responsetype.records);
    $(saron.table.unitlist.viewid).jtable('load', options);
    //$(TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
});

