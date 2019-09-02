<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . 'app/database/queries.php'; 
require_once SARON_ROOT . 'app/database/db.php';

                
    /*** REQUIRE USER AUTHENTICATION ***/
    $requireEditorRole = false;
    $user = wp_get_current_user();    

    $WP_id=$user->ID;

    if(!isPermitted($user, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
        $tableview = (String)filter_input(INPUT_POST, "tableview", FILTER_SANITIZE_STRING);
        $HomeId = (int)filter_input(INPUT_GET, "HomeId", FILTER_SANITIZE_NUMBER_INT);

        switch ($tableview){
            case "people":
                $sqlList = selectPeople() . ", " . setUserRoleInQuery($user);
                break;
            case "birthdays":
                $sqlList = selectBirthday();
                break;
            case "member":
                $sqlList = SQL_STAR_PEOPLE . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . DATES_AS_ALISAS_MEMBERSTATES . ", " . setUserRoleInQuery($user);
                break;
            case "baptist":
                $sqlList = SQL_STAR_PEOPLE . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . DATES_AS_ALISAS_MEMBERSTATES . ", " . setUserRoleInQuery($user);
                break;
            case "keys":
                $sqlList = "Select People.Id as PersonId, KeyToExp, KeyToChurch, DateOfBirth, " . DECRYPTED_ALIAS_COMMENT_KEY . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . DATES_AS_ALISAS_MEMBERSTATES . ", " . setUserRoleInQuery($user);
                break;
            case "total":
                $sqlList = selectTotal() . ", " . setUserRoleInQuery($user);
                break;
            default:    
                $sqlList = selectPeople() . ", " . setUserRoleInQuery($user);
                break;
        }


        $sqlFrom = SQL_FROM_PEOPLE_LEFT_JOIN_HOMES;    

        $groupId = (int)filter_input(INPUT_POST, "groupId", FILTER_SANITIZE_NUMBER_INT);    
        $sqlOrderByLatest="";

        //Medlemmar
        switch ($groupId){
            case 0:
                $sqlWhereGroup = SQL_WHERE_MEMBER;
                break;

            case 1:
                //Dopregister
                $sqlWhereGroup = "DateOfMembershipStart is not null and DateOfDeath is null and (" . DECRYPTED_LASTNAME . " not like '" . ANONYMOUS . "') ";
                break;

            case 2:
                //Senast ändrade
                $sqlOrderByLatest="Updated desc";
                $sqlWhereGroup = "true ";
                break;

            case 3:
                //Nya medlemmar innevarande år 
                $sqlWhereGroup = "EXTRACT(YEAR FROM NOW())=EXTRACT(YEAR FROM DateOfMembershipStart) ";
                break;

            case 4:
                //Nya medlemmar föregående år 
                $sqlWhereGroup = "EXTRACT(YEAR FROM NOW())-1=EXTRACT(YEAR FROM DateOfMembershipStart) ";
                break;

            case 5:
                //Avslutade medlemmar innevarande år 
                $sqlWhereGroup = "EXTRACT(YEAR FROM NOW())=EXTRACT(YEAR FROM DateOfMembershipEnd) ";
                break;

            case 6:
                //Avslutade medlemmar föregående år 
                $sqlWhereGroup = "EXTRACT(YEAR FROM NOW())-1=EXTRACT(YEAR FROM DateOfMembershipEnd) ";
                break;

            case 7:
                //Döpta innevarande år
                $sqlWhereGroup = "EXTRACT(YEAR FROM NOW())=EXTRACT(YEAR FROM DateOfBaptism) ";
                break;

            case 8:
                //Döpta förgående år
                $sqlWhereGroup = "EXTRACT(YEAR FROM NOW())-1=EXTRACT(YEAR FROM DateOfBaptism) ";
                break;

            case 9:
                //Medlemmar som inte syns i adresskalendern
                $sqlWhereGroup = SQL_WHERE_MEMBER . " and VisibleInCalendar != 2 ";
                break;

            case 10:
                //Medlemmar som dött
                $sqlWhereGroup = "EXTRACT(YEAR FROM NOW())=EXTRACT(YEAR FROM DateOfDeath) and  DateOfMembershipStart is not null and " . DECRYPTED_LASTNAME . " not like '" . ANONYMOUS . "' ";
                break;

            case 11:
                //Medlemmar som dött föregående år
                $sqlWhereGroup = "EXTRACT(YEAR FROM NOW())-1=EXTRACT(YEAR FROM DateOfDeath) and DateOfMembershipStart is not null and (" . DECRYPTED_LASTNAME . " not like '" . ANONYMOUS . "') ";
                break;

            case 12:
                //Hela registret
                $sqlWhereGroup = "true ";
                break;
            case 13:
                //Medlemmar utanför Norrköping
                $sqlWhereGroup = SQL_WHERE_MEMBER . " and Zip not like '6%' ";
                break;
            case 14:
                //ej medlem
                $sqlWhereGroup = "((DateOfMembershipStart is null and DateOfMembershipEnd is null) or (DateOfMembershipEnd is not null)) and DateOfDeath is null and (" . DECRYPTED_LASTNAME . " not like '" . ANONYMOUS . "') ";
                break;
            case 15:
                //underlag för anonymisering nästa år
                $sqlWhereGroup = "(((DateOfMembershipStart is null and DateOfMembershipEnd is null) or (DateOfMembershipEnd is not null)) and DateOfDeath is null) and " . DECRYPTED_LASTNAME . " NOT like '" . ANONYMOUS . "' and EXTRACT(YEAR FROM DateOfMembershipEnd) <> EXTRACT(YEAR FROM Now()) ";
                break;
            case 16:
                //anonymiserade
                $sqlWhereGroup = "(" . DECRYPTED_LASTNAME . " like '" . ANONYMOUS . "') ";
                break;
            default :
                $sqlWhereGroup = SQL_WHERE_MEMBER; 
        }
        
        $sqlWhereSearch = "";
        $uppercaseSearchString = strtoupper((String)filter_input(INPUT_POST, "searchString", FILTER_SANITIZE_STRING));

        if($uppercaseSearchString != ""){
            $sqlWhereSearchSubString = " like '%" . $uppercaseSearchString . "%'";
            $sqlWhereSearch = "and (UPPER(CONVERT(BINARY ";
            $sqlWhereSearch.= DECRYPTED_FIRSTNAME . " USING utf8)) ";
            $sqlWhereSearch.=$sqlWhereSearchSubString . " or  UPPER(CONVERT(BINARY ";
            $sqlWhereSearch.= DECRYPTED_LASTNAME . " USING utf8)) ";
            $sqlWhereSearch.=$sqlWhereSearchSubString . " or  UPPER(CONVERT(BINARY ";
            $sqlWhereSearch.= DECRYPTED_FAMILYNAME . " USING utf8)) ";
            $sqlWhereSearch.=$sqlWhereSearchSubString . " or ";
            $sqlWhereSearch.= "City";
            $sqlWhereSearch.=$sqlWhereSearchSubString . " or  UPPER(CONVERT(BINARY ";
            $sqlWhereSearch.= DECRYPTED_ADDRESS . " USING utf8)) ";
            $sqlWhereSearch.=$sqlWhereSearchSubString . " or  UPPER(CONVERT(BINARY ";
            $sqlWhereSearch.= DECRYPTED_COMMENT . " USING utf8)) ";
            $sqlWhereSearch.=$sqlWhereSearchSubString . " or  UPPER(CONVERT(BINARY "; 
            $sqlWhereSearch.= DECRYPTED_COMMENT_KEY . " USING utf8)) ";
            $sqlWhereSearch.=$sqlWhereSearchSubString . " or  UPPER(CONVERT(BINARY ";
            $sqlWhereSearch.= DECRYPTED_EMAIL . " USING utf8)) ";
            $sqlWhereSearch.=$sqlWhereSearchSubString . " or ";
            $sqlWhereSearch.= "Country";
            $sqlWhereSearch.=$sqlWhereSearchSubString . " or ";
            $sqlWhereSearch.= "MembershipNo";
            $sqlWhereSearch.=$sqlWhereSearchSubString . ") ";
        }
        $sqlWhere = SQL_WHERE . $sqlWhereGroup . $sqlWhereSearch;

        $sqlOrderBy = ""; 
        $jtSorting = (String)filter_input(INPUT_GET, "jtSorting", FILTER_SANITIZE_STRING);
        if(Strlen($jtSorting)>0 and Strlen($sqlOrderByLatest)>0){
            $sqlOrderBy = "ORDER BY " . $sqlOrderByLatest . ", " . $jtSorting . " ";
        }
        else if(Strlen($jtSorting)==0 and Strlen($sqlOrderByLatest)>0){
            $sqlOrderBy = "ORDER BY " . $sqlOrderByLatest . " ";
        }
        else if(Strlen($jtSorting)>0 and Strlen($sqlOrderByLatest)==0){
            $sqlOrderBy = "ORDER BY " . $jtSorting . " ";
        }
        else{
            $sqlOrderBy = "";         
        }

        $jtPageSize = (int)filter_input(INPUT_GET, "jtPageSize", FILTER_SANITIZE_NUMBER_INT);
        $jtStartIndex = (int)filter_input(INPUT_GET, "jtStartIndex", FILTER_SANITIZE_NUMBER_INT);
        $sqlLimit = "LIMIT " . $jtStartIndex . "," . $jtPageSize . ";";
        $sql = $sqlList . $sqlFrom . $sqlWhere . $sqlOrderBy . $sqlLimit; 
        try{
            $db = new db();
            $respons = $db->select($user, $sqlList, $sqlFrom, $sqlWhere, $sqlOrderBy, $sqlLimit );        
            $db = null;
            echo $respons;
        }
        catch(Exception $error){
            echo $error->getMessage();        
            $db = null;            
        }

    }
    
    function selectPeople(){
        return SQL_STAR_PEOPLE . ", " . DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . ADDRESS_ALIAS_LONG_HOMENAME . ", "  . DECRYPTED_ALIAS_PHONE . ", " . DATES_AS_ALISAS_MEMBERSTATES;
    }


    function selectBirthday(){
        $sql = SQL_STAR_PEOPLE . ", ";
        $sql.= "concat(";
        $sql.= "IF(" . FORMATTED_EMAILADDRESS . " like '', '', " . FORMATTED_EMAILADDRESS . "), ";
        $sql.= "IF(" . DECRYPTED_MOBILE . " is null, '', concat(" . DECRYPTED_MOBILE . ", ', ')), ";
        $sql.= "IF(" . DECRYPTED_PHONE . " is null, '', concat(" . DECRYPTED_PHONE . ", ', ')), ";
        $sql.= "IF(" . DECRYPTED_ADDRESS . " is null, ' ', concat(" . DECRYPTED_ADDRESS . ", ', ')), "; 
        $sql.= "IF(" . Zip. " is null, ' ', concat(" . Zip . ", ', ')), ";
        $sql.= "IF(" . City . " is null, ' ', " . City . ")"; 
        $sql.= ") as Contact, ";
        $sql.= DECRYPTED_LASTNAME_FIRSTNAME_AS_NAME . ", " . DATES_AS_ALISAS_MEMBERSTATES . ", ";
        $sql.= "extract(YEAR FROM NOW()) - extract(YEAR FROM DateOfBirth) as Age, ";
        $sql.= "concat(extract(year from now()),'-', DATE_FORMAT( STR_TO_DATE(extract(Month from DateOfBirth), '%m' ) , '%m' ),'-',DATE_FORMAT(STR_TO_DATE(extract(day from DateOfBirth), '%d' ) , '%d' )) as NextBirthday ";
        return $sql;
    }

    function selectTotal(){
        $selectPerson = "select People.Id as PersonId, concat('<b>'," . DECRYPTED_LASTNAME . ", ' ', " . DECRYPTED_FIRSTNAME . ", '</b><BR>', DateOfBirth, if(DateOfDeath is null,'', concat (' -- ', DateOfDeath)), '<BR><B>Status: </B>', " . DATES_AS_MEMBERSTATES. ") as Person, ";
        $selectMember = "concat (";
        $selectMember.= "'<B>Medlemskap start: </B>', if(DateOfMembershipStart is null,'',DateOfMembershipStart), '<BR>', ";
        $selectMember.= "'<B>Medlemskap avslut: </B>', if(DateOfMembershipEnd is null, '',DateOfMembershipEnd ), '<BR>', ";
        $selectMember.= "'<B>Medlemsnummer: </B>', if(MembershipNo is null,'-',MembershipNo), '<BR>', "; 
        $selectMember.= "'<B>Föregående församling: </B>', if(PreviousCongregation is null,'',PreviousCongregation), '<BR>', ";
        $selectMember.= "'<B>Nästa församling: </B>', if(NextCongregation is null,'',NextCongregation), '<BR>', ";
        $selectMember.= "'<B>Not: </B>', if(CommentEncrypt is null,'-'," . DECRYPTED_COMMENT . "), '<BR>' ";
        $selectMember.= ") as Membership, "; 

        $selectBaptist = "concat (";
        $selectBaptist.= "'<B>Dopdatum: </B>', if(DateOfBaptism is null,'',DateOfBaptism), '<BR>', ";
        $selectBaptist.= "'<B>Dopförrättare: </B>', if(BaptisterEncrypt is null,''," . DECRYPTED_BAPTISTER . "), '<BR>', ";
        $selectBaptist.= "'<B>Dopförsamling: </B>', if(CongregationOfBaptism is null,'',CongregationOfBaptism), '<BR>' ";
        $selectBaptist.= ") as Baptist, ";

        $selectAddress = "concat (";
        $selectAddress.= "'<B>','Adress:</B><BR>', ";
        $selectAddress.= "if(" . DECRYPTED_CO . " like '' or " . DECRYPTED_CO . " is null,'',concat('Co ', " . DECRYPTED_CO . ", '<BR>')), ";
        $selectAddress.= "if(AddressEncrypt is null,''," . DECRYPTED_ADDRESS . "), '<BR>', ";
        $selectAddress.= "if(Zip is null,'',concat(Zip, ' ')), ";
        $selectAddress.= "if(City is null,'',City), '<BR>', ";
        $selectAddress.= "if(Country is null,'',Country), '<BR>', ";
        $selectAddress.= FORMATTED_EMAILADDRESS . ", ";
        $selectAddress.= "'<b>Tel: </B>', if(PhoneEncrypt is null,'', " . DECRYPTED_PHONE. "), '<BR>', ";
        $selectAddress.= "'<b>Mobil: </B>', if(MobileEncrypt is null,''," . DECRYPTED_MOBILE . "), '<BR>'";
        $selectAddress.= ") as Contact, ";    

        $selectOther = "concat (";
        $selectOther.= "'<B>Brevutskick: </B>', if(Letter=1,'Ja','Nej'), '<BR>', ";
        $selectOther.= "'<B>Kyrknyckel: </B>', if(KeyToChurch=0,'Nej','Ja'), '<BR>', ";
        $selectOther.= "'<B>Kyrknyckel (Exp): </B>', if(KeyToExp=0,'Nej', 'Ja'), '<BR>', ";
        $selectOther.= "'<B>Kommentar (Nyckel): </B>', if(CommentKeyEncrypt is null, ''," . DECRYPTED_COMMENT_KEY . "), '<BR>', ";
        $selectOther.= "'<B>Synlig i adresskalender: </B>', if(VisibleInCalendar=2,'Ja','Nej'), '<BR>', ";
        $selectOther.= "'<B>Kön: </B>', IF(Gender=0,'-', IF(Gender=1,'Man','Kvinna')) ";
        $selectOther.= ") as Other ";   
           //
        return $selectPerson . $selectMember . $selectBaptist . $selectAddress  . $selectOther;    
    }

    function sqlToUCase($columnName){
        return "(" . $columnName . ")";
    }

