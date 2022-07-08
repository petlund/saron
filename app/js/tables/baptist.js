/* global DATE_FORMAT, PERSON, saron, 
 inputFormWidth, inputFormFieldWidth, 
 ORG, RECORD, saron.responsetype.records, OPTIONS,
saron.table.baptist.nameId, saron.table.baptist.name
 */
"use strict"; 

$(document).ready(function () {
    var tablePlaceHolder = $("#" + saron.table.baptist.name);
    tablePlaceHolder.jtable(baptistTableDef(null, null));
    var options = getPostData(null, saron.table.baptist.name, null, saron.table.baptist.name, saron.source.list, saron.responsetype.records);
    tablePlaceHolder.jtable('load', options);
    tablePlaceHolder.find('.jtable-toolbar-item-add-record').hide();
});  
    
function baptistTableDef(tableTitle, tablePath){
    var title = 'Dopuppgifter';
    if(tableTitle !== null)
        title = tableTitle;
    
    var tableName = saron.table.baptist.name;

    if(tablePath === null)
        tablePath = tableName;
    else
        tablePath+= '/' + tableName; 


    return {
        title:title,
        showCloseButton: false,
        paging: tablePath.startsWith(saron.table.baptist.name), //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'FamilyName ASC, DateOfBirthr ASC', //Set default sorting        
        actions: {
            listAction:   saron.root.webapi + 'listPeople.php', 
            updateAction: saron.root.webapi + 'updatePerson.php'
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
            Name: {
                title: 'Namn',
                width: '15%',
                edit: false,
                list: includedIn (saron.table.baptist.name, tablePath),
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }       
            },
            DateOfBirth: {
                title: 'Född',
                width: '7%',
                type: 'date',
                list: includedIn (saron.table.baptist.name, tablePath),
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


                                
    
