<?php
    require_once 'config.php';
    require_once SARON_ROOT . 'app/entities/SaronUser.php';
    require_once SARON_ROOT . 'app/database/queries.php';
    require_once SARON_ROOT . 'app/database/db.php';
    require_once TCPDF_PATH . '/tcpdf.php';

    $db = new db();
    try{
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE, TICKET_RENEWAL_CHECK);
    }
    catch(Exception $ex){
        header("Location: /" . SARON_URI . LOGOUT_URI);
        exit();                                                
    }

    $type = (String)filter_input(INPUT_GET, "type", FILTER_SANITIZE_STRING);

    if(strlen($type) > 0){
        setUpPdfDoc($type);
    }
    
function setUpPdfDoc($type){
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
            $name = createOrganizationCalender($pdf, $type);
            $path = SARON_ROOT . 'data/Organisationskalender.pdf';
            $pdf->Output($path, 'F'); // F = File on server
            break;
        case "decided":
            $typ = "Beslutad";
            $name = createOrganizationCalender($pdf, $type);
            $pdf->Output('Organisationskalender - ' . $typ . ' ' . date('Y-m-d', time()).'.pdf');
            break;
        default:
            $typ = "Förslag";
            $name = createOrganizationCalender($pdf, $type);
            $pdf->Output('Organisationskalender - ' . $typ . ' ' . date('Y-m-d', time()).'.pdf');
    }
    $pdf->close();
}
    


function createOrganizationCalender(TCPDF $pdf, String $type){
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

    $longName = '';
    
    $db = new db();
    $listResult = $db->sqlQuery(getSQL($type));
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
        default:
            $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, "Beslutad organisation - " . $decisionDate, 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_HEAD_CELL_HIGHT, 'T', true);
    }

    
    foreach($listResult as $aRow){
        if($longName !== $aRow['LongName']){
            $cnt = 0;
            $longName = $aRow['LongName'];
            
            If($aRow['Head_Level'] === "0"){
                $pdf->Ln();
                $pdf->SetFont(FONT_FAMILY, 'B', 16);
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, $longName, 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_HEAD_CELL_HIGHT, 'T', true);
//                $pdf->MultiCell(ORG_UNIT_TYPE_CELL_WIDTH, CELL_HIGHT, $aRow['Unit_Name'], 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
            }
            else If($aRow['Head_Level'] === "1"){
                $pdf->SetFont(FONT_FAMILY, 'B', 14);
                $pdf->Ln();
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, $longName, 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_HEAD_CELL_HIGHT, 'T');
            }
            else{
                $pdf->SetFont(FONT_FAMILY, 'P', 12);
                $pdf->Ln();                
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, $longName, 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
                
            }
            $info = $aRow['Info'];
            if(strlen($info) > 0){
                $pdf->SetFont(FONT_FAMILY, 'I', 10);
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, ' - ' . $info, '', 'L', BACKGROUND_NOT_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');                
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
        $comment = "";
        if(strlen($aRow['Pos_Comment']) > 0) {
            $comment = ", " . $aRow['Pos_Comment'];            
        }
        
        $pName =  $aRow['Person'];        

        $state = "";
        switch ($type){
            case "proposal":
                if($aRow['State_Id'] > 1){
                    $state=$aRow['State_Name'];
                    
                }
                else{
                    if($aRow['PersonId'] !== $aRow['PrevPersonId']){
                        $state = "Ny";
                    }
                }

                $comment = "";
                if(strlen($aRow['Pos_Comment']) > 0) {
                    $comment = ", " . $aRow['Pos_Comment'];            
                }

                $pdf->MultiCell(CELL_WIDTH * 3, CELL_HIGHT, $aRow['Role_Name'] . $comment, $line, 'L', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');                
                if($aRow['State_Id'] === '6'){ // Funktionsansvar
                    $pdf->MultiCell(CELL_WIDTH * 2, CELL_HIGHT, $aRow['FunctionRespons'] . "\n", $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', True);                     
                }
                else{
                    $pdf->MultiCell(CELL_WIDTH * 2, CELL_HIGHT, $pName . "\n", $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', True); 
                }
                $pdf->MultiCell(CELL_WIDTH, CELL_HIGHT, $state, $line, 'L', BACKGROUND_NOT_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');            
                break;
            default:
                $line_comment = '0';
                if(strlen($aRow['Pos_Comment']) > 0){
                    $line_comment = $line;
                    $line = '0';
                }
                $role = $aRow['Role_Name'];
                if(strlen($aRow['Pos_Comment']) > 0 ){
                    $role.=", " . $aRow['Pos_Comment'];
                }
                
                $pdf->MultiCell(CELL_WIDTH * 2, CELL_HIGHT, $role . "\n", $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', true);

                if($aRow['State_Id'] > 3){ // not checked person
                    $pdf->MultiCell(CELL_WIDTH * 3, CELL_HIGHT, $aRow['FunctionRespons'] . "\n", $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', True);                     
                    $pdf->MultiCell(CELL_WIDTH * 1, CELL_HIGHT, $aRow['State_Name'] . "\n", $line, 'J', BACKGROUND_NOT_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', True);                     
                }
                else{
                    $pdf->MultiCell(CELL_WIDTH * 1.5, CELL_HIGHT, $pName . "\n", $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', True);                                             
                    $pdf->MultiCell(CELL_WIDTH * 1.5, CELL_HIGHT, $aRow['People_Email']."\n", $line, 'J', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', true); 
                    $pdf->MultiCell(CELL_WIDTH * 1, CELL_HIGHT, $aRow['People_Mobile']."\n", $line, 'J', BACKGROUND_NOT_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'M', true); 
                }
        }        
    } 
    return 'Organisationskalender - ' . $type;
}

function getSQL($type){
    $sql = "select LongName, Tree.Prefix as Tree_Prefix, Tree.Name as Tree_Name, Tree.Description as Info, Unit.Name as Unit_Name, Role.Name as Role_Name, Pos.Comment as Pos_Comment, "
            . "(Select SortOrder from `Org_Role-UnitType` as RUT WHERE  RUT.OrgRole_FK = Pos.OrgRole_FK and RUT.OrgUnitType_FK = Tree.OrgUnitType_FK) as SortOrder,  "
            . "PState.Name as State_Name, PState.Id as State_Id, Pos.People_FK as PersonId, "
            . "Pos.PrevPeople_FK as PrevPersonId, Unit.Name as Unit_Name, "; 
    $sql.= "(Select T.Name from Org_Tree as T Where T.Id = PrevFunction_FK) as FunctionRespons, ";
    $sql.= getFieldSql("People", "Email", "EmailEncrypt", null, true, true);
    $sql.= getFieldSql("People", "Mobile", "MobileEncrypt", null, true, true);
    $sql.= "IF(People.Id>0, CONCAT(";
    $sql.= getFieldSql("People", null, "FirstNameEncrypt", null, true, false);
    $sql.= ", ' ', "; 
    $sql.= getFieldSql("People", null, "LastNameEncrypt", null, true, false);
    $sql.= "),";
    $sql.= "(Select Name from Org_Role Where Org_Role.Id = -People_FK)) as Person, ";
    $sql.= "QueryPath.Path, QueryPath.rel_depth as Head_Level ";
    $sql.= "from Org_Tree as Tree ";
    $sql.= "inner join Org_UnitType as Unit on Unit.Id = Tree.OrgUnitType_FK ";
    $sql.= "left outer join Org_Pos as Pos on Tree.Id = Pos.OrgTree_FK ";
    $sql.= "left outer join Org_Role as Role on Pos.OrgRole_FK=Role.Id ";
    $sql.= "left outer join Org_PosStatus as PState on Pos.OrgPosStatus_FK=PState.Id ";
    
    switch ($type){
        case "proposal":
            $sql.= "left outer join People on People.Id = Pos.People_FK ";  
            break;
        default:
            $sql.= "left outer join People on People.Id = Pos.PrevPeople_FK ";    
    }
    $sql.= "inner join (" . getSubSql() . ") as QueryPath on Tree.Id = QueryPath.Id ";

    $sql.= "order by QueryPath.Path, SortOrder, Role_Name, Pos_Comment, Person "; // Rut.SortOrder, 
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
    
            
