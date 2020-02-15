/* global SARON_URI, green */

"use strict";
const t0 = 300; //seconds
var t;
    
    $(document).ready(function () {

        setInterval(function(){ tick(); }, 1000);
        reset();
        //Comment
        document.addEventListener('mousemove',function (){
            reset();
        }); 
        document.addEventListener('click',function (){
            reset();
        }); 
        document.addEventListener('keypress',function (){
            reset();
        });
    });



    function reset(){
        t=0;
    }

    function tick(){
        t++;
        
        if(t > t0){
            reset();
            window.location='/' + SARON_URI + 'app/access/login.php?logout=true'; 
        } 
        updateProgressbar(t);
    }


    function eventFire(el, etype){
        if (el.fireEvent) {
            el.fireEvent('on' + etype);
        } 
        else {
            var evObj = document.createEvent('Events');
            evObj.initEvent(etype, true, false);
            el.dispatchEvent(evObj);
        }
    }
    
    
    window.addEventListener("oldURL", function (event) {
        var e = event.toString();
        console.log(e);
//        var url ='/' + SARON_URI + 'app/access/login.php?logout=true'; 
//        var xmlHttp = new XMLHttpRequest();
//        xmlHttp.open( "GET", url, false ); // false for synchronous request
//        xmlHttp.send( null );
//        return xmlHttp.responseText;
    });

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
 