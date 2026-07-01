<?php
$rootPath = dirname(__DIR__, 2);

require_once $rootPath . '/vendor/autoload.php';

include $rootPath . '/koneksi.php';

$query = "SELECT * FROM tb_jadwal ORDER BY kelas ASC, tanggal_ujian ASC, waktu_mulai ASC";
$result = mysqli_query($koneksi, $query) or die("Query failed: " . mysqli_error($koneksi));

// Gunakan orientation LANDSCAPE untuk tabel yang lebar
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

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('laporan_jadwal_ujian_' . date('YmdHis') . '.pdf', 'I');
