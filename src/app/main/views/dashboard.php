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
    <title>Dashboard - STAFFY</title>
    <link rel="stylesheet" href="../../../resources/style/style.css">
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body x-data="{ slideBarOpen : false, logoutModal : false }">
    <?php include __DIR__ . '../../../components/header.php'; ?>

    <div x-cloak >
        <?php include __DIR__ . '../../../components/logout_modal.php' ?>
    </div>

    <div x-cloak >
        <?php include __DIR__ . '../../../components/slidebar.php'; ?>
    </div>

    <div class="flex bg-white">
        <?php include __DIR__ . '../../../components/sidebar.php'; ?>
        
        <div class="flex flex-col gap-10 w-full sm:overflow-y-auto p-4 sm:p-8 mt-20 sm:mt-12">
            <h1 class="text-2xl font-bold font-secondary text-gray-800"><span class="text-indigo-500">Dashboard</span> Informasi</h1>
            <div class="flex flex-col gap-5 border border-gray-300 rounded-lg p-4">
                <div aria-label="text" class="flex flex-col gap-1">
                    <h2 class="text-xl font-semibold font-primary text-indigo-500">Panduan</h2>
                    <p class="text-base font-primary text-gray-500 text-justify">Berikut adalah panduan untuk menggunakan STAFFY</p>
                </div>
                <ul class="flex flex-col gap-2">
                    <li x-data="{open: false}" class="border border-gray-300 rounded-lg p-3">
                        <div @click="open = !open" class="flex items-center justify-between cursor-pointer">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-home text-indigo-500 text-lg"></i>
                                <h3 class="font-medium text-gray-800 text-lg">Dashboard</h3>
                            </div>
                            <i class="fa-solid fa-chevron-down text-gray-500 text-lg transition-transform duration-300" :class="{'rotate-180': open, 'rotate-0': !open}"></i>                        
                        </div>
                        <div 
                            x-cloak
                            x-show="open"
                            x-transition:enter="transition ease-out duration-400"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-40"
                            x-transition:leave="transition ease-in duration-400"
                            x-transition:leave-start="opacity-100 max-h-40"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden"
                        >
                            <p class="mt-2 text-sm text-gray-500 font-primary text-justify">
                                Pada halaman ini merupakan dashboard daripada website STAFFY. Semua informasi umum akan ditampilkan disini.
                            </p>
                        </div>
                    </li>
                    <li x-data="{open: false}" class="border border-gray-300 rounded-lg p-3">
                        <div @click="open = !open" class="flex items-center justify-between cursor-pointer">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-home text-indigo-500 text-lg"></i>
                                <h3 class="font-medium text-gray-800 text-lg">Karyawan</h3>
                            </div>
                            <i class="fa-solid fa-chevron-down text-gray-500 text-lg transition-transform duration-300" :class="{'rotate-180': open, 'rotate-0': !open}"></i>                        
                        </div>
                        <div 
                            x-cloak
                            x-show="open"
                            x-transition:enter="transition ease-out duration-400"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-40"
                            x-transition:leave="transition ease-in duration-400"
                            x-transition:leave-start="opacity-100 max-h-40"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden"
                        >
                            <p class="mt-2 text-sm text-gray-500 font-primary text-justify">
                                Pada halaman ini anda dapat menambahkan, mengedit, dan menghapus karyawan dari perusahaan anda.
                            </p>
                        </div>
                    </li>
                    <li x-data="{open: false}" class="border border-gray-300 rounded-lg p-3">
                        <div @click="open = !open" class="flex items-center justify-between cursor-pointer">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-home text-indigo-500 text-lg"></i>
                                <h3 class="font-medium text-gray-800 text-lg">Absensi</h3>
                            </div>
                            <i class="fa-solid fa-chevron-down text-gray-500 text-lg transition-transform duration-300" :class="{'rotate-180': open, 'rotate-0': !open}"></i>                        
                        </div>
                        <div 
                            x-cloak
                            x-show="open"
                            x-transition:enter="transition ease-out duration-400"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-40"
                            x-transition:leave="transition ease-in duration-400"
                            x-transition:leave-start="opacity-100 max-h-40"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden"
                        >
                            <p class="mt-2 text-sm text-gray-500 font-primary text-justify">
                                Pada halaman ini anda dapat melakukan absensi karyawan yang sudah terdaftar pada website STAFFY
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script type="module" src="../../../resources/js/dist/app.bundle.js"></script>
    <script type="module" src="../../../resources/js/dist/fa.min.js"></script>
</body>
</html>