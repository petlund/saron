<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . "menu.php";

    /*** REQUIRE USER AUTHENTICATION ***/
    isLoggedIn();
    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Adress</title>
        <link href="/<?php echo SARON_URI;?>jtable/themes/lightcolor/gray/jtable.min.css" rel="stylesheet" type="text/css" />        
        <link href="/<?php echo SARON_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet" type="text/css" />         
        
    </head>
    <body>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.js"></script>
 
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/homes.js"></script>     
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery.jtable.min.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/localization/jquery.jtable.se.js"></script>                  

        <form id="mainfilter">
                <div class="forms saronSmallText">Grupp:          
                        <select id="groupId" name="groupId" onchange="filterHomes('<?php include('../includes/viewId.php');?>');" >
                        <option selected="selected" value="0">Alla hem</option>
                        <option value="1">Hem utan mail- och mobiluppgifter</option>
                        <option value="2">Hem med brevutskick</option>
                    </select>     
                    Söksträng:
                <input type="text" name="searchString" id="searchString" oninput="filterHomes('<?php include('../includes/viewId.php');?>');"/>
                <button name="submitButton" type="submit" <?php include('../includes/searchId.php');?>>Sök</button>
                </div> 
            </form>
            
            <div id="homes"></div>
        
    </body>
</html>