<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once 'config.php';
    require_once SARON_ROOT . "menu.php";
    require_once SARON_ROOT . "app/util/AppCanvasName.php";
   
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Log</title>
   </head>
    <body>
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("util/", "changeLogFilter.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "changelog.js");?>"></script>     
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/localization/jquery.jtable.se.js"></script>                  
        
        <table class="saronFilter saronSmallText" style="width:0%; white-space: nowrap">
            <tr class="saronFilter">
                <td  class="saronFilter">
                    <form class="forms" id="mainfilter">
                        Användare:
                        <select id="uid" name="user" onchange="changeLogFilter('<?php echo getAppCanvasName(); ECHO '\', false, \'';?>');" >
                            <option selected="selected" value="">Alla</option>
                            <option value="saron">saron</option>
                            <option value="System">System</option>
                        </select>
                        Typ av ändring:
                        <select id="cid" name="change" onchange="changeLogFilter('<?php echo getAppCanvasName(); ECHO '\', false, \'';?>');" >
                            <option selected="selected" value="">Alla</option>
                        </select>     
                   </form>
                </td>
            </tr>
        </table>
        <div id="<?php echo getAppCanvasName();?>"></div>        
    </body>
</html>

