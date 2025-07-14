<?php

session_start();

require_once __DIR__ . '../../../config/configuration.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/views/login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Cek apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Metode request tidak valid';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Ambil data dari form
$karyawan_id = $_POST['karyawan_id'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

// Validasi input
if (empty($karyawan_id)) {
    $_SESSION['error'] = 'Karyawan harus dipilih';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Cek apakah karyawan ada dan milik user yang login
$employee_check = "SELECT id, nama_lengkap FROM employees WHERE id = ? AND user_id = ? AND deleted_at IS NULL";
$employee_stmt = $connection->prepare($employee_check);
$employee_stmt->bind_param('ii', $karyawan_id, $user_id);
$employee_stmt->execute();
$employee_result = $employee_stmt->get_result();

if ($employee_result->num_rows === 0) {
    $_SESSION['error'] = 'Karyawan tidak ditemukan';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$employee = $employee_result->fetch_assoc();

// Ambil tanggal dan jam saat ini
$tanggal_sekarang = date('Y-m-d');
$jam_sekarang = date('H:i:s');

// Cek apakah ada absensi untuk karyawan hari ini
$check_attendance = "SELECT id, jam_masuk, jam_keluar FROM attendances WHERE karyawan_id = ? AND tanggal = ? AND user_id = ?";
$check_stmt = $connection->prepare($check_attendance);
$check_stmt->bind_param('isi', $karyawan_id, $tanggal_sekarang, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $_SESSION['error'] = 'Karyawan ' . $employee['nama_lengkap'] . ' belum melakukan clock in hari ini';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$attendance = $check_result->fetch_assoc();

// Cek apakah sudah clock in
if (empty($attendance['jam_masuk'])) {
    $_SESSION['error'] = 'Karyawan ' . $employee['nama_lengkap'] . ' belum melakukan clock in hari ini';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Cek apakah sudah clock out
if (!empty($attendance['jam_keluar'])) {
    $_SESSION['error'] = 'Karyawan ' . $employee['nama_lengkap'] . ' sudah melakukan clock out hari ini';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Konversi keterangan kosong ke null
$keterangan = empty($keterangan) ? null : $keterangan;

// Update jam keluar
$update_query = "UPDATE attendances SET jam_keluar = ?, keterangan = CASE WHEN keterangan IS NULL THEN ? ELSE CONCAT(keterangan, ' | ', ?) END, updated_at = NOW() WHERE id = ?";
$update_stmt = $connection->prepare($update_query);
$update_stmt->bind_param('sssi', $jam_sekarang, $keterangan, $keterangan, $attendance['id']);

if ($update_stmt->execute()) {
    $_SESSION['success'] = 'Clock out berhasil untuk ' . $employee['nama_lengkap'] . ' pada ' . date('H:i');
} else {
    $_SESSION['error'] = 'Gagal melakukan clock out: ' . $connection->error;
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
