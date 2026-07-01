<?php
$rootPath = __DIR__;
require_once $rootPath . '/app/config/session.php';
include 'partials/index/header.php';
?>

<main class="d-flex flex-column min-vh-100">

    <section id="about" class="py-5">
        <div class="container" style="max-width: 900px;">

            <h1 class="fw-bold display-4 mb-3">
                Tentang <span class="text-primary">EduTrack</span>
            </h1>

            <p class="lead mb-5" style="color: var(--color-text-muted);">
                Solusi modern untuk melihat jadwal ujian yang akan berlangsung.
            </p>

            <div class="mb-5">
                <h2 class="fw-bold h3 mb-3">
                    Apa itu EduTrack?
                </h2>
                <p style="line-height: 1.9;">
                    EduTrack adalah sistem manajemen jadwal ujian yang dirancang untuk memudahkan
                    dalam mengatur dan menyampaikan informasi jadwal ujian kepada mahasiswa. Dengan antarmuka yang
                    intuitif dan fitur yang lengkap, EduTrack membantu menghilangkan kebingungan jadwal dan memastikan
                    setiap mahasiswa mendapatkan informasi yang akurat dan tepat waktu.
                </p>
            </div>

            <div class="mb-5">
                <h2 class="fw-bold h3 mb-3">
                    Kenapa Memilih Kami?
                </h2>
                <p style="line-height: 1.9;">
                    EduTrack dirancang dengan fokus pada kemudahan penggunaan dan efisiensi. Sistem ini memungkinkan
                    pengelolaan jadwal ujian yang terpusat, mengurangi risiko kesalahan penjadwalan, dan memastikan
                    komunikasi yang efektif antara institusi dan mahasiswa. Dengan EduTrack, informasi jadwal ujian
                    selalu tersedia dan dapat diakses kapan saja.
                </p>
            </div>

            <hr class="my-5" style="border-color: var(--color-border);">

            <h2 class="fw-bold h3 mb-4">
                Hubungi Kami
            </h2>

            <div class="d-flex flex-column gap-3">

                <div class="card card-feature rounded-4">
                    <a href="mailto:reza@azayaka.my.id" class="text-decoration-none">
                        <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                            <span class="fs-3 text-primary"><i class="bi bi-envelope-fill"></i></span>
                            <div>
                                <p class="small mb-0 text-uppercase" style="color: var(--color-text-muted);">Email</p>
                                <p class="fw-semibold mb-0">reza@azayaka.my.id</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="card card-feature rounded-4">
                    <a href="https://maps.google.com/maps?q=jakarta,indonesia" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                            <span class="fs-3 text-primary"><i class="bi bi-geo-alt-fill"></i></span>
                            <div>
                                <p class="small mb-0 text-uppercase" style="color: var(--color-text-muted);">Alamat</p>
                                <p class="fw-semibold mb-0">Jakarta, Indonesia</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="card card-feature rounded-4">
                    <a href="https://instagram.com/ahmad_reza_0101" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                            <span class="fs-3 text-primary"><i class="bi bi-instagram"></i></span>
                            <div>
                                <p class="small mb-0 text-uppercase" style="color: var(--color-text-muted);">Instagram</p>
                                <p class="fw-semibold mb-0">instagram.com/ahmad_reza_0101</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="card card-feature rounded-4">
                    <a href="https://github.com/ahmadreza0101" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <div class="card-body d-flex align-items-center gap-3 p-3 p-md-4">
                            <span class="fs-3 text-primary"><i class="bi bi-github"></i></span>
                            <div>
                                <p class="small mb-0 text-uppercase" style="color: var(--color-text-muted);">GitHub</p>
                                <p class="fw-semibold mb-0">github.com/ahmadreza0101</p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

        </div>
    </section>

</main>

<?php include 'partials/index/footer.php'; ?>