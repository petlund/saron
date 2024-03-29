/* global DATE_FORMAT, PERSON, saron, 
 inputFormWidth, inputFormFieldWidth, 
 ORG, RECORD, saron.responsetype.records, OPTIONS,
saron.table.baptist.nameId, saron.table.baptist.name
 */
"use strict"; 

$(document).ready(function () {
    var tablePlaceHolder = $(saron.table.baptist.nameId);
    var table = baptistTableDef(null, null, null, null);
    tablePlaceHolder.jtable(table);
    var options = getPostData(null, saron.table.baptist.name, null, saron.table.baptist.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
});  
    
function baptistTableDef(tableTitle, parentTablePath, parentId, parentTableDef){
    var tableName = saron.table.baptist.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title:'Dopuppgifter',
        showCloseButton: false,
        paging: true,
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'FamilyName ASC, DateOfBirthr ASC', //Set default sorting        
        actions: {
            listAction:   saron.root.webapi + 'listPeople.php', 
            updateAction: function(data) {
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: saron.root.webapi + 'updatePerson.php',
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        success: function (data) {
                            $dfd.resolve(data);
                            if(data.Result === 'OK'){
                                updateRelatedRows();  
                            }
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
                key: true,
                list: false
            },
            ParentId:{
                defaultValue: -1,
                type: 'hidden'
            },
            AppCanvasName:{
                type: 'hidden',
                defaultValue: saron.table.baptist.name
            },
            AppCanvasPath:{
                type: 'hidden',
                defaultValue: saron.table.baptist.name
            },
            DateOfAnonymization:{
                type: 'hidden'
            },
            Name: {
                title: 'Namn',
                width: '15%',
                edit: false,
                list: function(data){
                    return includedIn (saron.table.baptist.name, tablePath);
                },
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }       
            },
            DateOfBirth: {
                title: 'Född',
                width: '7%',
                type: 'date',
                list: function(data){
                    return includedIn (saron.table.baptist.name, tablePath);
                },
                displayFormat: DATE_FORMAT,
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "DateOfBirth", PERSON);
                }       
            },
            CongregationOfBaptismThis: {
                list: false,
                title: 'Döpt',
                options: _baptistOptions()
            },
            CongregationOfBaptism: {
                edit: true,
                create: false,
                width: '20%',
                title: 'Dopförsamling'
            },
            DateOfBaptism: {
                width: '7%',
                displayFormat: DATE_FORMAT,
                type: 'date',
                title: 'Dopdatum',
                display: function (data){
                    return _setClassAndValue(data, "DateOfBaptism", PERSON);
                }       
            },
            Baptister: {
                width: '15%',
                title: 'Dopförrättare'
            },
            MemberStateName: {
                title: 'Status',
                edit: false,
                create: false,
                width: '4%',
                display: function (data){
                    return _setClassAndValue(data, "MemberStateName", PERSON);
                }
            },
            Comment: {
                width: '34%',
                title: 'Not',
                type: 'textarea'
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
        rowInserted: function(event, data){
            alowedToUpdateOrDelete(event, data, tableDef);
            addAttributeForEasyUpdate(data);
        },        
        recordUpdated(data, event){
        },
        formCreated: function (event, data){
            data.row[0].style.backgroundColor = "yellow";
            data.form.css('width', inputFormWidth);
            data.form.find('input[name=Baptister]').css('width',inputFormFieldWidth);
            data.form.find('input[name=CongregationOfBaptism]').css('width',inputFormFieldWidth);
            data.form.find('input[name=CongregationOfBaptism]')[0].disabled = true;
            data.form.find('input[name=DateOfBaptism]')[0].disabled = true;
            data.form.find('input[name=Baptister]')[0].disabled = true;
            data.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);
            data.form.find('select[name=CongregationOfBaptismThis]').change(function () {
                baptistFormAuto(data, this.value);
            });
            baptistFormAuto(data);

            var dbox = document.getElementsByClassName('ui-dialog-title');            
            for(var i=0; i<dbox.length; i++)
                dbox[0].innerHTML='Uppdatera uppgifter för: ' + data.record.FirstName + ' ' + data.record.LastName;
        },
        formClosed: function (event, data){
            data.row[0].style.backgroundColor = '';
        }
    };

    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configBaptistTableDef(tableDef);
    
    return tableDef;    
}


function configBaptistTableDef(tableDef){
    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot !== saron.table.baptist.name){
        tableDef.fields.Name.list = false;
        tableDef.fields.DateOfBirth.list = false;
        tableDef.fields.MemberStateName.list = false;        
        tableDef.paging = false;
        tableDef.sorting = false;
    }    
    if(tablePathRoot === saron.table.statistics.name){
        tableDef.actions.updateAction = null;
    }
}

                                
    
