<?php

error_reporting(1);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'employee_system');

$connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($connection -> connect_error) {
    die("Connection failed: " . $connection -> connect_error);
}
