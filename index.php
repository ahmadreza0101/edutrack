<?php
$rootPath = __DIR__;
require_once $rootPath . '/app/config/session.php';
include 'partials/index/header.php';
?>

<main class="d-flex flex-column min-vh-100">

    <section class="hero-section py-4 py-md-5 container">
        <div class="hero-gradient p-4 p-md-5 p-lg-6">
            <div class="row align-items-center py-3 py-lg-5">
                <div class="col-12 col-lg-6 mb-4 mb-lg-0 text-center text-lg-start order-2 order-lg-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 mb-3 d-inline-block">Selamat Datang</span>
                    <h1 class="fw-bold fs-2 fs-md-3 fs-lg-4 mb-3 mb-md-4">
                        EduTrack - Cek Jadwal Ujian Dengan Mudah
                    </h1>
                    <p class="fs-6 fs-md-5 text-secondary mb-4">
                        Platform sederhana untuk melihat jadwal ujian sekolah/kampus secara terorganisir. Tidak perlu bingung cari informasi, semua ada di satu tempat!
                    </p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start mt-3 mt-md-4 gap-2 gap-md-3">
                        <a href="/daftar.php" class="btn btn-primary px-4 px-md-5 mb-2 mb-md-3">
                            <i class="bi bi-calendar-check me-2"></i>Lihat Jadwal
                        </a>
                        <a href="/tentang.php" class="btn btn-outline-secondary px-4 px-md-5 mb-2 mb-md-3">
                            <i class="bi bi-info-circle me-2"></i>Tentang Kami
                        </a>
                    </div>
                </div>
                <div class="col-12 col-lg-6 text-center order-1 order-lg-2">
                    <div class="hero-content position-relative">
                        <a href="/index.php" class="d-inline-flex align-items-center gap-3 text-decoration-none">
                            <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png"
                                 alt="Logo Pendidikan"
                                 width="80"
                                 height="80">
                            <h2 class="fw-bold mb-0">EduTrack</h2>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="feature-section py-4 py-md-5">
        <div class="container">
            <h2 class="text-center mb-4 mb-md-5 fw-bold fs-4 fs-md-3">
                Kenapa Pilih EduTrack?
            </h2>
            <div class="row g-3 g-md-4">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card feature-card h-100 rounded-4">
                        <div class="card-body text-center p-3 p-md-4">
                            <div class="fs-3 fs-md-2 fs-lg-1 mb-2 mb-md-3">
                                📅
                            </div>
                            <h5 class="card-title fw-bold fs-6 fs-md-5">
                                Jadwal Lengkap
                            </h5>
                            <p class="card-text small fs-6 fs-md-5">
                                Semua jadwal ujian tersedia lengkap dengan tanggal, waktu, dan ruangan yang jelas.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card feature-card h-100 rounded-4">
                        <div class="card-body text-center p-3 p-md-4">
                            <div class="fs-3 fs-md-2 fs-lg-1 mb-2 mb-md-3">
                                ⚡
                            </div>
                            <h5 class="card-title fw-bold fs-6 fs-md-5">
                                Akses Instan
                            </h5>
                            <p class="card-text small fs-6 fs-md-5">
                                Buka situs, lihat jadwal, selesai! Tidak perlu ribet login untuk pengunjung.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card feature-card h-100 rounded-4">
                        <div class="card-body text-center p-3 p-md-4">
                            <div class="fs-3 fs-md-2 fs-lg-1 mb-2 mb-md-3">
                                📱
                            </div>
                            <h5 class="card-title fw-bold fs-6 fs-md-5">
                                Mobile Friendly
                            </h5>
                            <p class="card-text small fs-6 fs-md-5">
                                Tampilan responsif, bisa dibuka dari hp, tablet, atau komputer dengan nyaman.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4 py-md-5 text-center container">
        <div class="cta-box p-4 p-md-5 rounded-4" style="background: linear-gradient(135deg, rgba(var(--color-primary-rgb), 0.15) 0%, rgba(var(--color-secondary-rgb), 0.1) 100%);">
            <h2 class="fw-bold mb-2 mb-md-3 fs-4 fs-md-3">
                Sudah Siap Lihat Jadwal?
            </h2>
            <p class="mb-3 mb-md-4 small fs-6 fs-md-5" style="color: var(--color-text-muted);">
                Cek jadwal ujianmu sekarang juga, jangan sampai kelewatan!
            </p>
            <a href="/daftar.php" class="btn btn-primary px-4 px-md-5">
                <i class="bi bi-arrow-right me-2"></i>Mulai Cek Jadwal
            </a>
        </div>
    </section>

</main>

<?php include 'partials/index/footer.php'; ?>