/* global 
 saron, HOME, PERSON 
 */

"use strict";

$(document).ready(function () {

    $(saron.table.efk.nameId).jtable(efkTableDef());
    var options = getPostData(null, saron.table.efk.name, null, saron.table.efk.name, saron.source.list, saron.responsetype.records);
    $(saron.table.efk.nameId).jtable('load', options);
        
});

function efkTableDef(){
    
    return{
        title: 'EFK Statistik ' + previousYear(),
            paging: false, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'AgeInterval ASC', //Set default sorting     

        actions: {
            listAction:   saron.root.webapi + 'listStatistics.php'
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
            TablePath:{
                type: 'hidden',
                defaultValue: saron.table.efk.name
            },
            Dummy: {
                title: '',
                width: '80%',
                sorting:false 
            }
        }
    };
}
    
function previousYear (){
    var d = new Date();
    return d.getFullYear() - 1;
} 