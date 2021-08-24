/* global DATE_FORMAT,  
saron,
inputFormWidth, inputFormFieldWidth,
ORG,
RECORD, OPTIONS
 */

"use strict";
    
$(document).ready(function () {
    $(saron.table.unittree.viewid).jtable(unitTableDef(saron.table.unittree.viewid, saron.table.unittree.name, 'Organisatoriska enheter'));
    var options = getPostData(saron.table.unittree.viewid, null, saron.table.unittree.name, null, saron.responsetype.records);
    $(saron.table.unittree.viewid).jtable('load', options);
});




