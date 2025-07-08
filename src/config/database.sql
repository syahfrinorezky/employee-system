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
    
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (karyawan_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (karyawan_id, tanggal)
);

-- Insert sample employees with user_id 3
INSERT INTO employees (user_id, nip, nama_lengkap, email, no_hp, alamat, tanggal_lahir, jenis_kelamin, created_at) VALUES
(1, 'EMP001', 'Ahmad Rizky Pratama', 'ahmad.rizky@company.com', '08123456789', 'Jl. Sudirman No. 123, Jakarta', '1990-05-15', 'Laki-laki', '2024-01-15 08:00:00'),
(1, 'EMP002', 'Siti Nurhaliza', 'siti.nurhaliza@company.com', '08234567890', 'Jl. Gatot Subroto No. 456, Jakarta', '1992-03-20', 'Perempuan', '2024-01-16 09:30:00'),
(1, 'EMP003', 'Budi Santoso', 'budi.santoso@company.com', '08345678901', 'Jl. Thamrin No. 789, Jakarta', '1988-12-10', 'Laki-laki', '2024-01-17 10:15:00'),
(1, 'EMP004', 'Dewi Lestari', 'dewi.lestari@company.com', '08456789012', 'Jl. Kuningan No. 321, Jakarta', '1995-07-25', 'Perempuan', '2024-01-18 11:00:00'),
(1, 'EMP005', 'Hendra Wijaya', 'hendra.wijaya@company.com', '08567890123', 'Jl. Senayan No. 654, Jakarta', '1987-11-08', 'Laki-laki', '2024-01-19 13:45:00'),
(1, 'EMP006', 'Maya Sari', 'maya.sari@company.com', '08678901234', 'Jl. Kemang No. 987, Jakarta', '1993-04-30', 'Perempuan', '2024-01-20 14:20:00'),
(1, 'EMP007', 'Ricky Hakim', 'ricky.hakim@company.com', '08789012345', 'Jl. Menteng No. 159, Jakarta', '1991-09-12', 'Laki-laki', '2024-01-21 15:30:00'),
(1, 'EMP008', 'Rina Mulyani', 'rina.mulyani@company.com', '08890123456', 'Jl. Cikini No. 753, Jakarta', '1989-06-18', 'Perempuan', '2024-01-22 16:00:00'),
(1, 'EMP009', 'Doni Hermawan', 'doni.hermawan@company.com', '08901234567', 'Jl. Pasar Minggu No. 246, Jakarta', '1994-02-14', 'Laki-laki', '2024-01-23 08:30:00'),
(1, 'EMP010', 'Lina Kartika', 'lina.kartika@company.com', '08012345678', 'Jl. Cilandak No. 135, Jakarta', '1996-08-22', 'Perempuan', '2024-01-24 09:15:00'),
(1, 'EMP011', 'Fahmi Abdullah', 'fahmi.abdullah@company.com', '08123456780', 'Jl. Pondok Indah No. 468, Jakarta', '1985-01-05', 'Laki-laki', '2024-01-25 10:45:00'),
(1, 'EMP012', 'Sari Indah', 'sari.indah@company.com', '08234567891', 'Jl. Kelapa Gading No. 802, Jakarta', '1997-10-11', 'Perempuan', '2024-01-26 11:30:00'),
(1, 'EMP013', 'Bambang Sutrisno', 'bambang.sutrisno@company.com', '08345678902', 'Jl. Pluit No. 579, Jakarta', '1986-12-28', 'Laki-laki', '2024-01-27 12:00:00'),
(1, 'EMP014', 'Widya Ningrum', 'widya.ningrum@company.com', '08456789013', 'Jl. Sunter No. 391, Jakarta', '1998-03-17', 'Perempuan', '2024-01-28 13:15:00'),
(1, 'EMP015', 'Agus Salim', 'agus.salim@company.com', '08567890124', 'Jl. Ancol No. 624, Jakarta', '1990-11-03', 'Laki-laki', '2024-01-29 14:30:00');

-- Insert employment info for each employee
INSERT INTO employment_info (employee_id, jabatan, departemen, tanggal_masuk, status_karyawan) VALUES
(1, 'Manager', 'IT', '2024-01-15', 'Aktif'),
(2, 'Staff', 'HR', '2024-01-16', 'Aktif'),
(3, 'Supervisor', 'Finance', '2024-01-17', 'Aktif'),
(4, 'Staff', 'Marketing', '2024-01-18', 'Aktif'),
(5, 'Manager', 'Operations', '2024-01-19', 'Aktif'),
(6, 'Staff', 'IT', '2024-01-20', 'Aktif'),
(7, 'Supervisor', 'HR', '2024-01-21', 'Aktif'),
(8, 'Staff', 'Finance', '2024-01-22', 'Aktif'),
(9, 'Staff', 'Marketing', '2024-01-23', 'Aktif'),
(10, 'Intern', 'Operations', '2024-01-24', 'Aktif'),
(11, 'Manager', 'IT', '2024-01-25', 'Aktif'),
(12, 'Staff', 'HR', '2024-01-26', 'Aktif'),
(13, 'Supervisor', 'Finance', '2024-01-27', 'Aktif'),
(14, 'Staff', 'Marketing', '2024-01-28', 'Aktif'),
(15, 'Staff', 'Operations', '2024-01-29', 'Aktif');

-- Insert contracts for each employee
INSERT INTO contracts (employee_id, jenis_kontrak, durasi_kontrak_bulan, tanggal_berakhir_kontrak) VALUES
(1, 'Tetap', NULL, NULL),
(2, 'Kontrak', 12, '2025-01-16'),
(3, 'Tetap', NULL, NULL),
(4, 'Kontrak', 24, '2026-01-18'),
(5, 'Tetap', NULL, NULL),
(6, 'Kontrak', 12, '2025-01-20'),
(7, 'Tetap', NULL, NULL),
(8, 'Kontrak', 18, '2025-07-22'),
(9, 'Kontrak', 12, '2025-01-23'),
(10, 'Kontrak', 6, '2024-07-24'),
(11, 'Tetap', NULL, NULL),
(12, 'Kontrak', 24, '2026-01-26'),
(13, 'Tetap', NULL, NULL),
(14, 'Kontrak', 12, '2025-01-28'),
(15, 'Kontrak', 18, '2025-07-29');

-- Insert education info for each employee
INSERT INTO educations (employee_id, pendidikan_terakhir, nama_sekolah, jurusan) VALUES
(1, 'S1', 'Universitas Indonesia', 'Teknik Informatika'),
(2, 'S1', 'Universitas Gadjah Mada', 'Psikologi'),
(3, 'S2', 'Institut Teknologi Bandung', 'Manajemen Keuangan'),
(4, 'S1', 'Universitas Padjadjaran', 'Komunikasi'),
(5, 'S1', 'Universitas Brawijaya', 'Teknik Industri'),
(6, 'D3', 'Politeknik Negeri Jakarta', 'Sistem Informasi'),
(7, 'S1', 'Universitas Airlangga', 'Manajemen SDM'),
(8, 'S1', 'Universitas Diponegoro', 'Akuntansi'),
(9, 'S1', 'Universitas Sebelas Maret', 'Marketing'),
(10, 'SMA', 'SMA Negeri 1 Jakarta', 'IPA'),
(11, 'S2', 'Universitas Bina Nusantara', 'Sistem Informasi'),
(12, 'S1', 'Universitas Trisakti', 'Hukum'),
(13, 'S1', 'Universitas Tarumanagara', 'Ekonomi'),
(14, 'D3', 'Politeknik Negeri Bandung', 'Desain Grafis'),
(15, 'S1', 'Universitas Pancasila', 'Manajemen Operasi');