DROP DATABASE IF EXISTS employee_system;
CREATE DATABASE employee_system;

USE employee_system;

-- Tabel Users (Admin)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Employees (Karyawan)
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    nip VARCHAR(20) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
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
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Tabel Contracts
CREATE TABLE contracts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    jenis_kontrak ENUM('Tetap', 'Kontrak') NOT NULL,
    durasi_kontrak_bulan INT,
    tanggal_berakhir_kontrak DATE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Tabel Educations
CREATE TABLE educations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    pendidikan_terakhir ENUM('SMA', 'D3', 'S1', 'S2', 'S3') NOT NULL,
    nama_sekolah VARCHAR(100),
    jurusan VARCHAR(100),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
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
    
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    FOREIGN KEY (karyawan_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (karyawan_id, tanggal)
);

-- Insert sample data
INSERT INTO users (username, password, nama, email) VALUES
('admin', 'admin123', 'Administrator', 'admin@company.com');

INSERT INTO employees (
    nip, nama_lengkap, email, no_hp, alamat, tanggal_lahir, jenis_kelamin,
    jabatan, departemen, tanggal_masuk, jenis_kontrak, durasi_kontrak_bulan,
    tanggal_berakhir_kontrak, pendidikan_terakhir, nama_sekolah, jurusan
) VALUES
('EMP001', 'John Doe', 'john@company.com', '081234567890', 'Jl. Merdeka No. 123', '1990-05-15', 'Laki-laki',
 'Manager', 'IT', '2023-01-01', 'Tetap', NULL, NULL, 'S1', 'Universitas Indonesia', 'Teknik Informatika'),

('EMP002', 'Jane Smith', 'jane@company.com', '081234567891', 'Jl. Sudirman No. 456', '1992-08-20', 'Perempuan',
 'Staff', 'HR', '2023-03-15', 'Kontrak', 12, '2024-03-14', 'S1', 'Universitas Gadjah Mada', 'Psikologi'),

('EMP003', 'Bob Wilson', 'bob@company.com', '081234567892', 'Jl. Thamrin No. 789', '1995-12-10', 'Laki-laki',
 'Staff', 'Finance', '2023-06-01', 'Tetap', NULL, NULL, 'D3', 'Politeknik Negeri Jakarta', 'Akuntansi');

INSERT INTO attendances (karyawan_id, tanggal, jam_masuk, jam_keluar, status_kehadiran, keterangan) VALUES
(1, '2025-01-01', '08:00:00', '17:00:00', 'Hadir', 'Masuk tepat waktu'),
(1, '2025-01-02', '08:15:00', '17:00:00', 'Terlambat', 'Terlambat 15 menit'),
(1, '2025-01-03', NULL, NULL, 'Sakit', 'Demam'),

(2, '2025-01-01', '08:00:00', '17:00:00', 'Hadir', 'Masuk tepat waktu'),
(2, '2025-01-02', '08:00:00', '17:00:00', 'Hadir', 'Masuk tepat waktu'),
(2, '2025-01-03', NULL, NULL, 'Izin', 'Keperluan keluarga'),

(3, '2025-01-01', '08:00:00', '17:00:00', 'Hadir', 'Masuk tepat waktu'),
(3, '2025-01-02', '08:00:00', '17:00:00', 'Hadir', 'Masuk tepat waktu'),
(3, '2025-01-03', '08:00:00', '17:00:00', 'Hadir', 'Masuk tepat waktu');