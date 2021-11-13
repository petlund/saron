/* global PERSON, inputFormWidth, inputFormFieldWidth,  
saron, 
RECORD, OPTIONS
 */
"use strict";

$(document).ready(function () {
    $(saron.table.homes.viewid).jtable(homeTableDef(saron.table.homes.viewid, null, null));
    var options = getPostData(null, saron.table.homes.viewid, null, saron.table.homes.name, 'list', saron.responsetype.records);
    $(saron.table.homes.viewid).jtable('load', options);
    $(saron.table.homes.viewid).find('.jtable-toolbar-item-add-record').hide();
});



function filterHomes(viewId, reload){
    if(reload)
        $('#searchString').val('');

    var options = {searchString: $('#searchString').val(), groupId: $('#groupId').val(), tableview: viewId};
            
    $('#' + viewId).jtable('load', options);
}


function homeTableDef(tableViewId, childTableTitle){
    var tableName = saron.table.homes.name;
    var title = 'Hem';
    if(childTableTitle !== null)
        title = childTableTitle;

    return {
        showCloseButton: false,        
        title: title,
        paging: true, //Enable paging
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: "FamilyName ASC",
        actions: {
            listAction:   '/' + saron.uri.saron + 'app/web-api/listHomes.php',
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
            if (data.record.user_role !== 'edit'){
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
