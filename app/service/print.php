<?php
$rootPath = dirname(__DIR__, 2);

require_once $rootPath . '/vendor/autoload.php';

include $rootPath . '/koneksi.php';

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

$query = "SELECT * FROM tb_jadwal ORDER BY kelas ASC, tanggal_ujian ASC, waktu_mulai ASC";
$result = mysqli_query($koneksi, $query) or die("Query failed: " . mysqli_error($koneksi));

$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setCreator('EduTrack CMS');
$pdf->setTitle('Laporan Jadwal Ujian');
$pdf->setHeaderData('', 0, 'Laporan Jadwal Ujian', 'EduTrack CMS - ' . date('d/m/Y'));
$pdf->setMargins(10, 20, 10);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();

$html = '<h2 style="text-align: center; margin-bottom: 20px; color: #2563eb;">Laporan Jadwal Ujian</h2>';
$html .= '<table border="1" cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
$html .= '<thead>
            <tr style="background-color: #2563eb; color: white;">
                <th style="text-align: center; width: 5%;">No</th>
                <th style="text-align: center; width: 10%;">Gambar</th>
                <th style="text-align: center; width: 10%;">Kelas</th>
                <th style="text-align: center; width: 18%;">Mata Kuliah</th>
                <th style="text-align: center; width: 5%;">SKS</th>
                <th style="text-align: center; width: 18%;">Dosen</th>
                <th style="text-align: center; width: 10%;">Tanggal</th>
                <th style="text-align: center; width: 14%;">Waktu</th>
                <th style="text-align: center; width: 10%;">Ruangan</th>
            </tr>
          </thead>
          <tbody>';

function getGambarHtml($gambarUrl, $width = 50, $height = 50) {
    if (empty($gambarUrl)) {
        return '<td style="text-align: center; width: 10%;">-</td>';
    }
    return '<td style="text-align: center; width: 10%;"><img src="' . htmlspecialchars($gambarUrl) . '" style="width: ' . $width . 'px; height: ' . $height . 'px; object-fit: cover;" /></td>';
}

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $bgColor = ($no % 2 == 0) ? '#f8fafc' : '#ffffff';
    $gambarHtml = getGambarHtml($row['gambar']);
    $html .= '<tr style="background-color: ' . $bgColor . ';">
                <td style="text-align: center; width: 5%;">' . $no++ . '</td>
                ' . $gambarHtml . '
                <td style="text-align: center; width: 10%;">' . htmlspecialchars($row['kelas']) . '</td>
                <td style="width: 18%;">' . htmlspecialchars($row['mata_kuliah']) . '</td>
                <td style="text-align: center; width: 5%;">' . htmlspecialchars($row['sks'] ?? 3) . '</td>
                <td style="width: 18%;">' . htmlspecialchars($row['dosen']) . '</td>
                <td style="text-align: center; width: 10%;">' . date('d/m/Y', strtotime($row['tanggal_ujian'])) . '</td>
                <td style="text-align: center; width: 14%;">' . date('H:i', strtotime($row['waktu_mulai'])) . ' - ' . date('H:i', strtotime($row['waktu_selesai'])) . '</td>
                <td style="width: 10%;">' . htmlspecialchars($row['ruangan']) . '</td>
            </tr>';
}
$html .= '</tbody></table>';

@$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('laporan_jadwal_ujian_' . date('YmdHis') . '.pdf', 'I');
