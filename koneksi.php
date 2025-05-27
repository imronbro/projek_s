<?php
$host = "localhost";
$user = "scoe1247_scover";
$pass = "g3bXRS3gc93rE58";
$db_name = "scoe1247_scover";

$conn = mysqli_connect($host, $user, $pass, $db_name);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
