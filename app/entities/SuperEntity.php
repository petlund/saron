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
class AbstractEntity {
    
    function getZeroToNull($nr){
        if($nr === null){
            return null;
        }
        if($nr===0){
            return "null";
        }
        else{
            return $nr;
        }    
        
    }
    
    function getEncryptedSqlString($str){
        if(strlen($str)>0){
            return "AES_ENCRYPT('" . salt() . $str . "', " . PKEY . ")";
        }
        else{
            return "null";                    
        }
    }
    
    
    function getSqlString($str){
        if(strlen($str)>0){
            return $str;
        }
        else{
            return "null";                    
        }
    }


    function getSqlDateString($str){
        if(strlen($str)>0){
            return "'" . $str . "'";
        }
        else{
            return "null";                    
        }
    }
   
}
