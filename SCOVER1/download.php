<?php
// Pastikan file yang diminta ada di folder uploads
if (isset($_GET['file'])) {
    $file_name = basename($_GET['file']); // Ambil nama file dari parameter
    $file_path = __DIR__ . '/uploads1/' . $file_name; // Path lengkap file

    // Periksa apakah file ada
    if (file_exists($file_path)) {
        // Tentukan jenis file berdasarkan ekstensi
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        switch ($file_extension) {
            case 'pdf':
                $content_type = 'application/pdf';
                break;
            case 'jpg':
            case 'jpeg':
                $content_type = 'image/jpeg';
                break;
            case 'png':
                $content_type = 'image/png';
                break;
            case 'gif':
                $content_type = 'image/gif';
                break;
            default:
                $content_type = 'application/octet-stream';
        }

        // Atur header untuk menampilkan file di browser
        header('Content-Type: ' . $content_type);
        header('Content-Length: ' . filesize($file_path));

        // Baca file dan kirim ke browser
        readfile($file_path);
        exit();
    } else {
        // Jika file tidak ditemukan
        echo "<script>alert('File tidak ditemukan.'); window.history.back();</script>";
    }
} else {
    // Jika parameter file tidak valid
    echo "<script>alert('Parameter file tidak valid.'); window.history.back();</script>";
}
?>