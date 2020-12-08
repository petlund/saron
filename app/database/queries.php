<?php

    $res = openssl_pkey_get_private (PKEY_FILE);
    openssl_pkey_export($res, $privkey);
    define ("PKEY", "'" . $privkey . "'");
    define("SALT_LENGTH", 13);
    define("MAX_STR_LEN", 250);

    define ("RECORD", "Record");
    define ("RECORDS", "Records");
    define ("OPTIONS", "Options");

    define("DATE_FORMAT", "'%Y-%m-%d'");
    define("DATE_OF_BIRTH", "DATE_FORMAT(DateOfBirth, " . DATE_FORMAT . ")");
    define("DATE_OF_BIRTH_ALIAS_DATE_OF_BIRTH", DATE_OF_BIRTH . " AS DateOfBirth");
    //        define("DATE_OF_BIRTH_ALIAS_DATE_OF_BIRTH", "'\/Date(1320259705710)\/' AS DateOfBirth");

    define("DECRYPTED_FIRSTNAME", "SUBSTR(AES_DECRYPT(FirstNameEncrypt, " . PKEY . "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_FIRSTNAME", DECRYPTED_FIRSTNAME . " as FirstName");

    define("DECRYPTED_LASTNAME", "SUBSTR(AES_DECRYPT(LastNameEncrypt, " . PKEY . "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_LASTNAME", DECRYPTED_LASTNAME . " as LastName");

    define("DECRYPTED_BAPTISTER", "SUBSTR(AES_DECRYPT(BaptisterEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_BAPTISTER", DECRYPTED_BAPTISTER . " as Baptister");
    define("DECRYPTED_MOBILE", "SUBSTR(AES_DECRYPT(MobileEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_MOBILE", DECRYPTED_MOBILE . " as Mobile");
    define("DECRYPTED_EMAIL", "SUBSTR(AES_DECRYPT(EmailEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_EMAIL", DECRYPTED_EMAIL . " as Email");
    define("DECRYPTED_COMMENT", "SUBSTR(AES_DECRYPT(CommentEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_COMMENT", DECRYPTED_COMMENT . " as Comment");
    define("DECRYPTED_COMMENT_KEY", "SUBSTR(AES_DECRYPT(CommentKeyEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_COMMENT_KEY", DECRYPTED_COMMENT_KEY . " as CommentKey");

    define("DECRYPTED_FAMILYNAME", "SUBSTR(AES_DECRYPT(FamilyNameEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_FAMILYNAME", DECRYPTED_FAMILYNAME . " as FamilyName");
    define("DECRYPTED_ADDRESS", "SUBSTR(AES_DECRYPT(AddressEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_ADDRESS", DECRYPTED_ADDRESS . " as Address");
    define("DECRYPTED_CO", "SUBSTR(AES_DECRYPT(CoEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_CO", DECRYPTED_CO . " as Co");
    define("DECRYPTED_PHONE", "SUBSTR(AES_DECRYPT(PhoneEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
    define("DECRYPTED_ALIAS_PHONE", DECRYPTED_PHONE . " as Phone");

    define("DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME_FL", "concat(" . DECRYPTED_FIRSTNAME . ", ' ', " . DECRYPTED_LASTNAME . ") as Name_FL");
    define("DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME", "concat(" . DECRYPTED_LASTNAME . ", ' ', " . DECRYPTED_FIRSTNAME . ") as Name");
    define("DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE", "concat(" . DECRYPTED_LASTNAME . ", ' ', " . DECRYPTED_FIRSTNAME . ", ' ', " . DATE_OF_BIRTH . ") ");
    define("DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE_AS_APPIDENTITYNAME", DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE . "as AppIdentityName ");

    define("DATES_AS_MEMBERSTATES",  getMemberStateSql("People", null, false));
    define("DATES_AS_ALISAS_MEMBERSTATES", getMemberStateSql("People", "MemberState", false));
    define("ALIAS_CUR_HOMES", "Homes");
    define("ALIAS_OLD_HOMES", "OldHome");

    $ALL_PEOPLE_FIELDS = "People.Id as PersonId, ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_FIRSTNAME . ", ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_LASTNAME . ", ";
    $ALL_PEOPLE_FIELDS.= DATE_OF_BIRTH_ALIAS_DATE_OF_BIRTH . ", DateOfDeath, PreviousCongregation, MembershipNo, VisibleInCalendar, DateOfMembershipStart, DateOfMembershipEnd, NextCongregation, DateOfBaptism, ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_BAPTISTER . ", ";
    $ALL_PEOPLE_FIELDS.= "CongregationOfBaptism, CongregationOfBaptismThis, Gender, ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_EMAIL . ", ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_MOBILE . ", ";
    $ALL_PEOPLE_FIELDS.= "KeyToChurch, KeyToExp, ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_COMMENT . ", ";
    $ALL_PEOPLE_FIELDS.= "People.HomeId, People.HomeId as OldHomeId, Updated, Inserter, Inserted, " . DECRYPTED_ALIAS_COMMENT_KEY . " ";
    
    define("SQL_STAR_PEOPLE", "Select " . $ALL_PEOPLE_FIELDS);

    $ALL_HOME_FIELDS = "Homes.Id as HomeId, ";
    $ALL_HOME_FIELDS.= DECRYPTED_ALIAS_FAMILYNAME . ", ";
    $ALL_HOME_FIELDS.= DECRYPTED_ALIAS_ADDRESS . ", ";
    $ALL_HOME_FIELDS.= DECRYPTED_ALIAS_CO . ", ";
    $ALL_HOME_FIELDS.= "City, Zip, Country,  ";
    $ALL_HOME_FIELDS.= DECRYPTED_ALIAS_PHONE . ", ";
    $ALL_HOME_FIELDS.= "Letter  ";    
    define("SQL_STAR_HOMES", "Select " . $ALL_HOME_FIELDS);
    define("SQL_ALL_FIELDS", "select " . $ALL_PEOPLE_FIELDS . ", " . $ALL_HOME_FIELDS);

    define("SQL_FROM_PEOPLE_LEFT_JOIN_HOMES", "FROM People left outer join Homes on People.HomeId=Homes.Id "); 
    define("SQL_WHERE", "Where ");  
    define("SQL_WHERE_MEMBER", "DateOfMembershipStart is not null and DateOfMembershipEnd is null and DateOfDeath is null and " . DECRYPTED_LASTNAME . " not like '" . ANONYMOUS . "' ");  
    define("SQL_WHERE_NOT_MEMBER", "DateOfMembershipStart is null and DateOfMembershipEnd is null and DateOfDeath is null and " . DECRYPTED_LASTNAME . " not like '" . ANONYMOUS . "' ");  

    define("FORMATTED_EMAILADDRESS", "if(" . DECRYPTED_EMAIL . " not like \"\", concat(\"<p class='Email'><a href='mailto:\"," . DECRYPTED_EMAIL . ",\"'>\", " . DECRYPTED_EMAIL . ", \"</a></p>\"),'') ");
    define("CONTACTS_ALIAS_RESIDENTS", "(SELECT GROUP_CONCAT('<b>', " . DECRYPTED_FIRSTNAME . ", ' ', " . DECRYPTED_LASTNAME . ", ':</b> ', " . getMemberStateSql("r", null, true) . "IF(" . DECRYPTED_EMAIL . " is NULL, '', CONCAT(', ', " . DECRYPTED_EMAIL . ")), IF(" . DECRYPTED_MOBILE . " is NULL, '', CONCAT(', ', " . DECRYPTED_MOBILE . ")) SEPARATOR '<BR>') FROM People as r where Homes.Id = r.HomeId  AND DateOfDeath is null and " . DECRYPTED_LASTNAME . " NOT LIKE '%" . ANONYMOUS . "' order by DateOfBirth) as Residents ");

    define("EMBEDDED_SELECT_SUPERPOS", "if(People_FK < 0, concat(' (som ', (Select Name from Org_Role as r2 where -People_FK = r2.Id),')'),'') ");
    define("ORG_POS_XREF", "(Select p1.Id, if(p1.People_FK < 0,(select p2.People_FK from Org_Pos as p2 where -p1.People_FK = p2.OrgRole_FK ), p1.People_FK) as People_FK2 from Org_Pos as p1) as xref ");
       

   
    function getFieldSql($tableAlias, $fieldAlias, $fieldName, $nullValue, $encrypt, $continue){
        $sql = "";
        IF(strlen($tableAlias) > 0){
            $sqlField = $tableAlias . "." . $fieldName;
        }
        else{
            $sqlField = $fieldName;
        }
        if($encrypt){
            $sql = "SUBSTR(AES_DECRYPT(" . $sqlField . ", " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")";
        }
        else{
            $sql = $sqlField;            
        }
        
        if(strlen($nullValue) > 0){
            $sql = "IF(" . $sql . " is null, '" . $nullValue . "', " . $sql . ")";
        }

        if(strlen($fieldAlias)>0){
            if(strlen($tableAlias)>0 && $tableAlias !== ALIAS_CUR_HOMES){            
                $sql.= " as " . $tableAlias . "_" . $fieldAlias;
            }
            else{
                $sql.= " as " . $fieldAlias;                
            }
        }
      
        if($continue){
           $sql.= ", "; 
        }
        else{
           $sql.= " ";             
        }
        return $sql; 
    }


    
    function getMemberStateSql($tableAlias = "People", $fieldAlias, $continue){
        $sql ="(SELECT MemberState.Name FROM MemberState Where MemberState.Id = ";
        $sql.=getMemberStateIndexSql($tableAlias, null, false);
        $sql.=") ";

        if(strlen($fieldAlias) > 0){
            $sql.= " AS " . $fieldAlias;
        }
        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;        
    }
    

    function getMemberStateIndexSql($tableAlias = "People", $fieldAlias, $continue){
        $sql="Case ";
        $sql.="WHEN " . $tableAlias . ".Id is null Then -1 ";
        $sql.="WHEN " . $tableAlias . ".DateOfDeath > 0 Then 5 ";
        $sql.="WHEN " . $tableAlias . ".DateOfMembershipStart > 0 AND " . $tableAlias . ".DateOfMembershipEnd is null Then 2 ";
        $sql.="WHEN UPPER(CONVERT(BINARY " . getFieldSql($tableAlias, null, "LastNameEncrypt", null, true, false) . " USING utf8)) like '%" . ANONYMOUS . "%' THEN 4 ";
        $sql.="WHEN (SELECT Count(*) from Org_Pos Where " . $tableAlias . ".Id = Org_Pos.People_FK) > 0 then 6 ";
        $sql.="WHEN " . $tableAlias . ".DateOfBaptism > 0  OR (" . $tableAlias . ".DateOfMembershipStart > 0 AND " . $tableAlias . ".DateOfMembershipEnd > 0) Then 3 ";
        $sql.="else 1 ";
        $sql.="END";

        if(strlen($fieldAlias) > 0){
            $sql.= " AS " . $fieldAlias;
        }
        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;        
    }    
    
    
    
    function getFilteredMemberStateSql($tableAlias = "People", $fieldAlias, $continue, $filterCreate, $filterUpdate){
        
        $sql1 = "";
        if($filterCreate){
            $sql1 = "IF(MemberState.FilterUpdate = '1', true, false) AND ";
        }
        else{
            $sql1 = "true AND ";            
        }
        
        $sql2 = "";
        if($filterUpdate){
            $sql1 = "IF(MemberState.FilterCreate = '1', true, false) ";            
        }
        else{
            $sql1 = "true ";            
        }
        
        
        $sql = "(SELECT ";
        $sql.= $sql1;
        $sql.= $sql2;
        $sql.= "FROM MemberState Where MemberState.Id = ";
        $sql.= getMemberStateIndexSql($tableAlias, null, false);
        $sql.= ") ";

        if(strlen($fieldAlias) > 0){
            $sql.= " AS " . $fieldAlias;
        }
        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;        
        
    }
    
    function getPersonSql($tableAlias, $fieldAlias, $continue){
        $sql = "CONCAT(";
        $sql.= getFieldSql($tableAlias, null, "LastNameEncrypt", null, true, false);
        $sql.= ", ' ', "; 
        $sql.= getFieldSql($tableAlias, null, "FirstNameEncrypt", null, true, false);
        $sql.= ", ' ', "; 
        $sql.= getFieldSql($tableAlias, null, "DateOfBirth", null, false, false);
        $sql.= ")";
        if(strlen($fieldAlias) > 0){
            $sql.= " AS " . $fieldAlias;
        }
        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;
    }
    
    function getLongHomeNameSql($tableAlias, $fieldAlias, $continue){
        $sql = "IF(" . $tableAlias . ".Id is null, 'Inget hem', ";
        $sql.= "concat(";
        $sql.= getFieldSql($tableAlias, "", "FamilyNameEncrypt", "", true, false);
        $sql.= ",' (',";
        $sql.= getFieldSql($tableAlias, "", "AddressEncrypt", "Adress saknas", true, false);
        $sql.= ",', ', ";
        $sql.= getFieldSql($tableAlias, "", "City", "Stad saknas", false, false);
        $sql.= ",') ')) as ";
        
        if(strlen($tableAlias)>0 && $tableAlias !== ALIAS_CUR_HOMES){
            $sql.= $tableAlias . "_";
        }
        
        $sql.= $fieldAlias;

        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }

        return $sql;
    }
 
    
    
    function getResidentsSql($tableAlias, $fieldAlias, $HomeId, $continue){
        $sql = "(SELECT GROUP_CONCAT(";
        $sql.= getFieldSql($tableAlias . "Res", "", "FirstNameEncrypt", "", true, false);
        $sql.= ", ' ', ";
        $sql.= getFieldSql($tableAlias . "Res", "", "LastNameEncrypt", "", true, false);
        $sql.= ", ' - ', ";
        $sql.= getMemberStateSql($tableAlias . "Res", null, false);
        $sql.= " SEPARATOR '<BR>') ";
        $sql.= "FROM People as " . $tableAlias . "Res ";
        $sql.= "where HomeId = ";

        if($HomeId !== null){
            $sql.= $HomeId . " "; 
        } 
        else{
            $sql.= "null ";             
        }

        $sql.= "AND DateOfDeath is null and " . DECRYPTED_LASTNAME . " NOT LIKE '%" . ANONYMOUS . "' ";
        $sql.= "order by DateOfBirth) as ";
        
        if(strlen($tableAlias)>0 && $tableAlias !== ALIAS_CUR_HOMES){
            $sql.= $tableAlias . "_";
        }
        
        $sql.= $fieldAlias;

        if($continue){
            $sql.= ", ";
        }
        else{
            $sql.= " ";            
        }
        return $sql;
    }
    
 