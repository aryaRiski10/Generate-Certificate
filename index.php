<?php
function show_form($error = '', $image_path = '') {
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <title>Generate Sertifikat</title>
  </head>
  <body>
    <h2>Form Sertifikat Kelas Online</h2>
    <?php if ($error): ?>
      <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
      Nama: <input type="text" name="nama" required><br><br>
      Email: <input type="email" name="email" required><br><br>
      Kode Unik: <input type="text" name="kode" required><br><br>
      <input type="submit" name="submit" value="Generate Sertifikat">
    </form>
  
    <?php if ($image_path): ?>
      <hr>
      <h3>✅ Sertifikat Berhasil Dibuat:</h3>
      <img src="<?php echo $image_path; ?>" alt="Sertifikat" style="max-width:100%; border:1px solid #ccc; padding:10px;">
    <?php endif; ?>
  </body>
  </html>
  <?php
  }
  


if (!isset($_POST['submit'])) {
  show_form();
  exit;
}

$nama_input = $_POST['nama'];
$email_input = $_POST['email'];
$kode_input = $_POST['kode'];

// GANTI DENGAN URL GOOGLE SCRIPT KAMU (yang sudah di-deploy public)
$sheet_url = 'https://script.google.com/macros/s/AKfycbzt45f0kqwZbvOsj_PlZKns3WlymQVMD9NZYrWXAFr4pWBUEGqOmWDEjlJNvOhxJDAd8Q/exec'; // <--- GANTI INI

// Ambil data dari Google Sheet
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sheet_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Mengikuti redirect
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Nonaktifkan verifikasi SSL (hanya untuk pengujian)

$response = curl_exec($ch);
if ($response === false) {
    show_form("❌ CURL Error: " . curl_error($ch));
    exit;
}

curl_close($ch);

// Cek jika gagal ambil data
$data = json_decode($response, true);
if (!$data || !is_array($data)) {
    show_form("❌ Gagal mengambil data dari Google Sheet. Cek URL & akses publik Web App.");
    exit;
}

// Cek apakah email & kode cocok
$valid = false;
foreach ($data as $row) {
    if ($row['email'] === $email_input && $row['kode'] === $kode_input) {
        $valid = true;
        $nama_resmi = $row['nama'];
        $judul_kelas = $row['judul_kelas']; // Pastikan kolom ini ada di data
        break;
    }
    
}
if (!$valid) {
    show_form("❌ Email atau kode tidak valid.");
    exit;
}

// Generate Sertifikat
$sertifikat = imagecreatefrompng('sertifikat.png'); // Pastikan file ini ada
$black = imagecolorallocate($sertifikat, 0, 0, 0);
$font = __DIR__ . '/arial.ttf'; // Pastikan font ini ada

// Menentukan ukuran font
$fontsize_name = 45; // Ukuran font
$fontsize_class = 14;
$angle = 0; // Sudut rotasi

// Menghitung posisi X dan Y untuk menempatkan teks di tengah
$text_box = imagettfbbox($fontsize_name, $angle, $font, $nama_resmi);
$text_width = $text_box[2] - $text_box[0]; // Lebar teks
$text_height = $text_box[1] - $text_box[7]; // Tinggi teks

$image_width = imagesx($sertifikat); // Lebar gambar
$image_height = imagesy($sertifikat); // Tinggi gambar

$x = ($image_width - $text_width) / 2; // Posisi X di tengah
$y = ($image_height + $text_height) / 2; // Posisi Y di tengah
$offset = 50; // Ubah nilai ini untuk menaikkan teks lebih tinggi atau lebih rendah
$y -= $offset; // Mengurangi nilai Y untuk menaikkan teks

imagettftext($sertifikat, $fontsize_name, $angle, $x, $y, $black, $font, $nama_resmi);


// $judul_size = 40; // Ukuran font lebih kecil
$judul_box = imagettfbbox($fontsize_class, $angle, $font, $judul_kelas);
$judul_width = $judul_box[2] - $judul_box[0];
$judul_x = ($image_width - $judul_width) / 2;
$judul_y = $y + 60; // Sekitar 60px di bawah nama

imagettftext($sertifikat, $fontsize_class, $angle, $judul_x, $judul_y, $black, $font, $judul_kelas);

// Buat nama file unik (hash email + waktu)
$filename_base = md5($email_input . time());
$image_path = __DIR__ . "/output/{$filename_base}.png"; // Lokasi simpan
$image_url = "output/{$filename_base}.png"; // URL relatif untuk <img>

// Simpan gambar ke file
imagepng($sertifikat, $image_path);
imagedestroy($sertifikat);

// Tampilkan form + hasil
?>
<!DOCTYPE html>
<html>
<head>
  <title>Sertifikat Sukses</title>
</head>
<body>
  <h2>✅ Sertifikat Berhasil Dibuat</h2>
  <img src="<?php echo $image_url; ?>" alt="Sertifikat" style="max-width:100%; border:1px solid #ccc;"><br><br>
  <a href="download_pdf.php?file=<?php echo urlencode($filename_base); ?>" target="_blank">
    <button type="button">⬇️ Download PDF</button>
  </a>
  <br><br>
  <a href="<?php echo $_SERVER['PHP_SELF']; ?>">🔁 Kembali ke Form</a>
</body>
</html>
<?php
exit;

// header('Content-Type: image/png');
// header('Content-Disposition: inline; filename="sertifikat.png"');
// imagepng($sertifikat);
// imagedestroy($sertifikat);
// Simpan sertifikat ke file
// $output_file = 'sertifikat_output.png';
// imagepng($sertifikat, $output_file);
// imagedestroy($sertifikat);

// Tampilkan kembali form dengan sertifikat di bawah
show_form('', $output_file);
exit;

?>

