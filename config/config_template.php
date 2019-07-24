<?php
date_default_timezone_set('Europe/Stockholm');

define("FullNameOfCongregation", "Baptistförsamlingen Saron-Korskyrkan");
define("ShortNameOfCongregation", "Korskyrkan");
define("UrlOfRegistry", "saron.korskyrkan.se");
define("UrlOfCongregation", "www.korskyrkan.se");
define("NameOfRegistry", "Saron");
define("EmailSysAdmin", "sysman@korskyrkan.se");

// DATABASE CONFIG
define("HOST", "hostname");         // The host you want to connect to.
define("USER", "user");             // The database username. 
define("PASSWORD", "password");     // The database password. 
define("DATABASE", "database");     // The database name.
 
// APPROVAL TEXT FOR GDPR AGREEMENT WITH THE MEMBERS
define ("APPROVAL", "Jag godkänner ....");
// this text replace all fields witch include personel data on delete/anonymous
define("ANONYMOUS", "_Anonymiserad_");

// PATH TO wp-load.php in your wordpress installation
define("WP_SARON_LOGIN", "wp-load.php");

// DISPLAYNAME USER ROLE
define("SARON_ROLE_PREFIX", "saron_");
define("SARON_ROLE_EDITOR", "edit");
define("SARON_ROLE_VIEWER", "viewer");
define("SARON_DISPLAY_NAME_EDITOR", "Uppdaterare");
define("SARON_DISPLAY_NAME_VIEWER", "Tittare");

//Override OTP if true
define("TEST_ENV", false);

define("WEB_ROOT", "www/");
define("WP_URI", "");
define("SARON_URI", "saron/");
//root for Saron
define("SARON_ROOT", WEB_ROOT . SARON_URI);
//Root for Wordpress
define("WP_ROOT", WEB_ROOT . WP_URI);
// Path to Private key 
define ("PKEY_FILE", "file:////" . SARON_ROOT . "cert/server.key");

// RESOURCES FOR PDF GENERATION.
// https://tcpdf.org/ 
define("TCPDF_PATH", WEB_ROOT . SARON_URI . "tcpdf");

