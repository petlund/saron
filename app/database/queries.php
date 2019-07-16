<?php
    require_once "config.php";
    require_once SARON_ROOT . "app/access/wp-authenticate.php";
    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = false;
    $user = wp_get_current_user(); 
    
    if(isPermitted($user, $requireEditorRole)){
        $res = openssl_pkey_get_private (PKEY_FILE);
        openssl_pkey_export($res, $privkey);
        define ("PKEY", "'" . $privkey . "'");
        define(SALT_LENGTH, 13);
        DEFINE("MAX_STR_LEN", 250);
        
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

//        define("DECRYPTED_FAMILYNAME", "SUBSTRING(AES_DECRYPT(FamilyNameEncrypt, " . PKEY . "), " . SALT_LENGTH . ")");
        define("DECRYPTED_FAMILYNAME", "SUBSTR(AES_DECRYPT(FamilyNameEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
        define("DECRYPTED_ALIAS_FAMILYNAME", DECRYPTED_FAMILYNAME . " as FamilyName");
        define("DECRYPTED_ADDRESS", "SUBSTR(AES_DECRYPT(AddressEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
        define("DECRYPTED_ALIAS_ADDRESS", DECRYPTED_ADDRESS . " as Address");
        define("DECRYPTED_CO", "SUBSTR(AES_DECRYPT(CoEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
        define("DECRYPTED_ALIAS_CO", DECRYPTED_CO . " as Co");
        define("DECRYPTED_PHONE", "SUBSTR(AES_DECRYPT(PhoneEncrypt, " . PKEY. "), " . SALT_LENGTH . ", " . MAX_STR_LEN .")");
        define("DECRYPTED_ALIAS_PHONE", DECRYPTED_PHONE . " as Phone");

        define("DECRYPTED_FIRSTNAME_LASTNAME_AS_NAME", "concat(" . DECRYPTED_FIRSTNAME . ", ' ', " . DECRYPTED_LASTNAME . ") as Name");
        define("DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME", "concat(" . DECRYPTED_LASTNAME . ", ' ', " . DECRYPTED_FIRSTNAME . ") as Name");
        define("DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE", "concat(" . DECRYPTED_LASTNAME . ", ' ', " . DECRYPTED_FIRSTNAME . ", ' ', DateOfBirth) ");
        define("DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE_AS_APPIDENTITYNAME", DECRYPTED_LASTNAME_FIRSTNAME_BIRTHDATE . "as AppIdentityName ");
        define("DATES_AS_MEMBERSTATES", " IF(UPPER(CONVERT(BINARY " . DECRYPTED_LASTNAME . " USING utf8)) like '%" . ANONYMOUS . "%', 'Anonymiserad', IF(DateOfDeath is not null, 'Avliden', IF(DateOfMemberShipStart is null, IF(DateOfBaptism is null and CongregationOfBaptism is null, 'Ej medlem', 'Dopregister'), IF(DateOfMemberShipEnd is null, 'Medlem', 'Dopregister')))) ");
        define("DATES_AS_ALISAS_MEMBERSTATES", DATES_AS_MEMBERSTATES . "as MemberState ");

        $longHomeName = "concat(";
        $longHomeName.= DECRYPTED_FAMILYNAME;
        $longHomeName.= ", ' (', IF(";
        $longHomeName.= DECRYPTED_ADDRESS;
        $longHomeName.= " is null, 'Adress saknas', ";
        $longHomeName.= DECRYPTED_ADDRESS;
        $longHomeName.= "), ', ', IF(City is null, 'Stad saknas', City), ')') as LongHomeName ";
        //"concat(FamilyName, ' (', IF(Address is null, 'Adress saknas', Address), ', ', IF(City is null, 'Stad saknas', City), ')') as LongHomeName "
        define("ADDRESS_ALIAS_LONG_HOMENAME", $longHomeName);

        $longHomeNameMultiLine = "concat(";
        $longHomeNameMultiLine.= DECRYPTED_FAMILYNAME;
        $longHomeNameMultiLine.= ", '<BR>', IF(";
        $longHomeNameMultiLine.= DECRYPTED_ADDRESS;
        $longHomeNameMultiLine.= " is null, 'Adress saknas', ";
        $longHomeNameMultiLine.= DECRYPTED_ADDRESS;
        $longHomeNameMultiLine.= "), '<BR>', IF(concat(Zip, ' ', City) is null, 'PA/Stad saknas', concat(Zip, ' ', City))   ) as LongHomeName ";    
        //"concat(FamilyName, '<BR>', IF(Address is null, 'Adress saknas', Address), '<BR>', IF(concat(Zip, ' ', City) is null, 'PA/Stad saknas', concat(Zip, ' ', City)), ')') as LongHomeName "
        define("ADDRESS_ALIAS_LONG_HOMENAME_MULTILINE", $longHomeNameMultiLine);

        $ALL_PEOPLE_FIELDS = "People.Id as PersonId, ";
        $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_FIRSTNAME . ", ";
        $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_LASTNAME . ", ";
        $ALL_PEOPLE_FIELDS.= "DateOfBirth, DateOfDeath, PreviousCongregation, MembershipNo, VisibleInCalendar, DateOfMembershipStart, DateOfMembershipEnd, NextCongregation, DateOfBaptism, ";
        $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_BAPTISTER . ", ";
        $ALL_PEOPLE_FIELDS.= "CongregationOfBaptism, CongregationOfBaptismThis, Gender, ";
        $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_EMAIL . ", ";
        $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_MOBILE . ", ";
        $ALL_PEOPLE_FIELDS.= "KeyToChurch, KeyToExp, ";
        $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_COMMENT . ", ";
        $ALL_PEOPLE_FIELDS.= "People.HomeId, Updater, Updated, Inserter, Inserted, " . DECRYPTED_ALIAS_COMMENT_KEY . " ";
        define("SQL_STAR_PEOPLE", "Select " . $ALL_PEOPLE_FIELDS);

        $ALL_HOME_FIELDS = "Homes.Id as HomesId, ";
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

        define("FORMATTED_EMAILADDRESS", "if(" . DECRYPTED_EMAIL . " not like \"\", concat(\"<p class='mailLink'><a href='mailto:\"," . DECRYPTED_EMAIL . ",\"'>\", " . DECRYPTED_EMAIL . ", \"</a></p>\"),'') ");
        define("NAMES_ALIAS_RESIDENTS", "(SELECT GROUP_CONCAT(" . DECRYPTED_FIRSTNAME . ", ' ', " . DECRYPTED_LASTNAME . " SEPARATOR '<BR>') FROM People as r where Homes.Id = r.HomeId order by DateOfBirth) as Residents ");
    }
    
    function setUserRoleInQuery($user){
        $alias = " as user_role ";
        $sql = "'";
        if(isEditor($user)){
            $sql.= SARON_ROLE_EDITOR . "'" . $alias;
        }
        else{
            $sql.= SARON_ROLE_VIEWER . "'" . $alias;            
        }
        return $sql;
    }
    
    function salt(){        
        //$abc = "!#$%&()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_`abcdefghijklmnopqrstuvwxyz{|}~";
        $abc = "!#$%&()*+,-./0123456789:;=?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_`abcdefghijklmnopqrstuvwxyz{|}~";
        $str = "";
        while(strlen($str)<SALT_LENGTH-1){
            $str.= substr($abc, rand(0, strlen($abc)), 1);
        }
        return $str;
    }



 