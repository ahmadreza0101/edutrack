<?php
$rootPath = __DIR__;

include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */

require_once $rootPath . '/app/config/session.php';

error_log("[DASHBOARD] Session check. Status: " . ($_SESSION['status'] ?? 'NOT SET'));
error_log("[DASHBOARD] Session data: " . print_r($_SESSION, true));

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    error_log("[DASHBOARD] Login check failed, redirecting to login");
    header("Location: /login.php?toast_type=warning&toast_title=Akses Ditolak&toast_message=Silakan login terlebih dahulu");
    exit();
}

error_log("[DASHBOARD] Login check passed, loading dashboard");

include 'partials/dashboard/header.php';
include 'partials/dashboard/sidebar.php';

$username = $_SESSION['username'];
$stmt = mysqli_prepare($koneksi, "SELECT username, email FROM tb_login WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$totalJadwalResult = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_jadwal");
$totalJadwal = mysqli_fetch_assoc($totalJadwalResult)['total'];

$totalAdminResult = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_login");
$totalAdmin = mysqli_fetch_assoc($totalAdminResult)['total'];

if (!isset($_SESSION['visitor_count'])) {
    $_SESSION['visitor_count'] = 1;
} else {
    if (!isset($_SESSION['visitor_incremented'])) {
        $_SESSION['visitor_count']++;
        $_SESSION['visitor_incremented'] = true;
    }
}
$visitorCount = $_SESSION['visitor_count'];

date_default_timezone_set('Asia/Jakarta');
$hariIni = date('l');
$tanggalSekarang = date('d F Y');
$jamSekarang = date('H:i:s');

$hariIndo = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
$hariIniIndo = $hariIndo[$hariIni];

$bulanIndo = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];
$tanggalSekarangIndo = strtr($tanggalSekarang, $bulanIndo);
?>

<style>
    .dashboard-menu-card {
        transition: transform 0.18s ease, box-shadow 0.18s ease !important;
        text-decoration: none;
        display: block;
        height: 100%;
    }
    .dashboard-menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.25) !important;
    }
    .dashboard-menu-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
    }
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
    }
    .stat-card:hover {
        transform: translateY(-3px);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="main-content">
    <div class="container-fluid mt-4 mb-5">
        <div class="row">
            <div class="col-lg-12">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <p class="text-muted small mb-1 text-uppercase">Dashboard</p>
                        <h2 class="fw-bold mb-2">
                            Selamat datang, <span class="text-primary"><?= htmlspecialchars($user['username'] ?? 'Admin') ?></span>
                        </h2>
                        <p class="mb-0">
                            <i class="bi bi-envelope-fill me-2" style="color: var(--color-text-muted);"></i><?= htmlspecialchars($user['email'] ?? '-') ?>
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase">Hari Ini</p>
                                <h4 class="fw-bold mb-0"><?= $hariIniIndo ?>, <?= $tanggalSekarangIndo ?></h4>
                            </div>
                            <div class="text-center text-md-end">
                                <p class="text-muted mb-1 small text-uppercase">Jam Sekarang</p>
                                <h4 class="fw-bold mb-0" id="live-clock"><?= $jamSekarang ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary-subtle text-primary flex-shrink-0">
                                        <i class="bi bi-calendar3 fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-muted mb-1 small text-uppercase">Total Jadwal</p>
                                        <h3 class="fw-bold mb-0"><?= $totalJadwal ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success-subtle text-success flex-shrink-0">
                                        <i class="bi bi-people fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-muted mb-1 small text-uppercase">Total Admin</p>
                                        <h3 class="fw-bold mb-0"><?= $totalAdmin ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning-subtle text-warning flex-shrink-0">
                                        <i class="bi bi-eye fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-muted mb-1 small text-uppercase">Pengunjung</p>
                                        <h3 class="fw-bold mb-0"><?= $visitorCount ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">

                    <div class="col-md-4">
                        <a href="/jadwal.php" class="dashboard-menu-card">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center p-4">
                                    <div class="dashboard-menu-icon bg-primary-subtle text-primary">
                                        <i class="bi bi-calendar-check-fill"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Kelola Jadwal</h5>
                                    <p class="text-muted small mb-0">Tambah, edit, atau hapus jadwal ujian</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="/user.php" class="dashboard-menu-card">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center p-4">
                                    <div class="dashboard-menu-icon bg-success-subtle text-success">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Kelola Admin</h5>
                                    <p class="text-muted small mb-0">Tambah, edit, atau hapus administrator</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="/app/proses/login/logout.php" class="dashboard-menu-card">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center p-4">
                                    <div class="dashboard-menu-icon bg-warning-subtle text-warning">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Logout</h5>
                                    <p class="text-muted small mb-0">Keluar dari halaman dashboard</p>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeString = hours + ':' + minutes + ':' + seconds;
        
        const liveClock = document.getElementById('live-clock');
        if (liveClock) {
            liveClock.textContent = timeString;
        }
        

    }
    
    setInterval(updateClock, 1000);
    updateClock();
</script>

<?php include 'partials/dashboard/footer.php'; ?>