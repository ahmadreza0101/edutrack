<?php
$rootPath = dirname(__DIR__, 4);
include $rootPath . '/koneksi.php';
/** @var mysqli  */
require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    echo "unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $harga     = trim($_POST['harga'] ?? '');
    $stok      = trim($_POST['stok'] ?? '1');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    $url_cloudinary_gambar = trim($_POST['gambar_url'] ?? '');
    $url_cloudinary_file   = trim($_POST['asset_url'] ?? '');

    if (empty($judul) || empty($kategori) || empty($harga) || empty($url_cloudinary_gambar) || empty($url_cloudinary_file)) {
        echo "error: Semua field wajib diisi";
        exit();
    }

    if (!$koneksi) {
        echo "error: Database connection failed";
        exit();
    }
    $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_produk (gambar, judul, kategori, harga, stok, file, deskripsi, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
    $harga_int = (int)$harga;
    $stok_int  = (int)$stok;

    mysqli_stmt_bind_param($stmt, 'ssssiss', $url_cloudinary_gambar, $judul, $kategori, $harga_int, $stok_int, $url_cloudinary_file, $deskripsi);

    if (mysqli_stmt_execute($stmt)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($koneksi);
    }
    mysqli_stmt_close($stmt);
}
