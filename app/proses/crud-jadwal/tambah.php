<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$rootPath = dirname(__DIR__, 3);
include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */
require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: /login.php?toast_type=warning&toast_title=Akses Ditolak&toast_message=Silakan login terlebih dahulu");
    exit();
}

$errors = [];
$oldPost = [
    'mata_kuliah' => '',
    'dosen' => '',
    'tanggal_ujian' => '',
    'waktu_mulai' => '',
    'waktu_selesai' => '',
    'ruangan' => '',
    'kelas' => '',
    'gambar' => '',
    'sks' => 3
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mata_kuliah = trim($_POST['mata_kuliah'] ?? '');
    $dosen = trim($_POST['dosen'] ?? '');
    $tanggal_ujian = trim($_POST['tanggal_ujian'] ?? '');
    $waktu_mulai = trim($_POST['waktu_mulai'] ?? '');
    $waktu_selesai = trim($_POST['waktu_selesai'] ?? '');
    $ruangan = trim($_POST['ruangan'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $gambar = trim($_POST['gambar'] ?? '');
    $sks = intval($_POST['sks'] ?? 3);

    $oldPost = compact('mata_kuliah', 'dosen', 'tanggal_ujian', 'waktu_mulai', 'waktu_selesai', 'ruangan', 'kelas', 'gambar', 'sks');

    if (empty($mata_kuliah)) $errors[] = "Mata kuliah wajib diisi.";
    if (empty($dosen)) $errors[] = "Nama dosen wajib diisi.";
    if (empty($tanggal_ujian)) $errors[] = "Tanggal ujian wajib diisi.";
    if (empty($waktu_mulai)) $errors[] = "Waktu mulai wajib diisi.";
    if (empty($waktu_selesai)) $errors[] = "Waktu selesai wajib diisi.";
    if (empty($ruangan)) $errors[] = "Ruangan wajib diisi.";
    if (empty($kelas)) $errors[] = "Kelas wajib diisi.";
    if ($sks < 1 || $sks > 10) $errors[] = "Jumlah SKS harus antara 1 dan 10.";

    if (empty($errors)) {
        $inserted = false;

        // Coba INSERT dengan dan tanpa kolom sks
        try {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_jadwal (mata_kuliah, dosen, tanggal_ujian, waktu_mulai, waktu_selesai, ruangan, sks, kelas, gambar, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            // Hitung: s s s s s s i s s → 9 karakter untuk 9 parameter (is_active hardcoded)
            mysqli_stmt_bind_param($stmt, 'sssssisss', $mata_kuliah, $dosen, $tanggal_ujian, $waktu_mulai, $waktu_selesai, $ruangan, $sks, $kelas, $gambar);
            
            if (mysqli_stmt_execute($stmt)) {
                $inserted = true;
            }
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            // Jika gagal, coba tanpa kolom sks
            try {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_jadwal (mata_kuliah, dosen, tanggal_ujian, waktu_mulai, waktu_selesai, ruangan, kelas, gambar, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
                // Hitung: s s s s s s s s → 8 karakter untuk 8 parameter (is_active hardcoded)
                mysqli_stmt_bind_param($stmt, 'ssssssss', $mata_kuliah, $dosen, $tanggal_ujian, $waktu_mulai, $waktu_selesai, $ruangan, $kelas, $gambar);
                
                if (mysqli_stmt_execute($stmt)) {
                    $inserted = true;
                }
                mysqli_stmt_close($stmt);
            } catch (Exception $e2) {
                $errors[] = "Gagal menyimpan ke database: " . $e2->getMessage();
            }
        }

        if ($inserted) {
            header("Location: /jadwal.php?toast_type=success&toast_title=Berhasil&toast_message=Jadwal ujian berhasil disimpan!");
            exit();
        }
    }
}

include $rootPath . '/partials/dashboard/header.php';
include $rootPath . '/partials/dashboard/sidebar.php';
?>
<div class="main-content">
    <div class="container-fluid mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="mb-4">
                    <a href="/jadwal.php" class="btn btn-outline-secondary btn-sm mb-3">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <div>
                        <h2 class="fw-bold mb-0 fs-4">Tambah Jadwal Ujian Baru</h2>
                        <p class="text-muted mb-0 small">Masukkan jadwal ujian baru ke sistem EduTrack.</p>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Ada kesalahan pengisian data:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0" style="background-color: var(--bg-card);">
                    <div class="card-body p-4">
                        <form action="" method="post" novalidate id="form-tambah-jadwal">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="mata_kuliah" class="form-label fw-semibold">Mata Kuliah <span class="text-danger">*</span></label>
                                        <input type="text" name="mata_kuliah" id="mata_kuliah" value="<?php echo htmlspecialchars($oldPost['mata_kuliah']); ?>" class="form-control" required placeholder="Contoh: Pemrograman Web">
                                    </div>
                                    <div class="mb-3">
                                        <label for="dosen" class="form-label fw-semibold">Dosen Pengampu <span class="text-danger">*</span></label>
                                        <input type="text" name="dosen" id="dosen" value="<?php echo htmlspecialchars($oldPost['dosen']); ?>" class="form-control" required placeholder="Contoh: Dr. Ahmad Reza">
                                    </div>
                                    <div class="mb-3">
                                        <label for="kelas" class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                                        <input type="text" name="kelas" id="kelas" value="<?php echo htmlspecialchars($oldPost['kelas']); ?>" class="form-control" required placeholder="Contoh: A, B, atau 3A">
                                    </div>
                                    <div class="mb-3">
                                        <label for="ruangan" class="form-label fw-semibold">Ruangan <span class="text-danger">*</span></label>
                                        <input type="number" name="ruangan" id="ruangan" value="<?php echo htmlspecialchars($oldPost['ruangan']); ?>" class="form-control" required placeholder="Contoh: 101">
                                    </div>
                                    <div class="mb-3">
                                        <label for="sks" class="form-label fw-semibold">Jumlah SKS <span class="text-danger">*</span></label>
                                        <input type="number" name="sks" id="sks" value="<?php echo htmlspecialchars($oldPost['sks']); ?>" class="form-control" required min="1" max="10" placeholder="Contoh: 3">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_ujian" class="form-label fw-semibold">Tanggal Ujian <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_ujian" id="tanggal_ujian" value="<?php echo htmlspecialchars($oldPost['tanggal_ujian']); ?>" class="form-control" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="waktu_mulai" class="form-label fw-semibold">Waktu Mulai <span class="text-danger">*</span></label>
                                                <input type="time" name="waktu_mulai" id="waktu_mulai" value="<?php echo htmlspecialchars($oldPost['waktu_mulai']); ?>" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="waktu_selesai" class="form-label fw-semibold">Waktu Selesai <span class="text-danger">*</span></label>
                                                <input type="time" name="waktu_selesai" id="waktu_selesai" value="<?php echo htmlspecialchars($oldPost['waktu_selesai']); ?>" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gambar" class="form-label fw-semibold">Gambar (Opsional)</label>
                                        <div class="input-group">
                                            <input type="text" name="gambar" id="gambar" value="<?php echo htmlspecialchars($oldPost['gambar']); ?>" class="form-control" readonly placeholder="URL gambar akan muncul disini...">
                                            <label class="btn btn-primary" for="file-gambar">
                                                <i class="bi bi-upload me-1"></i> Pilih Gambar
                                            </label>
                                            <input type="file" id="file-gambar" accept="image/*" style="display:none;">
                                        </div>
                                        <div id="upload-status" class="mt-2" style="display:none;"></div>
                                        <div id="preview-gambar" class="mt-2" style="display: <?php echo !empty($oldPost['gambar']) ? 'block' : 'none'; ?>;">
                                            <img src="<?php echo htmlspecialchars($oldPost['gambar']); ?>" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="d-flex gap-2">
                                <button type="submit" id="btn-submit-tambah" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Simpan Jadwal
                                </button>
                                <a href="/jadwal.php" class="btn btn-outline-secondary px-4">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('form-tambah-jadwal').addEventListener('submit', function(e) {
    const waktuMulai = document.getElementById('waktu_mulai').value;
    const waktuSelesai = document.getElementById('waktu_selesai').value;
    
    if (waktuMulai && waktuSelesai && waktuMulai >= waktuSelesai) {
        e.preventDefault();
        alert('Waktu selesai harus lebih besar dari waktu mulai!');
    }
});

document.getElementById('file-gambar').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const uploadStatus = document.getElementById('upload-status');
    uploadStatus.style.display = 'block';
    uploadStatus.innerHTML = `
        <div class="alert alert-info" role="alert">
            <i class="bi bi-hourglass-split me-2"></i> Mengunggah gambar... <span id="upload-progress-text">0%</span>
        </div>
    `;
    const progressText = document.getElementById('upload-progress-text');

    try {
        const signatureRes = await fetch('/app/service/cloudinary-signature.php');
        
        if (!signatureRes.ok) {
            throw new Error('Gagal mengambil signature');
        }
        
        const signatureData = await signatureRes.json();
        
        if (!signatureData.success) {
            throw new Error(signatureData.error || 'Gagal mengambil signature');
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('api_key', signatureData.apiKey);
        formData.append('timestamp', signatureData.timestamp);
        formData.append('signature', signatureData.signature);
        formData.append('folder', signatureData.folder);

        const xhr = new XMLHttpRequest();
        const uploadUrl = `https://api.cloudinary.com/v1_1/${signatureData.cloudName}/image/upload`;

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                progressText.textContent = percentComplete + '%';
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                const uploadData = JSON.parse(xhr.responseText);
                if (uploadData.secure_url) {
                    document.getElementById('gambar').value = uploadData.secure_url;
                    document.getElementById('preview-gambar').innerHTML = '<img src="' + uploadData.secure_url + '" alt="Preview" class="img-thumbnail" style="max-height: 150px;">';
                    document.getElementById('preview-gambar').style.display = 'block';
                    uploadStatus.innerHTML = '<div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i> Gambar berhasil diunggah!</div>';
                } else {
                    const errorMsg = uploadData.error?.message || 'Gagal mengunggah gambar';
                    throw new Error(errorMsg);
                }
            } else {
                throw new Error('Upload gagal: ' + xhr.status);
            }
        });

        xhr.addEventListener('error', function() {
            throw new Error('Gagal mengunggah gambar');
        });

        xhr.open('POST', uploadUrl);
        xhr.send(formData);
        
    } catch (err) {
        console.error(err);
        uploadStatus.innerHTML = '<div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle me-2"></i> ' + (err.message || 'Gagal mengunggah gambar') + '</div>';
    }
});
</script>
<?php include $rootPath . '/partials/dashboard/footer.php'; ?>
