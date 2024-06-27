<?php
require './query2.php';

$query = new Query();

if (isset($_POST['action'])) {
  if ($_POST['action'] == 'fetchEmplyByDep') {
    $appId = $query->testInput($_POST['appId']);
    echo json_encode($query->fetchEmplyByDep($appId));
  }

  if ($_POST['action'] == 'fetchTransactCountsByDateRange') {
    $startDate = $query->testInput($_POST['startDate']);
    $startDateExploded = explode('/', $startDate);
    $startDateJoined = $startDateExploded[2] ."-". $startDateExploded[0]. "-" .$startDateExploded[1];

    $endDate = $query->testInput($_POST['endDate']);
    $endDateExploded = explode('/', $endDate);
    $endDateJoined = $endDateExploded[2] ."-". $endDateExploded[0]. "-" . $endDateExploded[1];
    
    $transactCounts = $query->fetchTransactCountsByDateRange($startDateJoined . ' 12:00:00', $endDateJoined.' 11:59:59');

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
}