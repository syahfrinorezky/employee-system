<?php 

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_unset();
session_destroy();
header('Location: ../views/login.php');

?>