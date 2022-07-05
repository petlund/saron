/* global PERSON, HOME, OLD_HOME, inputFormWidth, inputFormFieldWidth,  
saron, 
RECORD, OPTIONS
 */
"use strict";

$(document).ready(function () {
    var mainTableViewId = saron.table.homes.nameId;
    var tablePlaceHolder = $(mainTableViewId);
    tablePlaceHolder.jtable(homeTableDef(mainTableViewId, null, null, null));
    var options = getPostData(null, mainTableViewId, null, saron.table.homes.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});



function filterHomes(viewId, reload, tableName){
    if(reload)
        $('#searchString').val('');

    var options = {searchString: $('#searchString').val(), 
                    groupId: $('#groupId').val(), 
                    TableViewId: saron.table.homes.nameId, 
                    TablePath: saron.table.homes.name,
                    ResultType: saron.responsetype.records
                };
            
    $(saron.table.homes.nameId).jtable('load', options);
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
        paging: mainTableViewId.includes(saron.table.homes.nameId), //Enable paging
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
                defaultValue: parentId,
                type: 'hidden'
            },
            TablePath:{
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
        },                        
        recordUpdated: function (event, data){
            _updateHomeFields(data);            
        }

    };
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