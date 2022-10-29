<?php
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once "config.php";
    require_once SARON_ROOT . "menu.php";
    require_once SARON_ROOT . "app/util/AppCanvasName.php";
    
?>
<html>   
    <Head>
        <meta charset="UTF-8">
        <title><?php echo NameOfRegistry;?> - Organisation</title> 
    </Head>
    <body> 
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "unittype.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "unit.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "tree.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "list.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "role.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "role_unittype.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "status.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "pos.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "version.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "memberstateAndReports.js");?>"></script>     
        <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/", "memberstate.js");?>"></script>     

        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/localization/jquery.jtable.se.js"></script>   
        <div class='saronSmallText'></div>
        <table class="saronFilter saronSmallText" style="width:0%; white-space: nowrap">
            <tr class="saronFilter">
                <td  class="saronFilter">
                    Sök i organisationstruktur:
                    <input type="text" name="searchString" id="searchString" oninput="filter('<?php  echo getAppCanvasName(); ECHO '\', false, \'';?>');"/>
                </td>
            </tr>
        </table>
        <div id="<?php echo getAppCanvasName();?>"></div>        
    </body
</html> 