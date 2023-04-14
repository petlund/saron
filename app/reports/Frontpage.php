<?php

function addFrontPage($pdf, $pageWidth, $title){
    define ("FONT", 'times');
    $pdf->AddPage(); // front page
    
    $pdf->SetFont(FONT, 'B', 24);
    $pdf->MultiCell($pageWidth, 30, "", 0, 'C', 0, 1, '', '', true, 0, false, true, 50, 'T', true);
    $pdf->MultiCell($pageWidth, 30, $title, 0, 'C', 0, 1, '', '', true, 0, false, true, 50, 'T', true);
    $pdf->SetFont(FONT, 'B', 16);
    $pdf->MultiCell($pageWidth, 10, FullNameOfCongregation, 0, 'C', 0, 1, '', '', true, 0, false, true, 10, 'T', true);
    $pdf->MultiCell($pageWidth, 10, Norrköping, 0, 'C', 0, 1, '', '', true, 0, false, true, 10, 'T', true);
    $pdf->SetFont(FONT, 'I', 10);
    $pdf->MultiCell($pageWidth, 10, 'Utskriftsdatum: ' . date("Y-m-d", time()), 0, 'C', 0, 1, '', '', true, 0, false, true, 10, 'T', true);

}
