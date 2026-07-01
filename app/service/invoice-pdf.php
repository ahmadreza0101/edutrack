<?php
$rootPath = dirname(__DIR__, 2);
include $rootPath . '/koneksi.php';
require_once $rootPath . '/vendor/autoload.php';

$id = (int)($_GET['id'] ?? 0);
$kelas = $_GET['kelas'] ?? '';

if (empty($id) && empty($kelas)) {
    die("ID atau kelas harus diberikan!");
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Sonata CMS');
$pdf->SetAuthor('Sonata');
$pdf->SetTitle('Jadwal Ujian');
$pdf->SetSubject('Detail Jadwal Ujian');
$pdf->SetKeywords('jadwal, ujian, sonata');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$html = '';

if (!empty($kelas)) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_jadwal WHERE kelas = ? AND is_active = 1 ORDER BY tanggal_ujian ASC, waktu_mulai ASC");
    mysqli_stmt_bind_param($stmt, 's', $kelas);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $html .= '<h1 style="text-align: center; margin-bottom: 20px;">Jadwal Ujian Kelas ' . htmlspecialchars($kelas) . '</h1>';
    $html .= '<table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
    $html .= '<thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="text-align: center; width: 8%;">No</th>
                    <th style="text-align: center; width: 15%;">Tanggal</th>
                    <th style="text-align: center; width: 12%;">Waktu</th>
                    <th style="text-align: center; width: 25%;">Mata Kuliah</th>
                    <th style="text-align: center; width: 20%;">Dosen</th>
                    <th style="text-align: center; width: 20%;">Ruangan</th>
                </tr>
              </thead>
              <tbody>';
    
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>
                    <td style="text-align: center;">' . $no++ . '</td>
                    <td style="text-align: center;">' . date('d/m/Y', strtotime($row['tanggal_ujian'])) . '</td>
                    <td style="text-align: center;">' . date('H:i', strtotime($row['waktu_mulai'])) . ' - ' . date('H:i', strtotime($row['waktu_selesai'])) . '</td>
                    <td>' . htmlspecialchars($row['mata_kuliah']) . '</td>
                    <td>' . htmlspecialchars($row['dosen']) . '</td>
                    <td>' . htmlspecialchars($row['ruangan']) . '</td>
                </tr>';
    }
    
    $html .= '</tbody></table>';
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_jadwal WHERE id = ? AND is_active = 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    if (!$row) {
        die("Jadwal ujian tidak ditemukan!");
    }
    
    $html .= '<h1 style="text-align: center; margin-bottom: 20px;">Detail Jadwal Ujian</h1>';
    $html .= '<div style="border: 1px solid #ddd; padding: 20px;">';
    $html .= '<p style="margin-bottom: 10px;"><strong>Mata Kuliah:</strong> ' . htmlspecialchars($row['mata_kuliah']) . '</p>';
    $html .= '<p style="margin-bottom: 10px;"><strong>Kelas:</strong> ' . htmlspecialchars($row['kelas']) . '</p>';
    $html .= '<p style="margin-bottom: 10px;"><strong>Dosen:</strong> ' . htmlspecialchars($row['dosen']) . '</p>';
    $html .= '<p style="margin-bottom: 10px;"><strong>Tanggal Ujian:</strong> ' . date('d/m/Y', strtotime($row['tanggal_ujian'])) . '</p>';
    $html .= '<p style="margin-bottom: 10px;"><strong>Waktu:</strong> ' . date('H:i', strtotime($row['waktu_mulai'])) . ' - ' . date('H:i', strtotime($row['waktu_selesai'])) . '</p>';
    $html .= '<p style="margin-bottom: 10px;"><strong>Ruangan:</strong> ' . htmlspecialchars($row['ruangan']) . '</p>';
    if (!empty($row['deskripsi'])) {
        $html .= '<p><strong>Catatan:</strong> ' . htmlspecialchars($row['deskripsi']) . '</p>';
    }
    $html .= '</div>';
}

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('jadwal_ujian_' . (empty($kelas) ? $id : $kelas) . '_' . date('YmdHis') . '.pdf', 'I');
