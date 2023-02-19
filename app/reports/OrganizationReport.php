<?php
    require_once 'config.php';
    require_once SARON_ROOT . 'app/entities/SaronUser.php';
    require_once SARON_ROOT . 'app/database/queries.php';
    require_once SARON_ROOT . 'app/database/db.php';
    require_once SARON_ROOT . 'app/entities/Person.php';
    require_once THREE_PP_PATH . 'tcpdf/tcpdf.php';
    
    $db;
    $saronUser;
    $person;

    define("NEW_RESOURCE", 'New');
    define("CURRENT_RESOURCE", 'Old');
    
    $type = (String)filter_input(INPUT_GET, "type", FILTER_SANITIZE_STRING);

    if(strlen($type) > 0){
        $db = new db();
        try{
            $saronUser = new SaronUser($db);
            $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE, TICKET_RENEWAL_CHECK);
            $person = new Person($db, $saronUser);
        }
        catch(Exception $ex){
            header("Location: /" . SARON_URI . LOGOUT_URI);
            exit();                                                
        }
        setUpPdfDoc($db, $person, $type);
    }
    
function setUpPdfDoc($db, $person, $type){
    define ("INNER", 1);
    define ("OUTER", 2);
    define ("BOOKLET_OUTER_MARGIN", 10);
    define ("BOOKLET_INNER_MARGIN", 30);
    define ("INNER_HEADER_TEXT", FullNameOfCongregation);
    define ("OUTER_HEADER_TEXT", 'Utskriftsdatum: ' . date("Y-m-d", time()));
    define ("INNER_FOOTER_TEXT", 'Får endast användas i kontakt mellan medlemmar');
    define ("OUTER_FOOTER_TEXT", 'Får endast användas i kontakt mellan medlemmar');
    //define ("HEADER_MARGIN", 10);
    //define ("FOOTER_MARGIN", 10);
    define ("HEADER_FOOTER_CELL_WIDTH", 90);
    define ("FONT_FAMILY", 'times');


    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(FullNameOfCongregation);
    $pdf->SetTitle('Organisatonsöversikt');
    $pdf->SetSubject('Organisaton');
    $pdf->SetKeywords('Organisaton');

    // set default header data
    $pdf->SetHeaderData('', 0, FullNameOfCongregation, 'Rapport från: ' . UrlOfRegistry . ' ' . date('Y-m-d', time()) . "\r\nEndast för kontakt mellan medlemmar.");


    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $PdfMarginTopBottom = 25;
    $pdf->SetMargins(PDF_MARGIN_LEFT, $PdfMarginTopBottom, PDF_MARGIN_RIGHT);

    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(true, $PdfMarginTopBottom);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    define("STORE_AT_SERVER_FILE_SYSTEM", "F");
    
    $typ = "";
    switch ($type){
        case "server":
            $typ = "Beslutad";
            $name = createOrganizationCalender($db, $pdf, $person, $type);
            $path = SARON_PDF_URI . 'Organisationskalender.pdf';
            $pdf->Output($path, 'F'); // F = File on server
            break;
        case "decided":
            $typ = "Beslutad";
            $name = createOrganizationCalender($db, $pdf, $person, $type);
            $pdf->Output('Organisationskalender - ' . $typ . ' ' . date('Y-m-d', time()).'.pdf');
            break;
        case "vacancy":
            $typ = "Vakanser";
            $name = createOrganizationCalender($db, $pdf, $person, $type);
            $pdf->Output('Organisationskalender - ' . $typ . ' ' . date('Y-m-d', time()).'.pdf');
            break;
        default:
            $typ = "Förslag";
            $name = createOrganizationCalender($db, $pdf, $person, $type);
            $pdf->Output('Organisationskalender - ' . $typ . ' ' . date('Y-m-d', time()).'.pdf');
    }
    $pdf->close();
}
    


function createOrganizationCalender(db $db, TCPDF $pdf, $person, String $type){
    define ("CELL_HIGHT", 5.5);
    define ("MAX_CELL_HIGHT", 5.5);
    define ("MAX_HEAD_CELL_HIGHT", 8.0);
    define ("LIST_FONT_SIZE", 12);
    define ("HEADER_FOOTER_FONT_SIZE", 10);
    define ("FONT", 'times');
    define ("TAB", 0);
    define ("NL", 1);
    define ("FULL_PAGE_WIDTH", 180);
    define ("CELL_WIDTH", 30);
    define ("BACKGROUND_FILLED", 1);
    define ("BACKGROUND_NOT_FILLED", 0);
    $maxCalculatedHight = 240;
    
    $longName = '';
    
    $listResult = $db->sqlQuery(getSQL($person, $type));
    if(!$listResult){
        exit();
    }

    $pdf->SetLineStyle(array('width' => 0.2, 'color' => array(100, 100, 100)));
    $pdf->SetFillColor(200, 200, 200);

    $pdf->AddPage();
    $pdf->Ln();
    $pdf->SetFont(FONT_FAMILY, 'B', 16);

    
    $pdf->SetLineStyle(array('width' => 0.2, 'color' => array(100, 100, 100)));
    $pdf->SetFillColor(200, 200, 200);

    $result = $db->sqlQuery("Select * from Org_Version WHERE id in (Select max(id) from Org_Version)");
    $decisionInfo = "Beslutsinformations saknas om orgnisation";
    foreach($result as $aRow){
        $decisionDate  = substr($aRow['decision_date'], 0, 10);
    }    
    switch ($type){
        case "proposal":
            $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, "Förslag till förändringar utifrån beslutad organisation - " . $decisionDate, 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_HEAD_CELL_HIGHT, 'T', true);
            break;
        case "vacancy":
            $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, "Vakanta uppdrag - " . date("Y-m-d", time()), 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_HEAD_CELL_HIGHT, 'T', true);
            break;
        default:
            $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, "Beslutad organisation - " . $decisionDate, 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_HEAD_CELL_HIGHT, 'T', true);
    }

    
    $calculatedHight = MAX_HEAD_CELL_HIGHT;
    foreach($listResult as $aRow){
        if($longName !== $aRow['LongName']){
            $cnt = 0;
            $longName = $aRow['LongName'];
            
            If($aRow['Head_Level'] === "0"){
                $pdf->Ln();
                $calculatedHight+=MAX_HEAD_CELL_HIGHT;
                $pdf->SetFont(FONT_FAMILY, 'B', 16);
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, $longName, 'B', 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_HEAD_CELL_HIGHT, 'T', true);
//                $pdf->MultiCell(ORG_UNIT_TYPE_CELL_WIDTH, CELL_HIGHT, $aRow['Unit_Name'], 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
                $calculatedHight+=MAX_HEAD_CELL_HIGHT;
            }
            else If($aRow['Head_Level'] === "1"){
                $pdf->SetFont(FONT_FAMILY, 'B', 14);
                $calculatedHight+=MAX_HEAD_CELL_HIGHT;
                $pdf->Ln();
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, $longName, 'B', 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_HEAD_CELL_HIGHT, 'T');
                $calculatedHight+=MAX_HEAD_CELL_HIGHT;
            }
            else{
                $pdf->SetFont(FONT_FAMILY, 'P', 12);
                $calculatedHight+=MAX_HEAD_CELL_HIGHT;
                $pdf->Ln();                
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, $longName, 'B', 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
                $calculatedHight+=MAX_CELL_HIGHT;
                
            }
            $info = $aRow['Info'];
            if(strlen($info) > 0){
                $pdf->SetFont(FONT_FAMILY, 'I', 10);
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, ' - ' . $info, 'B', 'L', BACKGROUND_NOT_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');                
                $calculatedHight+=MAX_CELL_HIGHT;
            }
        }
        

        $cnt++;
        if($cnt > 2){
            $line = 'B';
            $cnt = '0';
        }
        else{
            $line = '0';
        }
        $pdf->SetFont(FONT_FAMILY, '', 10);

        $responsible ="";
        switch ($aRow['ResponsibleType']){
            case 'Function':
                $responsible = "[" . $aRow['Responsible'] . "]\n"; 
            break;
            case 'OrganisationsRole':
                $responsible = "* " . $aRow['Responsible'] . "\n"; 
            break;
            default:
                $responsible = $aRow['Responsible'] . "\n";                         
        }


        $roleName = "";
        IF($aRow['RoleType']  > 0){
            $roleName = "* " . $aRow['Role_Name'];                
        }
        else{
            $roleName = $aRow['Role_Name'];                                    
        }

        if(strlen($aRow['Pos_Comment']) > 0 ){
            $roleName.=", " . $aRow['Pos_Comment'] . "\n";
        }
        else{
            $roleName.="\n";
        }
                        
        $state = "";
        if( $type === "proposal"){
            switch($aRow['State_Id']){
            case 1:
                if($aRow['IsNew'] === NEW_RESOURCE){
                    $state = "Ny";
                }
                else{
                    $state="";
                }
            break;
            case 5:
                $state="Tillsätts ej";
            break;
            default:
                $state="Vakant";
                $responsible = "";
            }
            if($aRow['State_Id']>0){
                $pdf->MultiCell(CELL_WIDTH * 3, CELL_HIGHT, $roleName, $line, 'L', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');                
                $pdf->MultiCell(CELL_WIDTH * 2, CELL_HIGHT, $responsible , $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', True); 
                $pdf->MultiCell(CELL_WIDTH, CELL_HIGHT, $state, $line, 'L', BACKGROUND_NOT_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');            
                $calculatedHight+=MAX_CELL_HIGHT;
            }
        }
        else{
            $line_comment = '0';
            if(strlen($aRow['Pos_Comment']) > 0){
                $line_comment = $line;
                $line = '0';
            }

            $pdf->MultiCell(CELL_WIDTH * 2, CELL_HIGHT, $roleName, $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', true);
            $pdf->MultiCell(CELL_WIDTH * 1.5, CELL_HIGHT, $responsible, $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', True);                                             
            $pdf->MultiCell(CELL_WIDTH * 1.5, CELL_HIGHT, $aRow['People_Email']."\n", $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', true); 
            $pdf->MultiCell(CELL_WIDTH * 1, CELL_HIGHT, $aRow['People_Mobile']."\n", $line, 'J', BACKGROUND_NOT_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', true); 
            $calculatedHight+=MAX_CELL_HIGHT;
        }
        if($calculatedHight > $maxCalculatedHight){
            $pdf->AddPage();
            $calculatedHight=0;
        }
    } 
    return 'Organisationskalender - ' . $type;
}

function getSQL($person, $type){
    
    $sql = "select LongName, Role.RoleType,Tree.Prefix as Tree_Prefix, Tree.Name as Tree_Name, Tree.Description as Info, Unit.Name as Unit_Name, Role.Name as Role_Name, Pos.Comment as Pos_Comment, "
            . "(Select SortOrder from `Org_Role-UnitType` as RUT WHERE  RUT.OrgRole_FK = Pos.OrgRole_FK and RUT.OrgUnitType_FK = Tree.OrgUnitType_FK) as SortOrder,  "
            . "PState.Name as State_Name, PState.Id as State_Id, Pos.People_FK as PersonId, "
            . "Pos.PrevPeople_FK as PrevPersonId, Unit.Name as Unit_Name, "; 
    $sql.= "Case ";
    $sql.= "When  Pos.Function_FK > 0 ";
    $sql.= "Then (Select T.Name from Org_Tree as T Where T.Id = Function_FK) ";
    $sql.= "When  Pos.People_FK > 0 ";
    $sql.= "Then CONCAT(";
    $sql.= $person->getFieldSql("People", null, "FirstNameEncrypt", null, true, false);
    $sql.= ", ' ', "; 
    $sql.= $person->getFieldSql("People", null, "LastNameEncrypt", null, true, false);
    $sql.= ") ";
    $sql.= "When  Pos.OrgSuperPos_FK > 0 ";
    $sql.= "Then (Select R.Name from Org_Role as R inner join Org_Pos as P on R.Id=P.OrgRole_FK inner join Org_Pos as PS on PS.OrgSuperPos_FK = P.Id Where PS.Id = Pos.Id) ";
    $sql.= "End ";
    $sql.= "as Responsible, ";
    $sql.= "Case ";
    $sql.= "When  Pos.Function_FK > 0 ";
    $sql.= "Then IF(Pos.PrevFunction_FK is null, '" . NEW_RESOURCE . "', IF(Pos.Function_FK != Pos.PrevFunction_FK, '" . NEW_RESOURCE . "', '" . CURRENT_RESOURCE . "')) ";
    $sql.= "When  Pos.People_FK > 0 ";
    $sql.= "Then IF(Pos.PrevPeople_FK is null, '" . NEW_RESOURCE . "', IF(Pos.People_FK != Pos.PrevPeople_FK, '" . NEW_RESOURCE . "', '" . CURRENT_RESOURCE . "')) ";
    $sql.= "When  Pos.OrgSuperPos_FK > 0 ";
    $sql.= "Then IF(Pos.PrevOrgSuperPos_FK is null, '" . NEW_RESOURCE . "', IF(Pos.OrgSuperPos_FK != Pos.PrevOrgSuperPos_FK, '" . NEW_RESOURCE . "', '" . CURRENT_RESOURCE . "')) ";
    $sql.= "End ";
    $sql.= "as IsNew, ";
    $sql.= "Case ";
    $sql.= "When  Pos.Function_FK > 0 ";
    $sql.= "Then 'Function' ";
    $sql.= "When  Pos.People_FK > 0 ";
    $sql.= "Then 'Person' ";
    $sql.= "When  Pos.OrgSuperPos_FK > 0 ";
    $sql.= "Then 'OrganisationsRole' ";
    $sql.= "End ";
    $sql.= "as ResponsibleType, ";
    $sql.= $person->getFieldSql("People", "Email", "EmailEncrypt", null, true, true);
    $sql.= $person->getFieldSql("People", "Mobile", "MobileEncrypt", null, true, true);
    $sql.= "QueryPath.Path, QueryPath.rel_depth as Head_Level ";
    $sql.= "from Org_Tree as Tree ";
    $sql.= "inner join Org_UnitType as Unit on Unit.Id = Tree.OrgUnitType_FK ";
    $sql.= "left outer join Org_Pos as Pos on Tree.Id = Pos.OrgTree_FK ";
    $sql.= "left outer join Org_Role as Role on Pos.OrgRole_FK=Role.Id ";
    $sql.= "left outer join Org_PosStatus as PState on Pos.OrgPosStatus_FK=PState.Id ";
    
    $sqlWhere = "";
    switch ($type){
        case "proposal":
            $sql.= "left outer join People on People.Id = Pos.People_FK ";  
            break;
        case "vacancy":
            $sql.= "left outer join People on People.Id = Pos.People_FK ";  
            $sqlWhere = "WHERE PState.Id = 4 ";
            break;
        default:
            $sql.= "left outer join People on People.Id = Pos.PrevPeople_FK ";    
    }
    $sql.= "inner join (" . getSubSql() . ") as QueryPath on Tree.Id = QueryPath.Id ";
    $sql.= $sqlWhere;
    $sql.= "order by QueryPath.Path, SortOrder, Role_Name, Pos_Comment, Responsible "; // Rut.SortOrder, 
    return $sql;
}


function getSubSql(){
    $longName = "concat(IF(Prefix is not null, concat(Prefix, ' '),''), name) ";
    $tLongName = "concat(IF(Prefix is not null, concat(t.Prefix, ' '),''), t.name) ";
    
    $sql = "WITH RECURSIVE Sub_Tree AS (";
    $sql.= "SELECT Id, " . $longName . " as LongName, 'top' as nodeType, CAST(" . $longName . " AS CHAR(5000)) AS path, 0 as rel_depth ";
    $sql.= "FROM Org_Tree ";
    $sql.= "where ParentTreeNode_FK is null ";
    $sql.= "UNION ALL ";
    $sql.= "SELECT t.Id, " . $tLongName.  " as LongName, 'sub' as nodeType, CONCAT(d.path, ' / ', " . $tLongName.  "), rel_depth + 1 ";
    $sql.= "FROM Sub_Tree d, Org_Tree t ";
    $sql.= "WHERE t.ParentTreeNode_FK = d.Id ";
    $sql.= ") ";
    $sql.= "SELECT * FROM Sub_Tree "; 
    $sql.= "ORDER BY path ";


    return $sql;
}
    
            
