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

if (!isset($_SESSION['employee_detail'])) {
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
    <title>Detail Karyawan - STAFFY</title>
    <link rel="stylesheet" href="../../../../resources/style/style.css">
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body x-data="{ 
    slideBarOpen : false, 
    logoutModal : false
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
                <h1 class="text-2xl font-bold font-secondary text-gray-800">Detail <span class="text-indigo-500">Karyawan</span></h1>
                <a href="./index.php" class="flex items-center gap-x-2 p-2 sm:p-3 rounded-lg text-white bg-gray-500 hover:bg-gray-600">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                    <span class="hidden sm:block">Kembali</span>
                </a>
            </div>

            <div class="bg-white rounded-lg border border-gray-300 shadow-md overflow-hidden">
                <div class="p-4 bg-indigo-500 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user text-white text-lg"></i>
                        <h2 class="text-sm sm:text-lg font-medium font-primary text-white"><?= $employee['nip'] ?></h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-indigo-500 text-sm bg-white bg-opacity-20 px-2 py-1 rounded">
                            <?= $EmpInfo['jabatan'] ?>
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6">
                        <!-- Informasi Umum Karyawan -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <h3 class="text-lg font-semibold text-indigo-500 border-b border-gray-200 pb-2 mb-4">
                                <i class="fa-solid fa-user mr-2"></i>Informasi Umum
                            </h3>
                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Nama Lengkap</label>
                                    <p class="text-gray-800 font-medium"><?= $employee['nama_lengkap'] ?></p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Jenis Kelamin</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="<?= $employee['jenis_kelamin'] === 'Laki-laki' ? 'fa-solid fa-mars text-blue-500' : 'fa-solid fa-venus text-pink-500' ?>"></i>
                                        <?= $employee['jenis_kelamin'] ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Tanggal Lahir</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-birthday-cake text-indigo-500"></i>
                                        <?= date('d F Y', strtotime($employee['tanggal_lahir'])) ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Email</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-envelope text-indigo-500"></i>
                                        <?= $employee['email'] ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">No. Handphone</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-phone text-indigo-500"></i>
                                        <?= $employee['no_hp'] ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Alamat</label>
                                    <p class="text-gray-800 flex items-start gap-2">
                                        <i class="fa-solid fa-home text-indigo-500 mt-1"></i>
                                        <span class="text-justify"><?= $employee['alamat'] ?? '-' ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pendidikan -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <h3 class="text-lg font-semibold text-indigo-500 border-b border-gray-200 pb-2 mb-4">
                                <i class="fa-solid fa-graduation-cap mr-2"></i>Pendidikan
                            </h3>
                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Pendidikan Terakhir</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-star text-indigo-500"></i>
                                        <?php
                                            $pendidikan = [
                                                'SD' => 'Sekolah Dasar',
                                                'SMP' => 'Sekolah Menengah Pertama',
                                                'SMA' => 'SLTA / Sederajat',
                                                'D3' => 'Diploma 3',
                                                'S1' => 'Strata 1',
                                                'S2' => 'Strata 2',
                                                'S3' => 'Strata 3'
                                            ];
                                            echo $pendidikan[$EmpEdu['pendidikan_terakhir']] ?? $EmpEdu['pendidikan_terakhir'];
                                        ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Nama Instansi</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-school text-indigo-500"></i>
                                        <?= $EmpEdu['nama_sekolah'] ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Jurusan</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-compass text-indigo-500"></i>
                                        <?= $EmpEdu['jurusan'] ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pekerjaan -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <h3 class="text-lg font-semibold text-indigo-500 border-b border-gray-200 pb-2 mb-4">
                                <i class="fa-solid fa-briefcase mr-2"></i>Pekerjaan
                            </h3>
                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">NIP</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-id-badge text-indigo-500"></i>
                                        <?= $employee['nip'] ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Jabatan</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-user-tie text-indigo-500"></i>
                                        <?= $EmpInfo['jabatan'] ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Departemen</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-building text-indigo-500"></i>
                                        <?= $EmpInfo['departemen'] ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Status Karyawan</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-user-check text-indigo-500"></i>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $EmpInfo['status_karyawan'] === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= $EmpInfo['status_karyawan'] ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Tanggal Masuk</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-plus text-indigo-500"></i>
                                        <?= date('d F Y', strtotime($EmpInfo['tanggal_masuk'])) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kontrak -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <h3 class="text-lg font-semibold text-indigo-500 border-b border-gray-200 pb-2 mb-4">
                                <i class="fa-solid fa-file-contract mr-2"></i>Kontrak
                            </h3>
                            <div class="space-y-4">
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Jenis Kontrak</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-file-signature text-indigo-500"></i>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $EmpContract['jenis_kontrak'] === 'Tetap' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                            <?= $EmpContract['jenis_kontrak'] ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Durasi Kontrak</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-clock text-indigo-500"></i>
                                        <?= $EmpContract['durasi_kontrak_bulan'] === 'Tetap' ? 'Tidak terbatas' : $EmpContract['durasi_kontrak_bulan'] . ' bulan' ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Tanggal Mulai</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-check text-indigo-500"></i>
                                        <?= date('d F Y', strtotime($EmpInfo['tanggal_masuk'])) ?>
                                    </p>
                                </div>
                                <div class="flex flex-col">
                                    <label class="text-sm font-medium text-gray-600">Tanggal Berakhir</label>
                                    <p class="text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-times text-indigo-500"></i>
                                        <?= $EmpContract['tanggal_berakhir_kontrak'] === '-' ? 'Tidak terbatas' : date('d F Y', strtotime($EmpContract['tanggal_berakhir_kontrak'])) ?>
                                    </p>
                                </div>
                                <?php if ($EmpContract['jenis_kontrak'] === 'Kontrak'): ?>
                                    <div class="flex flex-col">
                                        <label class="text-sm font-medium text-gray-600">Sisa Kontrak</label>
                                        <p class="text-gray-800 flex items-center gap-2">
                                            <i class="fa-solid fa-hourglass-half text-indigo-500"></i>
                                            <?php
                                                $sekarang = new DateTime();
                                                $berakhir = new DateTime($EmpContract['tanggal_berakhir_kontrak']);
                                                $selisih = $sekarang->diff($berakhir);
                                                
                                                if ($berakhir < $sekarang) {
                                                    echo '<span class="text-red-600 font-medium">Sudah berakhir</span>';
                                                } else {
                                                    echo $selisih->format('%a hari');
                                                }
                                            ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <a href="./edit.php?nip=<?= $employee['nip'] ?>" class="flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300">
                            <i class="fa-solid fa-edit"></i>
                            <span class="hidden sm:block">Edit Karyawan</span>
                        </a>
                        <button onclick="window.print()" class="flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300">
                            <i class="fa-solid fa-print"></i>
                            <span class="hidden sm:block">Cetak</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="../../../../resources/js/dist/app.bundle.js"></script>
    <script type="module" src="../../../../resources/js/dist/fa.min.js"></script>
</body>
</html>