<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';

require_once SARON_ROOT . 'app/enteties/Home.php';
require_once SARON_ROOT . 'app/enteties/Homes.php';
require_once SARON_ROOT . 'app/enteties/News.php';
require_once SARON_ROOT . 'app/enteties/People.php';
require_once SARON_ROOT . 'app/enteties/Person.php';
require_once SARON_ROOT . 'app/enteties/SaronUser.php';
require_once SARON_ROOT . 'app/enteties/SaronUsers.php';
require_once SARON_ROOT . 'app/enteties/Statistics.php';

    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = true;
    $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole)){
        echo notPermittedMessage();
        exit();
    }

