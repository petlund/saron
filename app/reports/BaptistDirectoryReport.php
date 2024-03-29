<?php
    require_once 'config.php';
    require_once SARON_ROOT . 'app/entities/SaronUser.php';
    require_once SARON_ROOT . 'app/reports/Frontpage.php';
    require_once SARON_ROOT . 'app/entities/PeopleFilter.php';
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
    define("TITLE", "Dopregister");                
    define ("INNER", 1);
    define ("OUTER", 2);
    define ("BOOKLET_OUTER_MARGIN", 10);
    define ("BOOKLET_INNER_MARGIN", 30);
    define ("INNER_HEADER_TEXT",  FullNameOfCongregation . ' dopregister');
    define ("OUTER_HEADER_TEXT", 'Utskriftsdatum: ' . date("Y-m-d", time()));
    define ("INNER_FOOTER_TEXT", 'Intern församlingsledningen');
    //define ("HEADER_MARGIN", 10);
    //define ("FOOTER_MARGIN", 10);
    define ("HEADER_FOOTER_CELL_WIDTH", 120);
    define ("CELL_HIGHT", 5.5);
    define ("MAX_CELL_HIGHT", 5.5);
    define ("LIST_FONT_SIZE", 12);
    define ("HEADER_FOOTER_FONT_SIZE", 10);
    define ("FONT", 'times');

    $peopleFilter = new PeopleFilter($db, $saronUser);
    $sql =SQL_STAR_PEOPLE . " ";
    $sql.="from view_people as People ";
    $sql.="where " . $peopleFilter->getPeopleFilterSql(1) . " "; // 1 = bpatistregistry
    $sql.="order by ";
    $sql.=DECRYPTED_LASTNAME;
    $sql.=", "; 
    $sql.=DECRYPTED_FIRSTNAME;
    $sql.=","; 
    $sql.="DateOfBirth"; 

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $name = FullNameOfCongregation;
    // set document information
    $pdf->SetCreator(UrlOfRegistry);
    $pdf->SetAuthor(FullNameOfCongregation);
    $pdf->SetTitle('Dopregister');
    $pdf->SetSubject('');
    $pdf->SetKeywords('Dopregister');

    // set default header data
    //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 005', PDF_HEADER_STRING);

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

    $listSpaceHight=$pdf->getPageHeight() - PDF_MARGIN_HEADER - PDF_MARGIN_FOOTER - 15;


    $headerPos_Y=PDF_MARGIN_HEADER;
    $footerPos_Y=$pdf->getPageHeight() - CELL_HIGHT;


    $pageWidth=($pdf->getPageWidth()-BOOKLET_INNER_MARGIN-BOOKLET_OUTER_MARGIN);

    addFrontPage($pdf, $pageWidth, TITLE);

    $cellWidth=$pageWidth/6;
    
    $NoOfBaptistDataRows=5;

    $line_count=0;
    $firstPage=true;

    $listResult = $db->sqlQuery($sql);
    if(!$listResult){
        exit();
    }
    foreach($listResult as $aRow){

        $line_count+=$NoOfBaptistDataRows;
        $restHight=$listSpaceHight-CELL_HIGHT*(1+$line_count);
        if($restHight<0 or $firstPage){
            $firstPage=false;
            $pdf->AddPage();
            $line_count=$NoOfBaptistDataRows;
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

        // Person
        $pdf->MultiCell($cellWidth * 2, CELL_HIGHT, $aRow['LastName'], 0, 'L', 1, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 2, CELL_HIGHT, $aRow['FirstName'], 0, 'L', 1, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 2, CELL_HIGHT, $aRow['DateOfBirth'], 0, 'R', 1, 1, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);


        // Baptist
        $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, "Dopdatum:", 0, 'L', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);    
        $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, $aRow['DateOfBaptism'], 0, 'L', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, "Medlemskap:", 0, 'L', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);    
        $pdf->MultiCell($cellWidth * 2, CELL_HIGHT, $aRow['DateOfMembershipStart'] . ' - ' . $aRow['DateOfMembershipEnd'], 0, 'L', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        if($aRow['MembershipNo']>0){
            $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, "Nr: " . $aRow['MembershipNo'], 0, 'R', 0, 1, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        }
        else{
            $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, "", 0, 'R', 0, 1, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        }

        $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, "Dopförrättare:", 0, 'L', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 5, CELL_HIGHT, $aRow['Baptister'], 0, 'L', 0, 1, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, "Dopförsamling:", 0, 'L', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 5, CELL_HIGHT, $aRow['CongregationOfBaptism'], 0, 'L', 0, 1, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 1, CELL_HIGHT, "Kommentar:", 0, 'L', 0, 0, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
        $pdf->MultiCell($cellWidth * 5, CELL_HIGHT, $aRow['Comment'], 0, 'L', 0, 1, '', '', true, 0, false, true, MAX_CELL_HIGHT, 'T', true);
    }
    $pdf->Output('Dopregister ' . date("Y-m-d", time()) . '.pdf');

    

function getAllignement(TCPDF $pdf, $innerOuter){
    if($pdf->getPage() % 2 == 0){
        if($innerOuter==INNER){
            return 'R' ;
        }
        else{
            return 'L';
        }
    }
    else{
        if($innerOuter==INNER){
            return 'L';
        }
        else{
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




 
 