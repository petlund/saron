<?php

    require_once 'config.php';
    require_once SARON_ROOT . 'app/entities/SaronUser.php';
    require_once SARON_ROOT . 'app/database/queries.php';
    require_once SARON_ROOT . 'app/database/db.php';

    require_once THREE_PP_PATH . 'tcpdf/tcpdf.php';

    $db = new db();
    try{
        $saronUser = new SaronUser($db);
        $saronUser->hasValidSaronSession(REQUIRE_VIEWER_ROLE, REQUIRE_ORG_VIEWER_ROLE, TICKET_RENEWAL_CHECK);
    }
    catch(Exception $ex){
        header("Location: /" . SARON_URI . LOGOUT_URI);
        exit();                                                
    }
   
    $id = (int)filter_input(INPUT_GET, "Id", FILTER_SANITIZE_NUMBER_INT);

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(FullNameOfCongregation);
    $pdf->SetTitle('Dossier');
    $pdf->SetSubject('Dossier');
    $pdf->SetKeywords('Medlemmar');

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

    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    $name = createDossier($pdf, $id);
    $pdf->Output('Registerutdrag - '. $name . ' ' . date('Y-m-d', time()).'.pdf');
    $pdf->close();



// ******************** Functions ************************************





function createDossier(TCPDF $pdf, $id){
    $FONT_FAMILY = 'times';
        
    $db = new db();
    $listResult = $db->sqlQuery(getSQL($id));
    
    if(!$listResult){
        exit();
    }

    $leftColWidth = 50;
    $rightColWidt = 120;
        

    foreach($listResult as $aRow){
        $pdf->AddPage();
        $pdf->SetFont($FONT_FAMILY, 'B', 14);
        $pdf->MultiCell($rightColWidt, 5, "Identitet", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->SetFont($FONT_FAMILY, '', 12);
        $pdf->MultiCell($leftColWidth, 5, "Namn", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T');

        $name = $aRow['FirstName'] . ' ' . $aRow['LastName'];
        $pdf->MultiCell($rightColWidt, 5, $name, 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');             
        $pdf->MultiCell($leftColWidth, 5, "Födelsedatum", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['DateOfBirth'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');             

        $pdf->SetFont($FONT_FAMILY, 'B', 14);
        $pdf->MultiCell($rightColWidt, 5, "", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, "Kontaktuppgifter", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->SetFont($FONT_FAMILY, '', 12);

        $address = "";
        if (strlen($aRow['Co'])>0){
            $address = 'Co ' . $aRow['Co'] . ', ';
        }
        
        $pdf->MultiCell($leftColWidth, 5, "Adress", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $address .= $aRow['Address'];
        $pdf->MultiCell($rightColWidt, 5, $address, 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');             
        $pdf->MultiCell($leftColWidth, 5, "", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['Zip'] . ' ' . $aRow['City'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($leftColWidth, 5, "", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['Country'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($leftColWidth, 5, "Telefon", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['Phone'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');             
        $pdf->MultiCell($leftColWidth, 5, "Mobil", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['Mobile'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');             
        $pdf->MultiCell($leftColWidth, 5, "Email", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['Email'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');             

        $pdf->SetFont($FONT_FAMILY, 'B', 14);
        $pdf->MultiCell($rightColWidt, 5, "", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, "Medlemsuppgifter", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->SetFont($FONT_FAMILY, '', 12);


        $pdf->MultiCell($leftColWidth, 5, "Medlemskap", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $membership = $aRow['DateOfMembershipStart'] . ' - ' . $aRow['DateOfMembershipEnd'];
        $pdf->MultiCell($rightColWidt, 5, $membership, 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');    
        
        $pdf->MultiCell($leftColWidth, 5, "Medlemsnummer", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['MembershipNo'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        
        $pdf->MultiCell($leftColWidth, 5, "Tidigare församling", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['PreviousCongregation'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');             
        
        $pdf->MultiCell($leftColWidth, 5, "Ny församling", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['NextCongregation'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');             

        $pdf->MultiCell($leftColWidth, 5, "Kommentar", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['Comment'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T', true); 

        $pdf->SetFont($FONT_FAMILY, 'B', 14);
        $pdf->MultiCell($rightColWidt, 5, "", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, "Dopuppgifter", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->SetFont($FONT_FAMILY, '', 12);
        $pdf->MultiCell($leftColWidth, 5, "Dopdatum", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['DateOfBaptism'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($leftColWidth, 5, "Församling", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['CongregationOfBaptism'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($leftColWidth, 5, "Dopförrättare", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['Baptister'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 

        
        $pdf->SetFont($FONT_FAMILY, 'B', 14);
        $pdf->MultiCell($rightColWidt, 5, "", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, "Övrigt", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->SetFont($FONT_FAMILY, '', 12);
        $pdf->MultiCell($leftColWidth, 5, "Synlig i adresskalender", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $VisibleInCalendar="Nej";
        if($aRow['VisibleInCalendar'] == 2){
            $VisibleInCalendar="Ja";
        }
        $pdf->MultiCell($rightColWidt, 5, $VisibleInCalendar, 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($leftColWidth, 5, "Brevutskick", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $Letter="Nej";
        if($aRow['Letter'] == 1){
            $Letter="Ja";
        }
        $pdf->MultiCell($rightColWidt, 5, $Letter, 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 

        $pdf->MultiCell($leftColWidth, 5, "Kön", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        switch ($aRow['Gender']){
            case 0:
                $Gender = "-";
                break;
            case 1:
                $Gender = "Man";
                break;
            default:
                $Gender = "Kvinna";
        }                            
        $pdf->MultiCell($rightColWidt, 5, $Gender, 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($leftColWidth, 5, "Kodad nyckel", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T');
        $Key1="Nej";
        if($aRow['KeyToChurch'] == 2){
            $Key1="Ja";
        }
        $pdf->MultiCell($rightColWidt, 5, $Key1, 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($leftColWidth, 5, "Vanlig nyckel", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $Key2="Nej";
        if($aRow['KeyToExp'] == 2){
            $Key2="Ja";
        }
        $pdf->MultiCell($rightColWidt, 5, $Key2, 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($leftColWidth, 5, "Kommentar (Nycklar)", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['CommentKey'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T', true); 

        $pdf->SetFont($FONT_FAMILY, 'B', 14);
        $pdf->MultiCell($rightColWidt, 5, "", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, "Godkännande", 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->SetFont($FONT_FAMILY, '', 12);
        $pdf->MultiCell($leftColWidth, 50, "", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 50, APPROVAL, 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T', true); 
        $pdf->MultiCell($leftColWidth, 5, "", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, '..............................................................................', 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($leftColWidth, 5, "", 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T'); 
        $pdf->MultiCell($rightColWidt, 5, $aRow['FirstName'] . ' ' . $aRow['LastName'], 0, 'L', 0, 1, '', '', true, 0, false, true, 10, 'T');             
    }
    if($id === 0){
        return 'Alla';
    }
    return $name;
}

function getSQL($id){
    $sql = SQL_ALL_FIELDS . " FROM People left outer join Homes on People.homeid=Homes.Id ";
    if ($id>0){
        $sql .= "where People.Id= " . $id;
    }
    else{
        $sql .= "where DateOfDeath is null and " . DECRYPTED_LASTNAME . " NOT LIKE '%" . ANONYMOUS . "' "; 
        
    }
    $sql .= " order by " . DECRYPTED_LASTNAME . " asc, address asc, DateOfBirth asc";            
    return $sql;
}
