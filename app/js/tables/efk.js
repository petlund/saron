/* global saron, HOME, PERSON
 * 
 */

"use strict";

const tableId = saron.table.efk.viewid;

$(document).ready(function () {

    $(tableId).jtable({
        title: 'EFK Statistik ' + previousYear(),
            paging: false, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'AgeInterval ASC', //Set default sorting     

        actions: {
            listAction:   saron.root.webapi + 'listStatistics.php?TablePath=' +  saron.table.efk.name
        },
        fields: {
            AgeInterval: {
                title: 'Åldersgrupp',
                width: '10%',
                key: true,
                display: function(data){
                    return _setClassAndValue(data, "AgeInterval", HOME);                   
                }
            },
            Amount: {
                title: 'Antal medlemmar',
                width: '10%',
                display: function (data){
                    return _setClassAndValue(data, "Amount", PERSON);
                }       
            },
            Dummy: {
                title: '',
                width: '80%',
                sorting:false 
            }
        }
    });
 //Re-load records when user click 'load records' button.
        $(tableId).click(function (e) {
            e.preventDefault();
            $(tableId).jtable('load');
        });
 
        //Load all records when page is first shown
        $(tableId).click();
});
    
function previousYear (){
    var d = new Date();
    return d.getFullYear() - 1;
} 