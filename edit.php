<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$rootPath = __DIR__;

include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */

require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: /login.php?toast_type=warning&toast_title=Akses Ditolak&toast_message=Silakan login terlebih dahulu");
    exit();
}

$errors = [];
$id_get = (int)($_GET['id'] ?? 0);

$row = [];
try {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_jadwal WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_get);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $errors[] = "Database error: " . $e->getMessage();
}

if (!$row) {
    header("Location: /jadwal.php?toast_type=error&toast_title=Error&toast_message=Jadwal ujian tidak ditemukan.");
    exit();
}

if (!isset($row['sks'])) {
    $row['sks'] = 3;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mata_kuliah = trim($_POST['mata_kuliah'] ?? $row['mata_kuliah']);
    $dosen = trim($_POST['dosen'] ?? $row['dosen']);
    $tanggal_ujian = trim($_POST['tanggal_ujian'] ?? $row['tanggal_ujian']);
    $waktu_mulai = trim($_POST['waktu_mulai'] ?? $row['waktu_mulai']);
    $waktu_selesai = trim($_POST['waktu_selesai'] ?? $row['waktu_selesai']);
    $ruangan = trim($_POST['ruangan'] ?? $row['ruangan']);
    $kelas = trim($_POST['kelas'] ?? $row['kelas']);
    $gambar = trim($_POST['gambar'] ?? $row['gambar'] ?? '');
    $sks = intval($_POST['sks'] ?? $row['sks'] ?? 3);
    $updated = false;

    try {
        $stmt = mysqli_prepare($koneksi, "UPDATE tb_jadwal SET mata_kuliah=?, dosen=?, tanggal_ujian=?, waktu_mulai=?, waktu_selesai=?, ruangan=?, sks=?, kelas=?, gambar=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'sssssisssi', $mata_kuliah, $dosen, $tanggal_ujian, $waktu_mulai, $waktu_selesai, $ruangan, $sks, $kelas, $gambar, $id_get);
        
        if (mysqli_stmt_execute($stmt)) {
            $updated = true;
        }
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        try {
            $stmt = mysqli_prepare($koneksi, "UPDATE tb_jadwal SET mata_kuliah=?, dosen=?, tanggal_ujian=?, waktu_mulai=?, waktu_selesai=?, ruangan=?, kelas=?, gambar=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssssssssi', $mata_kuliah, $dosen, $tanggal_ujian, $waktu_mulai, $waktu_selesai, $ruangan, $kelas, $gambar, $id_get);
            
            if (mysqli_stmt_execute($stmt)) {
                $updated = true;
            }
            mysqli_stmt_close($stmt);
        } catch (Exception $e2) {
            $errors[] = "Gagal update: " . $e2->getMessage();
        }
    }

    if ($updated) {
        header("Location: /jadwal.php?toast_type=success&toast_title=Berhasil&toast_message=Jadwal ujian berhasil diperbarui!");
        exit();
    } else {
        $row = array_merge($row, compact('mata_kuliah', 'dosen', 'tanggal_ujian', 'waktu_mulai', 'waktu_selesai', 'ruangan', 'kelas', 'gambar', 'sks'));
    }
}

include 'partials/dashboard/header.php';
include 'partials/dashboard/sidebar.php';
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
                        <h2 class="fw-bold mb-0 fs-4">Ubah Data Jadwal Ujian</h2>
                        <p class="text-muted mb-0 small">Perbarui informasi jadwal ujian di sistem EduTrack.</p>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Ada kesalahan:</strong>
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
                        <form action="" method="post" novalidate id="form-edit-jadwal">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="mata_kuliah" class="form-label fw-semibold">Mata Kuliah <span class="text-danger">*</span></label>
                                        <input type="text" name="mata_kuliah" id="mata_kuliah" value="<?php echo htmlspecialchars($row['mata_kuliah']); ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="dosen" class="form-label fw-semibold">Dosen Pengampu <span class="text-danger">*</span></label>
                                        <input type="text" name="dosen" id="dosen" value="<?php echo htmlspecialchars($row['dosen']); ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="kelas" class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
                                        <input type="text" name="kelas" id="kelas" value="<?php echo htmlspecialchars($row['kelas']); ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ruangan" class="form-label fw-semibold">Ruangan <span class="text-danger">*</span></label>
                                        <input type="number" name="ruangan" id="ruangan" value="<?php echo htmlspecialchars($row['ruangan']); ?>" class="form-control" required placeholder="Contoh: 101">
                                    </div>
                                    <div class="mb-3">
                                        <label for="sks" class="form-label fw-semibold">Jumlah SKS <span class="text-danger">*</span></label>
                                        <input type="number" name="sks" id="sks" value="<?php echo htmlspecialchars($row['sks'] ?? 3); ?>" class="form-control" required min="1" max="10">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_ujian" class="form-label fw-semibold">Tanggal Ujian <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_ujian" id="tanggal_ujian" value="<?php echo htmlspecialchars($row['tanggal_ujian']); ?>" class="form-control" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="waktu_mulai" class="form-label fw-semibold">Waktu Mulai <span class="text-danger">*</span></label>
                                                <input type="time" name="waktu_mulai" id="waktu_mulai" value="<?php echo htmlspecialchars($row['waktu_mulai']); ?>" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="waktu_selesai" class="form-label fw-semibold">Waktu Selesai <span class="text-danger">*</span></label>
                                                <input type="time" name="waktu_selesai" id="waktu_selesai" value="<?php echo htmlspecialchars($row['waktu_selesai']); ?>" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gambar" class="form-label fw-semibold">Gambar (Opsional)</label>
                                        <div class="input-group">
                                            <input type="text" name="gambar" id="gambar" value="<?php echo htmlspecialchars($row['gambar'] ?? ''); ?>" class="form-control" readonly placeholder="URL gambar akan muncul disini...">
                                            <label class="btn btn-primary" for="file-gambar">
                                                <i class="bi bi-upload me-1"></i> Pilih Gambar
                                            </label>
                                            <input type="file" id="file-gambar" accept="image/*" style="display:none;">
                                        </div>
                                        <div id="upload-status" class="mt-2" style="display:none;"></div>
                                        <div id="preview-gambar" class="mt-2" style="display: <?php echo !empty($row['gambar']) ? 'block' : 'none'; ?>;">
                                            <img src="<?php echo htmlspecialchars($row['gambar'] ?? ''); ?>" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="d-flex gap-2">
                                <button type="submit" id="btn-submit-edit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
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
document.getElementById('form-edit-jadwal').addEventListener('submit', function(e) {
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
<?php include 'partials/dashboard/footer.php'; ?>
