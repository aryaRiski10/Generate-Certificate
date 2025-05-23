<?php
require_once 'includes/form.php';
require_once 'includes/validate.php';
require_once 'includes/generate.php';
date_default_timezone_set('Asia/Jakarta');


if (!isset($_POST['submit'])) {
    show_form(); // hanya tampilkan form jika belum submit
    exit;
}

$nama_input = $_POST['nama'];
$email_input = $_POST['email'];
$kode_input = $_POST['kode'];

$result = validate_input($email_input, $kode_input);

if (!$result['valid']) {
    show_form($result['error']);
    exit;
}

$nama_resmi = $result['nama'];
$judul_kelas = $result['judul_kelas'];
$tanggal_mulai = $result['tanggal_mulai'];
$tanggal_selesai = $result['tanggal_selesai'];

$image_url = generate_sertifikat($nama_resmi, $judul_kelas, $email_input, $tanggal_mulai, $tanggal_selesai);

show_form('', $image_url);
exit;
?>
