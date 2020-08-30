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
        <title><?php echo NameOfRegistry;?> - Person</title>
    </head>
    <body>
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/people.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/member.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/keys.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/birthdays.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/baptist.js"></script>     
        <script type="text/JavaScript" src="/<?php echo SARON_URI;?>app/js/tables/total.js"></script>     
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo SARON_URI;?>jtable/localization/jquery.jtable.se.js"></script>        
        <form id="mainfilter">
                <div class="forms saronSmallText">Grupp:          
                        <select id="groupId" name="groupId" onchange="filterPeople('<?php include('../includes/viewId.php');?>');" >
                        <option selected="selected" value="0">Medlemmar</option>
                        <option value="1">Dopregister</option>
                        <option value="2">Senast ändrade personer</option>
                        <option value="3">Nya medlemmar <?php include('../util/CurrentYear.php');?></option>
                        <option value="4">Nya medlemmar <?php include('../util/PrevYear.php');?></option>
                        <option value="5">Avslutade medlemskap <?php include('../util/CurrentYear.php');?></option>
                        <option value="6">Avslutade medlemskap <?php include('../util/PrevYear.php');?></option>
                        <option value="7">Döpta <?php include('../util/CurrentYear.php');?></option>
                        <option value="8">Döpta <?php include('../util/PrevYear.php');?></option>
                        <option value="9">Medlemmar som inte syns i adresskalendern</option>
                        <option value="10">Avlidna <?php include('../util/CurrentYear.php');?></option>
                        <option value="11">Avlidna <?php include('../util/PrevYear.php');?></option>
                        <option value="12">Hela registret</option>
                        <option value="13">Medlemmar utanför Norrköping</option>
                        <option value="14">Ej medlem</option>
                        <option value="15">Underlag för anonymisering under <?php include('../util/CurrentYear.php');?></option>
                        <option value="16">Anonymiserade</option>
                    </select>     
                    Söksträng:
                <input type="text" name="searchString" id="searchString" oninput="filterPeople('<?php include('../includes/viewId.php');?>');"/>
                <button name="submitButton" type="submit" <?php include('../includes/searchId.php');?>>Sök</button>
                </div> 
            </form>
            
            <div id="<?php include('../includes/viewId.php');?>"></div>
    </body>
</html>