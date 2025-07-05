<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '../../../../config/configuration.php';

if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $errors = array();

    if (empty($email)) {
        $errors['email'] = 'Email tidak boleh kosong';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email tidak valid';
    }

    if (empty($username)) {
        $errors['username'] = 'Username tidak boleh kosong';
    } elseif (strlen($username) < 5) {
        $errors['minimumuser'] = 'Username minimal 5 karakter';
    }

    if (empty($password)) {
        $errors['password'] = 'Password tidak boleh kosong';
    } elseif (strlen($password) < 8) {
        $errors['minimumpass'] = 'Password minimal 8 karakter';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }


    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkEmail = "SELECT * FROM users WHERE email = ?";
    $stmt = $connection->prepare($checkEmail);
    $stmt -> bind_param('s', $email);
    $stmt -> execute();
    $resultEmail = $stmt -> get_result();

    $checkUsername = "SELECT * FROM users WHERE username = ?";
    $stmt = $connection->prepare($checkUsername);
    $stmt -> bind_param('s', $username);
    $stmt -> execute();
    $resultUsername = $stmt -> get_result();

    if ($resultEmail -> num_rows > 0) {
        $errors['email'] = 'Email sudah digunakan';
        $_SESSION['errors'] = $errors;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } elseif ($resultUsername -> num_rows > 0) {
        $errors['username'] = 'Username sudah digunakan';
        $_SESSION['errors'] = $errors;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        $insertUser = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($insertUser);
        $stmt -> bind_param('sss', $username, $hashedPassword, $email);
        $stmt -> execute();

        $_SESSION['success'] = 'Registrasi berhasil';
        header('Location: ../views/login.php');
        exit();
    }
}
