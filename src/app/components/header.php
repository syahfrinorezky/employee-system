<nav class="fixed top-0 flex w-full bg-white border-b border-gray-200 shadow-md shadow-gray-300 px-7 sm:px-10 py-5 sm:py-4 z-50">
    <div class="flex justify-between items-center w-full">
        <a href="./dashboard.php" aria-label="logo" class="font-extrabold text-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 bg-clip-text text-transparent">STAFFY</a>
        <div class="flex items-center">
            <button 
                @click="slideBarOpen = !slideBarOpen"
                type="button" 
                aria-label="hamburger" 
                class="sm:hidden text-indigo-500 hover:text-indigo-600 hover:underline transition-all duration-300 ease-in-out">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>
    </div>
</nav>