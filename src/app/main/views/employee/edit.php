<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '../../../../../config/configuration.php';

if (!isset($_SESSION['employee_edit'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$emp_id = $_GET['nip'] ?? null;

if (!$emp_id) {
    $_SESSION['errors'] = 'NIP Karyawan tidak ditemukan';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$queryEmp = "SELECT * FROM employees WHERE nip = ? AND deleted_at IS NULL";
$stmtEmp = $connection->prepare($queryEmp);
$stmtEmp->bind_param('s', $emp_id);
$stmtEmp->execute();
$resultEmp = $stmtEmp->get_result();

if ($resultEmp->num_rows > 1) {
    $_SESSION['errors'] = 'NIP Karyawan tidak ditemukan';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$employee = $resultEmp->fetch_assoc();
$employee_id = $employee['id'];

// dapetin data informasi pekerjaan karyawan
$queryEmpInfo = "SELECT * FROM employment_info WHERE employee_id = ?";
$stmtEmpInfo = $connection->prepare($queryEmpInfo);
$stmtEmpInfo->bind_param('i', $employee_id);
$stmtEmpInfo->execute();
$EmpInfo = $stmtEmpInfo->get_result()->fetch_assoc();

// dapetin data informasi pendidikan karyawan
$queryEmpEdu = "SELECT * FROM educations WHERE employee_id = ?";
$stmtEmpEdu = $connection->prepare($queryEmpEdu);
$stmtEmpEdu->bind_param('i', $employee_id);
$stmtEmpEdu->execute();
$EmpEdu = $stmtEmpEdu->get_result()->fetch_assoc();

// dapetin data informasi kontrak karyawan
$queryEmpContract = "SELECT * FROM contracts WHERE employee_id = ?";
$stmtEmpContract = $connection->prepare($queryEmpContract);
$stmtEmpContract->bind_param('i', $employee_id);
$stmtEmpContract->execute();
$EmpContract = $stmtEmpContract->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT - <?= $employee['nip'] ?></title>
    <link rel="stylesheet" href="../../../../resources/style/style.css">
</head>
<body>
    <div x-data="{step: 1, totalSteps: 4 }" class="flex items-center justify-center min-h-screen">
        <div class="bg-white w-4/5 sm:w-1/2 rounded-lg flex flex-col gap-4 items-center justify-center overflow-hidden border border-gray-300 shadow-md shadow-gray-300">
            <div class="p-4 bg-indigo-500 w-full flex justify-between">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-pen text-base text-white"></i>
                    <h1 class="text-base font-medium font-primary text-white">Edit Data</h1>
                </div>
                <a href="./index.php">
                    <i class="fa-solid fa-right-from-bracket text-white"></i>
                </a>
            </div>
            <form action="../../process/empedit_process.php" method="post" class="p-4 flex flex-col w-full gap-3">
                <!-- Step 1: Employee Information -->
                <div x-show="step === 1" class="flex flex-col gap-2">
                    <h2 class="text-lg font-semibold text-indigo-500">Informasi Karyawan</h2>
                    <div class="flex flex-col sm:flex-row gap-y-2 sm:gap-x-3">
                        <div class="flex flex-col gap-1 w-1/2">
                            <label for="nip" class=" font-medium text-primary text-gray-800">NIP</label>
                            <input type="text" id="nip" name="nip" value="<?= $employee['nip'] ?>" class="border border-gray-300 rounded p-2 w-full text-gray-600 cursor-not-allowed" readonly />
                        </div>
                        <div class="flex flex-col gap-1 sm:w-full">
                            <label for="nama_lengkap" class="font-medium text-primary text-gray-800">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan Nama Lengkap" value="<?= $employee['nama_lengkap'] ?>" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-y-2 sm:gap-x-3">
                        <div class="flex gap-x-3 sm:w-2/3">
                            <div class="flex flex-col gap-1 w-1/2">
                                <label for="email" class="font-medium text-primary text-gray-800">Email</label>
                                <input type="email" id="email" name="email" placeholder="Masukkan Email" value="<?= $employee['email'] ?>" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" required/>
                            </div>
                            <div class="flex flex-col gap-1 w-1/2">
                                <label for="no_hp" class="font-medium text-primary text-gray-800">No HP</label>
                                <input type="text" id="no_hp" name="no_hp" placeholder="Masukkan No HP" value="<?= $employee['no_hp'] ?>" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" required/>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1 sm:w-1/3">
                            <label for="tanggal_lahir" class="font-medium text-primary text-gray-800">Tanggal Lahir</label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" placeholder="Masukkan Tanggal Lahir" value="<?= $employee['tanggal_lahir'] ?>" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" required/>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="alamat" class=" font-medium text-primary text-gray-800">Alamat</label>
                        <textarea id="alamat" name="alamat" placeholder="Masukkan Alamat" class="border border-gray-300 rounded p-2 w-full" required><?= $employee['alamat'] ?></textarea>
                    </div>
                </div>

                <!-- Step 2: Education Info -->
                <div x-show="step === 2" class="flex flex-col gap-2">
                    <h2 class="text-lg font-semibold">Informasi Pendidikan</h2>
                    <div class="flex flex-col gap-1">
                        <label for="pendidikan_terakhir" class="block">Pendidikan Terakhir</label>
                        <select id="pendidikan_terakhir" name="pendidikan_terakhir" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full">
                            <option value="">Pilih Pendidikan Terakhir</option>
                            <option value="SMA" <?= $EmpEdu['pendidikan_terakhir'] === 'SMA' ? 'selected' : '' ?>>SMA</option>
                            <option value="D3" <?= $EmpEdu['pendidikan_terakhir'] === 'D3' ? 'selected' : '' ?>>D3</option>
                            <option value="S1" <?= $EmpEdu['pendidikan_terakhir'] === 'S1' ? 'selected' : '' ?>>S1</option>
                            <option value="S2" <?= $EmpEdu['pendidikan_terakhir'] === 'S2' ? 'selected' : '' ?>>S2</option>
                            <option value="S3" <?= $EmpEdu['pendidikan_terakhir'] === 'S3' ? 'selected' : '' ?>>S3</option>
                        </select>
                    </div> 
                    <div class="flex flex-col gap-1">
                        <label for="nama_sekolah" class="block">Nama Instansi</label>
                        <input type="text" id="nama_sekolah" name="nama_sekolah" value="<?= $EmpEdu['nama_sekolah'] ?>"  required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="jurusan" class="block">Jurusan</label>
                        <input type="text" id="jurusan" name="jurusan" value="<?= $EmpEdu['jurusan'] ?>" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                </div>

                <!-- Step 3: Informasi Pekerjaan/Jabatan Karyawan -->
                <div x-show="step === 3" class="flex flex-col gap-2">
                    <h2 class="text-lg font-semibold text-indigo-500">Informasi Pekerjaan</h2>
                    <div class="flex flex-col gap-1">
                        <label for="jabatan" class="block">Jabatan </label>
                        <select id="jabatan" name="jabatan" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full">
                            <option value="">Pilih Jabatan</option>
                            <option value="Manager" <?= $EmpInfo['jabatan'] === 'Manager' ? 'selected' : '' ?>>Manager</option>
                            <option value="Supervisor" <?= $EmpInfo['jabatan'] === 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
                            <option value="Staff" <?= $EmpInfo['jabatan'] === 'Staff' ? 'selected' : '' ?>>Staff</option>
                            <option value="Intern" <?= $EmpInfo['jabatan'] === 'Intern' ? 'selected' : '' ?>>Intern</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="departemen" class="block">Departemen</label>
                        <select id="departemen" name="departemen" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full">
                            <option value="">Pilih Divisi</option>
                            <option value="IT" <?= $EmpInfo['departemen'] === 'IT' ? 'selected' : '' ?>>IT</option>
                            <option value="HR" <?= $EmpInfo['departemen'] === 'HR' ? 'selected' : '' ?>>HR</option>
                            <option value="Finance" <?= $EmpInfo['departemen'] === 'Finance' ? 'selected' : '' ?>>Finance</option>
                            <option value="Marketing" <?= $EmpInfo['departemen'] === 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                            <option value="Operations" <?= $EmpInfo['departemen'] === 'Operations' ? 'selected' : '' ?>>Operations</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="tanggal_masuk" class="block">Tanggal Masuk</label>
                        <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="<?= $EmpInfo['tanggal_masuk'] ?>" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                </div>

                <!-- Step 4 : Informasi terkait kontrak karyawan -->
                <div x-show="step === 4" x-data="kontrakForm()" class="flex flex-col gap-2">
                    <h2 class="text-lg font-semibold text-indigo-500">Informasi Kontrak</h2>
                    <div class="flex flex-col gap-1">
                        <label for="jenis_kontrak" class="block">Jenis Kontrak</label>
                        <select x-model="jenisKontrak" id="jenis_kontrak" name="jenis_kontrak" required class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full">
                            <option value="">Pilih Jenis Kontrak</option>
                            <option value="Tetap" <?= $EmpContract['jenis_kontrak'] === 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                            <option value="Kontrak" <?= $EmpContract['jenis_kontrak'] === 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1" x-show="jenisKontrak === 'Kontrak'" x-transition>
                        <label for="durasi_kontrak_bulan" class="block">Durasi Kontrak (bulan)</label>
                        <input type="number" x-model.number="durasiBulan" id="durasi_kontrak_bulan" name="durasi_kontrak_bulan" min="1"
                            value="<?= $EmpContract['durasi_kontrak_bulan'] ?>" class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                    <div class="flex flex-col gap-1" x-show="jenisKontrak === 'Kontrak'" x-transition>
                        <label for="tanggal_berakhir_kontrak" class="block">Tanggal Berakhir Kontrak</label>
                        <input type="date" id="tanggal_berakhir_kontrak" name="tanggal_berakhir_kontrak"
                            :value="tanggalBerakhir" readonly
                            class="cursor-not-allowed bg-gray-100 text-gray-500 outline outline-gray-300 transition-all duration-300 ease-in-out rounded p-2 w-full" />
                    </div>
                    <input type="hidden" name="durasi_kontrak_bulan" :value="jenisKontrak === 'Tetap' ? 'Tetap' : durasiBulan" />
                    <input type="hidden" name="tanggal_berakhir_kontrak" :value="jenisKontrak === 'Tetap' ? '-' : tanggalBerakhir" />
                </div>


                <div class="flex justify-between mt-4">
                    
                    <button 
                        type="button" 
                        @click="step > 1 ? step-- : null" 
                        :class="step === 1 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-indigo-400 hover:bg-indigo-500 text-white cursor-pointer'" 
                        :disabled="step === 1"
                        class="rounded px-4 py-2"
                    >
                        Kembali
                    </button>
                    
                    
                    <button 
                        type="button" 
                        @click="step < totalSteps ? step++ : null" 
                        class="bg-indigo-500 hover:bg-indigo-600 text-white rounded px-4 py-2 cursor-pointer" 
                        x-show="step < totalSteps"
                    >
                        Selanjutnya
                    </button>
                    
                    
                    <button 
                        type="submit" 
                        class="bg-indigo-500 hover:bg-indigo-600 text-white rounded px-4 py-2 cursor-pointer" 
                        x-show="step === totalSteps"
                    >
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" src="../../../../resources/js/dist/app.bundle.js"></script>
    <script type="module" src="../../../../resources/js/dist/fa.min.js"></script>
    <script>
    function kontrakForm() {
        return {
            jenisKontrak: '<?= $EmpContract['jenis_kontrak'] ?>', 
            durasiBulan: <?= $EmpContract['durasi_kontrak_bulan'] ?>, 
            tanggalMasuk: '<?= $EmpInfo['tanggal_masuk'] ?>', 
            get tanggalBerakhir() {
                if (this.jenisKontrak !== 'Kontrak' || !this.tanggalMasuk || !this.durasiBulan) return '';
                const masuk = new Date(this.tanggalMasuk);
                masuk.setMonth(masuk.getMonth() + parseInt(this.durasiBulan));
                return masuk.toISOString().split('T')[0]; 
            }
        }
    }
    </script>
</body>
</html>
