<div class="px-3 py-4 bg-gray-50 border-t border-gray-300">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex sm:hidden items-center gap-2">
            <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white">
                <i class="fa-solid fa-chevron-left text-sm"></i>
            </button>

            <span class="text-sm text-gray-600 px-2">
                <span x-text="currentPage"></span> / <span x-text="totalPages"></span>
            </span>

            <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white">
                <i class="fa-solid fa-chevron-right text-sm"></i>
            </button>
            </div>

            <!-- Desktop pagination (full) -->
            <div class="hidden sm:flex items-center gap-2">
            <button
                @click="goToPage(1)"
                :disabled="currentPage === 1"
                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white">
                <i class="fa-solid fa-angles-left text-sm"></i>
            </button>

            <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white">
                <i class="fa-solid fa-chevron-left text-sm"></i>
            </button>

            <template
                x-for="page in Array.from({length: totalPages}, (_, i) => i + 1).filter(p => p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1)"
                :key="page">
                <button
                @click="goToPage(page)"
                :class="page === currentPage ? 'bg-indigo-500 text-white' : 'bg-white hover:bg-gray-200'"
                class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 text-sm font-medium"
                x-text="page"></button>
            </template>

            <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white">
                <i class="fa-solid fa-chevron-right text-sm"></i>
            </button>

            <button
                @click="goToPage(totalPages)"
                :disabled="currentPage === totalPages"
                :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-300 bg-white">
                <i class="fa-solid fa-angles-right text-sm"></i>
            </button>
        </div>

        <div class="text-sm text-gray-500">
            <span x-text="recordsPerPage"></span> per halaman
        </div>
    </div>
</div>
