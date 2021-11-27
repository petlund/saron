/* global DATE_FORMAT,  
saron, 
inputFormWidth, inputFormFieldWidth, 
unitListUri,
ORG,
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
    $(saron.table.unitlist.viewid).jtable(unitTableDef(saron.table.unitlist.viewid, saron.table.unitlist.name,  null)); //-1 => null parent === topnode
    var options = getPostData(null, saron.table.unitlist.viewid, null, saron.table.unitlist.name, saron.source.list, saron.responsetype.records, unitListUri);
    $(saron.table.unitlist.viewid).jtable('load', options);
    //$(TABLE_ID).find('.jtable-toolbar-item-add-record').hide();
});

