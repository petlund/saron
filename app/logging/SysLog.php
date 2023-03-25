<?php
require_once "config.php";

class SysLog{

    function saron_dev_log($logLevel, $class, $method, $msg, $sql=""){
        if(TEST_ENV === true){
            $msgTypeName = "";
            if($logLevel > LOG_LEVEL){
                return;
            } 
            switch($logLevel){
                case 0:
                    $msgTypeName = "LOG_EMERG";
                    break;
                case 1:
                    $msgTypeName = "LOG_ALERT";
                    break;
                case 2:
                    $msgTypeName = "LOG_CRIT";
                    break;
                case 3:
                    $msgTypeName = "LOG_ERR";
                    break;
                case 4:
                    $msgTypeName = "LOG_WARNING";
                    break;
                case 5:
                    $msgTypeName = "LOG_NOTICE";
                    break;
                case 6:
                    $msgTypeName = "LOG_INFO";
                    break;
                default:
                    $msgTypeName = "LOG_DEBUG";

            }
            error_log("****** " . $msgTypeName . ", Class: " .  $class . ", Method: " . $method . " *****");
            if(strlen($msg)>0){
                error_log($msg . "\r\n");
            }            
            if(strlen($sql)>0){
                error_log($sql . "\r\n");
            }            
            error_log("****** ". $msgTypeName . " END ******\r\n\r\n");
        }
    }  
}    