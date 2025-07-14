<?php
session_start();

require_once __DIR__ . '../../../../../config/configuration.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header("Location: ../../../auth/views/login.php");
    exit();
}

$username = $_SESSION['user']['username'];
$user_email = $_SESSION['user']['email'];
$user_id = $_SESSION['user']['id'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Pagination variables
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Base query untuk menghitung total record
$count_query = "SELECT COUNT(*) AS total FROM attendances a 
                INNER JOIN employees e ON a.karyawan_id = e.id 
                WHERE a.user_id = ? AND e.deleted_at IS NULL";

$params = [$user_id];
$types = 'i';

// Filter berdasarkan tanggal
if (!empty($date_filter)) {
    $count_query .= " AND a.tanggal = ?";
    $params[] = $date_filter;
    $types .= 's';
}

// Filter berdasarkan status
if (!empty($status_filter)) {
    $count_query .= " AND a.status_kehadiran = ?";
    $params[] = $status_filter;
    $types .= 's';
}

// Filter berdasarkan pencarian
if (!empty($search)) {
    $count_query .= " AND (e.nip LIKE ? OR e.nama_lengkap LIKE ?)";
    $search_term = '%' . $search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

$count_stmt = $connection->prepare($count_query);
$count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];

$records_per_page = 10;
$offset = ($current_page - 1) * $records_per_page;
$total_pages = ceil($total_records / $records_per_page);

// Query untuk menampilkan data absensi
$query = "SELECT a.*, e.nip, e.nama_lengkap 
          FROM attendances a 
          INNER JOIN employees e ON a.karyawan_id = e.id 
          WHERE a.user_id = ? AND e.deleted_at IS NULL";

$params = [$user_id];
$types = 'i';

if (!empty($date_filter)) {
    $query .= " AND a.tanggal = ?";
    $params[] = $date_filter;
    $types .= 's';
}

if (!empty($status_filter)) {
    $query .= " AND a.status_kehadiran = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($search)) {
    $query .= " AND (e.nip LIKE ? OR e.nama_lengkap LIKE ?)";
    $search_term = '%' . $search . '%';
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

$query .= " ORDER BY a.tanggal DESC, a.created_at DESC LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $connection->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Ambil data karyawan untuk dropdown
$employees_query = "SELECT id, nip, nama_lengkap FROM employees WHERE user_id = ? AND deleted_at IS NULL ORDER BY nama_lengkap";
$employees_stmt = $connection->prepare($employees_query);
$employees_stmt->bind_param('i', $user_id);
$employees_stmt->execute();
$employees_result = $employees_stmt->get_result();

$success = $_SESSION['success'] ?? [];
$error = $_SESSION['error'] ?? [];
unset($_SESSION['success']);
unset($_SESSION['error']);

// Cek apakah sudah absen hari ini
$today = date('Y-m-d');
$today_attendance_query = "SELECT COUNT(*) as count FROM attendances WHERE user_id = ? AND tanggal = ?";
$today_stmt = $connection->prepare($today_attendance_query);
$today_stmt->bind_param('is', $user_id, $today);
$today_stmt->execute();
$today_result = $today_stmt->get_result();
$today_count = $today_result->fetch_assoc()['count'];

function getStatusColor($status)
{
    switch ($status) {
        case 'Hadir': return 'bg-green-100 text-green-800 border-green-300';
        case 'Terlambat': return 'bg-yellow-100 text-yellow-800 border-yellow-300';
        case 'Tidak Hadir': return 'bg-red-100 text-red-800 border-red-300';
        case 'Sakit': return 'bg-blue-100 text-blue-800 border-blue-300';
        case 'Izin': return 'bg-purple-100 text-purple-800 border-purple-300';
        default: return 'bg-gray-100 text-gray-800 border-gray-300';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi - STAFFY</title>
    <link rel="stylesheet" href="../../../../resources/style/style.css">
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body x-data="{ 
    slideBarOpen: false, 
    logoutModal: false,
    addModal: false,
    clockInModal: false,
    clockOutModal: false,
    currentPage: <?= $current_page ?>,
    totalRecords: <?= $total_records ?>,
    recordsPerPage: <?= $records_per_page ?>,
    totalPages: <?= $total_pages ?>,
    searchData: '<?= htmlspecialchars($search, ENT_QUOTES) ?>',
    dateFilter: '<?= htmlspecialchars($date_filter, ENT_QUOTES) ?>',
    statusFilter: '<?= htmlspecialchars($status_filter, ENT_QUOTES) ?>',
    currentTime: '',
    
    init() {
        this.updateTime();
        setInterval(() => this.updateTime(), 1000);
    },
    
    updateTime() {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString('id-ID');
    },
    
    get startRecord() {
        return (this.currentPage - 1) * this.recordsPerPage + 1;
    },
    
    get endRecord() {
        return Math.min(this.currentPage * this.recordsPerPage, this.totalRecords);
    },
    
    goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            if (this.searchData) url.searchParams.set('search', this.searchData);
            if (this.dateFilter) url.searchParams.set('date', this.dateFilter);
            if (this.statusFilter) url.searchParams.set('status', this.statusFilter);
            window.location.href = url.toString();
        }
    },
    
    applyFilter() {
        const url = new URL(window.location);
        url.searchParams.set('page', 1);
        if (this.searchData) url.searchParams.set('search', this.searchData);
        if (this.dateFilter) url.searchParams.set('date', this.dateFilter);
        if (this.statusFilter) url.searchParams.set('status', this.statusFilter);
        window.location.href = url.toString();
    },
    
    clearFilter() {
        window.location.href = window.location.pathname;
    }
}">

    <?php include __DIR__ . '../../../../components/header.php'; ?>

    <div x-cloak>
        <?php include __DIR__ . '../../../../components/logout_modal.php' ?>
    </div>

    <div x-cloak>
        <?php include __DIR__ . '../../../../components/slidebar.php'; ?>
    </div>

    <!-- Add Attendance Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs">
        <div @click.away="addModal = false" class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Tambah Absensi</h2>
            <form action="../../process/attendance_process.php" method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                        <select name="karyawan_id" required class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Karyawan</option>
                            <?php
                            $employees_result->data_seek(0);
while ($employee = $employees_result->fetch_assoc()):
    ?>
                                <option value="<?= $employee['id'] ?>"><?= $employee['nip'] ?> - <?= $employee['nama_lengkap'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Kehadiran</label>
                        <select name="status_kehadiran" required class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Status</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Tidak Hadir">Tidak Hadir</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Keluar</label>
                        <input type="time" name="jam_keluar" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="3" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="addModal = false" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Clock In Modal -->
    <div x-show="clockInModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs">
        <div @click.away="clockInModal = false" class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Clock In - Jam Masuk</h2>
            <div class="text-center mb-4">
                <div class="text-3xl font-bold text-indigo-600" x-text="currentTime"></div>
                <div class="text-gray-600"><?= date('d F Y') ?></div>
            </div>
            
            <form action="../../process/jammsk_process.php" method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                        <select name="karyawan_id" required class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih Karyawan</option>
                            <?php
    $employees_result->data_seek(0);
while ($employee = $employees_result->fetch_assoc()):
    ?>
                                <option value="<?= $employee['id'] ?>"><?= $employee['nip'] ?> - <?= $employee['nama_lengkap'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Keterangan (opsional)..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="clockInModal = false" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        <i class="fa-solid fa-clock mr-2"></i>Clock In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="flex bg-white">
        <?php include __DIR__ . '../../../../components/sidebar.php'; ?>
        
        <div class="flex flex-col gap-7 w-full sm:overflow-y-auto p-4 sm:p-8 mt-20 sm:mt-12">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold font-secondary text-gray-800">Sistem <span class="text-indigo-500">Absensi</span></h1>
                <div class="text-right">
                    <div class="text-xl font-bold text-indigo-600" x-text="currentTime"></div>
                    <div class="text-sm text-gray-600"><?= date('d F Y') ?></div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                        <input type="text" x-model="searchData" placeholder="Cari karyawan..." class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" x-model="dateFilter" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="statusFilter" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Tidak Hadir">Tidak Hadir</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-2">
                        <button @click="applyFilter()" class="flex-1 bg-indigo-500 hover:bg-indigo-600 text-white p-2 rounded-lg">
                            <i class="fa-solid fa-filter mr-2"></i>Filter
                        </button>
                        <button @click="clearFilter()" class="bg-gray-500 hover:bg-gray-600 text-white p-2 rounded-lg">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="flex flex-col rounded-lg overflow-hidden border border-gray-300 shadow-md">
                <div class="p-3 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-check text-indigo-500 text-lg"></i>
                        <h1 class="font-bold font-primary text-lg text-indigo-500">Data Absensi</h1>
                    </div>
                    <div class="flex gap-x-2 items-center">
                        <div class="flex gap-x-2 items-center justify-center <?= $today_count > 0 ? 'bg-green-500' : 'bg-gray-500' ?> p-2 rounded-lg text-white">
                            <i class="fas fa-user-group text-base"></i>
                            <p class="sm:font-bold text-sm sm:text-base"><?= $today_count ?></p>
                        </div>
                        <button @click="clockInModal = true" class="flex items-center justify-center gap-3 p-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                                <i class="fa-solid fa-clock text-lg"></i>
                            <div class="text-left hidden sm:block">
                                <div class="font-bold">Auto</div>
                            </div>
                        </button>
                        <button @click="addModal = true" class="flex items-center justify-center gap-3 p-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-colors">
                            <i class="fa-solid fa-plus text-lg"></i>
                            <div class="text-left hidden sm:block">
                                <div class="font-bold">Manual</div>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="flex-grow mx-3 border-t border-gray-300"></div>

                <!-- Success/Error Messages -->
                <?php if (!empty($success)): ?>
                    <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        x-init="setTimeout(() => show = false, 3000)" 
                        class="p-2 mx-3 flex items-center gap-x-2 bg-green-500/40 rounded-lg border border-green-500">
                        <i class="fa-solid fa-check text-white text-sm"></i>
                        <p class="text-sm text-white"><?= $success ?></p>
                        <button @click="show = false" class="ml-auto text-white hover:text-gray-200">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        x-init="setTimeout(() => show = false, 3000)" 
                        class="p-2 mx-3 flex items-center gap-x-2 bg-red-500/40 rounded-lg border border-red-500">
                        <i class="fa-solid fa-exclamation-triangle text-white text-sm"></i>
                        <p class="text-sm text-white"><?= $error ?></p>
                        <button @click="show = false" class="ml-auto text-white hover:text-gray-200">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($result->num_rows > 0): ?>
                    <div class="p-3">
                        <div class="relative overflow-auto max-h-[400px] rounded-lg">
                            <table class="w-full border-collapse text-sm min-w-[800px]">
                                <thead class="!sticky top-0 bg-indigo-500 text-white font-bold uppercase z-20">
                                    <tr>
                                        <th class="p-3">No</th>
                                        <th class="p-3">NIP</th>
                                        <th class="p-3">Nama</th>
                                        <th class="p-3">Tanggal</th>
                                        <th class="p-3">Jam Masuk</th>
                                        <th class="p-3">Jam Keluar</th>
                                        <th class="p-3">Status</th>
                                        <th class="p-3">Keterangan</th>
                                        <th class="p-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-300">
                                    <?php
            $no = $offset + 1;
                    while ($attendance = $result->fetch_assoc()) :
                        ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-3 text-center"><?= $no++ ?></td>
                                            <td class="p-3"><?= $attendance['nip'] ?></td>
                                            <td class="p-3"><?= $attendance['nama_lengkap'] ?></td>
                                            <td class="p-3"><?= date('d/m/Y', strtotime($attendance['tanggal'])) ?></td>
                                            <td class="p-3 text-center"><?= $attendance['jam_masuk'] ?? '-' ?></td>
                                            <td class="p-3 text-center"><?= $attendance['jam_keluar'] ?? '-' ?></td>
                                            <td class="p-3">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full border <?= getStatusColor($attendance['status_kehadiran']) ?>">
                                                    <?= $attendance['status_kehadiran'] ?>
                                                </span>
                                            </td>
                                            <td class="p-3"><?= $attendance['keterangan'] ?? '-' ?></td>
                                            <td class="p-3">
                                                <div class="flex justify-center gap-2">
                                                    <a href="./edit.php?id=<?= $attendance['id'] ?>" class="bg-yellow-500 hover:bg-yellow-600 flex items-center justify-center w-8 h-8 rounded-lg">
                                                        <i class="fa-solid fa-pen-to-square text-white text-sm"></i>
                                                    </a>
                                                    <form action="../../process/attendance_delete.php" method="post" onsubmit="return confirm('Yakin ingin menghapus data absensi ini?')">
                                                        <input type="hidden" name="id" value="<?= $attendance['id'] ?>">
                                                        <button type="submit" class="bg-red-500 hover:bg-red-600 flex items-center justify-center w-8 h-8 rounded-lg">
                                                            <i class="fa-solid fa-trash text-white text-sm"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php include __DIR__ . '../../../../components/pagination_section.php'; ?>
                <?php else: ?>
                    <div class="flex flex-col gap-5 items-center justify-center p-5">
                        <video src="../../../../resources/public/icon/nodata.mp4" autoplay loop muted class="w-40 h-40 object-cover"></video>
                        <div class="flex flex-col gap-3 text-center">
                            <p class="text-sm text-gray-500">
                                <?= empty($search) && empty($date_filter) && empty($status_filter) ? 'Belum ada data absensi' : 'Data absensi tidak ditemukan' ?>
                            </p>
                            <button @click="addModal = true" class="border border-green-500 bg-white hover:bg-green-500 text-green-500 text-sm hover:text-white font-bold uppercase p-2 rounded-lg transition-all duration-300 cursor-pointer">
                                Tambah Absensi
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script type="module" src="../../../../resources/js/dist/app.bundle.js"></script>
    <script type="module" src="../../../../resources/js/dist/fa.min.js"></script>
</body>
</html>