/* global DATE_FORMAT, PERSON, saron, 
 inputFormWidth, inputFormFieldWidth, 
 ORG, RECORD, saron.responsetype.records, OPTIONS,
saron.table.baptist.viewid, saron.table.baptist.name
 */
"use strict"; 

$(document).ready(function () {

    $(saron.table.baptist.viewid).jtable(baptistTableDef(saron.table.baptist.viewid, null));
    var options = getPostData(null, saron.table.baptist.viewid, null, "list", null, saron.responsetype.records);
    $(saron.table.baptist.viewid).jtable('load', options);
    $(saron.table.baptist.viewid).find('.jtable-toolbar-item-add-record').hide();
});  
    
function baptistTableDef(tableViewId, tablePath, tableTitle){
    var tableName = saron.table.baptist.name;
    var title = 'Dopuppgifter';
    if(tableTitle !== null)
        title = tableTitle; 
//    if(tablePath === null)
//        tablePath = saron.table.baptist.name;
//    else
        tablePath += saron.table.baptist.name;

    return {
            showCloseButton: false,
            title: title,
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: true,
            defaultSorting: 'FamilyName ASC, DateOfBirthr ASC', //Set default sorting        
        actions: {
            listAction:   '/' + saron.uri.saron + 'app/web-api/listPeople.php', 
            updateAction: '/' + saron.uri.saron + 'app/web-api/updatePerson.php'
        },
        
        fields: { 
            Id: {
                key: true,
                list: false
            },
            TablePath:{
                defaultValue: tableName,
                type: 'hidden'
            },
            Name: {
                title: 'Namn',
                width: '15%',
                edit: false,
                list: includedIn (tableViewId, saron.table.baptist.viewid),
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }       
            },
            DateOfBirth: {
                title: 'Född',
                width: '7%',
                type: 'date',
                list: includedIn (tableViewId, saron.table.baptist.viewid),
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
            MemberState:{
                title: 'Status',
                width: '7%',
                edit: false
            },
            Comment: {
                width: '34%',
                title: 'Not',
                type: 'textarea'
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
            data.form.css('width', inputFormWidth);
            data.form.find('input[name=Baptister]').css('width',inputFormFieldWidth);
            data.form.find('input[name=CongregationOfBaptism]').css('width',inputFormFieldWidth);
            data.form.find('textarea[name=Comment]').css('width',inputFormFieldWidth);
            data.form.find('select[name=CongregationOfBaptismThis]').change(function () {baptistFormAuto(data, this.value)});

            var dbox = document.getElementsByClassName('ui-dialog-title');            
            for(var i=0; i<dbox.length; i++)
                dbox[i].innerHTML='Uppdatera uppgifter för: ' + data.record.FirstName + ' ' + data.record.LastName;
        },
        formClosed: function (event, data){
            data.row[0].style.backgroundColor = '';
        }
    };
};


                                
    
