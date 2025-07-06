<nav class="fixed top-0 flex w-full bg-white border-b border-gray-200 shadow-md shadow-gray-300 px-7 sm:px-10 py-5 sm:py-4 z-30">
    <div class="flex justify-between items-center w-full">
        <a href="./dashboard.php" aria-label="logo" class="font-extrabold text-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 bg-clip-text text-transparent">STAFFY</a>
        <div class="flex items-center">
            <button 
                @click="slideBarOpen = !slideBarOpen"
                type="button" 
                aria-label="hamburger" 
                class="lg:hidden text-indigo-500 hover:text-indigo-600 hover:underline transition-all duration-300 ease-in-out">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
            <button @click="logoutModal = !logoutModal" aria-label="logout" type="button" class="hidden lg:flex items-center justify-center aspect-square w-10 h-10 bg-red-500 hover:bg-red-700 focus:outline-3 focus:outline-red-300 transition-all duration-300 ease-in-out rounded-xl cursor-pointer">
                <i class="fa-solid fa-power-off text-xl text-white"></i>
            </button>
        </div>
    </div>
</nav>