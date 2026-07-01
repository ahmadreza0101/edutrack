<?php
$rootPath = dirname(__DIR__, 4);
include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */
require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    echo "unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_get    = (int)($_POST['id'] ?? 0);
    $judul     = trim($_POST['judul'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $harga     = trim($_POST['harga'] ?? '');
    $stok      = trim($_POST['stok'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    $gambar_url = trim($_POST['gambar_url'] ?? '') !== '' ? trim($_POST['gambar_url']) : null;
    $file_url   = trim($_POST['asset_url'] ?? '')  !== '' ? trim($_POST['asset_url'])  : null;
    $is_active  = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

    if ($id_get <= 0) {
        echo "error: Invalid product ID";
        exit();
    }

    // Get existing data to preserve unchanged fields
    $stmt = mysqli_prepare($koneksi, "SELECT gambar, file FROM tb_produk WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_get);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existing = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$existing) {
        echo "error: Product not found";
        exit();
    }

    // Use existing values if new ones not provided
    $gambar_url = $gambar_url ?: $existing['gambar'];
    $file_url   = $file_url ?: $existing['file'];

    $stmt = mysqli_prepare($koneksi, "UPDATE tb_produk SET gambar=?, judul=?, kategori=?, harga=?, stok=?, file=?, deskripsi=?, is_active=? WHERE id=?");
    // Urutan tipe yang benar: gambar(s), judul(s), kategori(s), harga(i), stok(i), file(s), deskripsi(s), is_active(i), id(i)
    mysqli_stmt_bind_param($stmt, 'sssiissii', $gambar_url, $judul, $kategori, $harga, $stok, $file_url, $deskripsi, $is_active, $id_get);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash_success'] = 'Produk berhasil diperbarui!';
        echo "success";
    } else {
        echo "error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}
