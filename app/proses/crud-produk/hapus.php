<?php
$rootPath = dirname(__DIR__, 3);
include $rootPath . '/koneksi.php';
/** @var mysqli */
require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    echo "unauthorized";
    exit();
}

if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    echo "invalid_id";
    exit();
}

$id_produk = (int)$_GET['id'];


if (empty($koneksi)) {
    echo "db_connection_error";
    exit();
}
$stmt = mysqli_prepare($koneksi, "DELETE FROM tb_produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id_produk);

if (mysqli_stmt_execute($stmt)) {
    echo "success"; 
} else {
    echo "error";
}

mysqli_stmt_close($stmt);
?>