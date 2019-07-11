<?php
require_once "config.php";
require_once SARON_ROOT . "app/database/queries.php";
require_once SARON_ROOT . "app/access/wp-authenticate.php";
 
class db {
    
    private $connection;
    private $appErrorMsg;
    function __construct() {
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $appErrorMsg = "Error when try connect to database.";
            $this->connection = new mysqli(HOST, USER, PASSWORD, DATABASE);                     
            $appErrorMsg = "Error when try to set UTF-8";
            $this->connection->set_charset("utf8");    
            $appErrorMsg="";
        }
        catch(Exception $error){            
            throw new Exception($this->jsonErrorMessage($appErrorMsg, $error, $sql=""));                               
        }            
    }
    
    
    function __destruct(){
        if($this->connection!=null){
            $this->connection->close();
        }                        
    }
    
    
    
    function transaction_begin(){
        if(!$this->connection->autocommit(false)){
            throw new Exception($this->jsonErrorMessage("Transaktionsfel (Begin). Kan inte uppdatera."));                               
        }
    }
    
    
    function transaction_end(){
        return $this->connection->autocommit(true);       
    }
    
    
    function transaction_roll_back(){
        return $this->connection->rollback();
    }
    
    
    public function delete($sqlDelete){
        if(!$listResult = $this->connection->query($sqlDelete)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            throw new Exception($this->jsonErrorMessage("SQL-Error in delete statement!", null, $technicalErrMsg));
        }
        else{
            $deleteResult['Result'] = "OK";
            return json_encode($deleteResult);
        }                
    }

    public function update($update, $set, $where){
        $sql = $update . $set . $where;
        if(!$listResult = $this->connection->query($sql)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            throw new Exception($this->jsonErrorMessage("SQL-Error in update statement!", null, $technicalErrMsg));
        }
        return true;
    } 
    
    
    
    
    public function selectHome($user, $HomeId, $responstype){
        $sql = SQL_STAR_HOMES . ", ";  
        $sql.= ADDRESS_ALIAS_LONG_HOMENAME . ", "; 
        $sql.= NAMES_ALIAS_RESIDENTS . ", ";
        $sql.= setUserRoleInQuery($user); 
        $sql.= "FROM Homes ";
        $sql.= "WHERE Id = " . $HomeId . ";"; 
        
        $sqlCount = "Select count(*) as c from Homes WHERE Id = "  . $HomeId . ";";
        
        if(!$listResult = $this->connection->query($sql)){
            throw new Exception($this->jsonErrorMessage("SQL-Error in selectHome statement!", null, $this->connection->error));
        }

        if(!$countResult = $this->connection->query($sqlCount)){
            throw new Exception($this->jsonErrorMessage("SQL-Error in selectHomeCount statement!", null, $this->connection->error));
        }
        $unSaltedRowSet = $this->resultSetToArray($listResult);
        $jsonResult = $this->processRowSet($user, $unSaltedRowSet, $countResult, $responstype);                
        return $jsonResult;           
    }
    
    public function exist($FirstName, $LastName, $DateOfBirth, $Id=-1){
        $sql = "select count(*) as c from People where "; 
        $sql.= "UPPER(CONVERT(BINARY " . DECRYPTED_FIRSTNAME . " USING utf8)) like '%" . $FirstName . "' and ";
        $sql.= "UPPER(CONVERT(BINARY " . DECRYPTED_LASTNAME . " USING utf8)) like '%" . $LastName . "' and ";
        $sql.= "DateOfBirth like '" . $DateOfBirth . "'";
        error_log("SQL: " . $sql);
        if($Id>0){
            $sql.= " and ID <> '" . $Id . "'";            
        }
        
        if(!$listResult = $this->connection->query($sql)){
            throw new Exception($this->jsonErrorMessage("SQL-Error in select /exist/ statement!", null, $sql));
        }
        $countRows = "0";
        while($countRow = mysqli_fetch_array($listResult)){
            $countRows = $countRow["c"];
        } 
        mysqli_free_result($listResult);
        
        if($countRows !== "0"){
            $errorMsg = "En person med identitet:<br><b>" . $FirstName . " " . $LastName . " " . $DateOfBirth . "</b><br>finns redan i databasen.";
            throw new Exception($this->jsonErrorMessage($errorMsg));
        }       
    }
    
        
    public function insert($insert, $keyTable, $keyColumn){
        $LastId = "0";
        try{
            if(!$listResult = $this->connection->query($insert)){
                throw new Exception($this->jsonErrorMessage("SQL-Error in insert statement!", null, $this->connection->error));
            }
            else{
                $sql = "Select " . $keyColumn . " from " . $keyTable . " Where Id = LAST_INSERT_ID()";
                if(!$listResult = $this->connection->query($sql)){
                    throw new Exception($this->jsonErrorMessage("SQL-Error in LAST_INSERT_ID() statement after insert.", null, $this->connection->error));
                }
                else{
                    while($listRow = mysqli_fetch_array($listResult)){
                        $LastId = $listRow[$keyColumn];
                    }      
                }
            }
            return $LastId;
        }
        catch(Exception $error){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            throw new Exception($this->jsonErrorMessage("Error in insert statement!", null, $technicalErrMsg));
        }
        
    }     
    
    
    public function select($user, $select, $from, $where, $orderby, $limit, $responstype="Records"){
        $sqlSelect = $select . $from . $where . $orderby . $limit;
        $sqlCount = "select count(*) as c " . $from . $where;
        try{
            return $this->selectSeparate($user, $sqlSelect, $sqlCount, $responstype);
        }
        catch(Exception $error){
            throw new Exception($error->getMessage());
        }
    } 
    
    public function selectSeparate($user, $sqlSelect, $sqlCount, $responstype="Records"){
        if(!$listResult = $this->connection->query($sqlSelect)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            throw new Exception($this->jsonErrorMessage("SQL-Error in select /list/ statement!", null, $technicalErrMsg));
        }

        if(!$countResult = $this->connection->query($sqlCount)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            throw new Exception($this->jsonErrorMessage("SQL-Error in select count statement!", null, $technicalErrMsg));
        }

        $unSaltedRowSet = $this->resultSetToArray($listResult);
        $jsonResult = $this->processRowSet($user, $unSaltedRowSet, $countResult, $responstype);   
            
        return $jsonResult;
    } 
    
    public function sqlQuery($sql){
        $listResult = $this->connection->query($sql);
        if(!$listResult){
            echo $this->getPublicErrMessage("SQL-Error in select statement.", $sql); 
            return false;
        }
        $resultArray = $this->resultSetToArray($listResult);

        return $resultArray;
    }

    private function jsonErrorMessage($appErrorMsg, $connectionError=null, $sqlError=""){
        $error = array();
        $error["Result"] = "ERROR";
        $errMsg = $appErrorMsg;
        if(TEST_ENV){
            if($connectionError !== null){
                $errMsg.= "Error in connection.<BR>File: " . $connectionError->getFile();
                $errMsg.= ", Line: " . $connectionError->getLine();
                $errMsg.= "<BR>Code (" . $connectionError->getCode();
                $errMsg.= "): " . $connectionError->getMessage();
            }
            else if(strlen($sqlError)>0){
                $errMsg.= "<BR>" . $sqlError;
            }
        }
        $errMsg.= mb_convert_encoding("<br><br>Om problemet kvarstår kontakta systemadministratören<BR>", "UTF-8", "auto");
        $errMsg.= "<a href='mailto:" . EmailSysAdmin . "'>" . EmailSysAdmin . "</a>";
        $error["Message"] = $errMsg;
        return json_encode($error);        
    }
        
    
    private function resultSetToArray($listResult){
        while($listRow = mysqli_fetch_array($listResult)){
            $listRows[] = $listRow;
        } 
        mysqli_free_result($listResult);
        return $listRows;
    }    
    
    
    
    private function processRowSet($user, $listRows, $countResult, $responstype){
        $jTableResult['Result'] = "OK";
        $jTableResult[$responstype] = $listRows;
        
        $countRows = "0";
        while($countRow = mysqli_fetch_array($countResult)){
            $countRows = $countRow["c"];
        } 
        mysqli_free_result($countResult);

        $jTableResult['TotalRecordCount'] = $countRows;
        if(isEditor($user)){
            $jTableResult['user_role'] = SARON_ROLE_EDITOR;
        }
        else{
            $jTableResult['user_role'] = SARON_ROLE_VIEWER;            
        }

        $jsonResult = json_encode($jTableResult);
        
        if($jsonResult===false){
            throw new Exception($this->jsonErrorMessage("Error i json_encode funktionen!", null, " -- processRowSet"));
        }
        return $jsonResult;
    }    
}
