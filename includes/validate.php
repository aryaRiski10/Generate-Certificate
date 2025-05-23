<?php
function validate_input($email, $kode) {
    $sheet_url = 'https://script.google.com/macros/s/AKfycbwypeyW64W666M-hUndd0kRk1rHECdgUAjZ-cuEDAnQhnTpNXN6_IGECaR0zl6Svdw0ZQ/exec';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sheet_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (!$data || !is_array($data)) {
        return ['valid' => false, 'error' => '❌ Gagal ambil data. Cek URL Google Script.'];
    }

    foreach ($data as $row) {
        if ($row['email'] === $email && $row['kode'] === $kode) {
            return [
                'valid' => true,
                'nama' => $row['nama'],
                'judul_kelas' => $row['judul_kelas'],
                'tanggal_mulai' => $row['tanggal_mulai'],
                'tanggal_selesai' => $row['tanggal_selesai']
            ];
        }
    }

    return ['valid' => false, 'error' => '❌ Email atau kode tidak cocok.'];
}
?>
