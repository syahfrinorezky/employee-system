<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAFTAR - STAFFY</title>
    <link rel="stylesheet" href="../../../resources/style/style.css">
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-white">
        <div class="flex flex-col sm:flex-row w-4/5 sm:max-w-4xl rounded-lg overflow-hidden shadow-xl shadow-gray-300 border border-gray-300">
            <div class="bg-white flex flex-col space-y-8 p-5 sm:p-10 sm:w-1/2 order-2 sm:order-2">
                <h1 class="text-center text-indigo-600 text-2xl font-bold font-primary uppercase text-shadow-md text-shadow-gray-300 text-primary">Register</h1>
                <form action="../process/register_process.php" method="post" class="flex flex-col space-y-6">
                    <label for="email" class="flex flex-col space-y-2 w-full">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-indigo-500 text-base"></i>
                            <p class="font-primary text-sm text-gray-600 text-shadow-xs text-shadow-gray-300">Email</p>
                        </div>
                        <input type="text" name="email" id="email" placeholder="Masukkan email anda" class="p-2  text-sm outline-1 outline-indigo-300 rounded-lg focus:outline-1 focus:outline-indigo-500  hover:outline-1 hover:outline-indigo-500 transition-all duration-300 ease-in-out">
                    </label>                    
                    <label for="username" class="flex flex-col space-y-2 w-full">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user text-indigo-500 text-base"></i>
                            <p class="font-primary text-sm text-gray-600 text-shadow-xs text-shadow-gray-300">Username</p>
                        </div>
                        <input type="text" name="username" id="username" placeholder="Masukkan username anda" class="p-2  text-sm outline-1 outline-indigo-300 rounded-lg focus:outline-1 focus:outline-indigo-500  hover:outline-1 hover:outline-indigo-500 transition-all duration-300 ease-in-out">
                    </label>
                    <label for="password" class="flex flex-col space-y-2 w-full">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-key text-indigo-500 text-base"></i>
                            <p class="font-primary text-sm text-gray-600 text-shadow-xs text-shadow-gray-300">Password</p>
                        </div>
                        <div x-data="{show : false}" class="flex gap-2 w-full">
                            <input :type="show ? 'text' : 'password'" name="password" id="password" placeholder="Masukkan password anda" class="p-2 w-full text-sm outline-1 outline-indigo-300 rounded-lg focus:outline-1 focus:outline-indigo-500  hover:outline-1 hover:outline-indigo-500 transition-all duration-300 ease-in-out">
                            <button type="button" @click="show = !show" class="w-1/6 sm:w-1/9 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex items-center justify-center transition-all duration-300 ease-in-out cursor-pointer">
                                <i class="fa-solid fa-eye" x-show="!show"></i>
                                <i class="fa-solid fa-eye-slash" x-show="show"></i>
                            </button>
                        </div>
                    </label>
                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold uppercase p-2 rounded-lg cursor-pointer">Daftar</button>
                    <p class="text-sm text-center text-gray-500">Sudah punya akun? <a href="./login.php" class="text-indigo-500 hover:text-indigo-600 hover:underline transition-all duration-300 ease-in-out">Login</a></p>
                </form>
            </div>
            <div class="hidden sm:relative sm:flex items-center justify-center w-1/2 order-1 bg-indigo-400 overflow-hidden">
                <img src="../../../resources/public/icon/circle-random.svg" alt="Decorative circle background" class="absolute w-full h-full object-cover z-10 opacity-50">
                <div class="flex flex-col relative z-20 items-center justify-center gap-y-5">
                    <h1 class="text-white font-primary text-3xl font-bold text-shadow-sm text-shadow-gray-500">Register to STAFFY</h1>
                    <img src="../../../resources/public/icon/register.svg" alt="Register" class="w-3/5 drop-shadow-md drop-shadow-gray-800 ">
                </div>
            </div>
        </div>
    </div>
    <script type="module" src="../../../resources/js/dist/app.bundle.js"></script>
    <script type="module" src="../../../resources/js/dist/fa.min.js"></script>
</body>
</html>