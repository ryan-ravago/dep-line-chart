<?php
require './query.php';

$query = new Query();

if (isset($_POST['action'])) {
  if ($_POST['action'] == 'fetchEmplyByDep') {
    $appId = $query->testInput($_POST['appId']);
    echo json_encode($query->fetchEmplyByDep($appId));
  }

}