<?php
$rootPath = __DIR__;
require_once $rootPath . '/app/config/session.php';
include 'koneksi.php'; /** @var mysqli $koneksi */
include 'partials/index/header.php';

function getDayName($dateStr) {
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $timestamp = strtotime($dateStr);
    return $days[date('w', $timestamp)];
}

$queryKelas = "
    SELECT 
        kelas,
        COUNT(*) as total_jadwal,
        MIN(tanggal_ujian) as tanggal_terdekat
    FROM tb_jadwal 
    WHERE is_active = 1 
    GROUP BY kelas 
    ORDER BY kelas ASC
";
$resultKelas = mysqli_query($koneksi, $queryKelas);
?>

<style>
    .feature-section {
        background: transparent !important;
    }
    .feature-section::before,
    .feature-section::after {
        display: none !important;
    }
    
    .card-theme {
        background-color: var(--color-bg-card) !important;
    }
    .class-card {
        background-color: var(--color-bg-card);
    }
    [data-bs-theme="dark"] .class-card {
        background-color: #0d1628 !important;
    }
    [data-bs-theme="light"] .class-card {
        background-color: #f8f9fa !important;
    }
    .class-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    .class-card h5, .class-card p {
        color: var(--color-text);
    }
    .class-card .text-muted {
        color: var(--color-text-muted) !important;
    }
</style>

<main class="d-flex flex-column min-vh-100">

    <section id="produk" class="feature-section py-5">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-theme shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-4 p-md-5">

                            <h2 class="text-center mb-5 fw-bold" style="color: var(--color-text);">
                                Daftar Jadwal Ujian
                            </h2>

                            <?php if (mysqli_num_rows($resultKelas) > 0): ?>

                                <div class="row g-4">
                                    <?php while ($kelasRow = mysqli_fetch_assoc($resultKelas)): ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card class-card shadow-sm border-0 rounded-4 h-100">
                                                <div class="card-body p-4">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div>
                                                            <span class="badge bg-primary mb-2">Kelas</span>
                                                            <h3 class="fw-bold mb-0" style="color: var(--color-text);"><?= htmlspecialchars($kelasRow['kelas']) ?></h3>
                                                        </div>
                                                        <div class="text-center">
                                                            <div class="fs-4 fw-bold text-primary"><?= $kelasRow['total_jadwal'] ?></div>
                                                            <div class="small" style="color: var(--color-text-muted);">Jadwal</div>
                                                        </div>
                                                    </div>

                                                    <?php if ($kelasRow['tanggal_terdekat']): ?>
                                                        <div class="mb-3">
                                                            <div class="small" style="color: var(--color-text-muted);">
                                                                <i class="bi bi-calendar-event me-1"></i> Ujian Terdekat
                                                            </div>
                                                            <div class="fw-semibold" style="color: var(--color-text);">
                                                                <?= getDayName($kelasRow['tanggal_terdekat']) ?>, 
                                                                <?= date('d F Y', strtotime($kelasRow['tanggal_terdekat'])) ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="mb-3">
                                                        <div class="small" style="color: var(--color-text-muted);">
                                                            <i class="bi bi-info-circle me-1"></i> Informasi
                                                        </div>
                                                        <div class="small" style="color: var(--color-text);">Lihat semua jadwal ujian untuk kelas ini</div>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent border-0 p-4 pt-0">
                                                    <a href="/daftar-detail.php?kelas=<?= urlencode($kelasRow['kelas']) ?>"
                                                       class="btn btn-primary w-100">
                                                        <i class="bi bi-eye me-2"></i>Lihat Selengkapnya
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>

                            <?php else: ?>

                                <div class="text-center py-5">
                                    <div class="fs-1 mb-3">📅</div>
                                    <h4 style="color: var(--color-text);">Belum ada jadwal ujian yang tersedia</h4>
                                    <p class="text-muted">Silakan cek kembali nanti</p>
                                </div>

                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

</main>

<?php include 'partials/index/footer.php'; ?>