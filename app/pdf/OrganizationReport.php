<?php

require '../../config/config.php';
require '../database/queries.php';
require_once (TCPDF_PATH . '/tcpdf.php');
require_once '../database/db.php';
require_once "../access/wp-authenticate.php";

    define ("INNER", 1);
    define ("OUTER", 2);
    define ("BOOKLET_OUTER_MARGIN", 10);
    define ("BOOKLET_INNER_MARGIN", 30);
    define ("INNER_HEADER_TEXT", FullNameOfCongregation);
    define ("OUTER_HEADER_TEXT", 'Utskriftsdatum: ' . date("Y-m-d", time()));
    define ("INNER_FOOTER_TEXT", 'Endast för kontakt mellan medlemmar');
    //define ("HEADER_MARGIN", 10);
    //define ("FOOTER_MARGIN", 10);
    define ("HEADER_FOOTER_CELL_WIDTH", 90);
    define ("FONT_FAMILY", 'times');

    $requireOrg = false;
    $requireEditorRole = false;
    $saronUser = new SaronUser(wp_get_current_user());    

    if(!isPermitted($saronUser, $requireEditorRole, $requireOrg)){
        echo notPermittedMessage();
    }
    else{
        header_remove(); 

        $PersonId = (int)filter_input(INPUT_GET, "PersonId", FILTER_SANITIZE_NUMBER_INT);

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(FullNameOfCongregation);
        $pdf->SetTitle('Organisatonsöversikt');
        $pdf->SetSubject('Organisaton');
        $pdf->SetKeywords('Organisaton');

        // set default header data
        $pdf->SetHeaderData('', 0, FullNameOfCongregation, 'Rapport från: ' . UrlOfRegistry . ' ' . date('Y-m-d', time()));


        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetAutoPageBreak(TRUE, 10);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $name = createOrganizationCalender($pdf);
        $pdf->Output('Organisationskalender - ' . date('Y-m-d', time()).'.pdf');
        $pdf->close();
    }
// ******************** Functions ************************************





function createOrganizationCalender(TCPDF $pdf){
    define ("CELL_HIGHT", 5.5);
    define ("MAX_CELL_HIGHT", 8);
    define ("LIST_FONT_SIZE", 12);
    define ("HEADER_FOOTER_FONT_SIZE", 10);
    define ("FONT", 'times');
    define ("TAB", 0);
    define ("NL", 1);
    define ("FULL_PAGE_WIDTH", 180);
    define ("CELL_WIDTH", 30);
    define ("BACKGROUND_FILLED", 1);
    define ("BACKGROUND_NOT_FILLED", 0);

    $treeName = '';
    
    $db = new db();
    $listResult = $db->sqlQuery(getSQL());
    if(!$listResult){
        exit();
    }

    $pdf->AddPage();

    
    $pdf->SetLineStyle(array('width' => 0.2, 'color' => array(100, 100, 100)));
    $pdf->SetFillColor(200, 200, 200);
    
    foreach($listResult as $aRow){
        if($treeName !== $aRow['Tree_Name']){
            $cnt = 0;
            $treeName = $aRow['Tree_Name'];
            If($aRow['Head_Level'] === "0"){
                $pdf->Ln();
                $pdf->SetFont(FONT_FAMILY, 'B', 16);
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, $aRow['Tree_Name'], 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
//                $pdf->MultiCell(ORG_UNIT_TYPE_CELL_WIDTH, CELL_HIGHT, $aRow['Unit_Name'], 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
            }
            else If($aRow['Head_Level'] === "1"){
                $pdf->SetFont(FONT_FAMILY, 'B', 12);
                $pdf->Ln();
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, $aRow['Tree_Name'], 0, 'L', BACKGROUND_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
            }
            else{
                $pdf->SetFont(FONT_FAMILY, 'B', 12);
                $pdf->Ln();                
                $pdf->MultiCell(FULL_PAGE_WIDTH, CELL_HIGHT, $aRow['Tree_Name'], 'B', 'L', BACKGROUND_NOT_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
                
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
        $pdf->MultiCell(CELL_WIDTH * 3, CELL_HIGHT, $aRow['Role_Name'] . $comment, $line, 'L', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
        //$pdf->MultiCell(CELL_WIDTH * 1, CELL_HIGHT, $aRow['Pos_Comment'], $line, 'L', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');
        
        $pName = "";
        $state = "";
        if($aRow['State_Id'] > 1){
            $state=$aRow['State_Name'];
        }
        else{
            if($aRow['PersonId'] !== $aRow['PrevPersonId']){
                $state = "Ny";
            }
        }
        $pName =  $aRow['Person'];

        $pdf->MultiCell(CELL_WIDTH * 2, CELL_HIGHT, $pName, $line, 'L', BACKGROUND_NOT_FILLED, TAB, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T'); 
        $pdf->MultiCell(CELL_WIDTH, CELL_HIGHT, $state, $line, 'L', BACKGROUND_NOT_FILLED, NL, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T');            
        
    }    
}

function getSQL(){
    $sql = "select Tree.Name as Tree_Name, Unit.Name as Unit_Name, Role.Name as Role_Name, Pos.Comment as Pos_Comment, "
            . "(Select SortOrder from `Org_Role-UnitType` as RUT WHERE  RUT.OrgRole_FK = Pos.OrgRole_FK and RUT.OrgUnitType_FK = Tree.OrgUnitType_FK) as SortOrder,  "
            . "PState.Name as State_Name, PState.Id as State_Id, Pos.People_FK as PersonId, "
            . "Pos.PrevPeople_FK as PrevPersonId, Unit.Name as Unit_Name, "; 
    $sql.= "IF(People_FK>0, CONCAT(";
    $sql.= getFieldSql("People", null, "FirstNameEncrypt", null, true, false);
    $sql.= ", ' ', "; 
    $sql.= getFieldSql("People", null, "LastNameEncrypt", null, true, false);
    $sql.= "),(Select Name from Org_Role Where Org_Role.Id = -People_FK)) as Person, ";
    $sql.= "QueryPath.Path, QueryPath.rel_depth as Head_Level ";
    $sql.= "from Org_Tree as Tree ";
    $sql.= "inner join Org_UnitType as Unit on Unit.Id = Tree.OrgUnitType_FK ";
    $sql.= "left outer join Org_Pos as Pos on Tree.Id = Pos.OrgTree_FK ";
    $sql.= "left outer join Org_Role as Role on Pos.OrgRole_FK=Role.Id ";
    $sql.= "left outer join Org_PosStatus as PState on Pos.OrgPosStatus_FK=PState.Id ";
    $sql.= "left outer join People on People.Id = Pos.People_FK ";    
    $sql.= "inner join (" . getSubSql() . ") as QueryPath on Tree.Id = QueryPath.Id ";

    $sql.= "order by QueryPath.Path, SortOrder, Pos_Comment, Person "; // Rut.SortOrder, 
    return $sql;
}


function getSubSql(){
    $sql = "WITH RECURSIVE Sub_Tree AS (";
    $sql.= "SELECT Id, name, 'top' as nodeType, CAST(name AS CHAR(5000)) AS path, 0 as rel_depth ";
    $sql.= "FROM Org_Tree ";
    $sql.= "where ParentTreeNode_FK is null ";
    $sql.= "UNION ALL ";
    $sql.= "SELECT t.Id, t.name, 'sub' as nodeType, CONCAT(d.path, ' / ', t.name), rel_depth + 1 ";
    $sql.= "FROM Sub_Tree d, Org_Tree t ";
    $sql.= "WHERE t.ParentTreeNode_FK = d.Id ";
    $sql.= ") ";
    $sql.= "SELECT * FROM Sub_Tree "; 
    $sql.= "ORDER BY path ";


    return $sql;
}
    
            
