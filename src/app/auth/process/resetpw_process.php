<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '../../../../config/configuration.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/forgotpass.php");
    exit();
}

$username = $_POST['username'] ?? '';
$newpass = $_POST['newpass'] ?? '';
$confirmpass = $_POST['confirm_pass'] ?? '';

// validasi kosong
if (empty($username) || empty($newpass) || empty($confirmpass)) {
    $_SESSION['errors'] = 'Semua kolom harus diisi!';
    header("Location: ../views/forgotpass.php");
    exit();
} elseif (strlen($username) < 5) {
    $_SESSION['errors'] = 'Username minimal 5 karakter!';
} elseif (strlen($newpass) < 8 || strlen($confirmpass) < 8) {
    $_SESSION['error'] = 'Password harus memiliki minimal 8 karakter!';
}

// password cocok ga
if ($newpass !== $confirmpass) {
    $_SESSION['errors'] = 'Password tidak cocok!';
    header("Location: ../views/forgotpass.php");
    exit();
}

// cari user
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['errors'] = 'Username tidak ditemukan!';
    header("Location: ../views/forgotpass.php");
    exit();
}

// hash sama update password
$hashed = password_hash($newpass, PASSWORD_DEFAULT);
$updateQuery = "UPDATE users SET password = ? WHERE username = ?";
$updateStmt = $connection->prepare($updateQuery);
$updateStmt->bind_param("ss", $hashed, $username);
$update = $updateStmt->execute();

if ($update) {
    $_SESSION['success'] = 'Password berhasil direset.';
    header("Location: ../views/login.php");
} else {
    $_SESSION['errors'] = 'Terjadi kesalahan saat menyimpan password!';
    header("Location: ../views/forgotpass.php");
}
exit();
