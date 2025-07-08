<?php
    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . '/../../../config/configuration.php';

    $nip = $_POST['nip'];

    $checkNIP = $connection -> prepare("SELECT * FROM employees WHERE nip = ? AND deleted_at IS NULL");
    $checkNIP -> bind_param("s", $nip);
    $checkNIP -> execute();
    $result = $checkNIP -> get_result();

    if ($result -> num_rows === 0) {
        $_SESSION['error'] = "Data tidak ditemukan";
        header("Location: ../index.php");
        exit();
    }

    $stmt = $connection -> prepare("UPDATE employees SET deleted_at = NOW() WHERE nip = ?");
    $stmt -> bind_param("s", $nip);
    $delete = $stmt -> execute();

    if ($delete) {
        $_SESSION['delete'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Data gagal dihapus!";
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
