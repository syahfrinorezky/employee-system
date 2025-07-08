<?php

$processPath = "/employee-system/src/app/main/process/";

?>

<div x-cloak x-data="{ step: 1, totalSteps: 4 }">
    <div
        x-cloak
        x-show="addModal"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="addModal = false"
        class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50">
        <div x-show="addModal" class="bg-white w-4/5 sm:w-1/2 rounded-lg flex flex-col gap-4 items-center justify-center z-50 overflow-hidden">
            <div class="p-4 bg-indigo-500 w-full flex justify-between">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus text-base text-white"></i>
                    <h1 class="text-base font-medium font-primary text-white">Tambah Data</h1>
                </div>
                <button @click="addModal = false" type="button" class="cursor-pointer">
                    <i class="fa-solid fa-xmark text-base text-white"></i>
                </button>
            </div>
            <form action="<?= $processPath . 'employee_process.php' ?>" method="post" class="p-4 flex flex-col w-full gap-3">
                <!-- Step 1: Employee Information -->
                <div x-show="step === 1" class="flex flex-col gap-2">
                    <h2 class="text-lg font-semibold text-indigo-500">Informasi Karyawan</h2>
                    <div class="flex flex-col gap-1">
                        <label for="nip" class=" font-medium text-primary text-gray-800">NIP</label>
                        <input type="text" id="nip" name="nip" value="EMP-" class="border border-gray-300 rounded p-2 w-full" readonly />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="nama_lengkap" class="font-medium text-primary text-gray-800">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan Nama Lengkap" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="email" class="font-medium text-primary text-gray-800">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan Email" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="no_hp" class="font-medium text-primary text-gray-800">No HP</label>
                        <input type="text" id="no_hp" name="no_hp" placeholder="Masukkan No HP" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                </div>

                <!-- Step 2: Education Info -->
                <div x-show="step === 2" class="flex flex-col gap-2">
                    <h2 class="text-lg font-semibold">Informasi Pendidikan</h2>
                    <div class="flex flex-col gap-1">
                        <label for="pendidikan_terakhir" class="block">Pendidikan Terakhir:</label>
                        <select id="pendidikan_terakhir" name="pendidikan_terakhir" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full">
                            <option value="">Pilih Pendidikan Terakhir</option>
                            <option value="SMA">SMA</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="nama_sekolah" class="block">Nama Sekolah</label>
                        <input type="text" id="nama_sekolah" name="nama_sekolah" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="jurusan" class="block">Jurusan</label>
                        <input type="text" id="jurusan" name="jurusan" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                </div>

                <!-- Step 3: Informasi Pekerjaan/Jabatan Karyawan -->
                <div x-show="step === 3" class="flex flex-col gap-2">
                    <h2 class="text-lg font-semibold text-indigo-500">Informasi Pekerjaan</h2>
                    <div class="flex flex-col gap-1">
                        <label for="jabatan" class="block">Jabatan </label>
                        <select id="jabatan" name="jabatan" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full">
                            <option value="">Pilih Jabatan</option>
                            <option value="Manager">Manager</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Staff">Staff</option>
                            <option value="Intern">Intern</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="departemen" class="block">Departemen:</label>
                        <select id="departemen" name="departemen" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full">
                            <option value="">Pilih Divisi</option>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Finance</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Operations">Operations</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="tanggal_masuk" class="block">Tanggal Masuk:</label>
                        <input type="date" id="tanggal_masuk" name="tanggal_masuk" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                </div>

                <!-- Step 4 : Informasi terkait kontrak karyawan -->
                <div x-show="step === 4" class="flex flex-col gap-2">
                    <h2 class="text-lg font-semibold text-indigo-500">Informasi Kontrak</h2>
                    <div class="flex flex-col gap-1">
                        <label for="jenis_kontrak" class="block">Jenis Kontrak</label>
                        <select id="jenis_kontrak" name="jenis_kontrak" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full">
                            <option value="">Pilih Jenis Kontrak</option>
                            <option value="Tetap">Tetap</option>
                            <option value="Kontrak">Kontrak</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="durasi_kontrak_bulan" class="block">Durasi Kontrak (bulan)</label>
                        <input type="number" id="durasi_kontrak_bulan" name="durasi_kontrak_bulan" min="1" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="tanggal_berakhir_kontrak" class="block">Tanggal Berakhir Kontrak</label>
                        <input type="date" id="tanggal_berakhir_kontrak" name="tanggal_berakhir_kontrak" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                </div>

               <div class="flex justify-between mt-4">
                    <!-- Kembali Button (visible on ALL steps, but disabled on Step 1) -->
                    <button 
                        type="button" 
                        @click="step > 1 ? step-- : null" 
                        :class="step === 1 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-indigo-400 hover:bg-indigo-500 text-white cursor-pointer'" 
                        :disabled="step === 1"
                        class="rounded px-4 py-2"
                    >
                        Kembali
                    </button>
                    
                    <!-- Next Button (visible if not last step) -->
                    <button 
                        type="button" 
                        @click="step < totalSteps ? step++ : null" 
                        class="bg-indigo-500 hover:bg-indigo-600 text-white rounded px-4 py-2 cursor-pointer" 
                        x-show="step < totalSteps"
                    >
                        Selanjutnya
                    </button>
                    
                    <!-- Submit Button (visible only on last step) -->
                    <button 
                        type="submit" 
                        class="bg-indigo-500 hover:bg-indigo-600 text-white rounded px-4 py-2 cursor-pointer" 
                        x-show="step === totalSteps"
                    >
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
