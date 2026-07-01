<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
$rootPath = dirname(__DIR__, 3);
include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */
require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: /login.php?toast_type=warning&toast_title=Akses Ditolak&toast_message=Silakan login terlebih dahulu");
    exit();
}

include $rootPath . '/partials/dashboard/header.php';
include $rootPath . '/partials/dashboard/sidebar.php';

$baseUrl = '';

$errors  = [];
$oldPost = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $harga     = trim($_POST['harga'] ?? '');
    $stok      = trim($_POST['stok'] ?? '1');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    $url_cloudinary_gambar = trim($_POST['gambar_url'] ?? '');
    $url_cloudinary_file   = trim($_POST['asset_url'] ?? '');

    $oldPost = compact('judul', 'kategori', 'harga', 'stok', 'deskripsi');

    if (empty($judul))    $errors[] = "Nama produk / judul wajib diisi.";
    if (empty($kategori)) $errors[] = "Kategori aset wajib dipilih.";
    if (empty($harga))    $errors[] = "Harga produk wajib diisi.";
    if (empty($url_cloudinary_gambar)) $errors[] = "Gambar preview/thumbnail wajib diunggah (upload gagal atau belum dipilih).";
    if (empty($url_cloudinary_file))   $errors[] = "File master aset (.ttf/.mp3) wajib diunggah (upload ke gagal atau belum dipilih).";

    if (empty($errors)) {
        if (!isset($koneksi) || !$koneksi) {
            $errors[] = "Koneksi database tidak tersedia.";
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_produk (gambar, judul, kategori, harga, stok, file, deskripsi, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $harga_int = (int)$harga;
            $stok_int  = (int)$stok;

            mysqli_stmt_bind_param($stmt, 'ssssiss', $url_cloudinary_gambar, $judul, $kategori, $harga_int, $stok_int, $url_cloudinary_file, $deskripsi);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['flash_success'] = 'Aset produk berhasil disimpan database!';
                echo "<script>window.location.href='/produk.php';</script>";
                exit();
            } else {
                $errors[] = "Gagal menyimpan ke database: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<div class="main-content">
    <div class="container-fluid mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="mb-4">
                    <a href="/produk.php" class="btn btn-outline-secondary btn-sm mb-3">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <div>
                        <h2 class="fw-bold mb-0 fs-4">Tambah Produk Baru</h2>
                        <p class="text-muted mb-0 small">Masukkan aset digital baru ke Sonata Store.</p>
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

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="" method="post" enctype="multipart/form-data" novalidate id="form-tambah-produk">
                            <input type="hidden" name="gambar_url" id="gambar_url" value="">
                            <input type="hidden" name="asset_url" id="asset_url" value="">

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="judul" class="form-label fw-semibold">Nama Produk / Judul <span class="text-danger">*</span></label>
                                        <input type="text" name="judul" id="judul" value="<?php echo htmlspecialchars($oldPost['judul'] ?? ''); ?>" class="form-control" placeholder="Contoh: Lato Pro Font Family" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="kategori" class="form-label fw-semibold">Kategori Aset <span class="text-danger">*</span></label>
                                        <input type="text" name="kategori" id="kategori" value="<?php echo htmlspecialchars($oldPost['kategori'] ?? ''); ?>" class="form-control" placeholder="Contoh: Font, Audio, Template, dll." required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="harga" class="form-label fw-semibold">Harga Jual (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" name="harga" id="harga" value="<?php echo htmlspecialchars($oldPost['harga'] ?? ''); ?>" class="form-control" placeholder="Contoh: 25000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="stok" class="form-label fw-semibold">Stok Barang Digital</label>
                                        <input type="number" name="stok" id="stok" value="<?php echo htmlspecialchars($oldPost['stok'] ?? '999'); ?>" class="form-control" placeholder="Default: 999" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi Produk</label>
                                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Keterangan mengenai lisensi atau kegunaan aset..."><?php echo htmlspecialchars($oldPost['deskripsi'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gambar_file" class="form-label fw-semibold">Gambar Preview / Thumbnail <span class="text-danger">*</span></label>
                                        <input type="file" name="gambar_file" id="gambar_file" class="form-control" accept="image/jpeg,image/png,image/webp" required>
                                        <div class="form-text">Format JPG/PNG/WEBP. Diupload langsung ke server.</div>
                                        <img id="preview" src="#" alt="" style="display:none;margin-top:8px;max-width:120px;max-height:120px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6">
                                    </div>
                                    <div class="mb-3">
                                        <label for="asset_file" class="form-label fw-semibold">File Master Asli (Font/Audio) <span class="text-danger">*</span></label>
                                        <input type="file" name="asset_file" id="asset_file" class="form-control" required>
                                        <div class="form-text">Akan otomatis terunggah ke server.</div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div id="upload-status" class="small text-muted mb-2"></div>
                            <div class="d-flex gap-2">
                                <button type="submit" id="btn-submit-tambah" class="btn btn-primary px-4">Simpan Aset</button>
                                <a href="/produk.php" class="btn btn-outline-secondary px-4">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="upload-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,15,20,0.75);z-index:9999;align-items:center;justify-content:center;flex-direction:column;color:#fff;text-align:center;padding:1rem;">
    <div class="spinner-border text-light mb-3" role="status" style="width:3rem;height:3rem;"></div>
    <div id="upload-overlay-text" class="fw-semibold fs-5">Mengunggah...</div>
    <div class="small mt-2 text-white-50">Proses ini bisa makan waktu untuk file berukuran besar.<br>Jangan tutup atau refresh halaman ini.</div>
</div>
<script>
document.getElementById('gambar_file').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');
    if (file) {
        const reader = new FileReader();
        reader.onload = ev => { preview.src = ev.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(file);
    } else { preview.style.display = 'none'; }
});

const SIGNATURE_ENDPOINT = '/app/service/cloudinary-signature.php';

async function getCloudinarySignature(type) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 15000);

    let res;
    try {
        res = await fetch(`${SIGNATURE_ENDPOINT}?type=${type}`, { signal: controller.signal });
    } catch (err) {
        throw new Error('Gagal menghubungi server untuk upload (timeout atau koneksi terputus). Cek koneksi internet lalu coba lagi.');
    } finally {
        clearTimeout(timeoutId);
    }

    let data;
    try {
        data = await res.json();
    } catch (err) {
        throw new Error('Server tidak mengembalikan respon yang valid dari cloudinary-signature.');
    }

    if (!res.ok || data.error) throw new Error(data.error || 'Gagal mengambil signature upload.');
    return data;
}

async function uploadToCloudinaryDirect(file, type, onProgress) {
    const sig = await getCloudinarySignature(type);

    const formData = new FormData();
    formData.append('file', file);
    formData.append('api_key', sig.apiKey);
    formData.append('timestamp', sig.timestamp);
    formData.append('signature', sig.signature);
    formData.append('folder', sig.folder);

    const uploadUrl = `https://api.cloudinary.com/v1_1/${sig.cloudName}/${sig.resourceType}/upload`;

    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', uploadUrl);
        xhr.timeout = 120000;
        xhr.upload.onprogress = (e) => {
            if (e.lengthComputable && onProgress) onProgress(Math.round((e.loaded / e.total) * 100));
        };
        xhr.onload = () => {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.error) return reject(new Error(data.error.message));
                resolve(data.secure_url);
            } catch (err) {
                reject(new Error('Respon server tidak valid.'));
            }
        };
        xhr.onerror = () => reject(new Error('Koneksi ke server gagal.'));
        xhr.ontimeout = () => reject(new Error('Upload ke server timeout (lebih dari 2 menit). Coba file lebih kecil atau cek koneksi internet.'));
        xhr.send(formData);
    });
}

const formTambah   = document.getElementById('form-tambah-produk');
const btnSubmit     = document.getElementById('btn-submit-tambah');
const statusEl       = document.getElementById('upload-status');
const overlay         = document.getElementById('upload-overlay');
const overlayText     = document.getElementById('upload-overlay-text');

let isSubmitting = false;

function showOverlay(text, percent = 0) {
    overlayText.textContent = text;
    document.getElementById('upload-overlay-percent').textContent = percent + '%';
    overlay.style.display = 'flex';
    document.body.style.filter = 'blur(4px)';
    document.body.style.transition = 'filter 0.3s ease';
}
function hideOverlay() {
    overlay.style.display = 'none';
    document.body.style.filter = 'none';
}
function beforeUnloadHandler(e) {
    e.preventDefault();
    e.returnValue = '';
}

(function waitForToastr() {
    if (typeof window.toastr === 'undefined') {
        setTimeout(waitForToastr, 50);
        return;
    }
    initTambahPage();
})();

function initTambahPage() {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: "4000",
        iconClass: ""
    };

    formTambah.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (isSubmitting) return;
        isSubmitting = true;

        btnSubmit.disabled = true;
        const originalLabel = btnSubmit.textContent;
        window.addEventListener('beforeunload', beforeUnloadHandler);
        showOverlay('Menyiapkan upload...');

        try {
            const gambarFile = document.getElementById('gambar_file').files[0];
            const assetFile  = document.getElementById('asset_file').files[0];
            const MAX_SIZE = 10 * 1024 * 1024; // 10 MB
            if ((gambarFile && gambarFile.size > MAX_SIZE) || (assetFile && assetFile.size > MAX_SIZE)) {
                throw new Error('Maksimal ukuran seluruh file dan gambar adalah 10 MB.');
            }

            if (gambarFile) {
                btnSubmit.textContent = 'Mengunggah gambar...';
                showOverlay('Mengunggah gambar preview ke Cloudinary... 0%');
                document.getElementById('gambar_url').value = await uploadToCloudinaryDirect(
                    gambarFile, 'image', (p) => {
                        statusEl.textContent = `Mengunggah gambar preview... ${p}%`;
                        showOverlay(`Mengunggah gambar preview... ${p}%`);
                    }
                );
            }

            if (assetFile) {
                btnSubmit.textContent = 'Mengunggah file master...';
                showOverlay('Mengunggah file ke server... 0%');
                document.getElementById('asset_url').value = await uploadToCloudinaryDirect(
                    assetFile, 'raw', (p) => {
                        statusEl.textContent = `Mengunggah file ke server... ${p}%`;
                        showOverlay(`Mengunggah file ke server... ${p}%`);
                    }
                );
            }

            document.getElementById('gambar_file').removeAttribute('name');
            document.getElementById('asset_file').removeAttribute('name');

            statusEl.textContent = 'Menyimpan data produk...';
            showOverlay('Menyimpan data produk ke database...', 100);

            window.removeEventListener('beforeunload', beforeUnloadHandler);

            // Submit form via AJAX to show success toast
            const formData = new FormData(formTambah);
            const response = await fetch(formTambah.action, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.text();
            
            hideOverlay();
            
            if (result === 'success') {
                toastr.success('Aset produk berhasil disimpan ke Cloudinary & database!');
                setTimeout(() => {
                    window.location.href = '/produk.php';
                }, 2000);
            } else {
                throw new Error(result.replace('error: ', ''));
            }
        } catch (err) {
            window.removeEventListener('beforeunload', beforeUnloadHandler);
            hideOverlay();
            statusEl.textContent = '';
            toastr.error('Gagal upload: ' + err.message);
            btnSubmit.disabled = false;
            btnSubmit.textContent = originalLabel;
            isSubmitting = false;
        }
    });
}
</script>
<?php include $rootPath . '/partials/dashboard/footer.php'; ?>