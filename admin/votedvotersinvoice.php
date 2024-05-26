<?php
require_once '../libs/fpdf/fpdf.php';
require_once './assets/php/query.php';

$query = new Query();

$elName = $query->fetchElecEvent($_GET['el_id'])['el_name'];

// START VOTED VOTERS
$elId = $query->testInput($_GET['el_id']);
$voters = $query->fetchVotedVoters($elId);
$conVoters = [];

foreach ($voters as $voter) {
  $voter['vo_time'] = date('h:ia', strtotime($voter['vo_time']));
  array_push($conVoters, $voter);
}

$uniqueVotedVoters = array();
foreach ($conVoters as $voter) {
  // If uniqueVotedVoters length is 0
  if (sizeof($uniqueVotedVoters) == 0) {
    array_push($uniqueVotedVoters, $voter);
  } else {
    // If uniqueVotedVoters length is greater than 0
    $count = 0; 
    foreach ($uniqueVotedVoters as $uVoter) {
      if ($uVoter['vo_v_id'] == $voter['vo_v_id']) {
        $count++;
      }
    }

    if ($count == 0) {
      array_push($uniqueVotedVoters, $voter);
    }
  }
}
// END VOTED VOTERS

$elTitle = strtoupper($elName) . " VOTED VOTERS";

class PDF extends FPDF {
  function Header() {
    $this->SetFont('Arial', '', 10);
    // Image (file name, x position, y position, width [optional], height [optional])
    $this->Image('./assets/img/siit.png', 1.03, .4, .88);
    $this->Cell(6.5, 0.36, 'Republic of the Philippines', 0, 1, 'C');
    $this->Image('./assets/img/ssc logo.png', 6.5, .39, .9);
    // $this->Image('./assets/img/header lines.png', 1, 1.25, 6.5);

    $this->SetFont('Arial', 'B', 11);
    $this->Cell(6.5, 0, 'SIARGAO ISLAND INSTITUTE OF TECHNOLOGY', 0, 1, 'C');
    
    $this->SetFont('Arial', '', 10);
    // $this->Cell(6.5, 0.36, 'SIIT Main Campus', 0, 1, 'C');
    $this->Cell(6.5, 0.36, 'Dapa, Surigao del Norte', 0, 1, 'C');
    
    $this->Ln(.2);
  }
  
  function Footer() {
    $this->SetY(-1);
    $this->SetFont('Arial', '', 10);
    $this->Cell(6.27, 1, $this->PageNo(), 0, 0, 'C');
  }
}

// A4 width: 219mm
// default margin: 100mm each side
// writable horizontal: 219 - (10 * 2) = 189mm
// w: 8.27in, h: 11.69in, mx: 2, width_available: 6.27
$pdf = new PDF('p', 'in', 'A4');

$pdf->SetMargins(1, .4, 1);
$pdf->AddPage();

// set font to arial, bold, 14pt
$pdf->SetFont('Arial', 'B', 13);

// Cell(width, height, text, border, end line, [align])
$pdf->Cell(6.27, .25, $elTitle, 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, .40, '', 0, 1);

$pdf->SetFont('Arial', 'B', 11);

// START TABLE
$pdf->Cell(0, .15, '', 0, 1);

$pdf->Cell(2.27, .35, 'Name', 1, 0, 'C');
$pdf->Cell(1, .35, 'Course', 1, 0, 'C');
$pdf->Cell(.45, .35, 'Year', 1, 0, 'C');
$pdf->Cell(1.9, .35, 'Date', 1, 0, 'C');
$pdf->Cell(.65, .35, 'Time', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);

foreach ($uniqueVotedVoters as $voter) {
  $pdf->Cell(2.27, .27, "{$voter['v_lname']}, {$voter['v_fname']} {$voter['v_mname']}", 1, 0, 'C');
  $pdf->Cell(1, .27, $voter['course_name'], 1, 0, 'C');
  $pdf->Cell(.45, .27, $voter['v_yrlvl'], 1, 0, 'C');
  $pdf->Cell(1.9, .27, "{$voter['vo_month']} {$voter['vo_date']} {$voter['vo_year']} ({$voter['vo_day']})", 1, 0, 'C');
  $pdf->Cell(.65, .27, $voter['vo_time'], 1, 1, 'C');
}
// END TABLE

$pdf->Output();
// $pdf->Output('D', 'voting_results.pdf');

?>