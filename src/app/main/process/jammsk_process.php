<?php

session_start();

require_once __DIR__ . '/../../../config/configuration.php';
date_default_timezone_set('Asia/Makassar');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Cek apakah sudah ada absensi untuk karyawan hari ini
$check_attendance = "SELECT id, jam_masuk FROM attendances WHERE karyawan_id = ? AND tanggal = ? AND user_id = ?";
$check_stmt = $connection->prepare($check_attendance);
$check_stmt->bind_param('isi', $karyawan_id, $tanggal_sekarang, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $existing_attendance = $check_result->fetch_assoc();
    if (!empty($existing_attendance['jam_masuk'])) {
        $_SESSION['error'] = 'Karyawan ' . $employee['nama_lengkap'] . ' sudah melakukan clock in hari ini';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

// Tentukan status kehadiran berdasarkan jam masuk
$jam_batas_normal = '08:00:00'; // Jam 8 pagi
$status_kehadiran = ($jam_sekarang <= $jam_batas_normal) ? 'Hadir' : 'Terlambat';

// Konversi keterangan kosong ke null
$keterangan = empty($keterangan) ? null : $keterangan;

try {
    // Start transaction
    $connection->begin_transaction();

    // Jika sudah ada record untuk hari ini, update jam masuk
    if ($check_result->num_rows > 0) {
        $update_query = "UPDATE attendances SET jam_masuk = ?, status_kehadiran = ?, keterangan = ?, updated_at = NOW() WHERE id = ?";
        $update_stmt = $connection->prepare($update_query);
        $update_stmt->bind_param('sssi', $jam_sekarang, $status_kehadiran, $keterangan, $existing_attendance['id']);

        if (!$update_stmt->execute()) {
            throw new Exception('Gagal melakukan clock in: ' . $connection->error);
        }

        $_SESSION['success'] = 'Clock in berhasil untuk ' . $employee['nama_lengkap'] . ' pada ' . date('H:i');
    } else {
        // Insert record baru
        $insert_query = "INSERT INTO attendances (user_id, karyawan_id, tanggal, jam_masuk, status_kehadiran, keterangan, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $insert_stmt = $connection->prepare($insert_query);
        $insert_stmt->bind_param('iissss', $user_id, $karyawan_id, $tanggal_sekarang, $jam_sekarang, $status_kehadiran, $keterangan);

        if (!$insert_stmt->execute()) {
            throw new Exception('Gagal melakukan clock in: ' . $connection->error);
        }

        $_SESSION['success'] = 'Clock in berhasil untuk ' . $employee['nama_lengkap'] . ' pada ' . date('H:i');
    }

    // Commit transaction
    $connection->commit();

} catch (Exception $e) {
    // Rollback transaction
    $connection->rollback();

    $_SESSION['error'] = $e->getMessage();
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
