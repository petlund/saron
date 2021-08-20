/* global DATE_FORMAT,  
SARON_URI, SARON_IMAGES_URI, 
inputFormWidth, inputFormFieldWidth, FullNameOfCongregation, 
ORG,
TABLE_VIEW_ROLE, TABLE_NAME_ROLE, 
TABLE_VIEW_UNITTYPE, TABLE_NAME_UNITTYPE,
TABLE_VIEW_UNIT, TABLE_NAME_UNIT,
TABLE_VIEW_UNITLIST, TABLE_NAME_UNITLIST,
TABLE_VIEW_UNITTREE, TABLE_NAME_UNITTREE,
RECORDS, RECORD, OPTIONS
 */

"use strict";
    
$(document).ready(function () {
    $(TABLE_VIEW_UNITTREE).jtable(unitTableDef(TABLE_VIEW_UNITTREE, TABLE_NAME_UNITTREE, 'Organisatoriska enheter'));
    var options = getPostData(TABLE_VIEW_UNITTREE, null, TABLE_NAME_UNITTREE, null, RECORDS);
    $(TABLE_VIEW_UNITTREE).jtable('load', options);
});




