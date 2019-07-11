<?php
require_once "config.php";
$display_role = "NO ROLE";

for($i = 0; $i < count($user->roles); $i++){
    if($user->roles[$i]===SARON_ROLE_PREFIX . SARON_ROLE_EDITOR){
        $display_role = SARON_DISPLAY_NAME_EDITOR;
        break;
    }
    if($user->roles[$i]===SARON_ROLE_PREFIX . SARON_ROLE_VIEWER){
        $display_role = SARON_DISPLAY_NAME_VIEWER;
        Break;
    }
}

echo "Inloggad som " . $user->user_firstname . " " . $user->user_lastname . " - " . $display_role; 
