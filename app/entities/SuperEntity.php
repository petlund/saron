<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AbstractEntity
 *
 * @author peter
 */
class SuperEntity {
    function __construct() {
        
    }

    
    function getZeroToNull($nr){
        if($nr === null){
            return 'null';
        }
        if($nr===0){
            return 'null';
        }
        else{
            return $nr;
        }    
        
    }
    
    function getEncryptedSqlString($str){
        if(strlen($str)>0){
            return "AES_ENCRYPT('" . $this->salt() . $str . "', " . PKEY . ")";
        }
        else{
            return 'null';                    
        }
    }
    
    
    function getSqlString($str){
        if(strlen($str)>0){
            return "'" . $str . "'";
        }
        else{
            return 'null';                    
        }
    }


    function getSqlDateString($str){
        if(strlen($str)>0){
            return "'" . $str . "'";
        }
        else{
            return 'null';                    
        }
    }

    
    
    function salt(){        
        //$abc = "!#$%&()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_`abcdefghijklmnopqrstuvwxyz{|}~";
        $abc = "!#$%&()*+,-./0123456789:;=?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_`abcdefghijklmnopqrstuvwxyz{|}~";
        $str = "";
        while(strlen($str)<SALT_LENGTH-1){
            $str.= substr($abc, rand(0, strlen($abc)), 1);
        }
        return $str;
    }   
}
