<?php

session_start();

require_once __DIR__ . '../../../../../config/configuration.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header("Location: ../../auth/views/login.php");
    exit();
}

$username = $_SESSION['user']['username'];
$user_email = $_SESSION['user']['email'];
$user_id = $_SESSION['user']['id'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// pagination variables
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// untuk menampilkan jumlah total record dengan kondisi gak search
$count_query = "SELECT COUNT(*) AS total FROM employees WHERE deleted_at IS NULL AND user_id = ?";

$params = [$user_id];
$types = 'i';

// jika ada search
if (!empty($search)) {
    $count_query .= " AND (nip LIKE ? OR nama_lengkap LIKE ?)";
    $search_term = '%' . $search . '%';
    $params = array_merge($params, [$search_term, $search_term]);
    $types .= 'ss';
}

$count_stmt = $connection->prepare($count_query);
$count_stmt-> bind_param($types, ...$params);
$count_stmt-> execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];

$records_per_page = 10;
$offset = ($current_page - 1) * $records_per_page;

// total pagenya itu total datanya di bagi setiap page dan dibulatkan keatas
$total_pages = ceil($total_records / $records_per_page);

// Validasi current_page tidak melebihi total_pages dan tidak kosong
if ($current_page > $total_pages && $total_pages > 0) {
    header("Location: ?page=1&per_page=" . $records_per_page . (!empty($search) ? "&search=" . urlencode($search) : ""));
    exit();
}

// data yang ingin ditampilkan secara perpage
$query = "SELECT id, nip, nama_lengkap, email, no_hp 
          FROM employees 
          WHERE deleted_at IS NULL AND user_id = ?";

$params = [$user_id];
$types = 'i';

// jika ada search
if (!empty($search)) {
    $query .= " AND (nip LIKE ? OR nama_lengkap LIKE ?)";
    $search_term = '%' . $search . '%';
    $params = array_merge($params, [$search_term, $search_term]);
    $types .= 'ss';
}

$query .= " ORDER BY created_at ASC LIMIT ? OFFSET ?";
$params = array_merge($params, [$records_per_page, $offset]);
$types .= 'ii';

$stmt = $connection->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$success = $_SESSION['success'] ?? [];
$delete = $_SESSION['delete'] ?? [];
unset($_SESSION['success']);
unset($_SESSION['delete']);

$_SESSION['employee_detail'] = true;
$_SESSION['employee_edit'] = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karyawan - STAFFY</title>
    <link rel="stylesheet" href="../../../../resources/style/style.css">
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body x-data="{ 
    slideBarOpen : false, 
    logoutModal : false,
    addModal : false,
    currentPage: <?= $current_page ?>,
    totalRecords: <?= $total_records ?>,
    recordsPerPage: <?= $records_per_page ?>,
    totalPages: <?= $total_pages ?>,
    searchData: '<?= htmlspecialchars($search, ENT_QUOTES) ?>',
    
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
            if (this.searchData) {
                url.searchParams.set('search', this.searchData);
            }
            window.location.href = url.toString();
        }
    }
}">


    <?php include __DIR__ . '../../../../components/header.php'; ?>

    <div x-cloak >
        <?php include __DIR__ . '../../../../components/logout_modal.php' ?>
    </div>

    <div x-cloak >
        <?php include __DIR__ . '../../../../components/slidebar.php'; ?>
    </div>

    <div x-cloak >
        <?php include __DIR__ . '../../../../components/add_modal.php'; ?>
    </div>

    <div class="flex bg-white">
        <?php include __DIR__ . '../../../../components/sidebar.php'; ?>
        
        <div class="flex flex-col gap-7 w-full sm:overflow-y-auto p-4 sm:p-8 mt-20 sm:mt-12">
            <h1 class="text-2xl font-bold font-secondary text-gray-800">Karyawan <span class="text-indigo-500">Informasi</span></h1>
            
            <!-- search bar -->
            <form action="" method="GET" class="w-full flex items-center justify-center gap-2">
                <input type="text" x-model="searchData" name="search" id="search" placeholder="Cari karyawan" value="<?= htmlspecialchars($search, ENT_QUOTES) ?>" class="h-10 px-3 w-full text-sm outline outline-gray-300 hover:outline-indigo-500 focus:outline-1 focus:outline-indigo-500 rounded-lg">
                <button type="submit" class="flex items-center justify-center h-10 aspect-square rounded-lg bg-indigo-500 hover:bg-indigo-600 cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass text-lg text-white"></i>
                </button>
                <?php if (!empty($search)): ?>
                    <a href="?" class="flex items-center justify-center h-10 aspect-square rounded-lg bg-gray-500 hover:bg-gray-600 cursor-pointer">
                        <i class="fa-solid fa-times text-lg text-white"></i>
                    </a>
                <?php endif; ?>
            </form>

            <!-- table layout -->
            <div class="flex flex-col rounded-lg overflow-hidden border border-gray-300 shadow-md">
                <div class="p-3 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user-group text-indigo-500 text-lg"></i>
                        <h1 class="font-bold font-primary text-lg text-indigo-500">Daftar Karyawan</h1>
                    </div>
                    <?php if ($total_records > 0): ?>
                        <button @click="addModal = !addModal" type="button" class="flex items-center justify-center w-10 h-10 bg-green-500 hover:bg-green-600 rounded-lg cursor-pointer">
                            <i class="fa-solid fa-plus text-lg text-white"></i>
                        </button>
                    <?php endif; ?>
                </div>

                <div class="flex-grow mx-3 border-t border-gray-300"></div>

                <?php if (!empty($success)): ?>
                    <div
                        x-cloak
                        x-data="{
                            show : false, visible : false
                        }" 
                        x-init="
                        setTimeout(() => { 
                            show = true; 
                            visible = true; 
                            setTimeout(() => visible = false, 3000);
                        }, 1000);" 
                        x-show="visible"
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-300 transform"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" 
                        class="p-2 mx-3 flex items-center gap-x-2 bg-green-500/40 rounded-lg border border-green-500">
                        <i class="fa-solid fa-check text-white text-sm"></i>
                        <p class="text-sm text-white text-shadow-sm text-shadow-gray-400"><?= $success; ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($delete)): ?>
                    <div
                        x-cloak
                        x-data="{
                            show : false, visible : false
                        }" 
                        x-init="
                        setTimeout(() => { 
                            show = true; 
                            visible = true; 
                            setTimeout(() => visible = false, 3000);
                        }, 1000);" 
                        x-show="visible"
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-300 transform"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" 
                        class="p-2 mx-3 flex items-center gap-x-2 bg-red-500/40 rounded-lg border border-red-500">
                        <i class="fa-solid fa-trash text-white text-sm"></i>
                        <p class="text-sm text-white text-shadow-sm text-shadow-gray-400"><?= $delete; ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($result->num_rows > 0): ?>
                    <div class="p-3">
                        <div class="relative overflow-auto max-h-[400px] rounded-lg">
                            <table class="w-full border-collapse text-sm min-w-[700px]">
                                <thead class="!sticky top-0 bg-indigo-500 text-white font-bold uppercase z-20 rounded-t-lg">
                                    <tr>
                                        <th class="p-3">No</th>
                                        <th class="p-3">NIP</th>
                                        <th class="p-3">Nama</th>
                                        <th class="p-3">Email</th>
                                        <th class="p-3">No. Telp</th>
                                        <th class="p-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-300">
                                    <?php
                                    $no = $offset + 1;
                                    while ($employee = $result->fetch_assoc()) :
                                        $nip = $employee['nip'];
                                        $nama = $employee['nama_lengkap'];
                                        $email = $employee['email'];
                                        $no_hp = $employee['no_hp'];
                                        ?>
                                        <tr class="hover:bg-gray-200">
                                            <td class="p-3 text-center"><?= $no++ ?></td>
                                            <td class="p-3"><?= $nip ?></td>
                                            <td class="p-3"><?= $nama ?></td>
                                            <td class="p-3"><?= $email ?></td>
                                            <td class="p-3 text-center"><?= $no_hp ?></td>
                                            <td class="p-3">
                                                <div class="flex justify-center gap-2">
                                                    <a href="./detail.php?nip=<?= $employee['nip'] ?>" id="employee_detail" name="employee_detail" class="bg-indigo-500 hover:bg-indigo-600 flex items-center justify-center w-8 h-8 rounded-lg">
                                                        <i class="fa-solid fa-circle-info text-white text-sm"></i>
                                                    </a>
                                                    <a href="./edit.php?nip=<?= $employee['nip'] ?>" id="employee_edit" name="employee_edit" class="bg-yellow-500 hover:bg-yellow-600 flex items-center justify-center w-8 h-8 rounded-lg cursor-pointer">
                                                        <i class="fa-solid fa-pen-to-square text-white text-sm"></i>
                                                    </a>
                                                    <form action="../../process/delete_employee.php" method="post">
                                                        <input type="hidden" name="nip" value="<?= $nip ?>">
                                                        <button type="submit" class="bg-red-500 hover:bg-red-700 flex items-center justify-center w-8 h-8 rounded-lg cursor-pointer">
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

                    <?php include __DIR__ . '/../../../components/pagination_section.php'; ?>
                <?php else: ?>
                    <div class="flex flex-col gap-5 items-center justify-center p-5">
                        <video src="../../../../resources/public/icon/nodata.mp4" autoplay loop muted class="w-40 h-40 object-cover"></video>
                        <div class="flex flex-col gap-3">
                            <p class="text-sm text-gray-500"><?= empty($search) ? 'Tidak ada data karyawan' : ' Data karyawan tidak ditemukan' ?></p>
                            <button @click ="addModal = !addModal" type="button" class="border border-green-500 bg-white hover:bg-green-500 text-green-500 text-sm hover:text-white font-bold uppercase p-2 rounded-lg cursor-pointer transition-all duration-300 ease-in-out">Tambah Karyawan</button>
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