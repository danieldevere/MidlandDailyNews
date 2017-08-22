<?php
require('fpdf/fpdf.php');
if(isset($_POST['articles'])) {
    $articles = json_decode($_POST['articles'], true);    
}
if(isset($_POST['obituaries'])) {
    $obituaries = json_decode($_POST['obituaries'], true);
}
if(isset($_POST['weddings'])) {
    $weddings = json_decode($_POST['weddings'], true);
}
$header = array(0);
$data = array();
class PDF extends FPDF
{
    function WeddingIndexTable($header, $data) {
        // Table Title
        $w = array(190);
        $this->SetFont('', 'B', 20);
        $this->Cell($w[0], 12, "Weddings/Anniversaries", 1, 0, 'C');
        $this->Ln();
        // Column widths
        $w = array(31, 31, 41, 39, 32, 16);
        // Header
        $this->SetFont('', '', 16);
        $this->SetFillColor(211,211,211);
        for($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i], 9, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        // Data
        $this->SetFont('', '', 10);
        $this->SetFillColor(232,232,232);
        $height = 5;
        $fill = false;
        foreach($data as $row) {
            $this->Cell($w[0], $height, $row['lastname'], 1, 0, 'L', $fill);
            $this->Cell($w[1], $height, $row['firstname'], 1, 0, 'L', $fill);
            $this->Cell($w[2], $height, $row['announcement'], 1, 0, 'L', $fill);
            $this->Cell($w[3], $height, $row['weddingdate'], 1, 0, 'L', $fill);
            $this->Cell($w[4], $height, $row['articledate'], 1, 0, 'L', $fill);
            $this->Cell($w[5], $height, $row['page'], 1, 0, 'L', $fill);   
            $this->Ln();
            $fill = !$fill;         
        }
    }

    function ObituaryIndexTable($header, $data) {
        // Table Title
        $w = array(190);
        $this->SetFont('', 'B', 20);
        $this->Cell($w[0], 12, "Obituaries", 1, 0, 'C');
        $this->Ln();
        // Column widths
        $w = array(35, 35, 30, 33, 40, 17);
        // Header
        $this->SetFont('', '', 16);
        $this->SetFillColor(211,211,211);
        for($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i], 9, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        // Data
        $this->SetFont('', '', 10);
        $this->SetFillColor(232,232,232);
        $height = 5;
        $fill = false;
        foreach($data as $row) {
            $this->Cell($w[0], $height, $row['lastname'], 1, 0, 'L', $fill);
            $this->Cell($w[1], $height, $row['firstname'], 1, 0, 'L', $fill);
            $this->Cell($w[2], $height, $row['birthdate'], 1, 0, 'L', $fill);
            $this->Cell($w[3], $height, $row['deathdate'], 1, 0, 'L', $fill);
            $this->Cell($w[4], $height, $row['obitdate'], 1, 0, 'L', $fill);
            $this->Cell($w[5], $height, $row['page'], 1, 0, 'L', $fill);   
            $this->Ln();
            $fill = !$fill;         
        }
    }

    // Better table
    function NewsIndexTable($header, $data)
    {
        // Table Title
        $w = array(190);
        // Title font style
        $this->SetFont('','B', 20);
        // Title
        $this->Cell($w[0], 12, "News Articles", 1, 0, 'C');
        $this->Ln();
        // Column widths
        $w = array(50, 105, 20, 15);
        // Set header font style
        $this->SetFont('','', 16);
        // Header cell styles
        $this->SetFillColor(211,211,211);
        // Header
        for($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i],9,$header[$i],1,0,'C', true);
        }
        $this->Ln();
        // Set table font style
        $this->SetFont('', '', 10);
        // Cell styles
        $this->SetFillColor(232,232,232);
        $height = 5;
        // Data
        $fill = false;
        foreach($data as $row)
        {
            $headline = $row['headline'];
            if(strlen($headline) > 57) {
                $headline = substr($headline, 0, 56) . '...';
            }
            $this->Cell($w[0],$height,$row['subject'],'1', 0, 'L', $fill);
            $this->Cell($w[1],$height,$headline,'1', 0, 'L', $fill);
            $this->Cell($w[2],$height,$row['date'],'1', 0, 'C', $fill);
            $this->Cell($w[3],$height,$row['page'],'1', 0, 'C', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
    }
}

class PDF_JavaScript extends FPDF {

    protected $javascript;
    protected $n_js;

    function IncludeJS($script, $isUTF8=false) {
        if(!$isUTF8)
            $script=utf8_encode($script);
        $this->javascript=$script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_put('<<');
        $this->_put('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_put('>>');
        $this->_put('endobj');
        $this->_newobj();
        $this->_put('<<');
        $this->_put('/S /JavaScript');
        $this->_put('/JS '.$this->_textstring($this->javascript));
        $this->_put('>>');
        $this->_put('endobj');
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_put('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }
}
class PDF_AutoPrint extends PDF_JavaScript
{
    function AutoPrint($printer='')
    {
        // Open the print dialog
        if($printer)
        {
            $printer = str_replace('\\', '\\\\', $printer);
            $script = "var pp = getPrintParams();";
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
            $script .= "pp.printerName = '$printer'";
            $script .= "print(pp);";
        }
        else
            $script = 'print(true);';
        $this->IncludeJS($script);
    }
    function WeddingIndexTable($header, $data) {
        // Table Title
        $w = array(190);
        $this->SetFont('', 'B', 20);
        $this->Cell($w[0], 12, "Weddings/Anniversaries", 1, 0, 'C');
        $this->Ln();
        // Column widths
        $w = array(31, 31, 41, 39, 32, 16);
        // Header
        $this->SetFont('', '', 16);
        $this->SetFillColor(211,211,211);
        for($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i], 9, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        // Data
        $this->SetFont('', '', 10);
        $this->SetFillColor(232,232,232);
        $height = 5;
        $fill = false;
        foreach($data as $row) {
            $this->Cell($w[0], $height, $row['lastname'], 1, 0, 'L', $fill);
            $this->Cell($w[1], $height, $row['firstname'], 1, 0, 'L', $fill);
            $this->Cell($w[2], $height, $row['announcement'], 1, 0, 'L', $fill);
            $this->Cell($w[3], $height, $row['weddingdate'], 1, 0, 'L', $fill);
            $this->Cell($w[4], $height, $row['articledate'], 1, 0, 'L', $fill);
            $this->Cell($w[5], $height, $row['page'], 1, 0, 'L', $fill);   
            $this->Ln();
            $fill = !$fill;         
        }
    }

    function ObituaryIndexTable($header, $data) {
        // Table Title
        $w = array(190);
        $this->SetFont('', 'B', 20);
        $this->Cell($w[0], 12, "Obituaries", 1, 0, 'C');
        $this->Ln();
        // Column widths
        $w = array(35, 35, 30, 33, 40, 17);
        // Header
        $this->SetFont('', '', 16);
        $this->SetFillColor(211,211,211);
        for($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i], 9, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();
        // Data
        $this->SetFont('', '', 10);
        $this->SetFillColor(232,232,232);
        $height = 5;
        $fill = false;
        foreach($data as $row) {
            $this->Cell($w[0], $height, $row['lastname'], 1, 0, 'L', $fill);
            $this->Cell($w[1], $height, $row['firstname'], 1, 0, 'L', $fill);
            $this->Cell($w[2], $height, $row['birthdate'], 1, 0, 'L', $fill);
            $this->Cell($w[3], $height, $row['deathdate'], 1, 0, 'L', $fill);
            $this->Cell($w[4], $height, $row['obitdate'], 1, 0, 'L', $fill);
            $this->Cell($w[5], $height, $row['page'], 1, 0, 'L', $fill);   
            $this->Ln();
            $fill = !$fill;         
        }
    }

    // Better table
    function NewsIndexTable($header, $data)
    {
        // Table Title
        $w = array(190);
        // Title font style
        $this->SetFont('','B', 20);
        // Title
        $this->Cell($w[0], 12, "News Articles", 1, 0, 'C');
        $this->Ln();
        // Column widths
        $w = array(50, 105, 20, 15);
        // Set header font style
        $this->SetFont('','', 16);
        // Header cell styles
        $this->SetFillColor(211,211,211);
        // Header
        for($i=0;$i<count($header);$i++) {
            $this->Cell($w[$i],9,$header[$i],1,0,'C', true);
        }
        $this->Ln();
        // Set table font style
        $this->SetFont('', '', 10);
        // Cell styles
        $this->SetFillColor(232,232,232);
        $height = 5;
        // Data
        $fill = false;
        foreach($data as $row)
        {
            $headline = $row['headline'];
            if(strlen($headline) > 57) {
                $headline = substr($headline, 0, 56) . '...';
            }
            $this->Cell($w[0],$height,$row['subject'],'1', 0, 'L', $fill);
            $this->Cell($w[1],$height,$headline,'1', 0, 'L', $fill);
            $this->Cell($w[2],$height,$row['date'],'1', 0, 'C', $fill);
            $this->Cell($w[3],$height,$row['page'],'1', 0, 'C', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
    }
}



if(isset($header) && isset($data)) {
    $pdf = new PDF_AutoPrint('P', 'mm', 'letter');
    $pdf->SetLeftMargin(17.0);
    $pdf->SetFont('Arial','',14);
    $pdf->AddPage();
    if(isset($articles) && count($articles) > 0) {
        $header = array('Subject', 'Headline', 'Date', 'Page');        
     //   $pdf->AddPage();
        $pdf->NewsIndexTable($header, $articles);        
    }
    if(isset($obituaries) && count($obituaries) > 0) {
        if(!empty($articles)) {
            $pdf->Ln();
        }
        $header = array('Last Name', 'First Name', 'Birth Date', 'Death Date', 'Obituary Date', 'Page');        
     //   $pdf->AddPage();
        $pdf->ObituaryIndexTable($header, $obituaries);
    }
    if(isset($weddings) && count($weddings) > 0) {
        if(!empty($articles) || !empty($obituaries)) {
            $pdf->Ln();
        }
        $header = array('Last Name', 'First Name', 'Announcement', 'Wedding Date', 'Article Date', 'Page');
     //   $pdf->AddPage();
        $pdf->WeddingIndexTable($header, $weddings);
    }
    $pdf->AutoPrint();
    $pdf->Output();
}
?>