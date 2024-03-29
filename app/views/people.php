<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "menu.php";
require_once SARON_ROOT . "app/util/AppCanvasName.php";

     
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Person</title>
    </head>
    <body>
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES, "people.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES, "homes.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES, "member.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES, "baptist.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES, "keys.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES, "birthdays.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES_ORG, "engagement.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getDistPath(APP_JS_TABLES, "total.js");?>"></script>     
        
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/localization/jquery.jtable.se.js"></script>        
        <table class="saronFilter saronSmallText" style="width:0%; white-space: nowrap">
            <tr class="saronFilter">
                <td  class="saronFilter">
                    <form class="forms" id="mainfilter">Grupp:
                        <select id="groupId" name="groupId" class="filter"/>
                        <option selected="selected" value="0">Medlemmar</option>
                        <option value="20">Registerförd</option>
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
                        <option value="14">Församlingens vänner</option>
                        <option value="15">Ej medlem</option>
                        <option value="16">Underlag för anonymisering under <?php include('../util/CurrentYear.php');?></option>
                        <option value="17">Anonymiserade</option>
                        </select>     
                   </form>
                </td>
                <td  class="saronFilter">
                    Söksträng:
                    <input type="text" name="searchString" id="searchString" class="filter"/>
                </td>
            </tr>
        </table>
        <div id="<?php echo getAppCanvasName();?>"></div>        
    </body>
</html>