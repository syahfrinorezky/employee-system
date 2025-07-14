<?php
session_start();

require_once __DIR__ . '/../../../config/configuration.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/views/login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Cek apakah request method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Metode request tidak valid';
    header("Location: ../views/attendance/index.php");
    exit();
}

// Ambil data dari form
$attendance_id = $_POST['id'] ?? '';
$karyawan_id = $_POST['karyawan_id'] ?? '';
$tanggal = $_POST['tanggal'] ?? '';
$status_kehadiran = $_POST['status_kehadiran'] ?? '';
$jam_masuk = $_POST['jam_masuk'] ?? '';
$jam_keluar = $_POST['jam_keluar'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

// Validasi input
if (empty($attendance_id) || !is_numeric($attendance_id)) {
    $_SESSION['error'] = 'ID absensi tidak valid';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

if (empty($karyawan_id) || !is_numeric($karyawan_id)) {
    $_SESSION['error'] = 'Karyawan harus dipilih';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

if (empty($tanggal)) {
    $_SESSION['error'] = 'Tanggal harus diisi';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

if (empty($status_kehadiran)) {
    $_SESSION['error'] = 'Status kehadiran harus dipilih';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

// Validasi tanggal
$date_obj = DateTime::createFromFormat('Y-m-d', $tanggal);
if (!$date_obj || $date_obj->format('Y-m-d') !== $tanggal) {
    $_SESSION['error'] = 'Format tanggal tidak valid';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

// Validasi jam masuk dan keluar jika diisi
if (!empty($jam_masuk)) {
    $time_obj = DateTime::createFromFormat('H:i', $jam_masuk);
    if (!$time_obj || $time_obj->format('H:i') !== $jam_masuk) {
        $_SESSION['error'] = 'Format jam masuk tidak valid';
        header("Location: " . $_SERVER['HTTP_REFERER']); 
        exit();
    }
}

if (!empty($jam_keluar)) {
    $time_obj = DateTime::createFromFormat('H:i', $jam_keluar);
    if (!$time_obj || $time_obj->format('H:i') !== $jam_keluar) {
        $_SESSION['error'] = 'Format jam keluar tidak valid';
        header("Location: " . $_SERVER['HTTP_REFERER']); 
        exit();
    }
}

// Validasi jam keluar tidak boleh lebih awal dari jam masuk
if (!empty($jam_masuk) && !empty($jam_keluar)) {
    if ($jam_keluar <= $jam_masuk) {
        $_SESSION['error'] = 'Jam keluar tidak boleh lebih awal dari jam masuk';
        header("Location: " . $_SERVER['HTTP_REFERER']); 
        exit();
    }
}

// Validasi status kehadiran
$valid_statuses = ['Hadir', 'Terlambat', 'Tidak Hadir', 'Sakit', 'Izin'];
if (!in_array($status_kehadiran, $valid_statuses)) {
    $_SESSION['error'] = 'Status kehadiran tidak valid';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

// Cek apakah data absensi ada dan milik user yang login
$check_query = "SELECT id FROM attendances WHERE id = ? AND user_id = ?";
$check_stmt = $connection->prepare($check_query);
$check_stmt->bind_param('ii', $attendance_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $_SESSION['error'] = 'Data absensi tidak ditemukan atau tidak memiliki akses';
    header("Location: ../views/attendance/index.php");
    exit();
}

// Cek apakah karyawan ada dan milik user yang login
$employee_query = "SELECT id FROM employees WHERE id = ? AND user_id = ? AND deleted_at IS NULL";
$employee_stmt = $connection->prepare($employee_query);
$employee_stmt->bind_param('ii', $karyawan_id, $user_id);
$employee_stmt->execute();
$employee_result = $employee_stmt->get_result();

if ($employee_result->num_rows === 0) {
    $_SESSION['error'] = 'Karyawan tidak ditemukan atau tidak memiliki akses';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

// Cek duplikasi absensi (karyawan yang sama pada tanggal yang sama, kecuali data yang sedang diedit)
$duplicate_query = "SELECT id FROM attendances WHERE karyawan_id = ? AND tanggal = ? AND id != ? AND user_id = ?";
$duplicate_stmt = $connection->prepare($duplicate_query);
$duplicate_stmt->bind_param('isii', $karyawan_id, $tanggal, $attendance_id, $user_id);
$duplicate_stmt->execute();
$duplicate_result = $duplicate_stmt->get_result();

if ($duplicate_result->num_rows > 0) {
    $_SESSION['error'] = 'Absensi karyawan pada tanggal tersebut sudah ada';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

// Bersihkan input
$keterangan = trim($keterangan);
if (strlen($keterangan) > 500) {
    $_SESSION['error'] = 'Keterangan maksimal 500 karakter';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

// Konversi jam kosong ke NULL
$jam_masuk = empty($jam_masuk) ? NULL : $jam_masuk;
$jam_keluar = empty($jam_keluar) ? NULL : $jam_keluar;
$keterangan = empty($keterangan) ? NULL : $keterangan;

try {
    // Start transaction
    $connection->begin_transaction();

    // Update data absensi
    $update_query = "UPDATE attendances SET 
                        karyawan_id = ?, 
                        tanggal = ?, 
                        status_kehadiran = ?, 
                        jam_masuk = ?, 
                        jam_keluar = ?, 
                        keterangan = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ? AND user_id = ?";

    $update_stmt = $connection->prepare($update_query);
    $update_stmt->bind_param('isssssii', $karyawan_id, $tanggal, $status_kehadiran, $jam_masuk, $jam_keluar, $keterangan, $attendance_id, $user_id);

    if (!$update_stmt->execute()) {
        throw new Exception('Gagal mengupdate data absensi');
    }

    // Check if any row was actually updated
    if ($update_stmt->affected_rows === 0) {
        throw new Exception('Tidak ada perubahan data atau data tidak ditemukan');
    }

    // Commit transaction
    $connection->commit();

    $_SESSION['success'] = 'Data absensi berhasil diperbarui';
    header("Location: ../views/attendance/index.php");
    exit();

} catch (Exception $e) {
    // Rollback transaction
    $connection->rollback();

    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}
?>