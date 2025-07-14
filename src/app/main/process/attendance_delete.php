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

// Cek apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Metode request tidak valid';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

// Ambil ID absensi yang akan dihapus
$attendance_id = $_POST['id'] ?? '';

// Validasi input
if (empty($attendance_id) || !is_numeric($attendance_id)) {
    $_SESSION['error'] = 'ID absensi tidak valid';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

// Cek apakah absensi ada dan milik user yang login
$check_query = "SELECT a.id, e.nama_lengkap, a.tanggal 
                FROM attendances a 
                INNER JOIN employees e ON a.karyawan_id = e.id 
                WHERE a.id = ? AND a.user_id = ? AND e.deleted_at IS NULL";

$check_stmt = $connection->prepare($check_query);
$check_stmt->bind_param('ii', $attendance_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $_SESSION['error'] = 'Data absensi tidak ditemukan';
    header("Location: " . $_SERVER['HTTP_REFERER']); 
    exit();
}

$attendance = $check_result->fetch_assoc();

try {
    // Start transaction
    $connection->begin_transaction();

    // Hapus data absensi
    $delete_query = "DELETE FROM attendances WHERE id = ? AND user_id = ?";
    $delete_stmt = $connection->prepare($delete_query);
    $delete_stmt->bind_param('ii', $attendance_id, $user_id);

    if (!$delete_stmt->execute()) {
        throw new Exception('Gagal menghapus data absensi: ' . $connection->error);
    }

    if ($delete_stmt->affected_rows > 0) {
        $_SESSION['success'] = 'Data absensi ' . $attendance['nama_lengkap'] . ' tanggal ' . date('d/m/Y', strtotime($attendance['tanggal'])) . ' berhasil dihapus';
    } else {
        throw new Exception('Gagal menghapus data absensi - data tidak ditemukan');
    }

    // Commit transaction
    $connection->commit();

} catch (Exception $e) {
    // Rollback transaction
    $connection->rollback();
    
    $_SESSION['error'] = $e->getMessage();
}

header("Location: ../views/attendance/index.php");
exit();
?>