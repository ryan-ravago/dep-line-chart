<?php
session_start();
require_once './admin/assets/php/query.php';
$query = new Query();

$studId = $_SESSION['eboto_student'];
$voter = $query->fetchVoter($studId);
$elec = $query->fetchElecEvent($voter['el_id']);

if (!isset($_SESSION["eboto_student"])) {
  header('location: index.php?message=loginError');
  exit();
} else {
  $onGoingElec = $query->checkOnGoingElec();

  // if there's an election going-on
  if (sizeof($onGoingElec) > 0) { 
    $elec = $onGoingElec[0];
    // if voter has not voted yet, redirect to vote.php 
    if ($elec['el_id'] == $voter['v_el_id']) {
      if ($query->fetchVoter($_SESSION["eboto_student"])['v_voted'] == 0) { 
        header('location: vote.php');
      }
    } 
  }


}

$fname = $voter['v_fname'];
$mname = $voter['v_mname'];
$lname = $voter['v_lname'];
$mnameInitial = $voter['v_mname'] ? "{$voter['v_mname']}." : "";
$elId = $voter['el_id'];

$parties = $query->fetchPartiesOfElecEvent($elId);
// $partyCands = $query->fetchCands($elId, $partyId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./libs/bootstrap.min.css">
  <link rel="stylesheet" href="./libs/icons-1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="./libs/Datatables/DataTables-1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="./libs/Datatables/FixedHeader-3.4.0/css/fixedHeader.bootstrap5.min.css">
  <link rel="stylesheet" href="./admin/assets/css/style.css">
  <link rel="stylesheet" href="./libs/summernote/summernote-lite.css">
  <title>
    <?php
      if (basename($_SERVER['PHP_SELF']) == 'dashboard.php') {
        echo 'Dashboard';
      } else if (basename($_SERVER['PHP_SELF']) == 'candidates.php') {
        echo 'Admin | Candidates';
      } else if (basename($_SERVER['PHP_SELF']) == 'positions.php') {
        echo 'Admin | Positions';
      }
    ?>
  </title>
  <style>
    <?php require_once "./admin/assets/css/style.css";?>
  </style>
</head>

<body>
  <header>
    <!-- Start Navbar -->
    <nav class="navbar navbar-dark navbar-expand-lg bg-success fixed-top">
      <div class="container-fluid">
        <button type="button" class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#offcanvas">
          <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand mx-auto" href="#"><?= $voter['el_name']?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link text-white" href="#">
                <i class="bi bi-person-circle"></i>
                <?= $fname?>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> 
                Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->

    <!-- Start Offcanvas -->
    <div class="offcanvas offcanvas-start sidebar-nav bg-dark text-white" tabindex="-1" id="offcanvas">
      <div class="offcanvas-header" style="margin-top:-3px;" >
        <a href="#" class="text-white" style="text-decoration: none;">
          <h5 class="offcanvas-title fw-bold" id="offcanvasExampleLabel">
            <i class="bi bi-speedometer2"></i> &nbsp;Dashboard
          </h5>
        </a>
        <a href="#" class="burger text-light" data-bs-dismiss="offcanvas"><i class="bi bi-list"></i></a>
      </div>
      <hr style="margin-top:-3px">
      <div class="offcanvas-body p-0">
        <nav class="navbar-dark">
          <ul class="navbar-nav">
            <li>
              <a href="campaign.php" class="nav-link text-white px-3 py-3 sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'campaign.php' ? 'bg-success' : ''?>">
                <i class="bi bi-flag"></i> &nbsp;Campaign
              </a>
            </li>
            <li>
              <a href="voted.php" class="nav-link text-white px-3 py-3 sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'voted.php' ? 'bg-success' : ''?>">
                <i class="bi bi-diagram-3"></i> &nbsp;Voted
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <!-- End Offcanvas -->

  </header>