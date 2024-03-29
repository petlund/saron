/* global PERSON,
saron
 
 */


"use strict";

$(document).ready(function () {
    const mainTablePlaceHolder = $(saron.table.users.nameId);
    mainTablePlaceHolder.jtable(test(mainTablePlaceHolder, 'TEST'));
    mainTablePlaceHolder.jtable('load');
});

    function test(mainTablePlaceHolder, title){
        return {
            title: function(){
                if(title === null)
                    return 'TEST';
                else
                    return title;
                },
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: false,
            defaultSorting: 'AgeInterval ASC', //Set default sorting        
            actions: {
                listAction:   saron.root.webapi + 'listUsers.php'
            },
            fields: {
                Id: {
                    width: '2%',
                    title: 'Id',
                    key: true,
                    list: true
                },
                icon: {
                    title: 'Ikon',
                    width: '2%',
                    display: function (data){
                        var tooltip = 'Detaljer';
                        var imgFile = "member.png";
                        var childTableName = 'test';
                        var $imgChild = getImageTag(data, imgFile, tooltip, childTableName, 0);
                        var $imgClose = _getImageCloseTag(data, childTableName, 0);
                        var url = null; 
                        var clientOnly = true;
                        
                        $imgChild.click(data, function (event){
                            var childTableDef = test(mainTablePlaceHolder, title + ' + ');
                            openChildTable(mainTablePlaceHolder, childTableDef, childTableName, $imgChild, event, url, clientOnly);
                        });

                        $imgClose.click(data, function (event){
                            closeChildTable(mainTablePlaceHolder, $imgClose, event, url, clientOnly);
                        });    

                        return getClickImg(data, childTableName, $imgChild, $imgClose);
                    }       
                },
                display_name:{
                    width: '96%',
                    title: 'Namn'
                }
            }
    };
}
    
    
    
