<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header('Access-Control-Expose-Headers: filename');

require_once 'config.php'; 
require_once SARON_ROOT . "app/util/AppCanvasName.php";

$fileName = getAppCanvasName("help") . ".pdf";
$file = SARON_PDF_URI . $fileName;
$size = filesize($file);

header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="' . $fileName . '"');
header('Content-Transfer-Encoding: binary');


ob_clean(); 
flush();
readfile ( $file );
exit();
