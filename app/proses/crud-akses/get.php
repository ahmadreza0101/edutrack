<?php
$rootPath = dirname(__DIR__, 3);
include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */
require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    echo "unauthorized";
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (!$koneksi) {
        echo "error: Database connection failed";
        exit();
    }
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_login WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    echo json_encode($user);
    mysqli_stmt_close($stmt);
}
?>