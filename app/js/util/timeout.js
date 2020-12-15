/* global SARON_URI, green */

"use strict";
const t0 = 600; //timer time in seconds
const LAST_ACTIVITY_TIMESTAMP = 'lastActivityTimeStamp';
var timeout;
    
    $(document).ready(function () {
        timeout = false;
        newTimeStamp(true);

        updateProgressbar(0);
        setInterval(function(){ checkTimerDiff(); }, 1000);
        newTimeStamp(false);
        //Comment
        document.addEventListener('mousemove',function (){
            newTimeStamp(false);
        }); 
        document.addEventListener('click',function (){
            newTimeStamp(false);
        }); 
        document.addEventListener('keypress',function (){
            newTimeStamp(false);
        });
    });



    function newTimeStamp(fromUser){
        if(fromUser){
            getUserSessionTime();
            checkTimerDiff();
        }
        else{
            checkTimerDiff();
            localStorage.setItem(LAST_ACTIVITY_TIMESTAMP, new Date().getTime());
        }
    }


    function checkTimerDiff(){
        var diff = (new Date().getTime() - localStorage.getItem(LAST_ACTIVITY_TIMESTAMP))/1000;
        if(diff > t0 && !timeout){
            timeout=true;
            window.location.replace('/' + SARON_URI + 'app/access/SaronLogin.php?logout=true'); 
            updateProgressbar(t0);
        }
        else
            updateProgressbar(diff);
    }



    function getUserSessionTime(){
        var time = 0;
        var httpCall = $.get( '/' + SARON_URI + 'app/web-api/listSaronUser.php?selection=time', function(jsonTime) {
            var data = JSON.parse(jsonTime);

            if(data.Record.Time_Stamp.length > 0){
                var strTime = data.Record.Time_Stamp;
                time = new Date(strTime).getTime();
                localStorage.setItem(LAST_ACTIVITY_TIMESTAMP, time);
            }
        })
        .done(function(){
        })
        .fail(function() {
        })
        .always(function() {
        });
    }
        
        
        
    function updateProgressbar(t) {
        var elem = document.getElementById("timerBar");
        if(elem === null)
            return;
        
        var tr = (t0-t)/t0 * 100; 
        if(tr > 40)
            elem.style.background = 'lightgreen';
        else if(tr > 25)
            elem.style.background = 'yellow';
        else
            elem.style.background = 'red';
        
        //elem.style.width = tr + "%";
    }
 