<?php
$saron = new stdClass();
$saron->start = 0;
$saron->text = "Hello world";
$saron->end = 10;

$myJSON = json_encode($saron);
?>  
<html>
    <head>
    <meta charset="UTF-8">
    <script><?php echo 'const tj = ' . $myJSON.";";?></script>

    <script type="text/JavaScript" src="js_test.js"></script>     

</head>
    <body>
        <p class="cl">Testar nya funktioner</p>
        js_test.php
    </body>
</html>