<?php 

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errors = $_SESSION['errors'] ?? [];

unset($_SESSION['errors']);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LUPA PASSWORD</title>
    <link rel="stylesheet" href="../../../resources/style/style.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body>
    <div class="flex items-center justify-center min-h-screen">
        <div class="flex flex-col gap-y-10 w-3/4 sm:w-1/4 rounded-lg overflow-hidden shadow-md shadow-gray-300 border border-gray-300 p-3 sm:p-6">
            <a href="./login.php" class="bg-indigo-500 hover:bg-indigo-600 text-white flex items-center justify-center w-10 aspect-square rounded-full">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <form action="../process/resetpw_process.php" method="post" class="flex flex-col gap-y-5">

                <?php if (!empty($errors)): ?>
                    <div
                        x-cloak
                        x-data="{
                            show : false, visible : false
                        }" 
                        x-init="
                        setTimeout(() => { 
                            show = true; 
                            visible = true; 
                            setTimeout(() => visible = false, 3000);
                        }, 1000);" 
                        x-show="visible"
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-300 transform"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" 
                        class="p-2 flex items-center gap-x-2 bg-red-500/40 rounded-lg border border-red-500">
                        <i class="fa-solid fa-xmark text-white text-sm"></i>
                        <p class="text-sm text-white text-shadow-sm text-shadow-gray-400"><?= $errors; ?></p>
                    </div>
                <?php endif; ?>
                <!-- username -->
                 <div class="flex flex-col gap-y-2">
                    <label for="username">
                        <div class="flex gap-x-2">
                            <i class="fa-solid fa-user text-indigo-500 text-base"></i>
                            <p class="font-primary text-sm text-gray-600 text-shadow-xs text-shadow-gray-300">Username</p>
                        </div>
                    </label>
                    <input type="text" id="username" name="username" required class="p-2 text-sm outline-1 outline-indigo-300 rounded-lg focus:outline-1 focus:outline-indigo-500  hover:outline-1 hover:outline-indigo-500 transition-all duration-300 ease-in-out">
                 </div>
                <!-- password baru -->
                 <div class="flex flex-col gap-y-2">
                    <label for="newpass">
                        <div class="flex gap-x-2">
                            <i class="fa-solid fa-key text-indigo-500 text-base"></i>
                            <p class="font-primary text-sm text-gray-600 text-shadow-xs text-shadow-gray-300">Password Baru</p>
                        </div>
                    </label>
                    <div x-data="{show : false}" class="flex gap-x-2">
                        <input :type="show ? 'text' : 'password'" id="newpass" name="newpass" class="p-2 text-sm outline-1 outline-indigo-300 rounded-lg focus:outline-1 focus:outline-indigo-500  hover:outline-1 hover:outline-indigo-500 transition-all duration-300 ease-in-out w-full">
                        <button type="button" @click="show = !show" class="w-1/6 sm:w-1/9 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex items-center justify-center transition-all duration-300 ease-in-out cursor-pointer">
                            <i class="fa-solid fa-eye" x-show="!show"></i>
                            <i class="fa-solid fa-eye-slash" x-show="show"></i>
                        </button>
                    </div>
                 </div>
                <!-- password confirm -->
                 <div class="flex flex-col gap-y-2">
                    <label for="confirm_pass">
                        <div class="flex gap-x-2">
                            <i class="fa-solid fa-lock text-indigo-500 text-base"></i>
                            <p class="font-primary text-sm text-gray-600 text-shadow-xs text-shadow-gray-300">Konfirmasi Password</p>
                        </div>
                    </label>
                    <div x-data="{show : false}" class="flex gap-x-2">
                        <input :type="show ? 'text' : 'password'" id="confirm_pass" name="confirm_pass" class="p-2 text-sm outline-1 outline-indigo-300 rounded-lg focus:outline-1 focus:outline-indigo-500  hover:outline-1 hover:outline-indigo-500 transition-all duration-300 ease-in-out w-full">
                        <button type="button" @click="show = !show" class="w-1/6 sm:w-1/9 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex items-center justify-center transition-all duration-300 ease-in-out cursor-pointer">
                            <i class="fa-solid fa-eye" x-show="!show"></i>
                            <i class="fa-solid fa-eye-slash" x-show="show"></i>
                        </button>
                    </div>
                 </div>

                <button type="submit" name="resetpass" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold uppercase p-2 rounded-lg cursor-pointer">Reset Password</button>
            </form>
        </div>
    </div>
    <script type="module" src="../../../resources/js/dist/app.bundle.js"></script>
    <script type="module" src="../../../resources/js/dist/fa.min.js"></script>
</body>
</html>