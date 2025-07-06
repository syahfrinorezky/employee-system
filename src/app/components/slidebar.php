<?php

$activePath = basename($_SERVER['PHP_SELF']);
$currentUri = $_SERVER['REQUEST_URI'];

$baseUrl = "/employee-system/src/app/main/views/";

$isEmployeeActive = ($activePath == 'index.php' && strpos($currentUri, '/employee/') !== false);
$isAttendanceActive = ($activePath == 'index.php' && strpos($currentUri, '/attendance/') !== false);
$isDashboardActive = ($activePath == 'dashboard.php');

?>

<div
    x-show="slideBarOpen"
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="lg:hidden fixed top-0 right-0 w-2/3 h-full bg-white shadow-md shadow-gray-300 border-l border-gray-300 z-40 transform">
    <div class="flex flex-col gap-10 px-7 py-5">
        <button @click="slideBarOpen = false" type="button" class="relative self-end cursor-pointer">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>
        <div class="flex flex-col items-center justify-center gap-2">
            <div class="flex items-center justify-center w-12 sm:w-18 h-12 sm:h-18 aspect-square bg-gray-400 rounded-full">
                <i class="fa-solid fa-user text-2xl sm:text-3xl text-white"></i>
            </div>
            <div class="flex flex-col items-center">
                <h1 class="font-bold font-primary text-lg sm:text-2xl uppercase"><?= $username ?></h1>
                <p class="text-xs sm:text-base text-gray-500"><?= $user_email ?></p>
            </div>
            <button @click="logoutModal = !logoutModal" type="button">
                <p class="text-xs text-red-500 hover:text-red-600 hover:underline transition-all duration-300 ease-in-out cursor-pointer">Logout</p>
            </button>
        </div>
        <div class="flex-grow border-t border-gray-300"></div>
        <ul class="flex flex-col">
            <li>
                <a href="<?= $baseUrl ?>dashboard.php" class="flex items-center gap-2 sm:gap-3 px-4 py-4 rounded-lg <?= $isDashboardActive ? 'bg-gray-300' : '' ?> hover:bg-gray-300 transition-all duration-300 ease-in-out">
                    <i class="fa-solid fa-home text-lg sm:text-2xl <?= $isDashboardActive ? 'text-indigo-500' : '' ?>"></i>
                    <span class="text-sm sm:text-lg <?= $isDashboardActive ? 'text-gray-900' : 'text-gray-500' ?>">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>employee/index.php" class="flex items-center gap-2 sm:gap-3 px-4 py-4 rounded-lg <?= $isEmployeeActive ? 'bg-gray-300' : '' ?> hover:bg-gray-300 transition-all duration-300 ease-in-out">
                    <i class="fa-solid fa-people-group text-lg sm:text-2xl <?= $isEmployeeActive ? 'text-indigo-500' : '' ?>"></i>
                    <span class="text-sm sm:text-lg <?= $isEmployeeActive ? 'text-gray-900' : 'text-gray-500' ?>">Karyawan</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>attendance/index.php" class="flex items-center gap-2 sm:gap-3 px-4 py-4 rounded-lg <?= $isAttendanceActive ? 'bg-gray-300' : '' ?> hover:bg-gray-300 transition-all duration-300 ease-in-out">
                    <i class="fa-solid fa-check-to-slot text-lg sm:text-2xl <?= $isAttendanceActive ? 'text-indigo-500' : '' ?>"></i>
                    <span class="text-sm sm:text-lg <?= $isAttendanceActive ? 'text-gray-900' : 'text-gray-500' ?>">Absensi</span>
                </a>
            </li>
        </ul>
    </div>
</div>