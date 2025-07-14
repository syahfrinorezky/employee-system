<?php
session_start();

require_once __DIR__ . '../../../../../config/configuration.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/views/login.php");
    exit();
}

$username = $_SESSION['user']['username'];
$user_email = $_SESSION['user']['email'];
$user_id = $_SESSION['user']['id'];

// Ambil ID absensi yang akan diedit
$attendance_id = $_GET['id'] ?? '';

if (empty($attendance_id) || !is_numeric($attendance_id)) {
    $_SESSION['error'] = 'ID absensi tidak valid';
    header("Location: ./index.php");
    exit();
}

// Ambil data absensi
$query = "SELECT a.*, e.nip, e.nama_lengkap 
          FROM attendances a 
          INNER JOIN employees e ON a.karyawan_id = e.id 
          WHERE a.id = ? AND a.user_id = ? AND e.deleted_at IS NULL";

$stmt = $connection->prepare($query);
$stmt->bind_param('ii', $attendance_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Data absensi tidak ditemukan';
    header("Location: ./index.php");
    exit();
}

$attendance = $result->fetch_assoc();

// Ambil data karyawan untuk dropdown
$employees_query = "SELECT id, nip, nama_lengkap FROM employees WHERE user_id = ? AND deleted_at IS NULL ORDER BY nama_lengkap";
$employees_stmt = $connection->prepare($employees_query);
$employees_stmt->bind_param('i', $user_id);
$employees_stmt->execute();
$employees_result = $employees_stmt->get_result();

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success']);
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Absensi - STAFFY</title>
    <link rel="stylesheet" href="../../../../resources/style/style.css">
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body x-data="{ slideBarOpen: false, logoutModal: false }">

    <?php include __DIR__ . '../../../../components/header.php'; ?>

    <div x-cloak>
        <?php include __DIR__ . '../../../../components/logout_modal.php' ?>
    </div>

    <div x-cloak>
        <?php include __DIR__ . '../../../../components/slidebar.php'; ?>
    </div>

    <div class="flex bg-white">
        <?php include __DIR__ . '../../../../components/sidebar.php'; ?>
        
        <div class="flex flex-col gap-7 w-full sm:overflow-y-auto p-4 sm:p-8 mt-20 sm:mt-12">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold font-secondary text-gray-800">Edit <span class="text-indigo-500">Absensi</span></h1>
                <a href="./index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <!-- Success/Error Messages -->
                <?php if (!empty($success)): ?>
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 p-3 flex items-center gap-x-2 bg-green-500/40 rounded-lg border border-green-500">
                        <i class="fa-solid fa-check text-white text-sm"></i>
                        <p class="text-sm text-white"><?= $success ?></p>
                        <button @click="show = false" class="ml-auto text-white hover:text-gray-200">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 p-3 flex items-center gap-x-2 bg-red-500/40 rounded-lg border border-red-500">
                        <i class="fa-solid fa-exclamation-triangle text-white text-sm"></i>
                        <p class="text-sm text-white"><?= $error ?></p>
                        <button @click="show = false" class="ml-auto text-white hover:text-gray-200">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <form action="../../process/attedit_process.php" method="POST">
                    <input type="hidden" name="id" value="<?= $attendance['id'] ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                            <select name="karyawan_id" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Pilih Karyawan</option>
                                <?php while ($employee = $employees_result->fetch_assoc()): ?>
                                    <option value="<?= $employee['id'] ?>" <?= $employee['id'] == $attendance['karyawan_id'] ? 'selected' : '' ?>>
                                        <?= $employee['nip'] ?> - <?= $employee['nama_lengkap'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" name="tanggal" value="<?= $attendance['tanggal'] ?>" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Kehadiran</label>
                            <select name="status_kehadiran" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Pilih Status</option>
                                <option value="Hadir" <?= $attendance['status_kehadiran'] == 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                                <option value="Terlambat" <?= $attendance['status_kehadiran'] == 'Terlambat' ? 'selected' : '' ?>>Terlambat</option>
                                <option value="Tidak Hadir" <?= $attendance['status_kehadiran'] == 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                                <option value="Sakit" <?= $attendance['status_kehadiran'] == 'Sakit' ? 'selected' : '' ?>>Sakit</option>
                                <option value="Izin" <?= $attendance['status_kehadiran'] == 'Izin' ? 'selected' : '' ?>>Izin</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                            <input type="time" name="jam_masuk" value="<?= date('H:i', strtotime($attendance['jam_masuk'])) ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Keluar</label>
                            <input type="time" name="jam_keluar" value="<?= date('H:i', strtotime($attendance['jam_keluar'])) ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Keterangan tambahan..."><?= $attendance['keterangan'] ?></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-8">
                        <a href="../../views/attendance/index.php" class="px-6 py-3 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-3 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                            <i class="fa-solid fa-save mr-2"></i>Update Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="module" src="../../../../resources/js/dist/app.bundle.js"></script>
    <script type="module" src="../../../../resources/js/dist/fa.min.js"></script>
</body>
</html>