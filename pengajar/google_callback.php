<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';
include '../koneksi.php';

session_start();

$client = new Google_Client();
$client->setClientId('437056474620-tuovfk26e0v50377prsdskb7mcofiiko.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://scovereign.my.id/pengajar/google_callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        echo "Google Auth Error: " . htmlspecialchars($token['error_description'] ?? $token['error']);
        exit;
    }
    $client->setAccessToken($token);

    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    $email = $userInfo->email;
    $full_name = $userInfo->name;
    $gambar = $userInfo->picture ?? '';

    // Field tambahan
    $ttl = '0000-00-00'; // Tanggal default, bisa diganti sesuai kebutuhan
    $alamat = '';
    $nohp = '';
    $mapel = ''; // Default mapel kosong, bisa diubah nanti

    // Periksa apakah pengguna sudah terdaftar
    $query = "SELECT * FROM mentor WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['user_name'] = $full_name;
        $_SESSION['user_email'] = $email;
        header("Location: home_mentor");
        exit();
    } else {
        // Tambahkan field baru pada query insert
        $query = "INSERT INTO mentor (full_name, email, ttl, alamat, nohp, gambar, password, mapel) 
                  VALUES ('$full_name', '$email', '$ttl', '$alamat', '$nohp', '$gambar', 'google-oauth', '$mapel')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;
            header("Location: home_mentor");
            exit();
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
} else {
    echo "Kode otorisasi tidak ditemukan.";
}
?>