<?php
require_once '../libs/fpdf/fpdf.php';
require_once './assets/php/query.php';

$query = new Query();

//current year
$elEvent = $query->fetchElecEvent($_GET['el_id']);
$elName = $elEvent['el_name'];
$elYear = $elEvent['el_year'];
$elYearAddedOne = $elEvent['el_year'] + 1;

// START RESULTS
$elId = $query->testInput($_GET['el_id']);
$cands = $query->fetchAllCandidates($elId);
$newCands = [];

foreach ($cands as $cand) {
  $count = $query->countVotesOfCand($cand['c_id'])['vote_count'];
  $cand['voteCount'] = $count;
  array_push($newCands, $cand);
}


foreach ($newCands as $newCand) {
  $gcands[$newCand['pos_name']][] = $newCand;
}
// echo json_encode($gcands);
// END RESULTS

$elTitle = "CERTIFICATE OF PROCLAMATION FOR ".strtoupper($elName);

$arr = [];

//proclaimed this
$curDate = date('F d, Y');

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
    $this->Cell(6.5, 1, $this->PageNo(), 0, 0, 'C');
  }
}

// A4 width: 219mm
// default margin: 100mm each side
// writable horizontal: 219 - (10 * 2) = 189mm
// w: 8.27in, h: 11.69in, mx: 2, width_available: 6.27

// long width: 8.5in
// Width without margin: 6.5
$pdf = new PDF('p', 'in', array(8.5, 13));

$pdf->SetMargins(1, .4, 1);
$pdf->AddPage();

// set font to arial, bold, 14pt
$pdf->SetFont('Arial', 'B', 12);

// Cell(width, height, text, border, end line, [align])
$pdf->Cell(6.5, .6, $elTitle, 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(6.5, .20, 'Announcing the Newly Supreme Student Council Officers of Siargao Island Institute of Technology', 0, 1, 'L');
$pdf->Cell(6.5, .20, '', 0, 1, 'L');
$pdf->MultiCell(6.5, .20, "The SNSU-Del Carmen Campus Electoral Commission proudly declares the following individuals as the duly elected officers of the University Student Council of the academic year {$elYear}-{$elYearAddedOne}:", '', 'L', 0);

$pdf->Cell(6.5, .20, '', 0, 1, 'L');

// CANDIDATES BY POSITION
function my_sort($a, $b) {
  if ($a['voteCount'] == $b['voteCount']) return 0;
  return ($a['voteCount'] > $b['voteCount']) ? -1 : 1;
}

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(6.5, .20, 'Officers:', 0, 1, 'L');
$pdf->Cell(6.5, .20, '', 0, 1, 'L');

foreach ($gcands as $gcand => $val) {
  $pdf->SetFont('Arial', '', 10);

  uasort($val, "my_sort");
  $many = $val[0]['pos_cand_many'];

  $val = array_slice($val, 0, $many);

  array_push($arr, $val);

  $names = '';
  foreach ($val as $c) {
    $pdf->SetFont('Arial', '', 10);
    $names .= "{$c['v_fname']} {$c['v_lname']}, ";
  }
  // $pdf->Cell(6.5, .15, "$gcand: $names", 0, 1, 'L');
  $pdf->Cell(1.4, .15, $gcand, 0, 0, 'L');
  $pdf->Cell(.1, .15, ':', 0, 0, 'L');
  $pdf->Cell(.3, .15, '', 0, 0, 'L');
  $pdf->Cell(1, .15, $names, 0, 1, 'L');

  $pdf->Cell(0, .05, '', 0, 1);
}

// $pdf->Cell(0, .15, '', 0, 1);

// $pdf->MultiCell(6.5, .20, 'We wholeheartedly commend these exemplary individuals for their unwavering commitment to serving the SNSU-Del Carmen Campus community. Their passion, dedication, and leadership qualities make them the ideal representatives for our student body.', '', 'L', 0);
$pdf->MultiCell(6.5, .20, '', '', 'L', 0);
// $pdf->MultiCell(6.5, .20, 'As they embark on this new chapter of their leadership journey, we wish them the utmost success in their endeavors. May they continue to inspire and uplift their fellow students, fostering a vibrant and thriving academic environment.', '', 'L', 0);
// $pdf->MultiCell(6.5, .20, '', '', 'L', 0);
$pdf->MultiCell(6.5, .20, "Proclaimed this {$curDate}.", '', 'L', 0);
$pdf->MultiCell(6.5, .20, '', '', 'L', 0);
$pdf->MultiCell(6.5, .20, '', '', 'L', 0);
$pdf->MultiCell(6.5, .20, 'Prepared by:', '', 'L', 0);
$pdf->MultiCell(6.5, .20, '', '', 'L', 0);
$pdf->MultiCell(6.5, .20, '_______________________', '', 'L', 0);
$pdf->MultiCell(6.5, .20, '           SECRETARY', '', 'L', 0);

$pdf->MultiCell(6.5, .20, '', '', 'L', 0);

$pdf->MultiCell(6.5, .20, 'Noted:', '', 'L', 0);
$pdf->MultiCell(0, .20, '', '', 'L', 0);
$pdf->Cell(1.5, .20, '_______________________', 0, 0, 'L');
$pdf->Cell(1, .20, '', 0, 1, 'L');
// $pdf->Cell(1.5, .20, '_______________________', 0, 1, 'C');
$pdf->Cell(1.5, .20, '        CHAIRMAN', 0, 1, 'C');
// $pdf->Cell(2.25, .20, 'USC ADVISER', 0, 1, 'R');

$pdf->MultiCell(6.5, .20, '', '', 'L', 0);

$pdf->MultiCell(6.5, .20, 'Concurred by:', '', 'L', 0);
$pdf->MultiCell(0, .20, '', '', 'L', 0);
$pdf->Cell(1.5, .20, '_______________________', 0, 0, 'L');
$pdf->Cell(0.8, .20, '', 0, 0, 'L');
$pdf->Cell(1.5, .20, '_______________________', 0, 1, 'L');
$pdf->Cell(1.5, .20, '        SAO', 0, 0, 'C');
$pdf->Cell(2.25, .20, 'USC ADVISER', 0, 1, 'R');

$pdf->MultiCell(6.5, .20, '', '', 'L', 0);

$pdf->MultiCell(6.5, .20, 'Approved by:', '', 'L', 0);
$pdf->MultiCell(0, .20, '', '', 'L', 0);
$pdf->Cell(1.5, .20, '_______________________', 0, 1, 'L');
$pdf->Cell(1.5, .20, '        CAMPUS DIRECTOR', 0, 0, 'C');


$pdf->Output();
?>
<script>
  console.log(JSON.parse('<?= json_encode($arr) ?>'))
</script>