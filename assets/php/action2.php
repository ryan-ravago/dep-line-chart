<?php
require './query2.php';

$query = new Query();

function fetchTransactCountsByDateRangeOfADep($startDateParam, $endDateParam, $department) {
  $query = new Query();

  $startDate = $query->testInput($startDateParam);
  $startDateExploded = explode('/', $startDate);
  $startDateJoined = $startDateExploded[2] ."-". $startDateExploded[0]. "-" .$startDateExploded[1] . ' 00:00:00';

  $endDate = $query->testInput($endDateParam);
  $endDateExploded = explode('/', $endDate);
  $endDateJoined = $endDateExploded[2] ."-". $endDateExploded[0]. "-" . $endDateExploded[1] .' 11:59:59';
  
  if ($department == 'sales') {
    $transactCounts = $query->fetchTransactCountsByDateRangeofSales($startDateJoined, $endDateJoined);
  }
  else if ($department == 'engr') {
    $transactCounts = $query->fetchTransactCountsByDateRangeOfEngr($startDateJoined, $endDateJoined);
  }
  else if ($department == 'operation') {
    $transactCounts = $query->fetchTransactCountsByDateRangeOfOper($startDateJoined, $endDateJoined);
  }


  $sortedUniqeArrOfDate = [];
  $uniqueDates = [];
  $data = array('transactCounts' => '', 'dates' => '');

  foreach ($transactCounts as $obj) {
    array_push($sortedUniqeArrOfDate, $obj['entered_date']);
  }

  foreach ($sortedUniqeArrOfDate as $date) {
    if (in_array($date, $uniqueDates)) {
    } else {
      array_push($uniqueDates, $date);
    }
  }

  $data['transactCounts'] = $transactCounts;
  $data['dates'] = $uniqueDates;

  return $data;
}

if (isset($_POST['action'])) {
  // Fetch Employees By Department
  if ($_POST['action'] == 'fetchEmplyByDep') {
    $appId = $query->testInput($_POST['appId']);
    echo json_encode($query->fetchEmplyByDep($appId));
  }
  
  // SALES DEPARTMENT
  if ($_POST['action'] == 'fetchTransactCountsByDateRangeOfSales') {
    $startDate = $query->testInput($_POST['startDate']);
    $startDateExploded = explode('/', $startDate);
    $startDateJoined = $startDateExploded[2] ."-". $startDateExploded[0]. "-" .$startDateExploded[1] . ' 00:00:00';

    $endDate = $query->testInput($_POST['endDate']);
    $endDateExploded = explode('/', $endDate);
    $endDateJoined = $endDateExploded[2] ."-". $endDateExploded[0]. "-" . $endDateExploded[1] .' 11:59:59';
    
    $transactCounts = $query->fetchTransactCountsByDateRangeofSales($startDateJoined, $endDateJoined);

    $sortedUniqeArrOfDate = [];
    $uniqueDates = [];
    $data = array('transactCounts' => '', 'dates' => '');

    foreach ($transactCounts as $obj) {
      array_push($sortedUniqeArrOfDate, $obj['entered_date']);
    }

    foreach ($sortedUniqeArrOfDate as $date) {
      if (in_array($date, $uniqueDates)) {
      } else {
        array_push($uniqueDates, $date);
      }
    }

    $data['transactCounts'] = $transactCounts;
    $data['dates'] = $uniqueDates;

    echo json_encode($data);
  }
  
  // ENGINEERING DEPARTMENT
  if ($_POST['action'] == 'fetchTransactCountsByDateRangeOfEngr') {
    echo json_encode(fetchTransactCountsByDateRangeOfADep(
      $_POST['startDate'],
      $_POST['endDate'],
      'engr'
    ));
  }
  
  // OPERATION DEPARTMENT
  if ($_POST['action'] == 'fetchTransactCountsByDateRangeOfOper') {
    echo json_encode(fetchTransactCountsByDateRangeOfADep(
      $_POST['startDate'],
      $_POST['endDate'],
      'operation'
    ));
  }
}