<?php
require_once '../libs/fpdf/fpdf.php';
require_once './assets/php/query.php';

$query = new Query();

$elName = $query->fetchElecEvent($_GET['el_id'])['el_name'];

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

$elTitle = strtoupper($elName) . " VOTING RESULTS";
$arr = [];
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

$pdf->SetMargins(1, .5, 1);
$pdf->AddPage();

// set font to arial, bold, 14pt
$pdf->SetFont('Arial', 'B', 13);

// Cell(width, height, text, border, end line, [align])
$pdf->Cell(6.27, .25, $elTitle, 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, .20, '', 0, 1);

// CANDIDATES BY POSITION
function my_sort($a, $b) {
  if ($a['voteCount'] == $b['voteCount']) return 0;
  return ($a['voteCount'] > $b['voteCount']) ? -1 : 1;
}

foreach ($gcands as $gcand => $val) {
  $pdf->SetFont('Arial', 'B', 11);
  $pdf->Cell(6.27, .20, $gcand, 0, 1, 'C');

  uasort($val, "my_sort");
  array_push($arr, $val);

  foreach ($val as $c) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(6.27, .20, "{$c['v_fname']} {$c['v_lname']} ({$c['par_name']}): {$c['voteCount']}", 0, 1, 'C');
  }
  $pdf->Cell(0, .12, '', 0, 1);
}

$pdf->Cell(6.27, .20, '', 0, 1, 'C');
$pdf->Cell(6.27, .20, '', 0, 1, 'C');
$pdf->Cell(6.27, .20, '', 0, 1, 'C');

// FIRST ROW NAMES
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.89, .20, '', 0, 0, 'C'); // Change string (name)
$pdf->Cell(0.3, .20, '', 0, 0, 'C');
$pdf->Cell(1.89, .20, '', 0, 0, 'C'); // Change string (name)
$pdf->Cell(0.3, .20, '', 0, 0, 'C');
$pdf->Cell(1.89, .20, '', 0, 1, 'C'); // Change string (name)

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(1.89, .20, 'POLL CHAIRMAN', 'T', 0, 'C');
$pdf->Cell(0.3, .20, '', 0, 0, 'C');
$pdf->Cell(1.89, .20, 'POLL SECRETARY/CLERK', 'T', 0, 'C');
$pdf->Cell(0.3, .20, '', 0, 0, 'C');
$pdf->Cell(1.89, .20, 'MEMBER', 'T', 0, 'C');

// line break
$pdf->Cell(6.27, .20, '', 0, 1, 'C');
$pdf->Cell(6.27, .20, '', 0, 1, 'C');
$pdf->Cell(6.27, .20, '', 0, 1, 'C');
$pdf->Cell(6.27, .20, '', 0, 1, 'C');

// SECOND ROW NAMES
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(1.89, .20, 'JANNAH LUZ T. POLICAN', 0, 0, 'C'); // Change string (name)
$pdf->Cell(0.3, .20, '', 0, 0, 'C');
$pdf->Cell(1.89, .20, 'MICHELLE ANN T. ANTONI', 0, 0, 'C'); // Change string (name)
$pdf->Cell(0.3, .20, '', 0, 0, 'C');
$pdf->Cell(1.89, .20, 'HAROLD N. DONGALLO', 0, 1, 'C'); // Change string (name)

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(1.89, .20, 'SECRETARY', 'T', 0, 'C');
$pdf->Cell(0.3, .20, '', 0, 0, 'C');
$pdf->Cell(1.89, .20, 'CHAIRMAN', 'T', 0, 'C');
$pdf->Cell(0.3, .20, '', 0, 0, 'C');
$pdf->Cell(1.89, .20, 'VICE CHAIRMAN', 'T', 0, 'C');

$pdf->Output();

?>
<script>
  console.log(JSON.parse('<?= json_encode($arr) ?>'))
</script>