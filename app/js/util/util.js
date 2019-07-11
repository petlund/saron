    var timeoutHandler;
    
    $(document).ready(function () {
        restartTimer();
        //Comment
        document.addEventListener('mousemove',function (){
            restartTimer();
        }); 
        document.addEventListener('click',function (){
            restartTimer();
        }); 
        document.addEventListener('keypress',function (){
            restartTimer();
        });
    });


    function restartTimer(){
        if(timeoutHandler !== null)
            clearTimeout(timeoutHandler);

        timeoutHandler = window.setTimeout(function() {
            //console.log('/' + SARON_URI + 'app/access/login.php?logout=true');
            window.location='/' + SARON_URI + 'app/access/login.php?logout=true'; 
        }, 
        600000);          
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
    
//    function logoutSaron(event) {
//        document.getElementById("demo").innerHTML = "Previous URL: " + event.oldURL + "<br>New URL: " + event.newURL;
//    }
    
    window.addEventListener("oldURL", function (event) {
        var e = event.toString();
        console.log(e);
//        var url ='/' + SARON_URI + 'app/access/login.php?logout=true'; 
//        var xmlHttp = new XMLHttpRequest();
//        xmlHttp.open( "GET", url, false ); // false for synchronous request
//        xmlHttp.send( null );
//        return xmlHttp.responseText;
    });