<?php
require_once "config.php";
    $saronUser = new SaronUser(wp_get_current_user());
    echo "Inloggad som " . $saronUser->getNameAndRole(); 
