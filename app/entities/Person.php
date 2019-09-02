<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Person
 *
 * @author peter
 */
class Person {
    private $HomeId;
    private $LastName;
    private $FirstName;
    private $DateOfBirth;
    private $Gender;
    private $Email;
    private $Mobile;
    private $DateOfMembershipStart;
    private $MembershipNo;
    private $VisibleInCalendar;
    private $Comment;
       
    function __construct() {
        $this->HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);
        $this->LastName = (String)filter_input(INPUT_POST, "LastName", FILTER_SANITIZE_STRING);
        $this->FirstName = (String)filter_input(INPUT_POST, "FirstName", FILTER_SANITIZE_STRING);
        $this->DateOfBirth = (String)filter_input(INPUT_POST, "DateOfBirth", FILTER_SANITIZE_STRING);
        $this->Gender = (int)filter_input(INPUT_POST, "Gender", FILTER_SANITIZE_NUMBER_INT);
        $this->Email = (String)filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL);
        $this->Mobile = (String)filter_input(INPUT_POST, "Mobile", FILTER_SANITIZE_STRING);
        $this->DateOfMembershipStart = (String)filter_input(INPUT_POST, "DateOfMembershipStart", FILTER_SANITIZE_STRING);
        $this->MembershipNo = (int)filter_input(INPUT_POST, "MembershipNo", FILTER_SANITIZE_NUMBER_INT);
        $this->VisibleInCalendar = (int)filter_input(INPUT_POST, "VisibleInCalendar", FILTER_SANITIZE_NUMBER_INT);    
        $this->Comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
    }
    
    function read(){
        return;
    }

    function checkData(){
        $error = array();
        $error["Result"] = "ERROR";
        if(strlen($this->FirstName)==0 or strlen($this->LastName)==0 or strlen($this->DateOfBirth)==0){
            $error["Message"] = "Personen behöver ett för- och ett efternamn samt ett födelsedadum för att kunna lagras i registret";
        }

        if($this->MembershipNo < 1 and strlen($this->this->DateOfMembershipStart)!==0){
            $error["Message"] = "Personen har ett datum för start av medlemskap men saknar medlemsnummer. Lägg till ett medlemsnummer.";
        }

        if($this->VisibleInCalendar === 0 and strlen($this->DateOfMembershipStart)!==0){
            $error["Message"] = "Ange om personen ska vara synlig i adresskalendern eller ej.";
        }

        if($this->VisibleInCalendar === 2){
            if((strlen($this->DateOfMembershipStart)===0 and strlen($this->DateOfMembershipEnd)===0) or strlen($this->DateOfMembershipEnd)!==0){
                $error["Message"] = "Icke medlemmar ska inte vara synliga i adresskalendern.";
            }
        }

        if($this->MembershipNo > 0 and strlen($this->DateOfMembershipStart)===0){
            $error["Message"] = "Personen har ett inget datum för start av medlemskap men har ett medlemsnummer. Lägg till ett datum för start av medlemskap.";
        }
        if(strlen($error["Message"])>0){
            return json_encode($error);
        }
        else{
            return true;
        }
    }
    
    function insert(){
        $this->sqlInsert = "INSERT INTO People (LastNameEncrypt, FirstNameEncrypt, DateOfBirth, Gender, EmailEncrypt, MobileEncrypt, DateOfMembershipStart, MembershipNo, VisibleInCalendar, CommentEncrypt, Inserter, HomeId) ";
        $this->sqlInsert.= "VALUES (";
        $this->sqlInsert.= "AES_ENCRYPT('" . salt() . $this->LastName . "', " . PKEY . "), ";
        $this->sqlInsert.= "AES_ENCRYPT('" . salt() . $this->FirstName . "', " . PKEY . "), ";
        $this->sqlInsert.= "'" . $this->DateOfBirth . "', ";
        $this->sqlInsert.= "" . $this->Gender . ", ";
        
        if(strlen($this->Email)>0){
            $this->sqlInsert.= "AES_ENCRYPT('" . salt() . $this->Email . "', " . PKEY . "), ";
        }
        else{
            $this->sqlInsert.= "null, ";                    
        }
        
        if(strlen($this->Mobile)>0){
            $this->sqlInsert.= "AES_ENCRYPT('" . salt() . $this->Mobile . "', " . PKEY . "), ";
        }
        else{
            $this->sqlInsert.= "null, ";                    
        }
        
        if(strlen($this->DateOfMembershipStart)>0){    
            $this->sqlInsert.= "'" . $this->DateOfMembershipStart . "', ";
        } 
        else{ 
            $this->sqlInsert.= "null, ";        
        }

        if($this->MembershipNo===0){
            $this->sqlInsert.= "null, ";
        }
        else{
            $this->sqlInsert.= $this->MembershipNo . ", ";
        }    

        $this->sqlInsert.= $this->VisibleInCalendar . ", ";
        
        if(strlen($this->Comment)>0){
            $this->sqlInsert.= "AES_ENCRYPT('" . salt() . $this->Comment . "', " . PKEY . "), ";
        }
        else{
            $this->sqlInsert.= "null, ";                    
        }
        
        $this->sqlInsert.= $this->id . ", ";


        
        return;
    }

    function read(){
        return;
    }
    
    function update(){
        return;
    }
    
    function delete(){
        return;
    }
    
    
}
