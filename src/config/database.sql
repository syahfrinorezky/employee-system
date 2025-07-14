DROP DATABASE IF EXISTS employee_system;
CREATE DATABASE employee_system;

USE employee_system;

-- Tabel Users (Admin)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Employees (Karyawan)
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    nip VARCHAR(20) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    no_hp VARCHAR(20),
    alamat TEXT,
    tanggal_lahir DATE,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Employment Info
CREATE TABLE employment_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    jabatan ENUM('Manager', 'Supervisor', 'Staff', 'Intern') NOT NULL,
    departemen ENUM('IT', 'HR', 'Finance', 'Marketing', 'Operations') NOT NULL,
    tanggal_masuk DATE NOT NULL,
    status_karyawan ENUM('Aktif', 'Resign') DEFAULT 'Aktif',
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_employee_jabatan (employee_id, jabatan)
);

-- Tabel Contracts
CREATE TABLE contracts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    jenis_kontrak ENUM('Tetap', 'Kontrak') NOT NULL,
    durasi_kontrak_bulan INT,
    tanggal_berakhir_kontrak DATE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_employee_kontrak (employee_id, jenis_kontrak)
);

-- Tabel Educations
CREATE TABLE educations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    pendidikan_terakhir ENUM('SMA', 'D3', 'S1', 'S2', 'S3') NOT NULL,
    nama_sekolah VARCHAR(100),
    jurusan VARCHAR(100),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_employee_pendidikan (employee_id, pendidikan_terakhir)
);

-- Tabel Attendances (Absensi)
CREATE TABLE IF NOT EXISTS attendances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    karyawan_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME,
    jam_keluar TIME,
    status_kehadiran ENUM('Hadir', 'Terlambat', 'Tidak Hadir', 'Sakit', 'Izin') NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (karyawan_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (karyawan_id, tanggal)
);