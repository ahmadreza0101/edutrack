<?php
$rootPath = dirname(__DIR__, 3);
include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */
require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    echo "unauthorized";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['user_id'];

    if (!$koneksi) {
        echo "error: Database connection failed";
        exit();
    }
    $stmt = mysqli_prepare($koneksi, "DELETE FROM tb_login WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "success";
    } else {
        echo "error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}
?>