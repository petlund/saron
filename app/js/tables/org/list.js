/* global DATE_FORMAT,  
saron, 
inputFormWidth, inputFormFieldWidth, 
ORG,
RECORD, OPTIONS
 */

"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.unitlist.nameId);
    tablePlaceHolder.jtable(unitTableDef(null, saron.table.unitlist.name, null, null)); //-1 => null parent === topnode

    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();

    var options = getPostData(null, saron.table.unitlist.name, null, saron.table.unitlist.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);


    
});

