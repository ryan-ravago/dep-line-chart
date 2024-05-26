<?php
require_once '../libs/fpdf/fpdf.php';
require_once './assets/php/query.php';

$query = new Query();

$voters = $query->fetchVotersBasedOnElecEv($_GET['el_id']);
$elName = $query->fetchElecEvent($_GET['el_id'])['el_name'];

$elTitle = strtoupper($elName) . " VOTERS";

class PDF extends FPDF {
  function Header() {
    $this->SetFont('Arial', '', 10);
    // Image (file name, x position, y position, width [optional], height [optional])
    $this->Image('./assets/img/siit.png', .5, .4, .88);
    $this->Cell(7.27, 0.36, 'Republic of the Philippines', 0, 1, 'C');
    $this->Image('./assets/img/ssc logo.png', 6.87, .39, .9);
    // $this->Image('./assets/img/header lines.png', 1, 1.25, 7.27);

    $this->SetFont('Arial', 'B', 11);
    $this->Cell(7.27, 0, 'SIARGAO ISLAND INSTITUTE OF TECHNOLOGY', 0, 1, 'C');
    
    $this->SetFont('Arial', '', 10);
    // $this->Cell(7.27, 0.36, 'SIIT Main Campus', 0, 1, 'C');
    $this->Cell(7.27, 0.36, 'Dapa, Surigao del Norte', 0, 1, 'C');
    
    $this->Ln(.2);
  }
  
  function Footer() {
    $this->SetY(-1);
    $this->SetFont('Arial', '', 10);
    $this->Cell(7.27, 1, $this->PageNo(), 0, 0, 'C');
  }
}

// A4 width: 219mm
// default margin: 100mm each side
// writable horizontal: 219 - (10 * 2) = 189mm
// w: 8.27in, h: 11.69in, mx: 2, width_available: 7.27
$pdf = new PDF('p', 'in', 'A4');

$pdf->SetMargins(0.5, .4, 1);
$pdf->AddPage();

// Image (file name, x position, y position, width [optional], height [optional])
// $pdf->Image('./assets/img/snsu.png', 1, 1, 2);

// set font to arial, bold, 14pt
$pdf->SetFont('Arial', 'B', 13);

// Cell(width, height, text, border, end line, [align])
$pdf->Cell(7.27, .25, $elTitle, 0, 1, 'C');


if (sizeof($voters) > 0) {
  $pdf->SetFont('Arial', 'B', 11);

  $pdf->Cell(0, .15, '', 0, 1);

  $pdf->Cell(1.26, .35, 'Last name', 1, 0, 'C');
  $pdf->Cell(1.51, .35, 'First name', 1, 0, 'C');
  $pdf->Cell(1.25, .35, 'Middle name', 1, 0, 'C');
  $pdf->Cell(.8, .35, 'Password', 1, 0, 'C');
  $pdf->Cell(1, .35, 'Course', 1, 0, 'C');
  $pdf->Cell(.7, .35, 'Yr. level', 1, 0, 'C');
  $pdf->Cell(.75, .35, 'Signature', 1, 1, 'C');

  $pdf->SetFont('Arial', '', 10);

  foreach ($voters as $voter) {
    $pdf->Cell(1.26, .27, $voter['v_lname'], 1, 0, 'C');
    $pdf->Cell(1.51, .27, $voter['v_fname'], 1, 0, 'C');
    $pdf->Cell(1.25, .27, $voter['v_mname'], 1, 0, 'C');
    $pdf->Cell(.8, .27, $voter['v_pass'], 1, 0, 'C');
    $pdf->Cell(1, .27, $voter['course_name'], 1, 0, 'C');
    $pdf->Cell(.7, .27, $voter['v_yrlvl'], 1, 0, 'C');
    $pdf->Cell(.75, .27, '', 1, 1, 'C');
  }
} else {
  $pdf->SetFont('Arial', 'I', 13);
  $pdf->Cell(7.27, 1, 'No voters.', 0, 1, 'C');
}

$pdf->Output();

?>