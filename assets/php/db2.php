<?php

class Database {
  private $dsn = 'mysql:host=192.168.0.12;dbname=dboperation';
  private $dbuser = 'SeoUser';
  private $dbpass = 'S3oReri2023';

  public $conn;

  public function __construct() {
    try {
      $this->conn = new PDO($this->dsn, $this->dbuser, $this->dbpass);
      // echo 'success';
    } catch (PDOException $e) {
      echo "Error {$e->getMessage()}";
    }
  }

  public function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = strip_tags($data);
    return $data;
  }
}