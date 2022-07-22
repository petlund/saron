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
        <title><?php echo NameOfRegistry;?> - Adress</title>
        <link href="/<?php echo THREE_PP_URI;?>jtable/jquery-ui-1.12.1.custom/jquery-ui.css" rel="stylesheet" type="text/css" />         
        
    </head>
    <body>
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "homes.js");?>"></script>     
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/localization/jquery.jtable.se.js"></script>                  
        <table class="saronSmallText" style="width:0%; white-space: nowrap">
            <tr   class="saronFilter">
                <td   class="saronFilter">
                    <form class="forms" id="mainfilter">
                        Grupp:          
                        <select id="groupId" name="groupId" onchange="filter('<?php echo getAppCanvasName(); ECHO '\', false, \'' . TABLE_NAME_HOMES;?>');" >
                            <option selected="selected" value="0">Alla hem</option>
                            <option value="1">Hem utan mail- och mobiluppgifter</option>
                            <option value="2">Hem med brevutskick</option>
                        </select>     
                    </form>
                </td>
                <td   class="saronFilter">
                    Söksträng:
                    <input type="text" name="searchString" id="searchString" oninput="filter('<?php echo getAppCanvasName();?>');"/>
                </td>
            </tr>
        </table>    
        <div id="<?php echo getAppCanvasName();?>"></div>        
    </body>
</html>