<?php
date_default_timezone_set('Asia/Jakarta');

// GANTI URL INI dengan URL Web App Google Script kamu
$sheet_url = 'https://script.google.com/macros/s/AKfycbwypeyW64W666M-hUndd0kRk1rHECdgUAjZ-cuEDAnQhnTpNXN6_IGECaR0zl6Svdw0ZQ/exec';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sheet_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Nonaktifkan hanya untuk testing lokal

$response = curl_exec($ch);

if ($response === false) {
    die("CURL Error: " . curl_error($ch));
}
curl_close($ch);

$data = json_decode($response, true);

if (!$data || !is_array($data)) {
    die("❌ Gagal mengambil data dari Google Sheet.");
}

function formatTanggalIndonesia($tanggal) {
    $bulanIndo = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $parts = explode('/', $tanggal); // Format: dd/mm/yyyy
    if (count($parts) !== 3) return $tanggal; // fallback jika format tidak sesuai

    $hari = ltrim($parts[0], '0'); // Hilangkan nol di depan
    $bulan = $bulanIndo[$parts[1]] ?? $parts[1];
    $tahun = $parts[2];

    return "$hari $bulan $tahun";
}

// Ambil header (kolom) dengan aman
$headers = [];
if (!empty($data)) {
    $headers = array_keys($data[0]);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Data dari Google Sheet</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #fafafa;
    }
    h2 {
      margin-bottom: 20px;
    }
    table {
      border-collapse: collapse;
      width: 90%;
      max-width: 1200px;
      background: white;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px 12px;
      text-align: left;
      vertical-align: middle;
    }
    th {
      background-color: #f2f2f2;
      font-weight: 600;
    }
    tr:nth-child(even) {
      background-color: #fbfbfb;
    }
  </style>
</head>
<body>
    <a href="/Generate-Certificate" style="padding:14px; background:#fafafa">Kembali</a>
  <h2>📋 Data Peserta dari Google Sheet</h2>

  <?php if (empty($data)): ?>
    <p>Tidak ada data untuk ditampilkan.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <?php foreach ($headers as $header): ?>
            <th><?php echo htmlspecialchars($header); ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $row): ?>
          <tr>
          <?php foreach ($row as $key => $value): ?>
            <td>
                <?php
                if (in_array($key, ['tanggal_mulai', 'tanggal_selesai'])) {
                    echo htmlspecialchars(formatTanggalIndonesia($value));
                } else {
                    echo htmlspecialchars($value);
                }
                ?>
            </td>
            <?php endforeach; ?>

          </tr>
        <?php endforeach; ?>
        
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
