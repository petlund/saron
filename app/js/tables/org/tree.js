/* global DATE_FORMAT,  
saron,
inputFormWidth, inputFormFieldWidth,
unitListUri,
ORG,
RECORD, OPTIONS
 */

"use strict";
    
$(document).ready(function () {
    $(saron.table.unittree.viewid).jtable(unitTableDef(saron.table.unittree.viewid, saron.table.unittree.name, null));
    var options = getPostData(null, saron.table.unittree.viewid, null, saron.table.unittree.name, saron.source.list, saron.responsetype.records, unitListUri);
    $(saron.table.unittree.viewid).jtable('load', options);
});




