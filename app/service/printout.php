<?php
$rootPath = dirname(__DIR__, 2);
include $rootPath . '/koneksi.php';
require_once $rootPath . '/vendor/autoload.php';

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

$id = (int)($_GET['id'] ?? 0);
$kelas = $_GET['kelas'] ?? '';

if (empty($id) && empty($kelas)) {
    die("ID atau kelas harus diberikan!");
}

$orientation = !empty($kelas) ? 'L' : 'P';

$pdf = new TCPDF($orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('EduTrack CMS');
$pdf->SetAuthor('EduTrack');
$pdf->SetTitle('Jadwal Ujian');
$pdf->SetSubject('Detail Jadwal Ujian');
$pdf->SetKeywords('jadwal, ujian, edutrack');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', !empty($kelas) ? 10 : 12);

$html = '';

if (!empty($kelas)) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_jadwal WHERE kelas = ? ORDER BY tanggal_ujian ASC, waktu_mulai ASC");
    mysqli_stmt_bind_param($stmt, 's', $kelas);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $html .= '<h1 style="text-align: center; margin-bottom: 20px; color: #2563eb;">Jadwal Ujian Kelas ' . htmlspecialchars($kelas) . '</h1>';
    $html .= '<table border="1" cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
    $html .= '<thead>
                <tr style="background-color: #2563eb; color: white;">
                    <th style="text-align: center; width: 5%;">No</th>
                    <th style="text-align: center; width: 10%;">Gambar</th>
                    <th style="text-align: center; width: 10%;">Tanggal</th>
                    <th style="text-align: center; width: 13%;">Waktu</th>
                    <th style="text-align: center; width: 20%;">Mata Kuliah</th>
                    <th style="text-align: center; width: 5%;">SKS</th>
                    <th style="text-align: center; width: 20%;">Dosen</th>
                    <th style="text-align: center; width: 17%;">Ruangan</th>
                </tr>
              </thead>
              <tbody>';
    
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $bgColor = ($no % 2 == 0) ? '#f8fafc' : '#ffffff';
        $gambarHtml = '';
        if (!empty($row['gambar'])) {
            $gambarHtml = '<td style="text-align: center; width: 10%;"><img src="' . htmlspecialchars($row['gambar']) . '" style="width: 50px; height: 50px; object-fit: cover;" /></td>';
        } else {
            $gambarHtml = '<td style="text-align: center; width: 10%;">-</td>';
        }
        $html .= '<tr style="background-color: ' . $bgColor . ';">
                    <td style="text-align: center; width: 5%;">' . $no++ . '</td>
                    ' . $gambarHtml . '
                    <td style="text-align: center; width: 10%;">' . date('d/m/Y', strtotime($row['tanggal_ujian'])) . '</td>
                    <td style="text-align: center; width: 13%;">' . date('H:i', strtotime($row['waktu_mulai'])) . ' - ' . date('H:i', strtotime($row['waktu_selesai'])) . '</td>
                    <td style="width: 20%;">' . htmlspecialchars($row['mata_kuliah']) . '</td>
                    <td style="text-align: center; width: 5%;">' . htmlspecialchars($row['sks'] ?? 3) . '</td>
                    <td style="width: 20%;">' . htmlspecialchars($row['dosen']) . '</td>
                    <td style="width: 17%;">' . htmlspecialchars($row['ruangan']) . '</td>
                </tr>';
    }
    
    $html .= '</tbody></table>';
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_jadwal WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    if (!$row) {
        die("Jadwal ujian tidak ditemukan!");
    }
    
    $html .= '<h1 style="text-align: center; margin-bottom: 30px; color: #2563eb;">Detail Jadwal Ujian</h1>';
    
    $html .= '<table style="width: 100%; border-collapse: collapse;">';
    $html .= '<tr>';
    
    if (!empty($row['gambar'])) {
        $html .= '<td style="width: 220px; vertical-align: top;">';
        $html .= '<img src="' . htmlspecialchars($row['gambar']) . '" 
                         style="width: 200px; height: auto; border-radius: 10px;" />';
        $html .= '</td>';
    }
    
    $html .= '<td style="vertical-align: top; padding-left: 20px;">';
    $html .= '<h2 style="color: #2563eb; margin-top: 0; margin-bottom: 20px;">' . htmlspecialchars($row['mata_kuliah']) . '</h2>';
    $html .= '<table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; width: 150px;"><strong>Kelas</strong></td>
                    <td style="padding: 8px 0;">' . htmlspecialchars($row['kelas']) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Dosen Pengampu</strong></td>
                    <td style="padding: 8px 0;">' . htmlspecialchars($row['dosen']) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Jumlah SKS</strong></td>
                    <td style="padding: 8px 0;">' . htmlspecialchars($row['sks'] ?? 3) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Tanggal Ujian</strong></td>
                    <td style="padding: 8px 0;">' . date('d/m/Y', strtotime($row['tanggal_ujian'])) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Waktu</strong></td>
                    <td style="padding: 8px 0;">' . date('H:i', strtotime($row['waktu_mulai'])) . ' - ' . date('H:i', strtotime($row['waktu_selesai'])) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Ruangan</strong></td>
                    <td style="padding: 8px 0;">' . htmlspecialchars($row['ruangan']) . '</td>
                </tr>
            </table>';
    $html .= '</td>';
    $html .= '</tr>';
    $html .= '</table>';
                
    if (!empty($row['deskripsi'])) {
        $html .= '<br><div style="background-color: #fefce8; border-radius: 10px; padding: 15px;">
                    <h3 style="margin-top: 0; margin-bottom: 10px; color: #854d0e;"><strong>Catatan</strong></h3>
                    <p style="margin: 0; color: #44403c;">' . htmlspecialchars($row['deskripsi']) . '</p>
                </div>';
    }
}

@$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('jadwal_ujian_' . (empty($kelas) ? $id : $kelas) . '_' . date('YmdHis') . '.pdf', 'I');
