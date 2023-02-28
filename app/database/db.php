<?php
require_once "config.php";
require_once SARON_ROOT . "app/database/queries.php";
require_once SARON_ROOT . "app/database/BusinessLogger.php";
require_once SARON_ROOT . "app/util/GlobalConstants_php.php";
 
class db {
    private $businessLogger;
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
            echo $this->jsonErrorMessage($appErrorMsg, $error, "");
            throw new Exception($this->jsonErrorMessage($appErrorMsg, $error, ""));                               
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
        $this->php_dev_error_log("transaction_begin", "", "");
        if(!$this->connection->autocommit(false)){
            throw new Exception($this->jsonErrorMessage("Transaktionsfel (Begin). Kan inte uppdatera."));                               
        }
    }
  
    
    function transaction_end(){
        $this->php_dev_error_log("transaction_end", "", "");
        return $this->connection->autocommit(true);       
    }
    
    
    function transaction_roll_back(){
        $this->php_dev_error_log("transaction_roll_back", "", "");
        return $this->connection->rollback();
    }
    
    
    public function insert($insert, $keyTable, $keyColumn, $businessEntityName, $businessKeyName, $description, $saronUser, $createLogPost = true){
        $this->businessLogger = new BusinessLogger($this,$saronUser);

        $changeType = "Tillägg av " . $businessEntityName;
        $lastId = "0";
        try{
            $this->php_dev_error_log("insert", "INFO", $insert);
            if(!$listResult = $this->connection->query($insert)){
                $msg = $this->jsonErrorMessage("SQL-Error in insert statement! ", null, $this->connection->error);
                $this->php_dev_error_log("insert", "ERROR " . $msg, $insert);
                throw new Exception($msg);
            }
            else{
                $sql = "Select " . $keyColumn . " from " . $keyTable . " Where " . $keyColumn . " = LAST_INSERT_ID()";
                $this->php_dev_error_log("select after insert", "INFO", $sql);
                if(!$listResult = $this->connection->query($sql)){
                    throw new Exception($this->jsonErrorMessage("SQL-Error in LAST_INSERT_ID() statement after insert.", null, $this->connection->error));
                }
                else{
                    $lastId = 0;
                    while($listRow = mysqli_fetch_array($listResult)){
                        $lastId = $listRow[$keyColumn];
                    }
                    if($createLogPost){
                        $postSql = $this->businessLogger->getChangeValidationSQL($keyTable, $keyColumn, $lastId);
                        $postListResult = $this->sqlQuery($postSql);
                        if($description === null){
                            $description = $this->businessLogger->createInsertDescription($postListResult, $changeType);
                        }
                        $businessKeyValue = $this->businessLogger->getBusinessKey($keyTable, $keyColumn, $lastId, $businessKeyName, $saronUser);
                        $this->businessLogger->insertLogPost($keyTable, $keyColumn, $lastId, $changeType, $businessKeyName, $businessKeyValue, $description, $saronUser);
                    }
                }
            }
            return $lastId;
        }
        catch(Exception $error){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            $this->php_dev_error_log("insert", "Exception: " . $technicalErrMsg, $insert);
            throw new Exception($this->jsonErrorMessage("Error in insert function", null, $technicalErrMsg));
        }
    }     
    
    
    public function update($update, $set, $where, $keyTable, $keyColumn, $key, $businessEntityName, $businessKeyName, $description, $saronUser, $createLogPost = true){
        $this->businessLogger = new BusinessLogger($this, $saronUser);

        $businessKeyValue = "";
        $changeType = "Uppdatering av " . $businessEntityName;

        $sql = $update . $set . $where;

        $prevListResult = null;
        $prevPostSql = null;
        if($createLogPost){
            $prevPostSql = $this->businessLogger->getChangeValidationSQL($keyTable, $keyColumn, $key);
            $businessKeyValue = $this->businessLogger->getBusinessKey($keyTable, $keyColumn, $key, $businessKeyName, $saronUser);
            $prevListResult = $this->sqlQuery($prevPostSql);
        }
        
        $this->php_dev_error_log("update", "INFO",  $sql);
        if(!$listResult = $this->connection->query($sql)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            throw new Exception($this->jsonErrorMessage("Exception in update function", null, $technicalErrMsg));
        }
        
        if($createLogPost){
            $postListResult = $this->sqlQuery($prevPostSql);
            if($description === null){
                $description = $this->businessLogger->createUpdateDescription($prevListResult, $postListResult, $changeType);
            }
            $this->businessLogger->insertLogPost($keyTable, $keyColumn, $key, $changeType, $businessKeyName, $businessKeyValue, $description, $saronUser);
        }
        return true;
    } 
    
    
    
    public function delete($sqlDelete, $keyTable, $keyColumn, $key, $businessEntityName, $businessKeyName, $description, $saronUser, $createLogPost = true){
        $this->businessLogger = new BusinessLogger($this, $saronUser);

        $changeType = "Borttag av " . $businessEntityName;
        if($createLogPost){
            $preSql = $this->businessLogger->getChangeValidationSQL($keyTable, $keyColumn, $key);
            $preListResult = $this->sqlQuery($preSql);
            if($description === null){
                $description = $this->businessLogger->createDeleteDescription($preListResult, $changeType);
            }
            $businessKeyValue = $this->businessLogger->getBusinessKey($keyTable, $keyColumn, $key, $businessKeyName, $saronUser);
            $this->businessLogger->insertLogPost($keyTable, $keyColumn, $key, $changeType, $businessKeyName, $businessKeyValue, $description, $saronUser);
        }

        $this->php_dev_error_log("delete", "INFO", $sqlDelete);
        if(!$listResult = $this->connection->query($sqlDelete)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
             $this->php_dev_error_log("delete", "Error " . $technicalErrMsg, $sqlDelete);
            throw new Exception($this->jsonErrorMessage("Exception in delete function", null, $technicalErrMsg));
        }
        else{
            $deleteResult['Result'] = "OK";
            return json_encode($deleteResult);
        }                
    }

    
    
    public function fieldValueExist($value, $id, $field, $table){
        $sql = "select count(*) as c from " . $table . " where "; 
        $sql.= "UPPER(" . $field . ") like UPPER('" . $value . "') AND Id <> " . $id . " ";
        
        if(!$listResult = $this->connection->query($sql)){
            $msg = $this->jsonErrorMessage("SQL-Error in select /exist/ statement!", null, $sql);
            $this->php_dev_error_log("fieldValueExist", "Exception" . $msg , "");
            throw new Exception($msg);
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
    
    
    public function exist($FirstName, $LastName, $DateOfBirth, $id=-1){
        $sql = "select count(*) as c from People where "; 
        $sql.= "UPPER(CONVERT(BINARY " . DECRYPTED_FIRSTNAME . " USING utf8)) like '" . $FirstName . "' and ";
        $sql.= "UPPER(CONVERT(BINARY " . DECRYPTED_LASTNAME . " USING utf8)) like '" . $LastName . "' and ";
        $sql.= "DateOfBirth like '" . $DateOfBirth . "'";
        
        if($id>0){
            $sql.= " and ID <> '" . $id . "'";            
        }
        
        if(!$listResult = $this->connection->query($sql)){
            $msg = $this->jsonErrorMessage("SQL-Error in select /exist/ statement!", null, $sql);
            $this->php_dev_error_log("Exist", "Exception", "");
            throw new Exception($msg);
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
   
        
    public function select($saronUser, $select, $from, $where, $orderby, $limit, $responstype=RECORDS){
        $sqlSelect = $select . $from . $where . $orderby . $limit;
        $sqlCount = "select count(*) as c " . $from . $where;

        try{
            return $this->selectSeparate($saronUser, $sqlSelect, $sqlCount, $responstype);
        }
        catch(Exception $error){
            $this->php_dev_error_log("select", "Exception" . $error->getMessage(), $sqlSelect);
            throw new Exception($error->getMessage());
        }
    } 
    
    
    
    public function selectSeparate($saronUser, $sqlSelect, $sqlCount, $responstype=RECORDS){
        if(TEST_ENV === true){
            $this->php_dev_error_log("selectSeparate", "INFO",  $sqlSelect);
            //syslog(LOG_INFO, "INFO SQL: " . $sql . "\r\n");
        }

        $listResult = $this->connection->query($sqlSelect);
        if(!$listResult){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            $this->php_dev_error_log("select function part 1 ", "Exception" . $technicalErrMsg, $sqlSelect);
            throw new Exception($this->jsonErrorMessage("SQL-Error in select /list/ statement!", null, $technicalErrMsg));
        }
        
        $countResult = $this->connection->query($sqlCount);
        if(!$countResult){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            $this->php_dev_error_log("select function part 2 ", "Exception" . $technicalErrMsg, $sqlSelect);
            throw new Exception($this->jsonErrorMessage("SQL-Error in select count statement!", null, $technicalErrMsg));
        }

//        $result = $this->resultSetToArray($listResult, $responstype);
        $jsonResult = $this->processRowSet($saronUser, $listResult, $countResult, $responstype);   
            
        return $jsonResult;
    } 
    
    
    function php_dev_error_log($method, $msg, $sql){
        if(TEST_ENV === true){
            error_log("**** DB: " . $method . " " . $msg . " *****");
            if(strlen($sql)>0){
                error_log($sql . "\r\n");
            }            
            error_log("****** END ******\r\n\r\n");
        }
    }    
    
    public function sqlQuery($sql, $toArray=true){
        $this->php_dev_error_log("sqlQuery", "INFO", $sql);
        $listResult = $this->connection->query($sql);
        if(!$listResult){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            echo "SQL-Error in statement: \r\n" .  $sql . "\r\n" .  $technicalErrMsg; 
            $this->php_dev_error_log("sqlQuery", "Exception" . $technicalErrMsg, $sql);
            return false;
        }
        if($toArray){
            $result = $this->resultSetToArray($listResult, RECORDS);
            return $result;
        }
        return $listResult;
    }

    
    private function jsonErrorMessage($appErrorMsg, $connectionError=null, $sqlError=""){
        $error = array();
        $error["Result"] = "ERROR";
        $error["Message"] = "";
        
        $errMsg = $appErrorMsg;
        if(TEST_ENV === true){
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
            $countRows = $countRow[0];
        } 
        mysqli_free_result($countResult);

        if($responstype === RECORDS){
            $jTableResult['TotalRecordCount'] = $countRows;
            $jTableResult['user_role'] = $saronUser->getRole();
        }

        $jsonResult = json_encode($jTableResult);

        if($responstype === RECORD AND $countRows > 1){
            $msg = $this->jsonErrorMessage("Error i json_encode funktionen! Not a unic Record", null, " -- processRowSet");
            $this->php_dev_error_log("processRowSet", $msg, "");
            throw new Exception($msg);            
        }
        
        
        if($jsonResult===false){
            $msg = $this->jsonErrorMessage("Error i json_encode funktionen!", null, " -- processRowSet");
            $this->php_dev_error_log("processRowSet", $msg, "");
            throw new Exception($msg);
        }
        return $jsonResult;
    }    
    
    

    
    function getResultSetAsHTMLTable($sql){
        if(TEST_ENV === true){
            $this->php_dev_error_log("sqlQuery", "INFO",  $sql);
            //syslog(LOG_INFO, "INFO SQL: " . $sql . "\r\n");
        }
        $listResult = $this->connection->query($sql);
        if(!$listResult){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            echo "SQL-Error in statement: \r\n" .  $sql . "\r\n" .  $technicalErrMsg; 
            $this->php_dev_error_log("getResultSetAsHTMLTable", "Exception " . $technicalErrMsg, $sql);
            return false;
        }
        

        $nFields = mysqli_num_fields($listResult);
        $nRows = mysqli_num_rows($listResult);

        while($listRow = mysqli_fetch_array($listResult, MYSQLI_BOTH)){
            $listRows[] = $listRow;
        }
        $fields = mysqli_fetch_fields($listResult);
                
        $html = "<table class='saronHtmlTable'>";
        $html.= "<tr class='saronHtmlTable_row'>";
        for($c=0;$c<$nFields;  $c++){
            $html.= "<th class='saronHtmlTable_col'>";
            $html.= $fields[$c]->name;
            $html.= "</th>";
        }
        $html.= "</tr>";
        for($r = 0; $r < $nRows; $r++){
            if($r/2 % 2 ){
                $html.= "<tr class='saronHtmlTable_odd saronHtmlTable_row'>";            
            }
            else{
                $html.= "<tr class='saronHtmlTable_even saronHtmlTable_row'>";            
            }
            for($c=0;$c<$nFields;  $c++){
                if($fields[$c]->type === 1){ 
                    $align = 'number';
                }
                elseif($fields[$c]->type === 3){ 
                    $align = 'number';
                }
                elseif($fields[$c]->type === 8){ 
                    $align = 'number';
                }
                elseif($fields[$c]->type === 10){ 
                    $align = 'number';
                }
                else { 
                    $align = 'text';
                }
                if($r/2 % 2 ){
                    $html.= "<td class='saronHtmlTable_odd saronHtmlTable_col " . $align .  "'>";            
                }
                else{
                    $html.= "<td class='saronHtmlTable_even saronHtmlTable_col " . $align .  "'>";            
                }
                $field = $listRows[$r][$c];
                $html.= $field;
                $html.= "</td>";
            }
            $html.= "</tr>";
        }
        $html.= "</table>";
        return $html;
    }        
}
    
