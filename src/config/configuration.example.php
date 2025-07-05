<?php

error_reporting(1);
ini_set('display_errors', 1);

define('DB_HOST', 'localhost');
define('DB_USERNAME', 'your-username');
define('DB_PASSWORD', 'your-password');
define('DB_DATABASE', 'your-database');

$connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($connection -> connect_error) {
    die("Connection failed: " . $connection -> connect_error);
}
