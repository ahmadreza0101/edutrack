<?php
$rootPath = dirname(__DIR__, 3);
include $rootPath . '/koneksi.php';
/** @var mysqli $koneksi */
require_once $rootPath . '/app/config/session.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    echo "unauthorized";
    exit();
}

if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM tb_login";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($koneksi));
}

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td class='ps-3'>" . $no++ . "</td>";
    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['password']) . "</td>";
    echo "<td class='text-center'>";
    echo "<div class='d-flex gap-1 justify-content-center'>";
    echo "<button data-id='" . $row['id'] . "' class='btn btn-sm btn-warning btn-edit'>Edit</button>";
    echo "<button data-id='" . $row['id'] . "' class='btn btn-sm btn-danger btn-hapus'>Hapus</button>";
    echo "</div>";
    echo "</td>";
    echo "</tr>";
}
?>