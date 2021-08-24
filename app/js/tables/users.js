/* global PERSON,
saron
 
 */
"use strict";

$(document).ready(function () {

    $(saron.table.users.viewid).jtable({
        title: 'Användare av Saron',
            paging: true, //Enable paging
            pageSize: 10, //Set page size (default: 10)
            pageList: 'minimal',
            sorting: true, //Enable sorting
            multiSorting: false,
            defaultSorting: 'AgeInterval ASC', //Set default sorting        
        actions: {
            listAction:   '/' + saron.uri.saron + 'app/web-api/listUsers.php'
        },
        fields: {
            id: {
                key: true,
                list: false
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
    });
 //Re-load records when user click 'load records' button.
        $(saron.table.users.viewid).click(function (e) {
            e.preventDefault();
            $(saron.table.users.viewid).jtable('load');
        });
 
        //Load all records when page is first shown
        $(saron.table.users.viewid).click();
});
    
