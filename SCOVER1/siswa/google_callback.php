<?php
require '../vendor/autoload.php';
include '../koneksi.php';

session_start();

$client = new Google_Client();
$client->setClientId('437056474620-tuovfk26e0v50377prsdskb7mcofiiko.apps.googleusercontent.com'); // Ganti dengan Client ID Anda
$client->setClientSecret('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'); // Ganti dengan Client Secret Anda
$client->setRedirectUri('http://localhost/projek_s-1/SCOVER1/siswa/google_callback.php'); // Ganti dengan Redirect URI Anda

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    $email = $userInfo->email;
    $full_name = $userInfo->name;

    // Periksa apakah pengguna sudah terdaftar
    $query = "SELECT * FROM siswa WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Jika pengguna sudah terdaftar, arahkan ke halaman home
        $_SESSION['user_name'] = $full_name;
        $_SESSION['user_email'] = $email;
        header("Location: home.php");
        exit();
    } else {
        // Jika pengguna belum terdaftar, tambahkan ke database
        $query = "INSERT INTO siswa (full_name, email) VALUES ('$full_name', '$email')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;
            header("Location: home.php");
            exit();
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
} else {
    echo "Kode otorisasi tidak ditemukan.";
}
?>