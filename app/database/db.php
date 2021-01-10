<?php
require_once "config.php";
require_once SARON_ROOT . "app/database/queries.php";
 
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
            echo $this->jsonErrorMessage($appErrorMsg, $error, $sql="");
            throw new Exception($this->jsonErrorMessage($appErrorMsg, $error, $sql=""));                               
        }            
    }
    
    
    function __destruct(){
        if($this->connection !== null){
            if ($this->connection->connect_error !== null) { 
                die("Connection failed: " . $this->connection->connect_error); 
            } 
            $this->connection->close(); 
        }
    }
    
    function dispose(){
        //$this->__destruct();
    }
    
    function transaction_begin(){
        $this->php_dev_error_log("transaction_begin", "");
        if(!$this->connection->autocommit(false)){
            throw new Exception($this->jsonErrorMessage("Transaktionsfel (Begin). Kan inte uppdatera."));                               
        }
    }
    
    
    function transaction_end(){
        $this->php_dev_error_log("transaction_end  ", "");
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
            throw new Exception($this->jsonErrorMessage("Exception in delete function", null, $technicalErrMsg));
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
            throw new Exception($this->jsonErrorMessage("Exception in update function", null, $technicalErrMsg));
        }
        return true;
    } 
    
    
    
    public function fieldValueExist($value, $Id, $field, $table){
        $sql = "select count(*) as c from " . $table . " where "; 
        $sql.= "UPPER(" . $field . ") like UPPER('" . $value . "') AND Id <> " . $Id . " ";
        
        if(!$listResult = $this->connection->query($sql)){
            $this->php_dev_error_log("Exception in exist function", $sql);
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
    
    
    
    public function isItTimeToReNewTicket($ticket){

        try{
            $sql = "select if(TIME_TO_SEC(TIMEDIFF(Now(), Time_Stamp)) > " . TICKET_RENEWIAL_PERIOD_IN_SEC . ",if(TIME_TO_SEC(TIMEDIFF(Now(), Last_Activity)) > " . HTTP_SESSION_EXPIRES . ", -1, 1),0) as Answer from SaronUser Where AccessTicket = '" . $ticket ."'";
            $result = $this->sqlQuery($sql);

            $answer=0;
            foreach($result as $aRow){
                $answer = $aRow["Answer"];
            }

            if($answer === '1'){
                return true;
            }
            else if($answer === '-1'){ // Not relevant with the new sql statement
                throw new Exception("Session timed out!");
            }
            return false;  // It is´t time yet          
        }
        catch(Exception $ex){
            throw new Exception($ex);
        }
    } 
    
    
    
    public function checkTicket($ticket, $editor, $org_editor){
        if(strlen($ticket) === 0){
            throw new Exception($this->jsonErrorMessage("Missing ticket", null, ""));
        }
        $this->cleanSaronUser(-1);
        
        $sql = "Select count(*) as c FROM SaronUser ";
        $sql.= "WHERE AccessTicket = '" . $ticket . "' and ";
        $sql.= "Editor >= " . $editor . " AND (";
        $sql.= "Org_Editor >= " . $org_editor . " OR Editor >= " . $editor . ")";
    
        if(!$listResult = $this->connection->query($sql)){
            $this->php_dev_error_log("Exception in exist function", $sql);
            throw new Exception($this->jsonErrorMessage("SQL-Error in select /exist/ statement!", null, $sql));
        }
        
        $countRows = 0;
        
        while($countRow = mysqli_fetch_array($listResult)){
            $countRows = $countRow["c"];
        } 
        mysqli_free_result($listResult);
        
        if($countRows === '1'){
            return true;
        }
        throw new Exception($this->jsonErrorMessage("Missing valid ticket", null, ""));
    }
    
    
    
    function storeSaronSessionUser($wp_id, $editor, $org_editor, $userDisplayName ){
        $this->cleanSaronUser($wp_id);   
        $sql = "INSERT INTO SaronUser (AccessTicket, Editor, Org_Editor, WP_ID, UserDisplayName) values (";
        $sql.= $this->getAccessTicket() . ", "; 
        $sql.= $editor . ", ";
        $sql.= $org_editor . ", ";
        $sql.= $wp_id . ", '";
        $sql.= $userDisplayName . "') ";
        echo $sql;
        try{
            $lastId = $this->insert($sql, "SaronUser", "Id");
            $result = $this->sqlQuery("Select AccessTicket from SaronUser where Id = " . $lastId);
    
            $ticket = "";
            foreach($result as $aRow){
                $ticket = $aRow["AccessTicket"];
            }
            return $ticket;
        }
        catch(Exception $ex){
            throw new Exception($ex);
        }
    }
    
    
    private function getAccessTicket(){
        return "'" . random_int(pow(10,floor(log(PHP_INT_MAX)/log(10))), PHP_INT_MAX) . random_int(pow(10,floor(log(PHP_INT_MAX)/log(10))), PHP_INT_MAX) . "'";
    }
    
    
    function loadSaronUser($ticket){
        $sql = "select * from  SaronUser where AccessTicket='" . $ticket . "'"; 
        
        $attributes = $this->sqlQuery($sql);
        return $attributes;       
    }
    
    
    
    function renewTicket($oldTicket){
        try{
            $this->transaction_begin();

            $result1 = $this->sqlQuery("Select Id from SaronUser where AccessTicket = '" . $oldTicket . "'");
            $id;
            if($result1){
                foreach($result1 as $aRow){
                    $id = $aRow["Id"];
                }
            }
            else{
                throw new Exception("Valid ticket is missing.");
            }

            $update = "update SaronUser ";
            $set = "SET ";        
            $set.= "AccessTicket = " . $this->getAccessTicket() . ", ";
            $set.= "Time_Stamp = Now() ";
            $where = "WHERE AccessTicket = '" . $oldTicket . "'";

            $this->update($update, $set, $where);
            
            $result2 = $this->sqlQuery("Select AccessTicket from SaronUser where Id = " . $id);
    
            $ticket;
            foreach($result2 as $aRow){
                $ticket = $aRow["AccessTicket"];
            }
            $this->transaction_end();

            return $ticket;
        }
        catch(Exception $ex){
            $this->transaction_roll_back();
            $this->transaction_end();
            throw new Exception($ex);
        }
    }
    
  
    
    function cleanSaronUser($wp_id){
        $sql = "DELETE from SaronUser where TIME_TO_SEC(TIMEDIFF(Now(), Last_Activity)) > " . SESSION_EXPIRES . " OR WP_ID=" . $wp_id;
        $this->delete($sql);
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
            $this->php_dev_error_log("Exception in exist function", $sql);
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
            $this->php_dev_error_log("Exception in insert function: " . $error->getMessage(), $insert);
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            throw new Exception($this->jsonErrorMessage("Error in insert function", null, $technicalErrMsg));
        }
    }     
    
    
    public function select($saronUser, $select, $from, $where, $orderby, $limit, $responstype=RECORDS){
        $sqlSelect = $select . $from . $where . $orderby . $limit;
        $sqlCount = "select count(*) as c " . $from . $where;

        try{
            return $this->selectSeparate($saronUser, $sqlSelect, $sqlCount, $responstype);
        }
        catch(Exception $error){
            $this->php_dev_error_log("Exception in select function", $sqlSelect);
            throw new Exception($error->getMessage());
        }
    } 
    
    
    
    public function selectSeparate($saronUser, $sqlSelect, $sqlCount, $responstype=RECORDS){
        $listResult = $this->connection->query($sqlSelect);
        if(!$listResult){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            $this->php_dev_error_log("Exception in select function part 1", $sqlSelect);
            throw new Exception($this->jsonErrorMessage("SQL-Error in select /list/ statement!", null, $technicalErrMsg));
        }
        
        $countResult = $this->connection->query($sqlCount);
        if(!$countResult){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            $this->php_dev_error_log("Exception in select function part 2", $sqlSelect);
            throw new Exception($this->jsonErrorMessage("SQL-Error in select count statement!", null, $technicalErrMsg));
        }

//        $result = $this->resultSetToArray($listResult, $responstype);
        $jsonResult = $this->processRowSet($saronUser, $listResult, $countResult, $responstype);   
            
        return $jsonResult;
    } 
    
    
    
    public function sqlQuery($sql){
        $listResult = $this->connection->query($sql);
        if(!$listResult){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            echo "SQL-Error in statement: <br>" .  $sql . "<br>" .  $technicalErrMsg; 
            $this->php_dev_error_log("Exception in sqlQuery function", $sql);
            return false;
        }
        $result = $this->resultSetToArray($listResult, RECORDS);
    
        return $result;
    }

    
    
    private function jsonErrorMessage($appErrorMsg, $connectionError=null, $sqlError=""){
        $error = array();
        $error["Result"] = "ERROR";
        $error["Message"] = "";
        
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
    
    
    
    private function resultSetToArray($listResult, $responstype){
        $listRows = Array();    
        
        if($responstype === RECORD){
            $listRow = mysqli_fetch_array($listResult, MYSQLI_ASSOC);
            mysqli_free_result($listResult);
            return $listRow;
        }
        else{
            while($listRow = mysqli_fetch_array($listResult, MYSQLI_ASSOC)){
                $listRows[] = $listRow;
            } 
            mysqli_free_result($listResult);
            return $listRows;
        }
    }    
    
    
 
    private function processRowSet($saronUser, $listResult, $countResult, $responstype){
        $jTableResult['Result'] = "OK";
        
        $jTableResult[$responstype] = $this->resultSetToArray($listResult, $responstype);

        $countRows = "0";
        while($countRow = mysqli_fetch_array($countResult)){
            $countRows = $countRow["c"];
        } 
        mysqli_free_result($countResult);

        if($responstype === RECORDS){
            $jTableResult['TotalRecordCount'] = $countRows;
            $jTableResult['user_role'] = $saronUser->getRole();
        }

        $jsonResult = json_encode($jTableResult);

        if($responstype === RECORD AND $countRows > 1){
            $this->php_dev_error_log("processRowSet", "");
            throw new Exception($this->jsonErrorMessage("Error i json_encode funktionen! Not a unic Record", null, " -- processRowSet"));            
        }
        
        
        if($jsonResult===false){
            $this->php_dev_error_log("processRowSet", "");
            throw new Exception($this->jsonErrorMessage("Error i json_encode funktionen!", null, " -- processRowSet"));
        }
        return $jsonResult;
    }    
    
    
    
    function php_dev_error_log($method, $msg){
        if(TEST_ENV === true){
            error_log("**** DB: " . $method . " *****");
            if(strlen($msg)>0){
                error_log($msg . "\n\n");
            }
        }
    }

    
}
