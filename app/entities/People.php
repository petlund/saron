<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of People
 *
 * @author peter
 */
class People {
    //put your code here
    
    
    function getSortSql(){
        $sqlOrderBy = ""; 
        $jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
        if(Strlen($jtSorting)>0 and Strlen($sqlOrderByLatest)>0){
            $sqlOrderBy = "ORDER BY " . $sqlOrderByLatest . ", " . $jtSorting . " ";
        }
        else if(Strlen($jtSorting)==0 and Strlen($sqlOrderByLatest)>0){
            $sqlOrderBy = "ORDER BY " . $sqlOrderByLatest . " ";
        }
        else if(Strlen($jtSorting)>0 and Strlen($sqlOrderByLatest)==0){
            $sqlOrderBy = "ORDER BY " . $jtSorting . " ";
        }
        else{
            $sqlOrderBy = "";         
        }

        $jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);
        $sqlLimit = "LIMIT " . $jtStartIndex . "," . $jtPageSize . ";";
        
    }
    
}
