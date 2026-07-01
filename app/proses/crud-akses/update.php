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
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($koneksi, "UPDATE tb_login SET username = ?, email = ?, password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $password, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "success";
    } else {
        echo "error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}
?>