/* global DATE_FORMAT,  
saron
 */

"use strict";
    
$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.unittree.nameId);
    var table = unitTableDef(null, saron.table.unittree.name, null, null);
    table.defaultSorting = "Prefix, Name";
    tablePlaceHolder.jtable(table);
    var options = getPostData(null, saron.table.unittree.name, null, saron.table.unittree.name, saron.source.list, saron.responsetype.records);
    
    var addButton = tablePlaceHolder.find('.jtable-toolbar-item-add-record');
    addButton.hide();

    tablePlaceHolder.jtable('load', options);
});




