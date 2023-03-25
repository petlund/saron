<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    
    require_once("config.php");
    require_once(SARON_ROOT . "app/access/wp-authenticate.php");
    
       
    /*** Change to HTTPS ***/
        if(filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL) !== LOCAL_DEV_APP_HOST){
        if(filter_input(INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_URL) !== "on"){
            header("Location: https://" . filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL) . filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL));
        }
    }
    /*** LOG OUT CURRENT USER ***/
    $userlogout = (String)filter_input(INPUT_GET, "userlogout", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $logout = (String)filter_input(INPUT_GET, "logout", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if($logout === 'true' || $userlogout === 'true'){
       logout($userlogout);
    }
    
    /*** IF THE FORM HAS BEEN SUBMITTED, ATTEMPT AUTHENTICATION ***/
    if(count($_POST) > 0){
       authenticate();  
    }

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - logga in!</title>
        <link rel="stylesheet" type="text/css" href="/<?php echo SARON_URI;?>app/css/saron.css" />
        
        <link rel="icon" href=<?php echo Favicon;?> type="png"/>        
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
        <script>        
            $(document).ready(function () {
                document.getElementById("loginButton").disabled = false;
                document.getElementById("js-on").innerHTML = "";
            });            
        </script>
    </head>
    <body>
        <p class='saronBigText' style='text-align: center'><?php echo FullNameOfCongregation;?></p>
        <table width="100%">
            <col width="44%">
            <col width="12%">            
            <col width="44%">
            <tr>
                <td>
                </td>
                <td>
                    <p class='saronSmallText'>Logga in till: <?php echo NameOfRegistry;?></p>
                </td>
                <td>    
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td style="text-align: right">
                    <form class='saronSmallText' action="SaronLogin.php" method="post">            
                        <input style='width: 100%' type="text" autocomplete="off" placeholder="Användarnamn" name="log"/><BR>
                        <input style='width: 100%' type="password" autocomplete="off" placeholder="Lösenord" name="pwd"/><BR>
                        <input style='width: 100%' type="text" autocomplete="off" placeholder="OTP code" name="wp-otp-code"/>
                       <button type="submit" value="Submit" id="loginButton" disabled="true" style="text-align: right">Logga in</button>
                    </form>

                    <p  class='saronSmallText'><a href="/<?php echo WP_URI;?>wp-login.php?action=lostpassword">Glömt lösenordet?</a></p>
                </td>
                <td>
                </td>
            </tr>
        <table>
        <div class="saronRedSmallText" id="js-on" style="text-align:center">Du behöver aktivera javascript för att kunna logga in.</div>
        <?php
        if(count($_POST) > 0){
            echo "<p class='saronSmallText' style='text-align:center'>Möjliga felorsaker:  felaktigt användarnamn, felaktigt lösenord och/eller felaktig behörighetsnivå</p>";
        }
        ?>
    </body>
</html>