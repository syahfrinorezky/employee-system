<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config/configuration.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'];

    // Data umum dari karyawan
    $nip_awal = htmlspecialchars(trim($_POST['nip']));
    $nama_lengkap = htmlspecialchars(trim($_POST['nama_lengkap']));
    $email = htmlspecialchars(trim($_POST['email']));
    $no_hp = htmlspecialchars(trim($_POST['no_hp']));
    $alamat = htmlspecialchars(trim($_POST['alamat']));
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;

    // Data umum pendidikan dari karyawan
    $pendidikan_terakhir = $_POST['pendidikan_terakhir'] ?? null;
    $nama_sekolah = htmlspecialchars(trim($_POST['nama_sekolah']));
    $jurusan = htmlspecialchars(trim($_POST['jurusan']));

    // Data umum pekerjaan dari karyawan
    $jabatan = $_POST['jabatan'] ?? null;
    $departemen = $_POST['departemen'] ?? null;
    $tanggal_masuk = $_POST['tanggal_masuk'] ?? null;

    // Data kontrak karyawan
    $jenis_kontrak = $_POST['jenis_kontrak'] ?? null;
    $durasi_kontrak_bulan = $_POST['durasi_kontrak_bulan'] ?? null;
    $tanggal_berakhir_kontrak = $_POST['tanggal_berakhir_kontrak'] ?? null;

    // Deklarasi variabel errors untuk menyimpan semua array error
    $errors = [];

    // Pengecekan form - data karyawan
    if (empty($nama_lengkap)) {
        $errors['nama_lengkap'] = 'Nama karyawan tidak boleh kosong';
    }

    if (empty($email)) {
        $errors['email'] = 'Email karyawan tidak boleh kosong';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid';
    }

    if (empty($no_hp)) {
        $errors['no_hp'] = 'No. HP karyawan tidak boleh kosong';
    } elseif (!preg_match('/^[0-9+\-\s]+$/', $no_hp)) {
        $errors['no_hp'] = 'Format nomor HP tidak valid';
    }

    if (empty($alamat)) {
        $errors['alamat'] = 'Alamat karyawan tidak boleh kosong';
    } 

    if (empty($tanggal_lahir)) {
        $errors['tanggal_lahir'] = 'Tanggal lahir karyawan tidak boleh kosong';
    } elseif (!DateTime::createFromFormat('Y-m-d', $tanggal_lahir)) {
        $errors['tanggal_lahir'] = 'Format tanggal lahir tidak valid';
    }

    // Pengecekan form - data pendidikan
    if (empty($pendidikan_terakhir)) {
        $errors['pendidikan_terakhir'] = 'Pendidikan terakhir karyawan tidak boleh kosong';
    }

    if (empty($nama_sekolah)) {
        $errors['nama_sekolah'] = 'Nama sekolah karyawan tidak boleh kosong';
    }

    if (empty($jurusan)) {
        $errors['jurusan'] = 'Jurusan karyawan tidak boleh kosong';
    }

    // Pengecekan form - data pekerjaan
    if (empty($jabatan)) {
        $errors['jabatan'] = 'Jabatan karyawan tidak boleh kosong';
    }

    if (empty($departemen)) {
        $errors['departemen'] = 'Departemen karyawan tidak boleh kosong';
    }

    if (empty($tanggal_masuk)) {
        $errors['tanggal_masuk'] = 'Tanggal masuk karyawan tidak boleh kosong';
    } elseif (!DateTime::createFromFormat('Y-m-d', $tanggal_masuk)) {
        $errors['tanggal_masuk'] = 'Format tanggal masuk tidak valid';
    }

    // Pengecekan form - data kontrak
    if (empty($jenis_kontrak)) {
        $errors['jenis_kontrak'] = 'Jenis kontrak karyawan tidak boleh kosong';
    } elseif ($jenis_kontrak === 'Kontrak') {
        if (empty($durasi_kontrak_bulan)) {
            $errors['durasi_kontrak_bulan'] = 'Durasi kontrak karyawan tidak boleh kosong';
        } elseif (!is_numeric($durasi_kontrak_bulan) || $durasi_kontrak_bulan <= 0) {
            $errors['durasi_kontrak_bulan'] = 'Durasi kontrak harus berupa angka positif';
        }
    } elseif ($jenis_kontrak === 'Tetap') {
        // Jika jenis kontrak adalah "Tetap", set nilai default
        $durasi_kontrak_bulan = 'Tetap';
        $tanggal_berakhir_kontrak = '-';
    }

    // Format tanggal masuk jadi tahun bulan hari untuk dimasukkan ke NIP
    $nip = '';
    if (!empty($tanggal_masuk)) {
        $tanggal_format = DateTime::createFromFormat('Y-m-d', $tanggal_masuk);
        if ($tanggal_format) {
            $random_digit = mt_rand(10, 99);
            $nip = $nip_awal . $tanggal_format->format('Ymd') . $random_digit; // hasilnya: EMP-20231001(random_digit)
        } else {
            $errors['tanggal_masuk'] = 'Format tanggal tidak valid';
        }
    }

    // Hitung tanggal berakhir kontrak jika jenis kontrak adalah "Kontrak"
    if ($jenis_kontrak === 'Kontrak' && !empty($tanggal_masuk) && !empty($durasi_kontrak_bulan) && empty($errors)) {
        try {
            $tanggalMasuk = new DateTime($tanggal_masuk);
            $tanggalMasuk->modify("+{$durasi_kontrak_bulan} months");
            $tanggal_berakhir_kontrak = $tanggalMasuk->format('Y-m-d');
        } catch (Exception $e) {
            $errors['tanggal_masuk'] = 'Gagal menghitung tanggal berakhir kontrak';
        }
    } elseif ($jenis_kontrak === 'Tetap') {
        // Kalau jenisnya "Tetap"
        $durasi_kontrak_bulan = null; // Set ke null untuk database
        $tanggal_berakhir_kontrak = null; // Set ke null untuk database
    }

    // Cek apabila errornya tidak kosong
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST; // Simpan data lama untuk refill form
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Pengecekan duplikasi data
    try {
        // Pengecekan data NIP
        $checkNIP = "SELECT id FROM employees WHERE nip = ?";
        $stmt = $connection->prepare($checkNIP);
        $stmt->bind_param('s', $nip);
        $stmt->execute();
        $checkNIP_result = $stmt->get_result();

        // Pengecekan data nama
        $checkNama = "SELECT id FROM employees WHERE nama_lengkap = ?";
        $stmt = $connection->prepare($checkNama);
        $stmt->bind_param('s', $nama_lengkap);
        $stmt->execute();
        $checkNama_result = $stmt->get_result();

        // Pengecekan data email
        $checkEmail = "SELECT id FROM employees WHERE email = ?";
        $stmt = $connection->prepare($checkEmail);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $checkEmail_result = $stmt->get_result();

        if ($checkNIP_result->num_rows > 0) {
            $errors['nip'] = 'Data dengan NIP ' . $nip . ' sudah ada';
        }

        if ($checkNama_result->num_rows > 0) {
            $errors['nama_lengkap'] = 'Data dengan nama ' . $nama_lengkap . ' sudah ada';
        }

        if ($checkEmail_result->num_rows > 0) {
            $errors['email'] = 'Data dengan email ' . $email . ' sudah ada';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Mulai transaksi
        $connection->begin_transaction();

        // Insert ke tabel employees
        $insertEmployee = "INSERT INTO employees (user_id, nip, nama_lengkap, email, no_hp, alamat, tanggal_lahir) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($insertEmployee);
        $stmt->bind_param('sssssss', $user_id, $nip, $nama_lengkap, $email, $no_hp, $alamat, $tanggal_lahir);

        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan data karyawan');
        }

        // Dapatkan ID karyawan dari tabel yang baru di-insert
        $employee_id = $stmt->insert_id;

        // Insert data ke tabel educations
        $insertEducations = "INSERT INTO educations (employee_id, pendidikan_terakhir, nama_sekolah, jurusan) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($insertEducations);
        $stmt->bind_param('isss', $employee_id, $pendidikan_terakhir, $nama_sekolah, $jurusan);

        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan data pendidikan');
        }

        // Insert data ke tabel employment_info
        $insertEmploymentInfo = "INSERT INTO employment_info (employee_id, jabatan, departemen, tanggal_masuk) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($insertEmploymentInfo);
        $stmt->bind_param('isss', $employee_id, $jabatan, $departemen, $tanggal_masuk);

        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan data pekerjaan');
        }

        // Insert data ke tabel contracts
        $insertKontrak = "INSERT INTO contracts (employee_id, jenis_kontrak, durasi_kontrak_bulan, tanggal_berakhir_kontrak) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($insertKontrak);

        // Handle NULL values untuk kontrak tetap
        if ($jenis_kontrak === 'Tetap') {
            $stmt->bind_param('isss', $employee_id, $jenis_kontrak, $durasi_kontrak_bulan, $tanggal_berakhir_kontrak);
        } else {
            $stmt->bind_param('isis', $employee_id, $jenis_kontrak, $durasi_kontrak_bulan, $tanggal_berakhir_kontrak);
        }

        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan data kontrak');
        }

        // Commit transaksi
        $connection->commit();

        $_SESSION['success'] = 'Data karyawan berhasil disimpan dengan NIP: ' . $nip;
        unset($_SESSION['old_data']); // Hapus data lama setelah berhasil
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();

    } catch (Exception $e) {
        // Rollback jika ada kesalahan
        $connection->rollback();
        $_SESSION['errors']['database'] = 'Gagal menyimpan data karyawan: ' . $e->getMessage();
        $_SESSION['old_data'] = $_POST;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

// Jika bukan POST request
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
