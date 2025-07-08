<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config/configuration.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'];

    // data umum dari karyawan
    $nip_awal = htmlspecialchars(trim($_POST['nip']));
    $nama_lengkap = htmlspecialchars(trim($_POST['nama_lengkap']));
    $email = htmlspecialchars(trim($_POST['email']));
    $no_hp = htmlspecialchars(trim($_POST['no_hp']));

    // data umum pendidikan dari karyawan
    $pendidikan_terakhir = $_POST['pendidikan_terakhir'] ?? null;
    $nama_sekolah = htmlspecialchars(trim($_POST['nama_sekolah']));
    $jurusan = htmlspecialchars(trim($_POST['jurusan']));

    // data umum pekerjaan dari karyawan
    $jabatan = $_POST['jabatan'] ?? null;
    $departemen = $_POST['departemen'] ?? null;
    $tanggal_masuk = $_POST['tanggal_masuk'] ?? null;

    // data kontrak karyawan
    $jenis_kontrak = $_POST['jenis_kontrak'] ?? null;
    $durasi_kontrak_bulan = $_POST['durasi_kontrak_bulan'] ?? null;
    $tanggal_berakhir_kontrak = $_POST['tanggal_berakhir_kontrak'] ?? null;

    // deklarasi variabel errors untuk menyimpan semua array error
    $errors = [];

    // pengecekan form
    if (empty($nama_lengkap)) {
        $errors['nama_lengkap'] = 'Nama karyawan tidak boleh kosong';
    }

    if (empty($email)) {
        $errors['email'] = 'Email karyawan tidak boleh kosong';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $errors['email'] = 'Email tidak valid';
    }

    if (empty($no_hp)) {
        $errors['no_hp'] = 'No. Hp karyawan tidak boleh kosong';
    }

    if (empty($pendidikan_terakhir)) {
        $errors['pendidikan_terakhir'] = 'Pendidikan terakhir karyawan tidak boleh kosong';
    }

    if (empty($nama_sekolah)) {
        $errors['nama_sekolah'] = 'Nama sekolah karyawan tidak boleh kosong';
    }

    if (empty($jurusan)) {
        $errors['jurusan'] = 'Jurusan karyawan tidak boleh kosong';
    }

    if (empty($jabatan)) {
        $errors['jabatan'] = 'Jabatan karyawan tidak boleh kosong';
    }

    if (empty($departemen)) {
        $errors['departemen'] = 'Departemen karyawan tidak boleh kosong';
    }

    if (empty($tanggal_masuk)) {
        $errors['tanggal_masuk'] = 'Tanggal masuk karyawan tidak boleh kosong';
    }

    // ngeformat tanggal masuk jadi tahun bulan hari untuk dimasukkan ke nip
    $tanggal_format = DateTime::createFromFormat('Y-m-d', $tanggal_masuk);
    if (!$tanggal_format) {
        $errors['tanggal_masuk'] = 'Format tanggal tidak valid';
    } else {
        $nip = $nip_awal . $tanggal_format->format('Ymd'); // hasilnya nanti: EMP-20231001 
    }

    // cek apabila errornya tidak kosong
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // pengecekan data nip
        $checkNIP = "SELECT * FROM employees WHERE nip = ?";
        $stmt = $connection->prepare($checkNIP);
        $stmt->bind_param('s', $nip);
        $stmt->execute();
        $checkNIP_result = $stmt->get_result();

        // pengecekan data nama
        $checkNama = "SELECT * FROM employees WHERE nama_lengkap = ?";
        $stmt = $connection->prepare($checkNama);
        $stmt->bind_param('s', $nama_lengkap);
        $stmt->execute();
        $checkNama_result = $stmt->get_result();

        if ($checkNIP_result->num_rows > 0) {
            $errors['nip'] = 'Data dengan NIP ' . $nip . ' sudah ada';
            $_SESSION['errors'] = $errors;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } elseif ($checkNama_result->num_rows > 0) {
            $errors['nama_lengkap'] = 'Data dengan nama ' . $nama_lengkap . ' sudah ada';
            $_SESSION['errors'] = $errors;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            // Mulai transaksi
            $connection->begin_transaction();

            try {
                // Insert ke tabel employees
                $insertEmployee = "INSERT INTO employees (user_id, nip, nama_lengkap, email, no_hp) VALUES (?, ?, ?, ?, ?)";
                $stmt = $connection->prepare($insertEmployee);
                $stmt->bind_param('sssss', $user_id, $nip, $nama_lengkap, $email, $no_hp);
                $stmt->execute();

                // dapet id karyawan dari table yang barus di insert
                $employee_id = $stmt->insert_id;

                // masuk data ke table
                $insertEducations = "INSERT INTO educations (employee_id, pendidikan_terakhir, nama_sekolah, jurusan) VALUES (?, ?, ?, ?)";
                $stmt = $connection->prepare($insertEducations);
                $stmt->bind_param('isss', $employee_id, $pendidikan_terakhir, $nama_sekolah, $jurusan);
                $stmt->execute();

                // masuk data ke table employmentinfo
                $insertEmploymentInfo = "INSERT INTO employment_info (employee_id, jabatan, departemen, tanggal_masuk) VALUES (?, ?, ?, ?)";
                $stmt = $connection->prepare($insertEmploymentInfo);
                $stmt->bind_param('isss', $employee_id, $jabatan, $departemen, $tanggal_masuk);
                $stmt->execute();

                // masuk data ke table kontrak
                $insertKontrak = "INSERT INTO contracts (employee_id, jenis_kontrak, durasi_kontrak_bulan, tanggal_berakhir_kontrak) VALUES (?, ?, ?, ?)";
                $stmt = $connection->prepare($insertKontrak);
                $stmt->bind_param('isis', $employee_id, $jenis_kontrak, $durasi_kontrak_bulan, $tanggal_berakhir_kontrak);
                $stmt->execute();

                // kommit/cod knalpot transaksi
                $connection->commit();

                $_SESSION['success'] = 'Data berhasil disimpan';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            } catch (Exception $e) {
                // rollback kalo ada kesalahan
                $connection->rollback();
                $_SESSION['errors']['database'] = 'Gagal menyimpan data karyawan';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }
}
?>
