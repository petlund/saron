/* global SARON_URI, SESSION_EXPIRES, green */

"use strict";
const t0 = SESSION_EXPIRES; //timer time in seconds
const LAST_ACTIVITY_TIMESTAMP = 'lastActivityTimeStamp';
const SERVER_NOW = 'serverNow';
var timeout;
    
    $(document).ready(function () {
        timeout = false;
        localStorage.setItem(LAST_ACTIVITY_TIMESTAMP, new Date().getTime());

        updateProgressbar(0);
        setInterval(function(){ checkTimerDiff(); }, 1000);
        newTimeStamp();
        //Comment
        document.addEventListener('mousemove',function (){
            newTimeStamp();
        }); 
        document.addEventListener('click',function (){
            newTimeStamp();
        }); 
        document.addEventListener('keypress',function (){
            newTimeStamp();
        });
    });

   

    function newTimeStamp(){
        checkTimerDiff();
        localStorage.setItem(LAST_ACTIVITY_TIMESTAMP, new Date().getTime());
    }


    function checkTimerDiff(){
        var diff = (new Date().getTime() - localStorage.getItem(LAST_ACTIVITY_TIMESTAMP))/1000;
        if(diff > t0 && !timeout){
            timeout=true;
            deleteSaronUser();
            updateProgressbar(t0);
            window.location.replace('/' + SARON_URI + 'app/access/SaronLogin.php?logout=true'); 
        }
        else
            updateProgressbar(diff);
    }


    function updateProgressbar(t) {
        var elem = document.getElementById("timerBar");
        if(elem === null)
            return;
        
        var tr = (t0-t)/t0 * 100; 
        if(tr > 25){
            titleBlink(false);
            elem.style.background = 'lightgreen';
        }
        else if(tr > 10){
            titleBlink(false);
            elem.style.background = 'yellow';
        }
        else{
            titleBlink(true);
            elem.style.background = 'red';
        }
        //elem.style.width = tr + "%";
    }



    function titleBlink(startBlink){
        const on = "Loggar ut ";
        const off = "__________ ";
        var titles = document.getElementsByTagName("TITLE");
        var title = titles[0];
        var str = title.innerHTML;
        if(startBlink)
            if(!(str.startsWith(on) || str.startsWith(off)))
                title.innerHTML = on + str;
            else{
                if(str.startsWith(on))
                    title.innerHTML = off + str.slice(on.length, str.length);
                else
                    title.innerHTML = on + str.slice(off.length, str.length);
            }
        else
            if(str.startsWith(off))
                title.innerHTML = str.slice(off.length, str.length);
            else
                title.innerHTML = str.slice(on.length, str.length);

    }



    function deleteSaronUser(){
        var httpCall = $.get( '/' + SARON_URI + 'app/web-api/deleteSaronUser.php', function() {
            window.location.replace('/' + SARON_URI + 'app/access/SaronLogin.php?logout=true'); 
        })
        .done(function(){
        })
        .fail(function() {
        })
        .always(function() {
        });
    }
        
        
        

//    function setServerTime(){
//        var httpCall = $.get( '/' + SARON_URI + 'app/web-api/getServerTime.php', function(jsonTime) {
//            var data = JSON.parse(jsonTime);
//
//            if(data.millis > 0){
//                var time = data.millis;
//                localStorage.setItem(SERVER_NOW, time * 1000);
//            }
//        })
//        .done(function(){
//        })
//        .fail(function() {
//        })
//        .always(function() {
//        });
//    }
//        
//        
//        
//    function getUserSessionTime(){
//        var httpCall = $.get( '/' + SARON_URI + 'app/web-api/listSaronUser.php?selection=time', function(jsonTime) {
//            var data = JSON.parse(jsonTime);
//
//            if(data.Record.Time_Stamp.length > 0){
//                var strTime = data.Record.Time_Stamp;
//                var time = new Date(strTime).getTime();
//                localStorage.setItem(LAST_ACTIVITY_TIMESTAMP, time);
//            }
//        })
//        .done(function(){
//        })
//        .fail(function() {
//        })
//        .always(function() {
//        });
//    }
//        
//        
//        

