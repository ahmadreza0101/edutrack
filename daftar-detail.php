<?php
$rootPath = __DIR__;
require_once $rootPath . '/app/config/session.php';
include 'koneksi.php';
include 'partials/index/header.php';

function getDayName($dateStr) {
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $timestamp = strtotime($dateStr);
    return $days[date('w', $timestamp)];
}

$id = (int)($_GET['id'] ?? 0);
$kelas = $_GET['kelas'] ?? '';
$isClassView = !empty($kelas);

if ($isClassView) {
    $query = "SELECT * FROM tb_jadwal WHERE is_active = 1 AND kelas = '" . mysqli_real_escape_string($koneksi, $kelas) . "' ORDER BY tanggal_ujian ASC, waktu_mulai ASC";
    $result = mysqli_query($koneksi, $query);
    $pageTitle = "Jadwal Kelas " . htmlspecialchars($kelas);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_jadwal WHERE id = ? AND is_active = 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$row) {
        echo '<div class="container mt-5 text-center" style="padding: 100px 0;">';
        echo '<div class="fs-1 mb-3 text-secondary"><i class="bi bi-calendar-x"></i></div>';
        echo '<h4 style="color: var(--color-text);">Jadwal ujian tidak ditemukan</h4>';
        echo '<a href="/daftar.php" class="btn btn-primary mt-3"><i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar</a>';
        echo '</div>';
        include 'partials/index/footer.php';
        exit();
    }
    $pageTitle = htmlspecialchars($row['mata_kuliah']);
}
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
    .text-theme {
        color: var(--color-text);
    }
    .text-muted-theme {
        color: var(--color-text-muted);
    }

    .detail-topbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-bottom: 1.5rem;
    }
    .detail-title {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--color-text);
        margin: 0 0 1rem 0;
    }
    .detail-title i {
        font-size: 1.25rem;
        color: var(--color-primary, #0d6efd);
    }
    .btn-back-top {
        border: 1px solid var(--color-border);
        color: var(--color-text-muted);
        background: transparent;
        font-size: 0.875rem;
        padding: 0.4rem 0.85rem;
        border-radius: 0.5rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-print {
        border-radius: 0.5rem;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }

    #table-detail_wrapper .dt-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    #table-detail_wrapper .dataTables_length,
    #table-detail_wrapper .dataTables_filter {
        margin: 0;
    }
    #table-detail_wrapper .dataTables_filter label,
    #table-detail_wrapper .dataTables_length label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
        font-size: 0.875rem;
        color: var(--color-text-muted);
        font-weight: 500;
    }
    #table-detail_wrapper .dataTables_length select,
    #table-detail_wrapper .dataTables_filter input {
        border: 1px solid var(--color-border);
        background: var(--color-bg-card);
        color: var(--color-text);
        border-radius: 0.45rem;
        padding: 0.35rem 0.6rem;
        font-size: 0.875rem;
        outline: none;
    }
    #table-detail_wrapper .dataTables_filter input {
        min-width: 200px;
    }

    #table-detail_wrapper .dataTables_scroll {
        margin-bottom: 0.25rem;
    }
    #table-detail_wrapper .dataTables_scrollHead,
    #table-detail_wrapper .dataTables_scrollBody {
        border-bottom: none;
    }
    #table-detail {
        --bs-table-bg: transparent;
        --bs-table-color: var(--color-text);
        --bs-table-striped-bg: transparent;
        --bs-table-hover-bg: transparent;
        background-color: var(--color-bg-card);
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
    }
    #table-detail thead,
    #table-detail tbody,
    #table-detail tr {
        background-color: var(--color-bg-card);
    }
    #table-detail thead th {
        font-weight: 600;
        font-size: 0.78rem;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        color: var(--color-text-muted);
        background: transparent;
        border: none;
        border-bottom: 2px solid var(--color-border);
        padding: 0.75rem 1rem;
    }
    #table-detail tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        color: var(--color-text);
        background-color: var(--color-bg-card);
        border-bottom: 1px solid var(--color-border);
    }
    #table-detail tbody tr:last-child td {
        border-bottom: none;
    }
    #table-detail_wrapper .dataTables_scrollHead,
    #table-detail_wrapper .dataTables_scrollHeadInner,
    #table-detail_wrapper .dataTables_scrollBody {
        background-color: var(--color-bg-card);
    }

    #table-detail_wrapper .dt-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--color-border);
    }
    #table-detail_wrapper .dataTables_info {
        font-size: 0.85rem;
        color: var(--color-text-muted);
        padding: 0 !important;
    }
    #table-detail_wrapper .dataTables_paginate {
        display: flex;
        gap: 0.25rem;
    }
    #table-detail_wrapper .dataTables_paginate .paginate_button {
        border: 1px solid var(--color-border) !important;
        background: var(--color-bg-card) !important;
        color: var(--color-text) !important;
        border-radius: 0.4rem !important;
        padding: 0.3rem 0.7rem !important;
        font-size: 0.85rem !important;
        margin: 0 !important;
    }
    #table-detail_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--color-primary, #0d6efd) !important;
        border-color: var(--color-primary, #0d6efd) !important;
        color: #fff !important;
    }
    #table-detail_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
    }

    .detail-bottom-actions {
        margin-top: 2rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--color-border);
    }

    @media (max-width: 576px) {
        .detail-title {
            font-size: 1.15rem;
        }
        #table-detail_wrapper .dataTables_filter input {
            min-width: 0;
            width: 100%;
        }
        #table-detail_wrapper .dataTables_filter label {
            width: 100%;
        }
        #table-detail_wrapper .dt-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        #table-detail_wrapper .dt-footer {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }
        #table-detail_wrapper .dataTables_paginate {
            justify-content: center;
        }
    }
</style>

<main class="d-flex flex-column min-vh-100">
    <section class="feature-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card card-theme shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-4 p-md-5">
                            <h1 class="detail-title">
                                <i class="bi bi-calendar-check"></i>
                                <?= $pageTitle ?>
                            </h1>

                            <div class="detail-topbar">
                                <a href="/daftar.php" class="btn-back-top">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <a href="<?= $isClassView ? "/app/service/printout.php?kelas=" . urlencode($kelas) : "/app/service/printout.php?id=$id" ?>"
                                   class="btn btn-success btn-print" target="_blank">
                                    <i class="bi bi-printer me-2"></i>Cetak PDF
                                </a>
                            </div>

                            <?php if ($isClassView): ?>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <table class="table align-middle w-100" id="table-detail">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Gambar</th>
                                                <th>Mata Kuliah</th>
                                                    <th>Dosen</th>
                                                    <th>Hari</th>
                                                    <th>Tanggal</th>
                                                    <th>Waktu</th>
                                                    <th>SKS</th>
                                                    <th>Ruangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1; 
                                                while ($row = mysqli_fetch_assoc($result)): 
                                                ?>
                                                    <tr>
                                                        <td class="text-muted-theme fw-bold"><?= $no++ ?></td>
                                                        <td>
                                                            <?php if (!empty($row['gambar'])): ?>
                                                                <img src="<?= htmlspecialchars($row['gambar']) ?>" 
                                                                     alt="<?= htmlspecialchars($row['mata_kuliah']) ?>" 
                                                                     class="rounded-3 shadow-sm"
                                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                                            <?php else: ?>
                                                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-secondary bg-opacity-10"
                                                                     style="width: 60px; height: 60px;">
                                                                    <i class="bi bi-image text-muted fs-4"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold mb-1"><?= htmlspecialchars($row['mata_kuliah']) ?></div>
                                                            <span class="badge bg-primary bg-opacity-75"><?= htmlspecialchars($row['kelas']) ?></span>
                                                        </td>
                                                        <td>
                                                            <i class="bi bi-person me-1 text-muted-theme"></i>
                                                            <?= htmlspecialchars($row['dosen']) ?>
                                                        </td>
                                                        <td class="fw-semibold"><?= getDayName($row['tanggal_ujian']) ?></td>
                                                        <td><?= date('d F Y', strtotime($row['tanggal_ujian'])) ?></td>
                                                        <td>
                                                            <i class="bi bi-clock me-1 text-muted-theme"></i>
                                                            <?= date('H:i', strtotime($row['waktu_mulai'])) ?> - 
                                                            <?= date('H:i', strtotime($row['waktu_selesai'])) ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info bg-opacity-75"><?= htmlspecialchars($row['sks'] ?? 3) ?> SKS</span>
                                                        </td>
                                                        <td>
                                                            <i class="bi bi-geo-alt me-1 text-muted-theme"></i>
                                                            <?= htmlspecialchars($row['ruangan']) ?>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>

                                    <script>
                                    $(document).ready(function() {
                                        $('#table-detail').DataTable({
                                            responsive: false,
                                            scrollX: true,
                                            dom: '<"dt-toolbar"lf>rt<"dt-footer"ip>',
                                            language: {
                                                search: "Cari:",
                                                lengthMenu: "Tampilkan _MENU_ data",
                                                info: "Menampilkan _START_–_END_ dari _TOTAL_ jadwal",
                                                infoEmpty: "Tidak ada jadwal",
                                                paginate: { previous: "‹", next: "›" }
                                            },
                                            order: []
                                        });
                                    });
                                    </script>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <div class="fs-1 mb-3 text-secondary"><i class="bi bi-calendar"></i></div>
                                        <h4 class="text-theme">Belum ada jadwal ujian untuk kelas <?= htmlspecialchars($kelas) ?></h4>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="row g-4">
                                    <div class="col-lg-4">
                                        <?php if (!empty($row['gambar'])): ?>
                                            <div class="mb-4">
                                                <img src="<?= htmlspecialchars($row['gambar']) ?>" 
                                                     alt="Gambar <?= htmlspecialchars($row['mata_kuliah']) ?>" 
                                                     class="img-thumbnail rounded-4 shadow-sm w-100">
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="card border-0 shadow-sm" style="background-color: var(--color-bg-card);">
                                            <div class="card-body p-4">
                                                    <h5 class="fw-bold mb-3" style="color: var(--color-text);">Informasi</h5>
                                                    <div class="mb-3">
                                                        <div class="small text-muted-theme mb-1">
                                                            <i class="bi bi-door-open me-1"></i>Kelas
                                                        </div>
                                                        <div class="fw-semibold" style="color: var(--color-text);"><?= htmlspecialchars($row['kelas']) ?></div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="small text-muted-theme mb-1">
                                                            <i class="bi bi-person me-1"></i>Dosen Pengampu
                                                        </div>
                                                        <div class="fw-semibold" style="color: var(--color-text);"><?= htmlspecialchars($row['dosen']) ?></div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="small text-muted-theme mb-1">
                                                            <i class="bi bi-book me-1"></i>Jumlah SKS
                                                        </div>
                                                        <div class="fw-semibold" style="color: var(--color-text);"><?= htmlspecialchars($row['sks'] ?? 3) ?></div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="small text-muted-theme mb-1">
                                                            <i class="bi bi-geo-alt me-1"></i>Ruangan
                                                        </div>
                                                        <div class="fw-semibold" style="color: var(--color-text);"><?= htmlspecialchars($row['ruangan']) ?></div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-8">
                                        <div class="card border-0 shadow-sm mb-4" style="background-color: var(--bg-card);">
                                            <div class="card-body p-4">
                                                <h5 class="fw-bold mb-3" style="color: var(--color-text);">
                                                    <i class="bi bi-calendar-event me-2 text-primary"></i>Jadwal Ujian
                                                </h5>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="small text-muted-theme mb-1">Hari</div>
                                                        <div class="fw-semibold" style="color: var(--color-text);"><?= getDayName($row['tanggal_ujian']) ?></div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="small text-muted-theme mb-1">Tanggal</div>
                                                        <div class="fw-semibold" style="color: var(--color-text);"><?= date('d F Y', strtotime($row['tanggal_ujian'])) ?></div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="small text-muted-theme mb-1">Waktu</div>
                                                        <div class="fw-semibold" style="color: var(--color-text);">
                                                            <i class="bi bi-clock me-1"></i>
                                                            <?= date('H:i', strtotime($row['waktu_mulai'])) ?> - 
                                                            <?= date('H:i', strtotime($row['waktu_selesai'])) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($row['deskripsi'])): ?>
                                            <div class="card border-0 shadow-sm" style="background-color: var(--color-bg-card);">
                                                <div class="card-body p-4">
                                                    <h5 class="fw-bold mb-3" style="color: var(--color-text);">
                                                        <i class="bi bi-info-circle me-2 text-primary"></i>Catatan
                                                    </h5>
                                                    <p class="text-theme mb-0">
                                                        <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
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