<?php
$rootPath = __DIR__;

include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */

require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: /login.php?toast_type=warning&toast_title=Akses Ditolak&toast_message=Silakan login terlebih dahulu");
    exit();
}

$activeMenu = 'jadwal';
include 'partials/dashboard/header.php';
include 'partials/dashboard/sidebar.php';

$queryKelas = "SELECT DISTINCT kelas FROM tb_jadwal ORDER BY kelas ASC";
$resultKelas = mysqli_query($koneksi, $queryKelas);

$query  = "SELECT * FROM tb_jadwal ORDER BY tanggal_ujian ASC, waktu_mulai ASC";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Error database: " . mysqli_error($koneksi) . "<br>Pastikan tabel tb_jadwal sudah dibuat. Jalankan SQL di file database-update.sql");
}
?>

<link rel="stylesheet" href="/style/table.css">

<div class="main-content">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h2 class="fw-bold mb-0 fs-4">Kelola Jadwal Ujian</h2>
                                <p class="text-muted small mb-0">EduTrack CMS</p>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="/app/proses/crud-jadwal/tambah.php" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal
                                </a>
                                <a href="/app/service/print.php" class="btn btn-outline-secondary" target="_blank">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Filter Kelas</label>
                                <select id="filterKelas" class="form-select">
                                    <option value="">Semua Kelas</option>
                                    <?php while ($rowKelas = mysqli_fetch_assoc($resultKelas)): ?>
                                        <option value="<?= htmlspecialchars($rowKelas['kelas']) ?>">
                                            Kelas <?= htmlspecialchars($rowKelas['kelas']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-2 p-md-3">
                            <table class="table table-hover align-middle mb-0 w-100 dt-card-table" id="table_jadwal">
                                <thead id="table-thead-jadwal">
                                    <tr>
                                        <th class="ps-3">No</th>
                                        <th>Gambar</th>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Kelas</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Ruangan</th>
                                        <th>SKS</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $no = 1;
                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr id="baris-jadwal-<?= $row['id'] ?>" data-kelas="<?= htmlspecialchars($row['kelas']) ?>">
                                        <td class="ps-3 text-muted"><?= $no++ ?></td>
                                        <td>
                                            <?php if (!empty($row['gambar'])): ?>
                                                <img src="<?= htmlspecialchars($row['gambar']) ?>" 
                                                     alt="Gambar <?= htmlspecialchars($row['mata_kuliah']) ?>" 
                                                     class="rounded" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="bi bi-image text-secondary"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-semibold">
                                            <a href="/detail.php?id=<?= (int)$row['id'] ?>" 
                                               class="text-decoration-none text-reset">
                                                <?= htmlspecialchars($row['mata_kuliah']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($row['dosen']) ?></td>
                                        <td data-search="<?= htmlspecialchars($row['kelas']) ?>">
                                            <span class="badge bg-light text-dark border">
                                                <?= htmlspecialchars($row['kelas']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_ujian'])) ?></td>
                                        <td><?= date('H:i', strtotime($row['waktu_mulai'])) ?> - <?= date('H:i', strtotime($row['waktu_selesai'])) ?></td>
                                        <td><?= htmlspecialchars($row['ruangan']) ?></td>
                                        <td><span class="badge bg-primary"><?= htmlspecialchars($row['sks'] ?? 3) ?></span></td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column flex-sm-row gap-1 justify-content-center">
                                                <a href="/edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <button type="button" class="btn btn-danger btn-sm btn-hapus-kustom" data-id="<?= $row['id'] ?>" data-judul="<?= htmlspecialchars($row['mata_kuliah']) ?>">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                } 
                                ?>
                                </tbody>
                            </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapusProduk" tabindex="-1" aria-labelledby="modalHapusProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" id="modal-content-hapus">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHapusProdukLabel">Konfirmasi Hapus Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                Apakah Anda yakin ingin menghapus jadwal ujian <strong id="nama-produk-modal" class="text-warning"></strong> dari sistem?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-konfirmasi-eksekusi" class="btn btn-danger">Ya, Hapus Jadwal</button>
            </div>
        </div>
    </div>
</div>

<script>
function updateProdukTheme() {
    const thead = document.getElementById('table-thead-jadwal');
    const modalHapus = document.getElementById('modal-content-hapus');
    const htmlTheme = document.documentElement.getAttribute('data-bs-theme');
    
    if (thead) {
        thead.classList.remove('table-dark', 'table-light');
        thead.classList.add(htmlTheme === 'dark' ? 'table-dark' : 'table-light');
    }
    
    if (modalHapus) {
        modalHapus.classList.remove('bg-dark', 'text-white', 'border-secondary');
        if (htmlTheme === 'dark') {
            modalHapus.classList.add('bg-dark', 'text-white', 'border-secondary');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateProdukTheme();
});

const observerProduk = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.attributeName === 'data-bs-theme') {
            updateProdukTheme();
        }
    });
});
observerProduk.observe(document.documentElement, { attributes: true });

(function waitForJQuery() {
    if (typeof window.jQuery === 'undefined') {
        setTimeout(waitForJQuery, 50);
        return;
    }
    initJadwalPage(jQuery);
})();

function initJadwalPage($) {
$(document).ready(function () {
    console.log('Initializing DataTable for jadwal...');
    
    if ($.fn.DataTable.isDataTable('#table_jadwal')) {
        $('#table_jadwal').DataTable().destroy();
    }
   
    var table = $('#table_jadwal').DataTable({
        responsive: true,
        autoWidth: false,
        dom: '<"dt-toolbar d-flex justify-content-between align-items-center flex-wrap"lf>rt<"d-flex justify-content-between align-items-center flex-wrap"ip>',
        columnDefs: [
            { orderable: false, targets: [0, 1, 9] },
            { responsivePriority: 1, targets: 2 },
            { responsivePriority: 2, targets: 9 },
            { responsivePriority: 3, targets: 4 }
        ],
        language: {
            search:      "Cari:",
            lengthMenu:  "Tampilkan _MENU_ data",
            info:        "Menampilkan _START_–_END_ dari _TOTAL_ jadwal",
            infoEmpty:   "Tidak ada data",
            zeroRecords: "Jadwal tidak ditemukan. Silakan tambah jadwal baru.",
            paginate: { previous: "‹ Prev", next: "Next ›" }
        },
        order: [],
     
        initComplete: function (settings, json) {
            console.log('DataTable initialized successfully!');
            var $tableNode = $(this.api().table().node());
            if (!$tableNode.parent().hasClass('table-scroll-x')) {
                $tableNode.wrap('<div class="table-scroll-x"></div>');
            }
        }
    });

    $('#filterKelas').on('change', function() {
        var selectedKelas = $(this).val();
        
        if (selectedKelas) {
            table.column(4).search('^' + selectedKelas + '$', true, false).draw();
        } else {
            table.column(4).search('').draw();
        }
    });

    var idJadwalYgDihapus = null;

  
    toastr.options = {
        "closeButton": true,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "timeOut": "3000",
        "iconClass": ""
    };


   
    $(document).on('click', '.btn-hapus-kustom', function() {
        idJadwalYgDihapus = $(this).data('id');
        var judulJadwal = $(this).data('judul');
        $('#nama-produk-modal').text(judulJadwal);
        $('#modalHapusProduk').modal('show');
    });

    $('#btn-konfirmasi-eksekusi').click(function() {
        if(idJadwalYgDihapus) {
            $.ajax({
                url: "/app/proses/crud-jadwal/hapus.php", 
                method: "GET",
                data: { id: idJadwalYgDihapus },
                success: function(response) {
                    $('#modalHapusProduk').modal('hide');

                    if (typeof showToast === 'function') {
                        showToast('success', 'Berhasil', 'Jadwal ujian berhasil dihapus dari sistem!');
                    }
                    table.row('#baris-jadwal-' + idJadwalYgDihapus).remove().draw(false);
                },
                error: function() {
                    $('#modalHapusProduk').modal('hide');
                    if (typeof showToast === 'function') {
                        showToast('error', 'Gagal', 'Terjadi kesalahan server saat mencoba menghapus data.');
                    }
                }
            });
        }
    });
});
} 
</script>
<?php include 'partials/dashboard/footer.php'; ?>
