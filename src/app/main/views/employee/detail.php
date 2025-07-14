<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '../../../../../config/configuration.php';

if (!isset($_SESSION['employee_detail'])) {
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
    <title>DETAIL - <?= $employee['nip'] ?></title>
    <link rel="stylesheet" href="../../../../resources/style/style.css">
</head>
<body>
    <div class="flex flex-col items-center justify-center min-h-screen">
        <div class="flex flex-col w-5/6 bg-white shadow-md shadow-gray-300 rounded-lg overflow-hidden">
            <div class="flex items-center bg-indigo-500 px-3 sm:px-6 py-2 sm:py-4 w-full">
                <a href="./index.php">
                    <i class="fas fa-arrow-left text-xl text-white"></i>
                </a>
            </div>
            <div class="flex flex-col gap-y-3 p-4 sm:p-6 overflow-hidden border-r border-b border-l border-gray-300 h-[500px] sm:h-[600px]">
                <h1 class="text-lg text-center font-bold font-secondary flex-shrink-0">Detail Karyawan</h1>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-y-10 w-full overflow-y-auto flex-grow font-primary divide-y sm:divide-y-0 sm:divide-x divide-gray-300">
                    <!-- informasi umum karyawan -->
                    <div class="flex flex-col p-4 gap-y-3 sm:gap-y-4">
                        <h1 class="flex gap-x-2 items-center justify-center mb-2">
                            <i class="fas fa-user text-indigo-500 text-lg"></i>
                            <span class="font-bold font-primary text-lg text-indigo-500">Karyawan</span>
                            <i class="fas fa-user text-indigo-500 text-lg"></i>
                        </h1>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-user text-indigo-500 inline mr-2"></i>Nama</h3>
                            <p class="text-gray-600"><?= $employee['nama_lengkap'] ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="<?= $employee['jenis_kelamin'] === 'Laki-laki' ? 'fas fa-mars' : 'fas fa-venus' ?> text-indigo-500 inline mr-2"></i>Jenis Kelamin</h3>
                            <p class="text-gray-600"><?= $employee['jenis_kelamin'] ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-birthday-cake text-indigo-500 inline mr-2"></i>Tanggal Lahir</h3>
                            <p class="text-gray-600"><?= date('d-m-Y', strtotime($employee['tanggal_lahir'])) ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-envelope text-indigo-500 inline mr-2"></i>Email</h3>
                            <p class="text-gray-600"><?= $employee['email'] ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-phone text-indigo-500 inline mr-2"></i></i>Nomor Handphone</h3>
                            <p class="text-gray-600"><?= $employee['no_hp'] ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-home text-indigo-500 inline mr-2"></i>Alamat</h3>
                            <p class="text-justify text-gray-600"><?= $employee['alamat'] ?? '-' ?></p>
                        </div>
                    </div>

                    <!-- informasi pendidikan karyawan -->
                    <div class="flex flex-col gap-y-3 p-4 sm:gap-y-4">
                        <h1 class="flex gap-x-2 items-center justify-center mb-2">
                            <i class="fas fa-graduation-cap text-indigo-500 text-lg"></i>
                            <span class="font-bold font-primary text-lg text-indigo-500">Pendidikan</span>
                            <i class="fas fa-graduation-cap text-indigo-500 text-lg"></i>
                        </h1>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-star text-indigo-500 inline mr-2"></i>Pendidikan Terakhir</h3>
                            <p class="text-gray-600">
                                <?php
                                    if ($EmpEdu['pendidikan_terakhir'] === 'SD') {
                                        echo 'Sekolah Dasar';
                                    } elseif ($EmpEdu['pendidikan_terakhir'] === 'SMP') {
                                        echo 'Sekolah Menengah Pertama';
                                    } elseif ($EmpEdu['pendidikan_terakhir'] === 'SMA') {
                                        echo 'SLTA / Sederajat';
                                    } elseif ($EmpEdu['pendidikan_terakhir'] === 'D3') {
                                        echo 'Diploma 3';
                                    } elseif ($EmpEdu['pendidikan_terakhir'] === 'S1') {
                                        echo 'Strata 1';
                                    } elseif ($EmpEdu['pendidikan_terakhir'] === 'S2') {
                                        echo 'Strata 2';
                                    } elseif ($EmpEdu['pendidikan_terakhir'] === 'S3') {
                                        echo 'Strata 3';
                                    }
?>
                            </p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-school text-indigo-500 inline mr-2"></i>Nama Instansi</h3>
                            <p class="text-gray-600"><?= $EmpEdu['nama_sekolah'] ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-compass text-indigo-500 inline mr-2"></i>Jurusan</h3>
                            <p class="text-gray-600"><?= $EmpEdu['jurusan'] ?></p>
                        </div>
                    </div>

                    <!-- informasi pekerjaan karyawan -->
                    <div class="flex flex-col gap-y-3 p-4 sm:gap-y-4">
                        <h1 class="flex gap-x-2 items-center justify-center mb-2">
                            <i class="fas fa-user-tie text-indigo-500 text-lg"></i>
                            <span class="font-bold font-primary text-lg text-indigo-500">Informasi Pekerjaan</span>
                            <i class="fas fa-user-tie text-indigo-500 text-lg"></i>
                        </h1>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-tag text-indigo-500 inline mr-2"></i>NIP</h3>
                            <p class="text-gray-600"><?= $employee['nip'] ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-puzzle-piece text-indigo-500 inline mr-2"></i>Jabatan</h3>
                            <p class="text-gray-600"><?= $EmpInfo['jabatan'] ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-user-tag text-indigo-500 inline mr-2"></i>Departemen</h3>
                            <p class="text-gray-600"><?= $EmpInfo['departemen'] ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-user-check text-indigo-500 inline mr-2"></i>Status</h3>
                            <p class="text-gray-600"><?= $EmpInfo['status_karyawan'] ?></p>
                        </div>
                    </div>

                    <!-- informasi pekerjaan karyawan -->
                    <div class="flex flex-col gap-y-3 p-4 sm:gap-y-4">
                        <h1 class="flex gap-x-2 items-center justify-center mb-2">
                            <i class="fas fa-clock text-indigo-500 text-lg"></i>
                            <span class="font-bold font-primary text-lg text-indigo-500">Kontrak Pekerjaan</span>
                            <i class="fas fa-clock text-indigo-500 text-lg"></i>
                        </h1>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-calendar text-indigo-500 inline mr-2"></i>Rentang Tanggal</h3>
                            <p class="text-gray-600"><?= date('d/m/Y', strtotime($EmpInfo['tanggal_masuk'])) ?> - <?= date('d/m/Y', strtotime($EmpContract['tanggal_berakhir_kontrak'])) ?></p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-calendar-week text-indigo-500 inline mr-2"></i>Durasi</h3>
                            <p class="text-gray-600"><?= $EmpContract['durasi_kontrak_bulan'] ?> (bulan)</p>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="font-medium text-gray-900"><i class="fas fa-sign-hanging text-indigo-500 inline mr-2"></i>Jenis Kontrak</h3>
                            <p class="text-gray-600"><?= $EmpContract['jenis_kontrak'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="../../../../resources/js/dist/app.bundle.js"></script>
    <script type="module" src="../../../../resources/js/dist/fa.min.js"></script>
</body>
</html>