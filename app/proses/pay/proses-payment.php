<?php
include __DIR__ . '/../../../koneksi.php';
require_once __DIR__ . '/../../../app/config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../../daftar.php');
    exit();
}

header('Location: ../../../daftar.php?err=auth_required');
exit();
$productId = (int)($_POST['product_id'] ?? 0);

if ($productId <= 0) {
    header('Location: ../../../daftar.php?err=notfound');
    exit();
}

if (!$koneksi) {
    throw new Exception('Database connection failed');
}
mysqli_begin_transaction($koneksi);

try {
    $stmt = mysqli_prepare($koneksi, "SELECT harga, stok FROM tb_produk WHERE id = ? AND is_active = 1 FOR UPDATE");
    mysqli_stmt_bind_param($stmt, 'i', $productId);
    mysqli_stmt_execute($stmt);
    $result  = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$product) {
        throw new Exception('notfound');
    }
    if ((int)$product['stok'] <= 0) {
        throw new Exception('stok');
    }

    $stmtCheck = mysqli_prepare($koneksi, "SELECT transaction_id FROM tb_pay WHERE google_id = ? AND product_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtCheck, 'si', $googleId, $productId);
    mysqli_stmt_execute($stmtCheck);
    $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCheck));
    mysqli_stmt_close($stmtCheck);

    if ($existing) {
        mysqli_commit($koneksi);
        header('Location: ../../../detail.php?id=' . $productId . '&msg=bought');
        exit();
    }

    $transactionId = 'TRX-' . date('Ymd-His') . '-' . substr(bin2hex(random_bytes(4)), 0, 8);
    $grossAmount   = (int)$product['harga'];

    $stmtInsert = mysqli_prepare(
        $koneksi,
        "INSERT INTO tb_pay (transaction_id, google_id, product_id, gross_amount, payment_type, transaction_status)
         VALUES (?, ?, ?, ?, 'manual', 'success')"
    );
    mysqli_stmt_bind_param($stmtInsert, 'ssii', $transactionId, $googleId, $productId, $grossAmount);
    mysqli_stmt_execute($stmtInsert);
    mysqli_stmt_close($stmtInsert);

    $stmtStok = mysqli_prepare($koneksi, "UPDATE tb_produk SET stok = stok - 1 WHERE id = ? AND stok > 0");
    mysqli_stmt_bind_param($stmtStok, 'i', $productId);
    mysqli_stmt_execute($stmtStok);
    mysqli_stmt_close($stmtStok);

    mysqli_commit($koneksi);

    header('Location: ../../../detail.php?id=' . $productId . '&msg=bought');
    exit();

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    $err = in_array($e->getMessage(), ['notfound', 'stok']) ? $e->getMessage() : 'notfound';
    header('Location: ../../../detail.php?id=' . $productId . '&err=' . $err);
    exit();
}