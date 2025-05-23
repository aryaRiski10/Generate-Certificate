<?php
require('fpdf/fpdf.php');
// Hapus file PNG di folder output yang lebih lama dari 15 menit
$folder = __DIR__ . '/output';
$files = glob($folder . '/*.png');

foreach ($files as $file) {
    if (filemtime($file) < time() - (30 * 60)) { // 15 menit
        unlink($file);
    }
}

// Validasi input
if (!isset($_GET['file'])) {
    die("❌ Parameter file tidak tersedia.");
}

$filename_base = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['file']);
$image_path = __DIR__ . "/output/{$filename_base}.png";

if (!file_exists($image_path)) {
    die("❌ File tidak ditemukan.");
}

// Buat PDF dari gambar
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->Image($image_path, 0, 0, 297, 210); // A4 landscape

$pdf->Output('D', 'sertifikat.pdf'); // Download langsung
