/* global DATE_FORMAT, PERSON, 
saron, 
RECORD, OPTIONS
*/
"use strict";

    $(document).ready(function () {
        var tablePlaceHolder = $(saron.table.birthday.nameId);
        tablePlaceHolder.jtable(birthdayTableDef(null, saron.table.birthday.name, null, null));
        var options = getPostData(null, saron.table.birthday.name, null, saron.table.birthday.name, saron.source.list, saron.responsetype.records);
        tablePlaceHolder.jtable('load', options);
    });
    

    function birthdayTableDef(tableTitle, parentTablePath, parentId, parentTableDef) {
    var tableName = saron.table.birthday.name;
    var tablePath = getChildTablePath(parentTablePath, tableName);

    var tableDef = {
        parentId: parentId,
        tableName: tableName,
        tablePath: tablePath,
        parentTableDef: parentTableDef,
        title: 'Födelsedagar',
        paging: true, //Enable paging
        pageSize: 10, //Set page size (default: 10)
        pageList: 'minimal',
        sorting: true, //Enable sorting
        multiSorting: true,
        defaultSorting: 'NextBirthday ASC', //Set default sorting        
        actions: {
            listAction:  saron.root.webapi + 'listPeople.php'
        },
        fields: {
            Id: {
                title: 'id',
                list: false,
                key: true
            },
            Name: {
                title: 'Namn',
                width: '15%',
                display: function (data){
                    return _setClassAndValue(data, "Name", PERSON);
                }       
            },
            DateOfBirth: {
                title: 'Född',
                width: '7%',
                type: 'date',
                displayFormat: DATE_FORMAT,                
                display: function (data){
                    return _setClassAndValue(data, "DateOfBirth", PERSON);
                }       
            },
            Age: {
                title: 'Blir',
                width: '5%',
                display: function (data){
                    return _setClassAndValue(data, "Age", PERSON);
                }       
            },
            NextBirthday: {
                title: 'När',
                width: '7%',
                type: 'date',
                displayFormat: DATE_FORMAT,
                display: function (data){
                    return _setClassAndValue(data, "NextBirthday", PERSON);
                }       
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
            Email: {
                width: '13%',
                title: 'Mail',
                display: function (data){
                    return _setMailClassAndValue(data, "Email", '', PERSON);
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
            Mobile: {
                title: 'Mobil',
                inputTitle: 'Mobil <BR> - Hemtelefonuppgifter matas in under "Adressuppgifter"',
                width: '7%',
                display: function (data){
                    return _setClassAndValue(data, "Mobile", PERSON);
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
                title: 'Adress',
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
            }
        }
    };
    if(tableTitle !== null)
        tableDef.title = tableTitle;
    
    configBirthdaysTableDef(tableDef);
    
    return tableDef;
}


function configBirthdaysTableDef(tableDef){

    var tablePathRoot = getRootElementFromTablePath(tableDef.tablePath);

    if(tablePathRoot === tableDef.tableName){
        
    }

}    
