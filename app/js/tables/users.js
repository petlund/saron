/* global PERSON,
saron
 
 */
"use strict";

$(document).ready(function () {
    tablePlaceHolder = $(saron.table.users.nameId);
    tablePlaceHolder.jtable(usersTableDef(null, saron.table.users.name, null, null));
    tablePlaceHolder.jtable('load');
});
        
function usersTableDef(tableTitle, tablePath, parentId, parentTableDef){
    return {
        title: 'Användare av Saron',
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: false,
            defaultSorting: 'AgeInterval ASC', //Set default sorting        
        actions: {
            listAction:  saron.root.webapi + 'listUsers.php'
        },
        fields: {
            Id: {
                
                title: 'ID',
                key: true,
                list: true
            },
            display_name: {
                title: 'Namn',
                width: '20%',
            },
            user_login: {
                title: 'Användare',
                width: '20%',
            },
            user_email: {
                title: 'Mail',
                width: '20%',
                display: function (data){
                    return _setMailClassAndValue(data, "user_email", PERSON);
                },       
            },
            wp_otp: {
                title: 'OTP',
                width: '12%',
                display: function (data){
                    if(data.record.wp_otp === "1")
                        return "Ja";
                    else
                        return "";
                }
            },
            saron_reader: {
                title: 'Läsanvändare',
                width: '12%',
                display: function (data){
                    if(data.record.saron_reader === 1)
                        return "Ja";
                    else
                        return "";
                }
            },
            saron_editor: {
                title: 'Uppdaterare',
                width: '12%',
                display: function (data){
                    if(data.record.saron_editor === 1)
                        return "Ja";
                    else
                        return "";
                }
            },
            saron_org: {
                title: 'Organisationsuppdaterare',
                width: '12%',
                display: function (data){
                    if(data.record.saron_org === 1)
                        return "Ja";
                    else
                        return "";
                }
            }
        }
    };
}
