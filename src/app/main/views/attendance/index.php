<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/views/login.php");
}

$username = $_SESSION['user']['username'];
$user_email = $_SESSION['user']['email'];
$user_id = $_SESSION['user']['id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - STAFFY</title>
    <link rel="stylesheet" href="../../../../resources/style/style.css">
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body x-data="{ slideBarOpen : false }">
    <?php include __DIR__ . '../../../../components/header.php'; ?>

    <div
        x-cloak
        x-show="slideBarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="slideBarOpen = false"
        class="fixed inset-0 bg-black/40 bg-opacity-50 backdrop-blur-xs z-50""></div>

    <div x-cloak >
        <?php include __DIR__ . '../../../../components/slidebar.php'; ?>
    </div>

    <div class="flex bg-white">
        <?php include __DIR__ . '../../../../components/sidebar.php'; ?>
        
        <div class="flex flex-col gap-10 w-full sm:overflow-y-auto p-4 sm:p-8 mt-20 sm:mt-12">
            
        </div>
    </div>


    <script type="module" src="../../../../resources/js/dist/app.bundle.js"></script>
    <script type="module" src="../../../../resources/js/dist/fa.min.js"></script>
</body>
</html>