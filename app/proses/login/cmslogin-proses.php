<?php
$rootPath = dirname(__DIR__, 3);

include $rootPath . '/koneksi.php';
/** @var mysqli  */
require_once $rootPath . '/app/config/session.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

error_log("[LOGIN] Attempt: username=$username");
error_log("[LOGIN] Session status: " . session_status());
error_log("[LOGIN] Session ID: " . session_id());

if ($username === '' || $password === '') {
    error_log("[LOGIN] Empty credentials");
    header("Location: /login.php?toast_type=error&toast_title=Login Gagal&toast_message=Username dan password tidak boleh kosong");
    exit();
}

if (!$koneksi) {
    error_log("[LOGIN] Database connection failed");
    header("Location: /login.php?toast_type=error&toast_title=Login Gagal&toast_message=Koneksi database gagal");
    exit();
}

$query = "SELECT * FROM tb_login WHERE username=? AND password=?";
$stmt  = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    error_log("[LOGIN] Prepare failed: " . mysqli_error($koneksi));
    header("Location: /login.php?toast_type=error&toast_title=Login Gagal&toast_message=Terjadi kesalahan sistem");
    exit();
}

mysqli_stmt_bind_param($stmt, "ss", $username, $password);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $_SESSION['username'] = $username;
    $_SESSION['status']   = "login";
    error_log("[LOGIN] Success: username=$username");
    error_log("[LOGIN] Session after login: " . print_r($_SESSION, true));

    mysqli_stmt_close($stmt);
    header("Location: /dashboard.php?toast_type=success&toast_title=Login Berhasil&toast_message=Selamat datang kembali!");
    exit();
} else {
    error_log("[LOGIN] Failed: no match found");
    error_log("[LOGIN] Query result: " . ($result ? "has result" : "no result"));
    error_log("[LOGIN] Num rows: " . ($result ? mysqli_num_rows($result) : "N/A"));
    mysqli_stmt_close($stmt);
    header("Location: /login.php?toast_type=error&toast_title=Login Gagal&toast_message=Username atau password salah");
    exit();
}