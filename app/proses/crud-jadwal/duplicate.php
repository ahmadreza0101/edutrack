<?php
include __DIR__ . '/../../../koneksi.php';
require_once __DIR__ . '/../../../app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    echo "unauthorized";
    exit();
}

$id_jadwal = (int)($_POST['id'] ?? 0);
$kelas_tujuan = trim($_POST['kelas_tujuan'] ?? '');

if ($id_jadwal <= 0) {
    echo "invalid_id";
    exit();
}

if (empty($kelas_tujuan)) {
    echo "invalid_kelas";
    exit();
}

if (empty($koneksi)) {
    echo "db_connection_error";
    exit();
}

// Get the original schedule
$stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_jadwal WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id_jadwal);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$original = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$original) {
    echo "not_found";
    exit();
}

// Check if schedule already exists for target class
$stmt_check = mysqli_prepare($koneksi, "SELECT id FROM tb_jadwal WHERE mata_kuliah = ? AND dosen = ? AND kelas = ? AND tanggal_ujian = ? AND waktu_mulai = ?");
mysqli_stmt_bind_param($stmt_check, 'sssss', $original['mata_kuliah'], $original['dosen'], $kelas_tujuan, $original['tanggal_ujian'], $original['waktu_mulai']);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
if (mysqli_num_rows($result_check) > 0) {
    echo "duplicate_exists";
    exit();
}
mysqli_stmt_close($stmt_check);

// Insert duplicate with new class
$stmt = mysqli_prepare($koneksi, "INSERT INTO tb_jadwal (mata_kuliah, dosen, tanggal_ujian, waktu_mulai, waktu_selesai, ruangan, sks, kelas, deskripsi, gambar, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
mysqli_stmt_bind_param($stmt, 'sssssissss', 
    $original['mata_kuliah'], 
    $original['dosen'], 
    $original['tanggal_ujian'], 
    $original['waktu_mulai'], 
    $original['waktu_selesai'], 
    $original['ruangan'],
    $original['sks'] ?? 3, 
    $kelas_tujuan, 
    $original['deskripsi'],
    $original['gambar'] ?? ''
);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
?>
