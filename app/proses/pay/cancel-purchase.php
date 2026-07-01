<?php
include __DIR__ . '/../../../koneksi.php';
require_once __DIR__ . '/../../../app/config/session.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../../daftar.php');
    exit();
}

header('Location: ../../../daftar.php?err=auth_required');
exit();
$transactionId = $_POST['transaction_id'] ?? '';
$productId     = (int)($_POST['product_id'] ?? 0);

if ($transactionId === '' || $productId <= 0) {
    header('Location: ../../../daftar.php?err=notfound');
    exit();
}

if (!$koneksi) {
    throw new Exception('Database connection failed');
}
mysqli_begin_transaction($koneksi);

try {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT transaction_id FROM tb_pay WHERE transaction_id = ? AND google_id = ? AND product_id = ? FOR UPDATE"
    );
    mysqli_stmt_bind_param($stmt, 'ssi', $transactionId, $googleId, $productId);
    mysqli_stmt_execute($stmt);
    $owned = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$owned) {
        throw new Exception('notfound');
    }

    $stmtDelete = mysqli_prepare($koneksi, "DELETE FROM tb_pay WHERE transaction_id = ?");
    mysqli_stmt_bind_param($stmtDelete, 's', $transactionId);
    mysqli_stmt_execute($stmtDelete);
    mysqli_stmt_close($stmtDelete);

    $stmtStok = mysqli_prepare($koneksi, "UPDATE tb_produk SET stok = stok + 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmtStok, 'i', $productId);
    mysqli_stmt_execute($stmtStok);
    mysqli_stmt_close($stmtStok);

    mysqli_commit($koneksi);

    header('Location: ../../../detail.php?id=' . $productId . '&msg=cancelled');
    exit();

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    header('Location: ../../../detail.php?id=' . $productId . '&err=notfound');
    exit();
}