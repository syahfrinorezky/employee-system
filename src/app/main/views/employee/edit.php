<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '../../../../../config/configuration.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/views/login.php");
    exit();
}

if (!isset($_SESSION['employee_edit'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$username = $_SESSION['user']['username'];
$user_email = $_SESSION['user']['email'];
$user_id = $_SESSION['user']['id'];

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
    <title>Edit Karyawan - STAFFY</title>
    <link rel="stylesheet" href="../../../../resources/style/style.css">
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body x-data="{ 
    slideBarOpen : false, 
    logoutModal : false,
    step: 1, 
    totalSteps: 4 
}">

    <?php include __DIR__ . '../../../../components/header.php'; ?>

    <div x-cloak >
        <?php include __DIR__ . '../../../../components/logout_modal.php' ?>
    </div>

    <div x-cloak >
        <?php include __DIR__ . '../../../../components/slidebar.php'; ?>
    </div>

    <div class="flex bg-white">
        <?php include __DIR__ . '../../../../components/sidebar.php'; ?>
        
        <div class="flex flex-col gap-7 w-full sm:overflow-y-auto p-4 sm:p-8 mt-20 sm:mt-12">
            <div class="flex justify-between items-center gap-2">
                <h1 class="text-2xl font-bold font-secondary text-gray-800">Edit <span class="text-indigo-500">Karyawan</span></h1>
                <a href="./index.php" class="flex items-center gap-x-2 p-2 sm:p-3 rounded-lg text-white bg-gray-500 hover:bg-gray-600">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                    Kembali
                </a>
            </div>

            <div class="bg-white rounded-lg border border-gray-300 shadow-md overflow-hidden">
                <div class="p-4 bg-indigo-500 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user-pen text-white text-lg"></i>
                        <h2 class="text-sm sm:text-lg font-medium font-primary text-white"><?= $employee['nip'] ?></h2>
                    </div>
                    <div class="text-white text-sm">
                        <span x-text="step"></span> / <span x-text="totalSteps"></span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 h-2">
                    <div class="bg-green-500 h-2 transition-all duration-300 ease-in-out" 
                         :style="'width: ' + (step / totalSteps * 100) + '%'"></div>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <form action="../../process/empedit_process.php" method="post" class="flex flex-col gap-4">
                        <!-- Step 1: Employee Information -->
                        <div x-show="step === 1" class="flex flex-col gap-4">
                            <h3 class="text-xl font-semibold text-indigo-500 border-b border-gray-200 pb-2">
                                <i class="fa-solid fa-user mr-2"></i>Informasi Karyawan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label for="nip" class="font-medium text-gray-800">NIP</label>
                                    <input type="text" id="nip" name="nip" value="<?= $employee['nip'] ?>" 
                                           class="border border-gray-300 rounded-lg p-3 w-full text-gray-600 cursor-not-allowed bg-gray-50" readonly />
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label for="nama_lengkap" class="font-medium text-gray-800">Nama Lengkap</label>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan Nama Lengkap" 
                                           value="<?= $employee['nama_lengkap'] ?>" required 
                                           class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label for="email" class="font-medium text-gray-800">Email</label>
                                    <input type="email" id="email" name="email" placeholder="Masukkan Email" 
                                           value="<?= $employee['email'] ?>" required
                                           class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full" />
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label for="no_hp" class="font-medium text-gray-800">No HP</label>
                                    <input type="text" id="no_hp" name="no_hp" placeholder="Masukkan No HP" 
                                           value="<?= $employee['no_hp'] ?>" required
                                           class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full" />
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label for="tanggal_lahir" class="font-medium text-gray-800">Tanggal Lahir</label>
                                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" 
                                           value="<?= $employee['tanggal_lahir'] ?>" required
                                           class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full" />
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="alamat" class="font-medium text-gray-800">Alamat</label>
                                <textarea id="alamat" name="alamat" placeholder="Masukkan Alamat" rows="3"
                                          class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full" required><?= $employee['alamat'] ?></textarea>
                            </div>
                        </div>

                        <!-- Step 2: Education Info -->
                        <div x-show="step === 2" class="flex flex-col gap-4">
                            <h3 class="text-xl font-semibold text-indigo-500 border-b border-gray-200 pb-2">
                                <i class="fa-solid fa-graduation-cap mr-2"></i>Informasi Pendidikan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label for="pendidikan_terakhir" class="font-medium text-gray-800">Pendidikan Terakhir</label>
                                    <select id="pendidikan_terakhir" name="pendidikan_terakhir" required 
                                            class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full">
                                        <option value="">Pilih Pendidikan Terakhir</option>
                                        <option value="SMA" <?= $EmpEdu['pendidikan_terakhir'] === 'SMA' ? 'selected' : '' ?>>SMA</option>
                                        <option value="D3" <?= $EmpEdu['pendidikan_terakhir'] === 'D3' ? 'selected' : '' ?>>D3</option>
                                        <option value="S1" <?= $EmpEdu['pendidikan_terakhir'] === 'S1' ? 'selected' : '' ?>>S1</option>
                                        <option value="S2" <?= $EmpEdu['pendidikan_terakhir'] === 'S2' ? 'selected' : '' ?>>S2</option>
                                        <option value="S3" <?= $EmpEdu['pendidikan_terakhir'] === 'S3' ? 'selected' : '' ?>>S3</option>
                                    </select>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label for="nama_sekolah" class="font-medium text-gray-800">Nama Instansi</label>
                                    <input type="text" id="nama_sekolah" name="nama_sekolah" placeholder="Masukkan Nama Instansi"
                                           value="<?= $EmpEdu['nama_sekolah'] ?>" required 
                                           class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full" />
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="jurusan" class="font-medium text-gray-800">Jurusan</label>
                                <input type="text" id="jurusan" name="jurusan" placeholder="Masukkan Jurusan"
                                       value="<?= $EmpEdu['jurusan'] ?>" required 
                                       class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full" />
                            </div>
                        </div>

                        <!-- Step 3: Informasi Pekerjaan/Jabatan Karyawan -->
                        <div x-show="step === 3" class="flex flex-col gap-4">
                            <h3 class="text-xl font-semibold text-indigo-500 border-b border-gray-200 pb-2">
                                <i class="fa-solid fa-briefcase mr-2"></i>Informasi Pekerjaan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label for="jabatan" class="font-medium text-gray-800">Jabatan</label>
                                    <select id="jabatan" name="jabatan" required 
                                            class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full">
                                        <option value="">Pilih Jabatan</option>
                                        <option value="Manager" <?= $EmpInfo['jabatan'] === 'Manager' ? 'selected' : '' ?>>Manager</option>
                                        <option value="Supervisor" <?= $EmpInfo['jabatan'] === 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
                                        <option value="Staff" <?= $EmpInfo['jabatan'] === 'Staff' ? 'selected' : '' ?>>Staff</option>
                                        <option value="Intern" <?= $EmpInfo['jabatan'] === 'Intern' ? 'selected' : '' ?>>Intern</option>
                                    </select>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label for="departemen" class="font-medium text-gray-800">Departemen</label>
                                    <select id="departemen" name="departemen" required 
                                            class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full">
                                        <option value="">Pilih Departemen</option>
                                        <option value="IT" <?= $EmpInfo['departemen'] === 'IT' ? 'selected' : '' ?>>IT</option>
                                        <option value="HR" <?= $EmpInfo['departemen'] === 'HR' ? 'selected' : '' ?>>HR</option>
                                        <option value="Finance" <?= $EmpInfo['departemen'] === 'Finance' ? 'selected' : '' ?>>Finance</option>
                                        <option value="Marketing" <?= $EmpInfo['departemen'] === 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                                        <option value="Operations" <?= $EmpInfo['departemen'] === 'Operations' ? 'selected' : '' ?>>Operations</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="tanggal_masuk" class="font-medium text-gray-800">Tanggal Masuk</label>
                                <input type="date" id="tanggal_masuk" name="tanggal_masuk" 
                                       value="<?= $EmpInfo['tanggal_masuk'] ?>" required 
                                       class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full" />
                            </div>
                        </div>

                        <!-- Step 4: Informasi terkait kontrak karyawan -->
                        <div x-show="step === 4" x-data="kontrakForm()" class="flex flex-col gap-4">
                            <h3 class="text-xl font-semibold text-indigo-500 border-b border-gray-200 pb-2">
                                <i class="fa-solid fa-file-contract mr-2"></i>Informasi Kontrak
                            </h3>
                            <div class="flex flex-col gap-2">
                                <label for="jenis_kontrak" class="font-medium text-gray-800">Jenis Kontrak</label>
                                <select x-model="jenisKontrak" id="jenis_kontrak" name="jenis_kontrak" required 
                                        class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full">
                                    <option value="">Pilih Jenis Kontrak</option>
                                    <option value="Tetap" <?= $EmpContract['jenis_kontrak'] === 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                    <option value="Kontrak" <?= $EmpContract['jenis_kontrak'] === 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-2" x-show="jenisKontrak === 'Kontrak'" x-transition>
                                <label for="durasi_kontrak_bulan" class="font-medium text-gray-800">Durasi Kontrak (bulan)</label>
                                <input type="number" x-model.number="durasiBulan" id="durasi_kontrak_bulan" name="durasi_kontrak_bulan" 
                                       min="1" value="<?= $EmpContract['durasi_kontrak_bulan'] ?>" 
                                       class="outline outline-gray-300 hover:outline-indigo-500 focus:outline-indigo-500 transition-all duration-300 ease-in-out rounded-lg p-3 w-full" />
                            </div>
                            <div class="flex flex-col gap-2" x-show="jenisKontrak === 'Kontrak'" x-transition>
                                <label for="tanggal_berakhir_kontrak" class="font-medium text-gray-800">Tanggal Berakhir Kontrak</label>
                                <input type="date" id="tanggal_berakhir_kontrak" name="tanggal_berakhir_kontrak"
                                       :value="tanggalBerakhir" readonly
                                       class="cursor-not-allowed bg-gray-100 text-gray-500 border border-gray-300 rounded-lg p-3 w-full" />
                            </div>
                            <input type="hidden" name="durasi_kontrak_bulan" :value="jenisKontrak === 'Tetap' ? 'Tetap' : durasiBulan" />
                            <input type="hidden" name="tanggal_berakhir_kontrak" :value="jenisKontrak === 'Tetap' ? '-' : tanggalBerakhir" />
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <button 
                                type="button" 
                                @click="step > 1 ? step-- : null" 
                                :class="step === 1 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-indigo-500 hover:bg-indigo-600 text-white cursor-pointer'" 
                                :disabled="step === 1"
                                class="flex items-center gap-2 px-6 py-3 rounded-lg font-medium transition-all duration-300"
                            >
                                <i class="fa-solid fa-arrow-left"></i>
                                <span class="hidden sm:block">Kembali</span>
                            </button>
                            
                            <div class="flex items-center gap-2">
                                <template x-for="i in totalSteps" :key="i">
                                    <div :class="i <= step ? 'bg-indigo-500' : 'bg-gray-300'" 
                                         class="w-3 h-3 rounded-full transition-all duration-300"></div>
                                </template>
                            </div>
                            
                            <button 
                                type="button" 
                                @click="step < totalSteps ? step++ : null" 
                                class="flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium cursor-pointer transition-all duration-300" 
                                x-show="step < totalSteps"
                            >
                                <span class="hidden sm:block">Selanjutnya</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                            
                            <button 
                                type="submit" 
                                class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium cursor-pointer transition-all duration-300" 
                                x-show="step === totalSteps"
                            >
                                <i class="fa-solid fa-save"></i>
                                <span class="hidden sm:block">Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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