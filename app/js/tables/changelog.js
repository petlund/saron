/* global 
 saron, HOME, PERSON 
 */

"use strict";

$(document).ready(function () {

    var tablePlaceHolder = $(saron.table.changelog.nameId);
    tablePlaceHolder.jtable(changelogTableDef(null,saron.table.changelog.name, null, null));
    var options = getPostData(null, saron.table.changelog.name, null, saron.table.changelog.name, saron.source.list, saron.responsetype.records);
    $(saron.table.changelog.nameId).jtable('load', options);
        
});

function changelogTableDef(tableTitle, tablePath, parentId, parentTableDef){
    
    var tableDef = {
        title: 'Ändringslog (' +  saron.system.change_log_length + ' dygn)',
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'Inserted desc', //Set default sorting     

        actions: {
            listAction:   saron.root.webapi + 'listChangeLog.php'
        },
        fields: {
            Id: {
                key: true,
                list: false
            },
            ParentId:{
                defaultValue: -1,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.changelog.name
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.changelog.name
            },
            User: {
                title: 'Användare',
                width: '10%'
            },
            ChangeType: {
                title: 'Typ av ändring',
                width: '10%'
            },
            BusinessKey: {
                title: 'Nyckelvärde',
                width: '15%'
            },
            Description: {
                title: 'Beskrivning',
                width: '55%'
            },
            Inserted:{
                title: 'Genomförd',
                width: '10%',
                create: false,
                edit: false,
                display: function (data){
                    return getUpdateInfo(data);
                }
            }
        }
    };
    return tableDef;
}
