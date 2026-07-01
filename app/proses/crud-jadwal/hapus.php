<?php
include __DIR__ . '/../../../koneksi.php';
require_once __DIR__ . '/../../../app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    echo "unauthorized";
    exit();
}

$id_produk = (int)($_GET['id'] ?? 0);

if ($id_produk <= 0) {
    echo "invalid_id";
    exit();
}

if (empty($koneksi)) {
    echo "db_connection_error";
    exit();
}

$stmt = mysqli_prepare($koneksi, "DELETE FROM tb_jadwal WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id_produk);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
?>
