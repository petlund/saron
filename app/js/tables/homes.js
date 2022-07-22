/* global PERSON, HOME, OLD_HOME, inputFormWidth, inputFormFieldWidth,  
saron, 
RECORD, OPTIONS
 */
"use strict";

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.homes.nameId);
    var table = homeTableDef(null, null, null, null);
    table.paging = true;
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
        paging: false,
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: "FamilyName ASC",
        actions: {
            listAction:   saron.root.webapi + 'listHomes.php',
            updateAction: saron.root.webapi + 'updateHome.php'
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
                display: function (data){
                    return _setClassAndValue(data, "FamilyName", HOME);
                }       
            },
            Residents: {
                title: 'Boende på adressen',
                width: '15%',
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "Residents", HOME);
                }       
            },
            Phone: {
                title: 'Telefon',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Phone", HOME);
                }                   
            },
            Co: {
                title: 'Co',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Co", HOME);
                }       
            },
            Address: {
                title: 'Gatuadress',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Address", HOME);
                }       
            },
            Zip: {
                title: 'PA',
                width: '5%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Zip", HOME);
                }       
            },
            City: {
                title: 'Stad',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "City", HOME);
                }       
            },
            Country: {
                title: 'Land',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Country", HOME);
                }       
            },
            Letter: {
                title: 'Brevutskick',
                inputTitle: 'Församlingspost via brev',
                edit: true,
                width: '5%',
                options:_letterOptions()
            }
        },
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
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
            _updateHomeFields(data);            
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
    else if(tablePathRoot === saron.table.unitlist.name || tablePathRoot === saron.table.unittype.name || tablePathRoot === saron.table.role.name){ 
        //tableDef.fields.ParentTreeNode_FK.list = true; 
        //tableDef.fields.OrgPath.list = true; NOT IMPLEMENTED YET
        tableDef.fields.SubUnitEnabled.list = false;
        tableDef.fields.Prefix.list = false;
        tableDef.fields.Prefix.update = false;
    }    
    if(tablePathRoot === saron.table.statistics.name){
        tableDef.actions.updateAction = null;
    }
}




function _updateHomeFields(data){
    _updateFields(data, "LongHomeName", HOME);                                                
//    _updateFields(data, "LongHomeName", PERSON);                                                
    _updateFields(data, "Residents", HOME);                                                
    _updateFields(data, "Letter", HOME);                                                
    _updateFields(data, "Phone", HOME);                                                
//    _updateFields(data, "Name", PERSON);                                                
//    _updateFields(data, "DateOfBirth", PERSON);                                                
//    _updateFields(data, "DateOfMembershipEnd", PERSON);                                                
//    _updateFields(data, "MemberState", PERSON);                                                
//    _updateFields(data, "VisibleInCalendar", PERSON);                                                
//    _updateFields(data, "Comment", PERSON);                                                
//    _updateFields(data, "Mobile", PERSON);

    if(data.record.HomeId !== data.record.OldHome_HomeId && data.record.OldHome_HomeId > 0){
        _updateFields(data, "HomeId", OLD_HOME);                                                
        _updateFields(data, "LongHomeName", OLD_HOME);                                                
        _updateFields(data, "Residents", OLD_HOME);                                                
        _updateFields(data, "Phone", OLD_HOME);            
    }
}