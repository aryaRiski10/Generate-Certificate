<?php
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
function generate_sertifikat($nama_resmi, $judul_kelas, $email_input, $tanggal_mulai, $tanggal_selesai) {
    $sertifikat = imagecreatefrompng('template/sertifikat.png');
    $black = imagecolorallocate($sertifikat, 0, 0, 0);

    $font = __DIR__ . '/../fonts/Poppins-Regular.ttf';
    $font_bold = __DIR__ . '/../fonts/Poppins-Bold.ttf';

    $fontsize_name = 80;
    $fontsize_desc = 22;
    $angle = 0;

    // Teks Nama
    $box = imagettfbbox($fontsize_name, $angle, $font_bold, $nama_resmi);
    $text_width = $box[2] - $box[0];
    $text_height = $box[1] - $box[7];

    $image_width = imagesx($sertifikat);
    $image_height = imagesy($sertifikat);

    $x = ($image_width - $text_width) / 2;
    $y = ($image_height + $text_height) / 2 - 115;

    imagettftext($sertifikat, $fontsize_name, $angle, $x, $y, $black, $font_bold, $nama_resmi);

    // Teks Judul Kelas
    $before = 'For successfully completing the "';
    $after = '" program.';
    $combined = $before . $judul_kelas . $after;
    $box = imagettfbbox($fontsize_desc, $angle, $font, $combined);
    $total_width = $box[2] - $box[0];
    $start_x = ($image_width - $total_width) / 2;
    $y_text = $y + 125;

    imagettftext($sertifikat, $fontsize_desc, $angle, $start_x, $y_text, $black, $font, $before);
    $before_width = imagettfbbox($fontsize_desc, $angle, $font, $before)[2];
    imagettftext($sertifikat, $fontsize_desc, $angle, $start_x + $before_width, $y_text, $black, $font_bold, $judul_kelas);
    $judul_width = imagettfbbox($fontsize_desc, $angle, $font_bold, $judul_kelas)[2];
    imagettftext($sertifikat, $fontsize_desc, $angle, $start_x + $before_width + $judul_width, $y_text, $black, $font, $after);

    // Teks Tanggal Pelaksanaan
    $tanggal_mulai_formatted = formatTanggalIndonesia($tanggal_mulai);
    $tanggal_selesai_formatted = formatTanggalIndonesia($tanggal_selesai);

    $bootcamp_date = $tanggal_mulai_formatted . ' - ' . $tanggal_selesai_formatted;
    $box = imagettfbbox($fontsize_desc, $angle, $font, $bootcamp_date);
    $date_width = $box[2] - $box[0];
    $date_position_x = ($image_width - $date_width) / 2;
    $data_position_y = $y_text + 180;
    imagettftext($sertifikat, $fontsize_desc, $angle, $date_position_x, $data_position_y, $black, $font, $bootcamp_date);


    $filename = md5($email_input . time()) . '.png';
    $path = __DIR__ . '/../output/' . $filename;
    imagepng($sertifikat, $path);
    imagedestroy($sertifikat);

    return 'output/' . $filename;
}
?>
