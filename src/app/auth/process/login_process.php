<?php


session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '../../../../config/configuration.php';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $checkUser = "SELECT * FROM users WHERE username = ?";
    $stmt = $connection->prepare($checkUser);
    $stmt -> bind_param('s', $username);
    $stmt -> execute();
    $result = $stmt -> get_result();

    if ($result -> num_rows > 0) {
        $user = $result -> fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
            ];

            session_regenerate_id();

            header('Location: ../../main/views/dashboard.php');
            exit();
        } else {
            $_SESSION['errors'] = "Username atau Password salah";
            header("location: ../views/login.php");
            exit();
        }
    } else {
        $_SESSION['errors'] = "Username atau Password salah";
        header("location: ../views/login.php");
        exit();
    }
}
