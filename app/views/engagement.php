<?php
header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache"); //HTTP 1.0
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once "config.php";
    require_once SARON_ROOT . "menu.php";

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
                <td class="saronFilter">
                    Söksträng:
                    <input type="text" name="searchString" id="searchString" oninput="filterPeople('<?php include('../util/viewId.php');?>');"/>
                </TD>
        </table>
        <div id="<?php include('../util/viewId.php');?>"></div>
    </body>
</html> 