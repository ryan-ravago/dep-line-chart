<?php

require './db2.php';

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
  
  // Fetch Count of All hisStatCode filtered by daterange
  public function fetchTransactCountsByDateRange($startDate, $endDate) {
    $sql = "SELECT COUNT(lineNo) AS lineNoCount, hisStatCode, DATE_FORMAT(enteredDate, '%Y-%m-%d') AS entered_date
            FROM INQHIS 
            WHERE hisStatCode
              IN('TSCRT', 'TLTB', 'TSAQN', 'TCLTB', 'TSFQ')  AND
              enteredDate BETWEEN ? AND ?
            group by hisStatCode, entered_date
            order by entered_date";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$startDate, $endDate]);
    return $stmt->fetchAll();
  }
}