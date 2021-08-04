<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "menu.php";

     
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Person</title>
    </head>
    <body>
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "people.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "member.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "keys.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "birthdays.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "baptist.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "total.js");?>"></script>     
        
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/localization/jquery.jtable.se.js"></script>        
        <table class="saronFilter saronSmallText" style="width:0%; white-space: nowrap">
            <tr class="saronFilter">
                <td  class="saronFilter">
                    <form class="forms" id="mainfilter">Grupp:
                        <select id="groupId" name="groupId" onchange="filterPeople('<?php include('../util/viewId.php');?>', true);" >
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
                        <option value="17">Medhjälpare</option>
                        </select>     
                   </form>
                </td>
                <td  class="saronFilter">
                    Söksträng:
                    <input type="text" name="searchString" id="searchString" oninput="filterPeople('<?php include('../util/viewId.php');?>');"/>
                </td>
            </tr>
        </table>
        <div id="<?php include('../util/viewId.php');?>"></div>
    </body>
</html>