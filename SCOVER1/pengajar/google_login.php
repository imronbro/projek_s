<?php
require '../../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('437056474620-tuovfk26e0v50377prsdskb7mcofiiko.apps.googleusercontent.com'); // Ganti dengan Client ID Anda
$client->setClientSecret('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'); // Ganti dengan Client Secret Anda
$client->setRedirectUri('http://localhost/projek_s-1/SCOVER1/pengajar/google_callback.php'); // Ganti dengan Redirect URI Anda
$client->addScope('email');
$client->addScope('profile');

$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
exit();
?>