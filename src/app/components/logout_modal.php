<?php

$logoutPath = "/employee-system/src/app/auth/process/logout_process.php";
$iconPath = "/employee-system/src/resources/public/icon/logout.svg"

?>


<div
    x-cloak
    x-show="logoutModal"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click.self="logoutModal = false"
    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50">
    <div x-show="logoutModal" class="bg-white w-2/3 sm:w-1/4 rounded-lg p-5 sm:p-8 flex flex-col gap-5 items-center justify-center z-50">
        <img src="<?= $iconPath ?>" alt="logout image" class="w-30 h-auto">
        <p>Anda yakin ingin keluar ?</p>
        <div class="flex items-center justify-center space-x-5">
            <button @click="logoutModal = false" type="button" class="bg-gray-400 hover:bg-gray-500 text-white px-3 py-2 rounded-lg cursor-pointer">
                Batal
            </button>
            <form action="<?= $logoutPath ?>" method="post">
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-2 rounded-lg cursor-pointer">
                    Yakin
                </button>
            </form>
        </div>
    </div>
</div>