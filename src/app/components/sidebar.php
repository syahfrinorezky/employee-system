<?php

$activePath = basename($_SERVER['PHP_SELF']);
$currentUri = $_SERVER['REQUEST_URI'];

$baseUrl = "/employee-system/src/app/main/views/";

$isEmployeeActive = ($activePath == 'index.php' && strpos($currentUri, '/employee/') !== false);
$isAttendanceActive = ($activePath == 'index.php' && strpos($currentUri, '/attendance/') !== false);
$isDashboardActive = ($activePath == 'dashboard.php');

?>

<div class="hidden lg:flex sticky top-0 h-screen flex-col w-1/4 p-8 bg-white shadow-md shadow-gray-300 border-r border-gray-300 overflow-y-auto">
    <div class="flex flex-col gap-5 mt-20">
        <div class="flex flex-col items-center justify-center gap-2">
            <div class="flex items-center justify-center w-12 h-12 aspect-square bg-gray-400 rounded-full">
                <i class="fa-solid fa-user text-2xl text-white"></i>
            </div>
            <div class="flex flex-col items-center">
                <h1 class="font-bold font-primary text-lg uppercase"><?= $username ?></h1>
                <p class="text-xs text-gray-500"><?= $user_email ?></p>
            </div>
        </div>
        <div class="flex-grow border-t border-gray-300"></div>
        <ul class="flex flex-col">
            <li>
                <a href="<?= $baseUrl ?>dashboard.php" class="flex items-center gap-2 px-4 py-4 rounded-lg <?= $isDashboardActive ? 'bg-gray-300' : '' ?> hover:bg-gray-300 transition-all duration-300 ease-in-out">
                    <i class="fa-solid fa-home text-lg <?= $isDashboardActive ? 'text-indigo-500' : '' ?>"></i>
                    <span class="text-sm <?= $isDashboardActive ? 'text-gray-900' : 'text-gray-500' ?>">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>employee/index.php" class="flex items-center gap-2 px-4 py-4 rounded-lg <?= $isEmployeeActive ? 'bg-gray-300' : '' ?> hover:bg-gray-300 transition-all duration-300 ease-in-out">
                    <i class="fa-solid fa-people-group text-lg <?= $isEmployeeActive ? 'text-indigo-500' : '' ?>"></i>
                    <span class="text-sm <?= $isEmployeeActive ? 'text-gray-900' : 'text-gray-500' ?>">Karyawan</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>attendance/index.php" class="flex items-center gap-2 px-4 py-4 rounded-lg <?= $isAttendanceActive ? 'bg-gray-300' : '' ?> hover:bg-gray-300 transition-all duration-300 ease-in-out">
                    <i class="fa-solid fa-check-to-slot text-lg <?= $isAttendanceActive ? 'text-indigo-500' : '' ?>"></i>
                    <span class="text-sm <?= $isAttendanceActive ? 'text-gray-900' : 'text-gray-500' ?>">Absensi</span>
                </a>
            </li>
        </ul>
    </div>
</div>