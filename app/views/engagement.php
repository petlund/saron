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
       <script type="text/JavaScript" src="/<?php echo getJsAppDistPath("tables/org/", "engagement.js");?>"></script>     

        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/jquery.jtable.js"></script>
        <script type="text/javascript" src="/<?php echo THREE_PP_URI;?>jtable/localization/jquery.jtable.se.js"></script>   
        <table >
            <tr class='saronSmallText saronFilter'>
                <td  class="saronFilter">
                    <form class="forms" id="mainfilter">Grupp:
                        <select id="groupId" name="groupId" onchange="filter('<?php echo getAppCanvasName(); ECHO '\', false, \'';?>');" >
                        <option selected="selected" value="0">Alla</option>
                        <option value="1">Att se över</option>
                        </select>     
                   </form>
                </td>
                <td class="saronFilter">
                    Söksträng:
                    <input type="text" name="searchString" id="searchString" oninput="filter('<?php echo getAppCanvasName(); ECHO '\', false, \'';?>');"/>
                </td>
        </table>
        <div id="<?php echo getAppCanvasName();?>"></div>
    </body>
</html> 