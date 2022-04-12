/* global PERSON,
saron
 
 */


"use strict";

$(document).ready(function () {
    const mainTablePlaceHolder = $(saron.table.users.viewid);
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
                listAction:   '/' + saron.uri.saron + 'app/web-api/listUsers.php'
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
                        var url = null; //'/' + saron.uri.saron + 'app/web-api/listUsers.php';
                        
                        $imgChild.click(data, function (event){
                            var childTableDef = test(mainTablePlaceHolder, title + ' + ');
                            _clickActionOpen(mainTablePlaceHolder, childTableDef, childTableName, $imgChild, event, url, true);
                        });

                        $imgClose.click(data, function (event){
                            _clickActionClose(mainTablePlaceHolder, $imgClose, event, url, true);
                        });    

                        return _getClickImg(data, childTableName, $imgChild, $imgClose);

                    }       
                },
                display_name:{
                    width: '96%',
                    title: 'Namn'
                }
            }
    };
}
    
    
    
function _getClickImg(data, childTableName, $imgChild, $imgClose){
    var openChildTableServer = data.record.OpenChildTable;
    var openChildTable = _getClassNameOpenChild(data, childTableName);
    if(openChildTableServer !== false && openChildTable === openChildTableServer)
        return $imgClose;
    else
        return $imgChild;      
}    
    
    
function _clickActionOpen(mainTablePlaceHolder, childTableDef, childTableName, img, event, url, clientOnly){
    var tr = img.closest('.jtable-data-row');
    var tablePlaceHolder = getChildTablePlaceHolderFromImg(img, mainTablePlaceHolder);
    
    tablePlaceHolder.jtable('openChildTable', tr, childTableDef, function(callBackData){
        var options = {Id: event.data.record.Id}; //parentId
        var childOpen = _getClassNameOpenChild(event.data, childTableName );                                

        callBackData.childTable.jtable('load', options, function(childData){});
        
        _updateAfterClickAction(tablePlaceHolder, event, childOpen, url, clientOnly);
    });
}



function _clickActionClose(mainTablePlaceHolder, img, event, url, clientOnly){
    var tr = img.closest('.jtable-data-row');
    var tablePlaceHolder = getChildTablePlaceHolderFromImg(img, mainTablePlaceHolder);
    
    tablePlaceHolder.jtable('closeChildTable', tr, function(callBackData){
        var childOpen = false;
    
        _updateAfterClickAction(tablePlaceHolder, event, childOpen, url, clientOnly);
    });
}



function _updateAfterClickAction(tablePlaceHolder, event, childOpen, url, clientOnly){
    var options = {url:url, clientOnly:clientOnly};
    options.record = {Id: event.data.record.Id, OpenChildTable: childOpen}; 

    if(tablePlaceHolder !== null)
        tablePlaceHolder.jtable('updateRecord', options); //update icon
}



function getChildTablePlaceHolderFromImg(img, mainTablePlaceHolder){
    if(img !== null){
        var tablePlaceHolder = img.closest('div.jtable-child-table-container');
        if(tablePlaceHolder.length > 0)
            return tablePlaceHolder;
    }
    return mainTablePlaceHolder;
        
}