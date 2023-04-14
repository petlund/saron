<?php

    require_once 'config.php';
    require_once SARON_ROOT . 'app/database/queries.php';
    require_once SARON_ROOT . 'app/reports/Frontpage.php';
    require_once SARON_ROOT . 'app/database/db.php';
    require_once SARON_ROOT . 'app/entities/SaronUser.php';
    require_once SARON_ROOT . 'app/entities/MemberState.php';
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
    define("MAX_NO_OF_HORISONTAL_CELLS", 7);
    define("TITLE", "Adresskalender");
    
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
    define ("CELL_HIGHT", 5.5);
    define ("MAX_CELL_HIGHT", 5.5);
    define ("LIST_FONT_SIZE", 12);
    define ("HEADER_FOOTER_FONT_SIZE", 10);
    define ("FONT", 'times');

    // Enable sorting familys width different familyname on mulitiple places
    $tableQuery ="(Select distinct HomeId, " . DECRYPTED_LASTNAME . " as SortName from view_people WHERE MemberStateId = " . PEOPLE_STATE_MEMBERSHIP . ") as LastNameList ";

    $sql =SELECT_ALL_FIELDS_FROM_VIEW_PEOPLE  . ", LastNameList.SortName, ";
    $sql.="(select count(*) from view_people as pp where pp.HomeId=People.HomeId and MemberStateId = " . PEOPLE_STATE_MEMBERSHIP . ") as fam_member_count ";
    $sql.="from "; 
    $sql.=$tableQuery;
    $sql.="inner join view_people as People on People.HomeId=LastNameList.HomeId "; 

    $sql.="WHERE MemberStateId = " . PEOPLE_STATE_MEMBERSHIP . " and VisibleInCalendar=2 "; //Memberstatelogic
    $sql.="order by LastNameList.SortName, " . DECRYPTED_ADDRESS . ", HomeId, People.DateOfBirth"; 

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(UrlOfRegistry);
    $pdf->SetAuthor(FullNameOfCongregation);
    $pdf->SetTitle(TITLE);
    $pdf->SetSubject('');
    $pdf->SetKeywords(TITLE);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER); //5
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER); //10
    $pdf->setFooterData(array(255,255,255), array(255,255,255));
    // set auto page breaks
    $pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)

    // ---------------------------------------------------------
    // set display mode
    $pdf->SetDisplayMode($zoom='fullpage', $layout='TwoColumnRight', $mode='UseNone');

    // set pdf viewer preferences
    //$pdf->setViewerPreferences(array('Duplex' => 'DuplexFlipLongEdge'));

    // set booklet mode
    $pdf->SetBooklet(true, BOOKLET_OUTER_MARGIN, BOOKLET_INNER_MARGIN);
    // set font
    $pdf->SetFont(FONT, '', LIST_FONT_SIZE);

    // set cell padding
    $pdf->setCellPaddings(1, 0, 0, 0);

    // set cell margins
    $pdf->setCellMargins(0, 0, 0, 0);

    // set color for background
    $pdf->SetFillColor(220, 220, 220);

    $pageWidth=($pdf->getPageWidth()-BOOKLET_INNER_MARGIN-BOOKLET_OUTER_MARGIN);

    addFrontPage($pdf, $pageWidth, TITLE);
            
    $cellWidth= $pageWidth/MAX_NO_OF_HORISONTAL_CELLS;

    $listSpaceHight=$pdf->getPageHeight() - PDF_MARGIN_HEADER - PDF_MARGIN_FOOTER-15;

    $prevFamId=-1;
    $prevGroupName="";
    $GroupName="";
    $headerPos_Y=PDF_MARGIN_HEADER;
    $footerPos_Y=$pdf->getPageHeight() - CELL_HIGHT;

    $line_count=0;
    $firstFamily=true;

    $listResult = $db->sqlQuery($sql);
    if(!$listResult){
        exit();
    }

    foreach($listResult as $aRow){
        if($aRow['SortName']!=$prevGroupName or $aRow['HomeId']!=$prevFamId){
            $GroupName=$aRow['SortName'];
            $line_count+=$aRow['fam_member_count']+1;
            $restHight=$listSpaceHight-CELL_HIGHT*($aRow['fam_member_count']+1+$line_count);
            if($restHight<0 or $firstFamily){
                $firstFamily=false;
                $pdf->AddPage();
                $line_count=$aRow['fam_member_count']+1;

                $pdf->SetFont(FONT, '', HEADER_FOOTER_FONT_SIZE);
                $pdf->MultiCell(HEADER_FOOTER_CELL_WIDTH, CELL_HIGHT, INNER_HEADER_TEXT, 0, getAllignement($pdf, INNER), 0, 1, getPosition($pdf, INNER), $headerPos_Y, true, 0, false, true, CELL_HIGHT, 'T');        
                $pdf->MultiCell(HEADER_FOOTER_CELL_WIDTH, CELL_HIGHT, INNER_FOOTER_TEXT, 0, getAllignement($pdf, INNER), 0, 1, getPosition($pdf, INNER), $footerPos_Y, true, 0, false, true, CELL_HIGHT, 'T');        
                $pdf->MultiCell(HEADER_FOOTER_CELL_WIDTH, CELL_HIGHT, $pdf->getPage(), 0, getAllignement($pdf, OUTER), 0, 1, getPosition($pdf, OUTER), $footerPos_Y, true, 0, false, true, CELL_HIGHT, 'T');        
                $pdf->MultiCell(HEADER_FOOTER_CELL_WIDTH, CELL_HIGHT, OUTER_HEADER_TEXT, 0, getAllignement($pdf, OUTER), 0, 1, getPosition($pdf, OUTER), $headerPos_Y, true, 0, false, true, CELL_HIGHT, 'T');        
                $pdf->SetFont(FONT, '', LIST_FONT_SIZE);
            }
            else{
                $line_count+=1;
                $pdf->MultiCell($cellWidth*5, CELL_HIGHT, '', 0, 'L', 0, 1, '', '', true, 1, false, true, MAX_CELL_HIGHT, 'T');        
            }

            $pdf->MultiCell($cellWidth * 2, CELL_HIGHT, $aRow['SortName'], 0, 'L', 1, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
            $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, trimPhoneNumber($aRow['Phone']), 0, 'R', 1, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
            $pdf->MultiCell($cellWidth * 2, CELL_HIGHT, $aRow['Address'], 0, 'L', 1, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
            $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, $aRow['Zip'], 0, 'R', 1, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
            $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, $aRow['City'], 0, 'R', 1, 1, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);

            //$prevFamId=$aRow['HomeId'];
            $prevFamId=$aRow['HomeId'];
            $prevGroupName=$GroupName;
        }

        // Familymembers
        if($aRow['LastName']==$GroupName){
            $pdf->MultiCell($cellWidth * 2, CELL_HIGHT, $aRow['FirstName'], 0, 'L', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        }
        else{
            $pdf->MultiCell($cellWidth * 2, CELL_HIGHT, $aRow['FirstName'] . ' ' . $aRow['LastName'], 0, 'L', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        }
        $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, trimPhoneNumber($aRow['Mobile']), 0, 'R', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 3, CELL_HIGHT, $aRow['Email'], 0, 'R', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, $aRow['DateOfBirth'], 0, 'R', 0, 1, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
    }

    $pdf->Output('Adresskalender ' . date("Y-m-d", time()) . '.pdf');


function getAllignement(TCPDF $pdf, $innerOuter){
    if($pdf->getPage() % 2 == 0){
        if ($innerOuter == INNER) {
            return 'R';
        } 
        else {
            return 'L';
        }
    }
    else{
        if ($innerOuter == INNER) {
            return 'L';
        } 
        else {
            return 'R';
        }
    }
}

function getPosition(TCPDF $pdf, $innerOuter){
    if($pdf->getPage() % 2 == 0){
        if($innerOuter==INNER){
            return $pdf->getPageWidth()-HEADER_FOOTER_CELL_WIDTH - BOOKLET_INNER_MARGIN;
        }
        else{
            return BOOKLET_OUTER_MARGIN;
        }
    }
    else{
        if($innerOuter==INNER){
            return BOOKLET_INNER_MARGIN;
        }
        else{
            return $pdf->getPageWidth()-HEADER_FOOTER_CELL_WIDTH - BOOKLET_OUTER_MARGIN;
        }
    }
}


function trimPhoneNumber($number){
    if(strlen($number)==0){
        return '';
    }

    $trimmedNumber=$number;
    while(strpos($trimmedNumber, ' ')>0){
        $trimmedNumber=substr($trimmedNumber,0, strpos($trimmedNumber, ' ')) . substr($trimmedNumber,strpos($trimmedNumber, ' ')-strlen($trimmedNumber)+1);
    }
    if(strlen($trimmedNumber)>10){
        $trimmedNumber=substr($trimmedNumber,0, strlen($trimmedNumber)-4) .' ' . substr($trimmedNumber,-4);        
    }
    return $trimmedNumber;
}
    


 
 
