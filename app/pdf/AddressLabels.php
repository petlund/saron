<?php

require_once "../access/wp-authenticate.php";
require_once '../../config/config.php';
require_once '../database/queries.php';
require_once '../database/db.php';
require_once TCPDF_PATH . '/tcpdf.php';

    $requireEditorRole = false;
    $user = wp_get_current_user();    

    if(!isPermitted($user, $requireEditorRole)){
        echo notPermittedMessage();
    }
    else{
        $type = (String)filter_input(INPUT_GET, "type", FILTER_SANITIZE_STRING);
        
        switch ($type){
            case "6x3":
                define("MARGIN_HEADER", 0);
                define("MARGIN_FOOTER", 0);
                define("MARGIN_LEFT", 0);
                define("MARGIN_RIGHT", 0);
                define("MARGIN_TOP", 0);
                define("MARGIN_BOTTOM", 0);
                define("LABEL_COLUMNS", 3);
                define("LABEL_ROWS", 6);
                define("CELL_PADDING", 3);
                define("CELL_MARGIN", 2);
                break;
            case "9x3":
                define("MARGIN_HEADER", 0);
                define("MARGIN_FOOTER", 0);
                define("MARGIN_LEFT", 0);
                define("MARGIN_RIGHT", 0);
                define("MARGIN_TOP", 0);
                define("MARGIN_BOTTOM", 0);
                define("LABEL_COLUMNS", 3);
                define("LABEL_ROWS", 9);
                define("CELL_PADDING", 3);
                define("CELL_MARGIN", 2);
                break;
            default:
                define("MARGIN_HEADER", 0);
                define("MARGIN_FOOTER", 0);
                define("MARGIN_LEFT", 5);
                define("MARGIN_RIGHT", 5);
                define("MARGIN_TOP", 5);
                define("MARGIN_BOTTOM", 5);
                define("LABEL_COLUMNS", 3);
                define("LABEL_ROWS", 6);
                define("CELL_PADDING", 3);
                define("CELL_MARGIN", 1);
                $type='Default';
        }
        
        header_remove(); 

        define ("LIST_FONT_SIZE", 12);
        define ("FONT", 'times');

        $sql =SQL_STAR_HOMES;
        $sql.=", ";
        $sql.="(SELECT GROUP_CONCAT(" . DECRYPTED_FIRSTNAME . ", ' ', " . DECRYPTED_LASTNAME . " SEPARATOR '\n') FROM People as r where Homes.Id = r.HomeId AND DateOfMembershipStart is not null AND DateOfMembershipEnd is null and DateOfDeath is null and " . DECRYPTED_LASTNAME . " NOT LIKE '%" . ANONYMOUS . "' order by DateOfBirth) as Residents ";
        $sql.="from Homes ";
        $sql.="where "; 
        $sql.="letter=1 ";
        $sql.="order by ";
        $sql.=DECRYPTED_FAMILYNAME;

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(UrlOfRegistry);
        $pdf->SetAuthor(FullNameOfCongregation);
        $pdf->SetTitle('Adressetiketter - ' . $type);
        $pdf->SetSubject('');
        $pdf->SetKeywords('Adressetiketter');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(MARGIN_LEFT, MARGIN_TOP, MARGIN_RIGHT);
        $pdf->SetHeaderMargin(MARGIN_HEADER); //5
        $pdf->SetFooterMargin(MARGIN_FOOTER); //10

        $pdf->SetAutoPageBreak(FALSE, MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetDisplayMode($zoom='fullpage', $layout='TwoColumnRight', $mode='UseNone');

        $pdf->SetFont(FONT, '', LIST_FONT_SIZE);
        $pdf->setCellPaddings(CELL_PADDING, CELL_PADDING, CELL_PADDING, CELL_PADDING);
        $pdf->setCellMargins(CELL_MARGIN, CELL_MARGIN, CELL_MARGIN, CELL_MARGIN);
        $pdf->SetFillColor(255, 255, 255);

        $cellHeight=($pdf->getPageHeight() - MARGIN_HEADER - MARGIN_FOOTER) / LABEL_ROWS - CELL_MARGIN * 2;
        $cellWidth=($pdf->getPageWidth() - MARGIN_LEFT - MARGIN_RIGHT) / LABEL_COLUMNS - CELL_MARGIN * 2;


        $db = new db();
        $listResult = $db->sqlQuery($sql);
        if(!$listResult){
            exit();
        }

        $col=0;
        $row=0;
        $newRow=0;
        $firstPage=true;

        foreach($listResult as $aRow){
            if($row >=LABEL_ROWS or $firstPage){
                $row=0;
                $firstPage=false;
                $pdf->addPage();
            }
            if(strlen($aRow['Residents'])>0){
                $label = $aRow['Residents'] . "\n";
            }
            else{
                $label="<<MEDLEMMAR SAKNAS>>\n(" . $aRow['FamilyName'] . ")\n";
            }
            if(strlen($aRow['Co']) > 0){
                $label.= "Co " . $aRow['Co'] . "\n";
            }
            $label.= $aRow['Address'] . "\n"; 
            $label.= $aRow['Zip'] . " " . $aRow['City'];

            if(++$col <LABEL_COLUMNS){
                $newRow=0;
            }
            else {
                $col=0;
                $row++;
                $newRow=1;
            }           
            $pdf->MultiCell($cellWidth, $cellHeight, $label, 0, 'L', 1, $newRow, '', '', true, 0, false, true, $cellHeight, 'T', true);
        }
        $pdf->Output('Adressetiketter typ: ' . $type . " " . date("Y-m-d", time()) . '.pdf');
    }



 
 