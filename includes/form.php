<?php
function show_form($error = '', $image_path = '') {
?>
<!DOCTYPE html>
<html>
<head><title>Form Sertifikat</title></head>
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
  <a href="/Generate-Certificate/check-data.php">Check Data</a>

  <?php if ($image_path): ?>
    <hr>
    <h3>✅ Sertifikat Berhasil Dibuat:</h3>
    <img src="<?php echo $image_path; ?>" style="max-width:60%; border:1px solid #ccc; padding:10px;">
    <br><br>
    <a href="download_pdf.php?file=<?php echo urlencode(basename($image_path, '.png')); ?>" target="_blank">
      <button>⬇️ Download PDF</button>
    </a>
    <br>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>">🔁 Kembali ke Form</a>
  <?php endif; ?>
</body>
</html>
<?php } ?>
