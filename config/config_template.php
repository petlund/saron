<?php
date_default_timezone_set('Europe/Stockholm');

define("FullNameOfCongregation", "Baptistförsamlingen Saron-Korskyrkan");
define("ShortNameOfCongregation", "Korskyrkan");
define("UrlOfRegistry", "medlem.korskyrkan.se");
define("UrlOfCongregation", "www.korskyrkan.se");
define("NameOfRegistry", "Registret");
define("EmailSysAdmin", "sysman@korskyrkan.se");

// DATABASE CONFIG
define("HOST", "localhost");     // The host you want to connect to.
define("USER", "saron");    // The database username. 
define("PASSWORD", "saron");    // The database password. 
define("DATABASE", "saron");    // The database name.
 
// APPROVAL TEXT FOR GDPR AGREEMENT WITH THE MEMBERS
define ("APPROVAL", "Jag godkänner att Baptistförsamlingen Saron Korskyrkan registerför uppgifter inom ovan nämda rubrikområden i elektroniskt register, Uppgifterna är endast till för internt bruk inom församlingen. Icke individbaserad statistik såsom antalet medlemmar kan distribueras efter godkännande av församlingsledningen.");

// RESOURCES FOR PDF AND GRAPH GENERATION.
// https://tcpdf.org/ 
define("TCPDF_PATH", "/opt/registry_res/tcpdf");
// http://jpgraph.net/

// Path to True Type Fonts for jpgraph
define('TTF_DIR','/Library/Fonts/');

// https://www.webhostinghero.com/wordpress-authentication-integration-with-php/
// PATH TO wp-load.php in your wordpress installation
define("WP_SARON_LOGIN", "wp-load.php");

// DISPLAYNAME USER ROLE
define("SARON_ROLE_PREFIX", "saron_");
define("SARON_ROLE_EDITOR", "edit");
define("SARON_ROLE_VIEWER", "viewer");
define("SARON_DISPLAY_NAME_EDITOR", "Uppdaterare");
define("SARON_DISPLAY_NAME_VIEWER", "Tittare");


