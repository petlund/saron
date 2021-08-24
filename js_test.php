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
        Testar nya funktioner<br>
        js_test.php
    </body>
</html>