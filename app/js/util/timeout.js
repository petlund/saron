/* global SARON_URI, green */

"use strict";
const t0 = 600; //timer time in seconds
const LAST_ACTIVITY_TIMESTAMP = 'lastActivityTimeStamp';
var timeout;
localStorage.setItem(LAST_ACTIVITY_TIMESTAMP, new Date().getTime());
    
    $(document).ready(function () {
        timeout = false;
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
        localStorage.setItem('lastActivityTimeStamp', new Date().getTime());
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
 