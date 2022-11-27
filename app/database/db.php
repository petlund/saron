<?php
require_once "config.php";
require_once SARON_ROOT . "app/database/queries.php";
require_once SARON_ROOT . "app/util/GlobalConstants_php.php";
 
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
    
    
    public function insert($insert, $keyTable, $keyColumn, $businessEntityName, $businessKeyName, $description, $saronUser, $createLogPost = true){
        $changeType = "Tillägg av " . $businessEntityName;
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
                    $LastId = 0;
                    while($listRow = mysqli_fetch_array($listResult)){
                        $LastId = $listRow[$keyColumn];
                    }
                    if($createLogPost){
                        $postSql = $this->getChangeValidationSQL($keyTable, $keyColumn, $LastId);
                        $postListResult = $this->sqlQuery($postSql);
                        if($description === null){
                            $description = $this->createInsertDescription($postListResult, $changeType);
                        }
                        $businessKeyValue = $this->getBusinessKey($keyTable, $keyColumn, $key, $businessKeyName, $businessKeyValue, $saronUser);
                        $this->insertLogPost($keyTable, $keyColumn, $LastId, $changeType, $businessKeyName, $businessKeyValue, $description, $saronUser);
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
    
    
    public function update($update, $set, $where, $keyTable, $keyColumn, $key, $businessEntityName, $businessKeyName, $description, $saronUser, $createLogPost = true){
        $businessKeyValue = "";
        $changeType = "Uppdatering av " . $businessEntityName;

        $sql = $update . $set . $where;

        $prevListResult = null;
        $prevPostSql = $this->getChangeValidationSQL($keyTable, $keyColumn, $key);
        if($createLogPost){
            $businessKeyValue = $this->getBusinessKey($keyTable, $keyColumn, $key, $businessKeyName, $businessKeyValue, $saronUser);
            $prevListResult = $this->sqlQuery($prevPostSql);
        }
        if(!$listResult = $this->connection->query($sql)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            $this->php_dev_error_log("update", $sql);
            throw new Exception($this->jsonErrorMessage("Exception in update function", null, $technicalErrMsg));
        }
        
        if($createLogPost){
            $postListResult = $this->sqlQuery($prevPostSql);
            if($description === null){
                $description = $this->createUpdateDescription($prevListResult, $postListResult, $changeType);
            }
            $this->insertLogPost($keyTable, $keyColumn, $key, $changeType, $businessKeyName, $businessKeyValue, $description, $saronUser);
        }
        return true;
    } 
    
    
    
    public function delete($sqlDelete, $keyTable, $keyColumn, $key, $businessEntityName, $businessKeyName, $description, $saronUser, $createLogPost = true){
        $changeType = "Borttag av " . $businessEntityName;
        if($createLogPost){
            $preSql = $this->getChangeValidationSQL($keyTable, $keyColumn, $key);
            $preListResult = $this->sqlQuery($preSql);
            if($description === null){
                $description = $this->createDeleteDescription($preListResult, $changeType);
            }
            $businessKeyValue = $this->getBusinessKey($keyTable, $keyColumn, $key, $businessKeyName, $saronUser);
            $this->insertLogPost($keyTable, $keyColumn, $key, $changeType, $businessKeyName, $businessKeyValue, $description, $saronUser);
        }
        if(!$listResult = $this->connection->query($sqlDelete)){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
//            $this->php_dev_error_log("delete", $sqlDelete);
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
    
    
    public function exist($FirstName, $LastName, $DateOfBirth, $id=-1){
        $sql = "select count(*) as c from People where "; 
        $sql.= "UPPER(CONVERT(BINARY " . DECRYPTED_FIRSTNAME . " USING utf8)) like '" . $FirstName . "' and ";
        $sql.= "UPPER(CONVERT(BINARY " . DECRYPTED_LASTNAME . " USING utf8)) like '" . $LastName . "' and ";
        $sql.= "DateOfBirth like '" . $DateOfBirth . "'";
        
        if($id>0){
            $sql.= " and ID <> '" . $id . "'";            
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
        if(TEST_ENV === true){
            $this->php_dev_error_log("selectSeparate", "INFO SQL: " . $sqlSelect . "\r\n");
            //syslog(LOG_INFO, "INFO SQL: " . $sql . "\r\n");
        }

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
        if(TEST_ENV === true){
            $this->php_dev_error_log("sqlQuery", "INFO SQL: " . $sql . "\r\n");
            //syslog(LOG_INFO, "INFO SQL: " . $sql . "\r\n");
        }
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
    
    function getResultSetAsHTMLTable($sql){
        if(TEST_ENV === true){
            $this->php_dev_error_log("sqlQuery", "INFO SQL: " . $sql . "\r\n");
            //syslog(LOG_INFO, "INFO SQL: " . $sql . "\r\n");
        }
        $listResult = $this->connection->query($sql);
        if(!$listResult){
            $technicalErrMsg = $this->connection->errno . ": " . $this->connection->error;
            echo "SQL-Error in statement: <br>" .  $sql . "<br>" .  $technicalErrMsg; 
            $this->php_dev_error_log("Exception in sqlQuery function", $sql);
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
    
    private function insertLogPost($keyTable, $keyColumn, $key, $changeType, $businessKeyName, $businessKeyValue, $description, $saronUser){
        $sqlInsert = "INSERT INTO Changes (ChangeType, User, BusinessKey, Description, Inserter, InserterName) ";
        $sqlInsert.= "VALUES (";
        $sqlInsert.= "'" . $changeType . "', ";
        $sqlInsert.= "'" . $saronUser->userDisplayName . "', ";
        $sqlInsert.= '"' . $businessKeyValue . '", ';
        $sqlInsert.= '"' . $description . '", ';
        $sqlInsert.= $saronUser->WP_ID . ", ";
        $sqlInsert.= "'" . $saronUser->userDisplayName . "')";
        
        $this->insert($sqlInsert, 'Changes', 'Id', '',null, '', $saronUser, false);

        $sqlDelete = "DELETE FROM Changes where DATEDIFF(Now(), Inserted) > " . CHANGE_LOG_IN_DAYS;
        $this->delete($sqlDelete, 'Changes', 'Id', -1, '', null, '', $saronUser, false);
    }

    
    
    private function createInsertDescription($listRow, $changeType){
        $rowObj = new ArrayObject($listRow[0]);
        
        $description = "<b>" . $changeType . "</b><br>";
        $iterator = $rowObj->getIterator();
        
        while( $iterator->valid() ){
            $key = $iterator->key();
            if(!($this->str_ends_with($key, 'Encrypt') OR $this->str_ends_with($key, 'Hidden'))){
                $value = $iterator->current();
                $description.= "<b>" . $key . ": </b>'" . $value . "'<br> ";
            }
            $iterator->next();
        }
        
        return $description;
    }
    
    
    
    private function createDeleteDescription($listRow, $changeType){
        if(empty($listRow)){
            return "";
        }
        $rowObj = new ArrayObject($listRow[0]);
        
        $description = "<b>" . $changeType . "</b><br>";
        $iterator = $rowObj->getIterator();
        
        while( $iterator->valid() ){
            $key = $iterator->key(); 
            if(!($this->str_ends_with($key, 'Encrypt') OR $this->str_ends_with($key, 'Hidden'))){
                $value = $iterator->current();
                $description.= "<b>" . $key . ": </b>'" . $value . "'<br> ";
            }
            $iterator->next();
        }
        
        return $description;
    }
    
    
    
    private function createUpdateDescription($prevListRow, $postListRow, $changeType){
        $prevObj = new ArrayObject($prevListRow[0]);
        $postObj = new ArrayObject($postListRow[0]);
        
        $description = "";
        $iterator = $prevObj->getIterator();
        
        while( $iterator->valid() ){
            $key = $iterator->key(); 
            $prevValue = $iterator->current();
            $postValue = $postObj[$key];
            
            if(!($this->str_ends_with($key, 'Encrypt') OR $this->str_ends_with($key, 'Hidden'))){
                if($prevValue !== $postValue){
                    $description.= "<b>" . $key . ": </b>'" . $prevValue . "' <b>==></b> '" . $postValue . "'<br>";
                }
                else{// enable if you want to se all fields
                    //$description.= "<b>" . $key . ": </b>" . $prevValue . "<br>";                
                }
            }
            $iterator->next();
        }
        if(strlen($description)>0){
            return "<b>" . $changeType . "</b><br>" . $description;
        }
        return "<b>Ingen " . $changeType . "</b>";
    }    
    
    
    private function getChangeValidationSQL($keyTable, $keyColumn, $key){
        switch ($keyTable){
            case 'People':
                return SQL_STAR_PEOPLE . " From view_people_memberstate as People Where " . $keyColumn . " = " . $key;
            case 'Homes':
                return SQL_STAR_HOMES . " From Homes Where " . $keyColumn . " = " . $key;
            case 'view_organization':
                return 'SELECT *, ' . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE_MEMBERSTATENAME_HIDDEN . " as Person From view_organization Where " . $keyColumn . " = " . $key;
            default:
                return "Select * from " . $keyTable . " Where " . $keyColumn . " = " . $key;
        }
    }
    
    
    private function getBusinessKey($keyTable, $keyColumn, $key, $businessKeyName, $user){

        switch ($keyTable){
            case 'People':
                $sql = 'SELECT ' . DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE_MEMBERSTATENAME . " as KeyValue From view_people_memberstate as People Where Id = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'Homes':
                $sql =  'SELECT ' . DECRYPTED_FAMILYNAME . " as KeyValue From Homes Where Id = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'News':
                $sql = "SELECT news_date as KeyValue  From News Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'view_organization':
                $sql = "SELECT PosKeyValue as KeyValue From view_organization Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'MemberState':
                $sql = "SELECT Name as KeyValue From MemberState Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'Org_Role':
                $sql = "SELECT Name as KeyValue From Org_Role Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'Org_Tree':
                $sql = "SELECT Name as KeyValue From Org_Tree Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'Org_UnitType':
                $sql = "SELECT Name as KeyValue From Org_UnitType Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'Org_Version':
                $sql = "SELECT decision_date as KeyValue From Org_Version Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'org_role_unittype_view':   
                $sql = "SELECT Id as KeyValue From org_role_unittype_view Where id  = " . $key;
                return '<b>' . $businessKeyName . ':</b> ' . $this->getBusinessKeyValue($sql);
            case 'SaronUser':
                return '<b>' . $businessKeyName . ': </b> ' . $user->userDisplayName;
            case 'Statistics':
                return 'Datum';
            default:
                return 'Nyckel ej definerad';
        }
    }
    
    
    private function getBusinessKeyValue($sql){
        $result = $this->sqlQuery($sql);
        if($result){
            $resultObj = new ArrayObject($result[0]);
            $businessKey = $resultObj['KeyValue'];
            return $businessKey;
        }
        return 'Värde ej definerat';
        
    }
    
    
    
    private function str_ends_with($haystack, $needle){
        $haystack_len = strlen($haystack);
        $needle_len = strlen($needle);
        
        if($haystack_len === 0){
            return false;
        }
            
        if($needle_len  === 0){
            return false;
        }
            
        if($needle_len  > $haystack_len){
            return false;
        }
        $haystack_suffix = substr($haystack, $haystack_len - $needle_len);
        if($haystack_suffix === $needle){
            return true;
        }
        return false;
    }
        
}
    
