<?php
date_default_timezone_set('Europe/Stockholm');

define("FullNameOfCongregation", "FÖRSAMLINFSNAMNET");
define("ShortNameOfCongregation", "FÖRSAMLINGSNAMNET KORT");
define("UrlOfRegistry", "url...");
define("UrlOfCongregation", "url");
define("NameOfRegistry", "Saron");
define("EmailSysAdmin", "email to support");

define ("REQUIRE_VIEWER_ROLE", 0);
define ("REQUIRE_EDITOR_ROLE", 1);
define ("REQUIRE_ORG_VIEWER_ROLE", 0);
define ("REQUIRE_ORG_EDITOR_ROLE", 1);
define ("TICKET_RENEWAL_CHECK", true);

// DATABASE CONFIG
define("HOST", "hostname");         // The host you want to connect to.
define("USER", "user");             // The database username. 
define("PASSWORD", "password");     // The database password. 
define("DATABASE", "database");     // The database name.

//
define("COOKIE_NAME", "Saron");
define("COOCKIE_EXPIRES", 3600);
define("SESSION_EXPIRES", 600);
define("COOCKIE_PATH", '/saron');
define("COOCKIE_DOMAIN", '');
define("COOCKIE_SECURE", true);
define("COOCKIE_HTTP_ONLY", true);
define("COOCKIE_SAMESITE", "Strict");
define("TICKET_RENEWIAL_PERIOD_IN_SEC", 60);

// APPROVAL TEXT FOR GDPR AGREEMENT WITH THE MEMBERS
define ("APPROVAL", "Jag godkänner ....");
// this text replace all fields witch include personel data on delete/anonymous
define("ANONYMOUS", "_Anonymiserad_");

// PATH TO wp-load.php in your wordpress installation
define("WP_SARON_LOGIN", "wp-load.php");
define("LOGOUT_URI", "app/access/SaronLogin.php?logout=true");

// DISPLAYNAME USER ROLE
define("SARON_ROLE_PREFIX", "saron_");
define("SARON_ROLE_EDITOR", "edit");
define("SARON_ROLE_VIEWER", "viewer");
define("SARON_ROLE_ORG", "org");
define("SARON_DISPLAY_NAME_EDITOR", "Uppdaterare");
define("SARON_DISPLAY_NAME_ORG", "Uppdaterare organisation");
define("SARON_DISPLAY_NAME_VIEWER", "Läsanvändare");

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

define ("APP_JS", "app/js/");
define ("JS_DIST", "dist/");

// RESOURCES FOR PDF GENERATION.
// https://tcpdf.org/ 
define("THREE_PP_PATH", WEB_ROOT . SARON_URI . "3pp/tcpdf");
define("THREE_PP_URI", "3pp/jtable");
define("JTABLE_PATH", WEB_ROOT . SARON_URI . THREE_PP_URI);

