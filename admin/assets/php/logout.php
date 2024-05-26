<?php
session_start();
unset($_SESSION['eboto_admin']);
header('location: ../../index.php');
exit();
?>