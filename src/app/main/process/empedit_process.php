<?php


session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config/configuration.php';

//  periksa ada method post gak, kalau gada yaudah balik ke index
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['errors'] = 'Permintaan tidak valid.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// ambil data umum
$nip = $_POST['nip'];
$nama = $_POST['nama_lengkap'];
$email = $_POST['email'];
$no_hp = $_POST['no_hp'];
$tgl_lahir = $_POST['tanggal_lahir'];
$alamat = $_POST['alamat'];

//  ambil data pendidikan
$pendidikan = $_POST['pendidikan_terakhir'];
$nama_sekolah = $_POST['nama_sekolah'];
$jurusan = $_POST['jurusan'];

// ambil data  pekerjaan
$jabatan = $_POST['jabatan'];
$departemen = $_POST['departemen'];
$tanggal_masuk = $_POST['tanggal_masuk'];

// ambil data kontrak
$jenis_kontrak = $_POST['jenis_kontrak'];
$durasi_kontrak = $_POST['durasi_kontrak_bulan'];
$tanggal_berakhir_kontrak = $_POST['tanggal_berakhir_kontrak'];

// ini ambil data id karayawannya
$stmt = $connection->prepare("SELECT id FROM employees WHERE nip = ?");
$stmt->bind_param("s", $nip);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    $_SESSION['errors'] = 'Karyawan tidak ditemukan';
    header("Location: ../views/index.php");
    exit();
}
$employee_id = $result->fetch_assoc()['id'];

//  ini buat update table employeesnya
$queryEmp = "UPDATE employees SET nama_lengkap=?, email=?, no_hp=?, tanggal_lahir=?, alamat=? WHERE id=?";
$stmtEmp = $connection->prepare($queryEmp);
$stmtEmp->bind_param("sssssi", $nama, $email, $no_hp, $tgl_lahir, $alamat, $employee_id);
$stmtEmp->execute();

// ini buat update table educations
$queryEdu = "UPDATE educations SET pendidikan_terakhir=?, nama_sekolah=?, jurusan=? WHERE employee_id=?";
$stmtEdu = $connection->prepare($queryEdu);
$stmtEdu->bind_param("sssi", $pendidikan, $nama_sekolah, $jurusan, $employee_id);
$stmtEdu->execute();

// ini buat update table employment_info
$queryEmpInfo = "UPDATE employment_info SET jabatan=?, departemen=?, tanggal_masuk=? WHERE employee_id=?";
$stmtEmpInfo = $connection->prepare($queryEmpInfo);
$stmtEmpInfo->bind_param("sssi", $jabatan, $departemen, $tanggal_masuk, $employee_id);
$stmtEmpInfo->execute();

// ini buat update table contracts
$queryContract = "UPDATE contracts SET jenis_kontrak=?, durasi_kontrak_bulan=?, tanggal_berakhir_kontrak=? WHERE employee_id=?";
$stmtContract = $connection->prepare($queryContract);
$stmtContract->bind_param("sisi", $jenis_kontrak, $durasi_kontrak, $tanggal_berakhir_kontrak, $employee_id);
$stmtContract->execute();

// yaudah selesai berarti updatenya
$_SESSION['success'] = 'Data karyawan berhasil diperbarui';
header("Location: ../views/employee/index.php");
exit();
