<?php
require_once "config.php";
require_once SARON_ROOT . "app/database/queries.php";
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . "app/entities/SaronUser.php";
 
class db {
    
    private $connection;
    function __construct() {
        mysqli_report(MYSQLI_REPORT_STRICT);
        $appErrorMsg="";
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
        if($connection !== null){
            if ($connection->connect_error) { 
                die("Connection failed: " . $connection->connect_error); 
            } 
            $connection->close(); 
        }
    }
    
    function dispose(){
        $this->__destruct();
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
        $this->php_dev_error_log("transaction_roll_back", "");
        return $this->connection->rollback();
    }
    
    
    public function delete($sqlDelete){
        if(!$listResult = $this->connection->query($sqlDelete)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            $this->php_dev_error_log("delete", $sqlDelete);
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
            $this->php_dev_error_log("update", $sql);
            throw new Exception($this->jsonErrorMessage("SQL-Error in update statement!", null, $technicalErrMsg));
        }
        return true;
    } 
    
    
    public function exist($FirstName, $LastName, $DateOfBirth, $Id=-1){
        $sql = "select count(*) as c from People where "; 
        $sql.= "UPPER(CONVERT(BINARY " . DECRYPTED_FIRSTNAME . " USING utf8)) like '" . $FirstName . "' and ";
        $sql.= "UPPER(CONVERT(BINARY " . DECRYPTED_LASTNAME . " USING utf8)) like '" . $LastName . "' and ";
        $sql.= "DateOfBirth like '" . $DateOfBirth . "'";
        if($Id>0){
            $sql.= " and ID <> '" . $Id . "'";            
        }
        
        if(!$listResult = $this->connection->query($sql)){
            $this->php_dev_error_log("exist ", $sql);
            throw new Exception($this->jsonErrorMessage("SQL-Error in select /exist/ statement!", null, $sql));
        }
        $countRows = "0";
        while($countRow = mysqli_fetch_array($listResult)){
            $countRows = $countRow["c"];
        } 
        mysqli_free_result($listResult);
        
        if($countRows !== "0"){
            return true;
        }
        return false;
    }
    
        
    public function insert($insert, $keyTable, $keyColumn){
        $LastId = "0";
        try{
            if(!$listResult = $this->connection->query($insert)){
                throw new Exception($this->jsonErrorMessage("SQL-Error in insert statement!", null, $this->connection->error));
            }
            else{
                $sql = "Select " . $keyColumn . " from " . $keyTable . " Where " . $keyColumn . " = LAST_INSERT_ID()";
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
            $this->php_dev_error_log("insert", $insert);
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            throw new Exception($this->jsonErrorMessage("Error in insert statement!", null, $technicalErrMsg));
        }
        
    }     
    
    
    public function select($saronUser, $select, $from, $where, $orderby, $limit, $responstype="Records"){
        $sqlSelect = $select . $from . $where . $orderby . $limit;
        $sqlCount = "select count(*) as c " . $from . $where;
        try{
            return $this->selectSeparate($saronUser, $sqlSelect, $sqlCount, $responstype);
        }
        catch(Exception $error){
            $this->php_dev_error_log("select", $select);
            throw new Exception($error->getMessage());
        }
    } 
    
    public function selectSeparate($saronUser, $sqlSelect, $sqlCount, $responstype="Records"){
        if(!$listResult = $this->connection->query($sqlSelect)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            $this->php_dev_error_log("selectSeparate 1 ", $sqlSelect);
            throw new Exception($this->jsonErrorMessage("SQL-Error in select /list/ statement!", null, $technicalErrMsg));
        }

        if(!$countResult = $this->connection->query($sqlCount)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            $this->php_dev_error_log("selectSeparate 2 ", $sqlSelect);
            throw new Exception($this->jsonErrorMessage("SQL-Error in select count statement!", null, $technicalErrMsg));
        }

        $arrayResult = $this->resultSetToArray($listResult);
        $jsonResult = $this->processRowSet($saronUser, $arrayResult, $countResult, $responstype);   
            
        return $jsonResult;
    } 
    
    public function sqlQuery($sql){
        $listResult = $this->connection->query($sql);
        if(!$listResult){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            echo "SQL-Error in statement: <br>" .  $sql . "<br>" .  $technicalErrMsg; 
            $this->php_dev_error_log("sqlQuery", $sql);
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
        $this->php_dev_error_log("jsonErrorMessage", $errMsg);
        return json_encode($error);        
    }
        
    
    private function resultSetToArray($listResult){
        while($listRow = mysqli_fetch_array($listResult, MYSQLI_ASSOC)){
            $listRows[] = $listRow;
        } 
        mysqli_free_result($listResult);
        return $listRows;
    }    
    
    
    
    private function processRowSet($saronUser, $listRows, $countResult, $responstype){
        $jTableResult['Result'] = "OK";
        $jTableResult[$responstype] = $listRows;
        
        $countRows = "0";
        while($countRow = mysqli_fetch_array($countResult)){
            $countRows = $countRow["c"];
        } 
        mysqli_free_result($countResult);

        $jTableResult['TotalRecordCount'] = $countRows;
        $jTableResult['user_role'] = $saronUser->getRole();

        $jsonResult = json_encode($jTableResult);
        
        if($jsonResult===false){
            $this->php_dev_error_log("processRowSet", "");
            throw new Exception($this->jsonErrorMessage("Error i json_encode funktionen!", null, " -- processRowSet"));
        }
        return $jsonResult;
    }    
    
    
    
    function php_dev_error_log($method, $msg){
        if(TEST_ENV === true){
            error_log("\n\nDB method " . $method . ": " . $msg);
        }
    }

    
}
