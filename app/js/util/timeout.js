/* global SARON_URI, green */

"use strict";
const t0 = 300; //timer time in seconds
const LAST_ACTIVITY_TIMESTAMP = 'lastActivityTimeStamp';
localStorage.setItem(LAST_ACTIVITY_TIMESTAMP, new Date().getTime());
    
    $(document).ready(function () {

        setInterval(function(){ checkTimeDiff(); }, 1000);
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


    function checkTimeDiff(){
        var diff = (new Date().getTime() - localStorage.getItem(LAST_ACTIVITY_TIMESTAMP))/1000;
        
        if(diff > t0){
            newTimeStamp();
            window.location='/' + SARON_URI + 'app/access/login.php?logout=true'; 
        } 
        updateProgressbar(diff);
    }


    function updateProgressbar(t) {
        var elem = document.getElementById("timerBar");
        var tr = (t0-t)/t0 * 100; 
        if(tr > 40)
            elem.style.background = 'lightgreen';
        else if(tr > 25)
            elem.style.background = 'yellow';
        else
            elem.style.background = 'red';
        
        elem.style.width = tr + "%";
    }
 