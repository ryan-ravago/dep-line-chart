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
  // SALES
  public function fetchTransactCountsByDateRangeOfSales($startDate, $endDate) {
    $sql = "SELECT COUNT(lineNo) AS lineNoCount, hisStatCode, DATE_FORMAT(enteredDate, '%Y-%m-%d') AS entered_date
            FROM INQHIS 
            WHERE hisStatCode
              IN('TSCRT', 'TLTB', 'TSAQN', 'TCLTB', 'TSFQ')  AND
              DATE(enteredDate) BETWEEN ? AND ?
            group by hisStatCode, entered_date
            order by entered_date";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$startDate, $endDate]);
    return $stmt->fetchAll();
  }

  // Fetch Count of All hisStatCode filtered by daterange
  // ENGINEERING
  public function fetchTransactCountsByDateRangeOfEngr($startDate, $endDate) {
    $sql = "SELECT COUNT(lineNo) AS lineNoCount, hisStatCode, DATE_FORMAT(enteredDate, '%Y-%m-%d') AS entered_date
            FROM INQHIS 
            WHERE hisStatCode
              IN('TEAPB', 'TEDPB', 'TCFS', 'TIES')  AND
              enteredDate BETWEEN ? AND ?
            group by hisStatCode, entered_date
            order by entered_date";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$startDate, $endDate]);
    return $stmt->fetchAll();
  }

  // Fetch Count of All hisStatCode filtered by daterange
  // OPERATION
  public function fetchTransactCountsByDateRangeOfOper($startDate, $endDate) {
    $sql = "SELECT COUNT(lineNo) AS lineNoCount, hisStatCode, DATE_FORMAT(enteredDate, '%Y-%m-%d') AS entered_date
            FROM INQHIS 
            WHERE hisStatCode
              IN('TONST', 'PCOM')  AND
              enteredDate BETWEEN ? AND ?
            group by hisStatCode, entered_date
            order by entered_date";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$startDate, $endDate]);
    return $stmt->fetchAll();
  }

  // Fetch Count of All hisStatCode filtered by daterange
  // TRANSPORT
  // public function fetchTransactCountsByDateRangeOfTransport($startDate, $endDate) {
  //   $sql = "SELECT COUNT(lineNo) AS lineNoCount, hisStatCode, DATE_FORMAT(enteredDate, '%Y-%m-%d') AS entered_date
  //           FROM INQHIS 
  //           WHERE hisStatCode
  //             IN('TSCRT', 'TLTB', 'TSAQN', 'TCLTB', 'TSFQ')  AND
  //             enteredDate BETWEEN ? AND ?
  //           group by hisStatCode, entered_date
  //           order by entered_date";
  //   $stmt = $this->conn->prepare($sql);
  //   $stmt->execute([$startDate, $endDate]);
  //   return $stmt->fetchAll();
  // }
}