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
        $this->Id = (int)filter_input(INPUT_POST, "Id", FILTER_SANITIZE_NUMBER_INT);
        $this->FirstName = (String)filter_input(INPUT_POST, "FirstName", FILTER_SANITIZE_STRING);
        $this->LastName = (String)filter_input(INPUT_POST, "LastName", FILTER_SANITIZE_STRING);
        $this->DateOfBirth = (String)filter_input(INPUT_POST, "DateOfBirth", FILTER_SANITIZE_STRING);
        $this->DateOfDeath = (String)filter_input(INPUT_POST, "DateOfDeath", FILTER_SANITIZE_STRING);
        $this->MembershipNo = (int)filter_input(INPUT_POST, "MembershipNo", FILTER_SANITIZE_NUMBER_INT);
        $this->VisibleInCalendar = (int)filter_input(INPUT_POST, "VisibleInCalendar", FILTER_SANITIZE_NUMBER_INT);    
        $this->DateOfMembershipStart = (String)filter_input(INPUT_POST, "DateOfMembershipStart", FILTER_SANITIZE_STRING);
        $this->DateOfMembershipEnd = (String)filter_input(INPUT_POST, "DateOfMembershipEnd", FILTER_SANITIZE_STRING);
        $this->Gender = (int)filter_input(INPUT_POST, "Gender", FILTER_SANITIZE_NUMBER_INT);
        $this->Email = (String)filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL);
        $this->Mobile = (String)filter_input(INPUT_POST, "Mobile", FILTER_SANITIZE_STRING);
        $this->Comment = (String)filter_input(INPUT_POST, "Comment", FILTER_SANITIZE_STRING);
        $this->HomeId = (int)filter_input(INPUT_POST, "HomeId", FILTER_SANITIZE_NUMBER_INT);
    }
    
    
    function getCurrentHomeId(){
        return $this->HomeId;
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

        if($this->MembershipNo < 1 and strlen($this->DateOfMembershipStart)>0){
            $error["Message"] = "Personen har ett datum för start av medlemskap men saknar medlemsnummer. Lägg till ett medlemsnummer.";
        }

        if($this->VisibleInCalendar === 0 and strlen($this->DateOfMembershipStart)>0){
            $error["Message"] = "Ange om personen ska vara synlig i adresskalendern eller ej.";
        }

        if($this->VisibleInCalendar === 2){
            if((strlen($this->DateOfMembershipStart)===0 and strlen($this->DateOfMembershipEnd)===0) or strlen($this->DateOfMembershipEnd)!==0){
                $error["Message"] = "Icke medlemmar ska inte vara synliga i adresskalendern.";
            }
        }

        if($this->MembershipNo > 0 and strlen($this->DateOfMembershipStart)===0){
            $error["Message"] = "Personen har ett inget datum för start av medlemskap men har ett medlemsnummer. Lägg till ett datum för start av medlemskap eller ta bort medlemsnumret.";
        }
        if(strlen($error["Message"])>0){
            return json_encode($error);
        }
        else{
            return true;
        }
    }
    
    function getInsertSql($homeId, $inserter){
        $sqlInsert = "INSERT INTO People (LastNameEncrypt, FirstNameEncrypt, DateOfBirth, Gender, EmailEncrypt, MobileEncrypt, DateOfMembershipStart, MembershipNo, VisibleInCalendar, CommentEncrypt, Inserter, HomeId) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= $this->getEncryptedSqlString($this->LastName) . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->FirstName) . ", ";
        $sqlInsert.= $this->getSqlDateString($this->DateOfBirth) . ", ";
        $sqlInsert.= $this->Gender . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->Email) . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->Mobile) . ", ";
        $sqlInsert.= $this->getSqlDateString($this->DateOfMembershipStart) . ", ";
        $sqlInsert.= $this->getZeroToNull($this->MembershipNo) . ", ";
        $sqlInsert.= $this->VisibleInCalendar . ", ";
        $sqlInsert.= $this->getEncryptedSqlString($this->Comment) . ", ";
        $sqlInsert.= $inserter . ", ";
        $sqlInsert.= $this->getSqlString($this->homeId) . ") ";
 
        return $sqlInsert;
    }

        
    function getUpdateSql(){
        return;
    }
    
    
    function getDeleteSql(){
        return;
    }
    
    
    function getZeroToNull($nr){
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
