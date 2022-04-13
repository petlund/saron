/* global PERSON, inputFormWidth, inputFormFieldWidth,  
saron, 
RECORD, OPTIONS
 */
"use strict";
const homesListUri = 'app/web-api/listHomes.php';

$(document).ready(function () {
    var mainTableViewId = saron.table.homes.viewid;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(homeTableDef(mainTableViewId, null, null, null));
    var options = getPostData(null, mainTableViewId, null, saron.table.homes.name, saron.source.list, saron.responsetype.records, homesListUri);
    tablePlaceHolder.jtable('load', options);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});



function filterHomes(viewId, reload, tableName){
    if(reload)
        $('#searchString').val('');

    var options = {searchString: $('#searchString').val(), 
                    groupId: $('#groupId').val(), 
                    TableViewId: saron.table.homes.viewid, 
                    TablePath: saron.table.homes.name,
                    ResultType: saron.responsetype.records
                };
            
    $(saron.table.homes.viewid).jtable('load', options);
}


function homeTableDef(mainTableViewId, tablePath, newTableTitle, parentId){
    var title = 'Hem';
    if(newTableTitle !== null)
        title = newTableTitle;
    
    var tableName = saron.table.homes.name;
    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 

    return {
        title:title,
        initParameters: getInitParametes(mainTableViewId, tablePath, parentId),
        showCloseButton: false,        
        paging: mainTableViewId.includes(saron.table.homes.viewid), //Enable paging
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: "FamilyName ASC",
        actions: {
            listAction:   '/' + saron.uri.saron + homesListUri,
            updateAction: '/' + saron.uri.saron + 'app/web-api/updateHome.php'
        },
        fields: {
            Id: {
                list: false,
                key: true
            },
            ParentId: {
                list: false,
                create: false,
                edit: false,
                defaultValue: -1,
                tiile: 'Parent',
                type: 'hidden'
            },
            TablePath:{
                defaultValue: tableName,
                type: 'hidden'
            },
            FamilyName: {
                title: 'Familjenamn',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "FamilyName", PERSON);
                }       
            },
            Residents: {
                title: 'Boende på adressen',
                width: '15%',
                edit: false,
                display: function (data){
                    return _setClassAndValue(data, "Residents", PERSON);
                }       
            },
            Phone: {
                title: 'Telefon',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Phone", PERSON);
                }                   
            },
            Co: {
                title: 'Co',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Co", PERSON);
                }       
            },
            Address: {
                title: 'Gatuadress',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Address", PERSON);
                }       
            },
            Zip: {
                title: 'PA',
                width: '5%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Zip", PERSON);
                }       
            },
            City: {
                title: 'Stad',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "City", PERSON);
                }       
            },
            Country: {
                title: 'Land',
                width: '10%',
                edit: true,
                display: function (data){
                    return _setClassAndValue(data, "Country", PERSON);
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
            if (data.record.user_role !== saron.userrole.editor){
                data.row.find('.jtable-edit-command-button').hide();
                data.row.find('.jtable-delete-command-button').hide();
            }
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
         }                        

    };
}
