<?php

require './db.php';

class Query extends Database {
  // Fetch Employees by Department
  public function fetchEmplyByDep($appId, $isActive = 1) {
    $sql = "SELECT gUserName
            FROM dbusers.appusr
            WHERE appId = ? AND isActive = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$appId, $isActive]);
    return $stmt->fetchAll();
  }
}