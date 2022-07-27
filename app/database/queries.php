<?php
require_once SARON_ROOT . 'app/entities/MemberState.php';


    $res = openssl_pkey_get_private (PKEY_FILE);
    openssl_pkey_export($res, $privkey);
    define ("PKEY", "'" . $privkey . "'");
    define("SALT_LENGTH", 13);
    define("MAX_STR_LEN", 250);
    

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

    define("ALIAS_CUR_HOMES", "Homes");
    define("ALIAS_OLD_HOMES", "OldHome");

    $ALL_PEOPLE_FIELDS = "People.Id, ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_FIRSTNAME . ", ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_LASTNAME . ", ";
    $ALL_PEOPLE_FIELDS.= DATE_OF_BIRTH_ALIAS_DATE_OF_BIRTH . ", DateOfDeath, PreviousCongregation, MembershipNo, VisibleInCalendar, DateOfMembershipStart, DateOfMembershipEnd, NextCongregation, DateOfBaptism, DateOfFriendshipStart, ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_BAPTISTER . ", ";
    $ALL_PEOPLE_FIELDS.= "CongregationOfBaptism, CongregationOfBaptismThis, Gender, ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_EMAIL . ", ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_MOBILE . ", ";
    $ALL_PEOPLE_FIELDS.= "KeyToChurch, KeyToExp, ";
    $ALL_PEOPLE_FIELDS.= DECRYPTED_ALIAS_COMMENT . ", ";
    $ALL_PEOPLE_FIELDS.= "People.HomeId, People.HomeId as OldHomeId, Updated, Inserter, Inserted, " . DECRYPTED_ALIAS_COMMENT_KEY . " ";
    
    define("SQL_STAR_PEOPLE", "Select " . $ALL_PEOPLE_FIELDS);

    $ALL_HOME_FIELDS = "Homes.Id, ";
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
    
    define("FORMATTED_EMAILADDRESS", "if(" . DECRYPTED_EMAIL . " not like \"\", concat(\"<p class='Email'><a href='mailto:\"," . DECRYPTED_EMAIL . ",\"'>\", " . DECRYPTED_EMAIL . ", \"</a></p>\"),'') ");

    define("EMBEDDED_SELECT_SUPERPOS", "if(Pos.OrgSuperPos_FK > 0, "
                                            . "concat(' i rollen som <u>', "
                                            . "(Select r2.Name from Org_Role as r2 inner join Org_Pos as p2 on r2.Id=p2.OrgRole_FK where Pos.OrgSuperPos_FK = p2.Id), "
                                            . "'</u>'),"
                                            . "'') ");
    
    define("ORG_POS_XREF", "(Select p1.Id, if(p1.People_FK < 0,(select p2.People_FK from Org_Pos as p2 where -p1.People_FK = p2.OrgRole_FK ), p1.People_FK) as People_FK2 from Org_Pos as p1) as xref ");
       
    define("NOW_TIME_STAMP_DIFF", "if(TO_DAYS(NOW()) - TO_DAYS(Time_Stamp) > 0, 86400, 0) + (TIME_TO_SEC(now()) - TIME_TO_SEC(Time_Stamp)) ");
    define("NOW_LAST_ACTIVITY_DIFF", "if(TO_DAYS(NOW()) - TO_DAYS(Last_Activity) > 0, 86400, 0) + (TIME_TO_SEC(now()) - TIME_TO_SEC(Last_Activity)) ");
   
    


    
 
    
    
    
 