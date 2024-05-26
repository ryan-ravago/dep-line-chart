<?php
session_start();
unset($_SESSION['eboto_student']);
header('location: ./index.php');
exit();
?>