/* global PERSON, HOME, OLD_HOME, inputFormWidth, inputFormFieldWidth,  
saron, 
RECORD, OPTIONS
 */
"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.homes.nameId);
    var table = homeTableDef(null, null, null, null);
    tablePlaceHolder.jtable(table);
    var options = getPostData(null, saron.table.homes.name, null, saron.table.homes.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
});


function homeTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.homes.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);
    
    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title: 'Hem',
        showCloseButton: false,        
        paging: true,
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: "FamilyName ASC",
        actions: {
            listAction:   saron.root.webapi + 'listHomes.php',
            updateAction: function(postData) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: saron.root.webapi + 'updateHome.php',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (successData) {
                            if(successData.Result === 'OK'){
                                $dfd.resolve(successData); //Mandatory
                                updateRelatedRows();                            }
                            else
                                $dfd.resolve(successData);
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            }
        },
        fields: {
            Id: {
                list: false,
                key: true
            },
            ParentId:{
                defaultValue: -1,
                type: 'hidden'
            },
            CanvasName:{
                type: 'hidden',
                defaultValue: saron.table.homes.name
            },
            LongHomeName:{
                type: 'hidden',
                defaultValue: 'SARON-NOT-DEFINED'
            },
            FamilyName: {
                title: 'Familjenamn',
                width: '10%',
                edit: true,
                listClass: "FamilyName"      
            },
            Residents: {
                title: 'Boende på adressen',
                width: '15%',
                edit: false,
                listClass: "Residents"
            },
            Phone: {
                title: 'Telefon',
                width: '10%',
                edit: true,
                listClass:"Phone"
            },
            Co: {
                title: 'Co',
                width: '10%',
                edit: true,
                listClass: "Co"
            },
            Address: {
                title: 'Gatuadress',
                width: '10%',
                edit: true,
                listClass: "Address"
            },
            Zip: {
                title: 'PA',
                width: '5%',
                edit: true,
                listClass: "Zip"
            },
            City: {
                title: 'Stad',
                width: '10%',
                edit: true,
                listClass: "City"
            },
            Country: {
                title: 'Land',
                width: '10%',
                edit: true,
                listClass: "Country"
            },
            Letter: {
                title: 'Brevutskick',
                inputTitle: 'Församlingspost via brev',
                edit: true,
                width: '5%',
                options:_letterOptions()
            },
            Updated:{
                title: 'Uppdaterad',
                width: '5%',
                create: false,
                edit: false,
                display: function (data){
                    return getUpdateInfo(data);
                }
            }
        },
        recordsLoaded(event, data){
            
        },                    
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
            addAttributeForEasyUpdate(data);
        },        
        formCreated: function (event, data){
            data.row[0].style.backgroundColor = "yellow";
            data.form.css('width',inputFormWidth);
            data.form.find('input[name=FamilyName]').css('width',inputFormFieldWidth);
            data.form.find('input[name=Phone]').css('width',inputFormFieldWidth);
            data.form.find('input[name=Co]').css('width',inputFormFieldWidth);
            data.form.find('input[name=Address]').css('width',inputFormFieldWidth);
            data.form.find('input[name=City]').css('width',inputFormFieldWidth);
            data.form.find('input[name=Country]').css('width',inputFormFieldWidth);
            var dbox = document.getElementsByClassName('ui-dialog-title');            
            for(var i=0; i<dbox.length; i++)
                dbox[i].innerHTML='Uppdatera uppgifter för: ' + data.record.FamilyName;
         },
         formClosed: function (event, data){
             data.row[0].style.backgroundColor = '';
        },                        
        recordUpdated: function (event, data){
        }

    };
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configHomesTableDef(tableDef);
    
    return tableDef;
}

function configHomesTableDef(tableDef){

    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === saron.table.homes.name){
    }
    else{ 
        tableDef.paging = false;
        tableDef.sorting = false;
    }    
    if(tablePathRoot === saron.table.statistics.name){
        tableDef.actions.updateAction = null;
    }
}

