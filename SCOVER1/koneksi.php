 <?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "scover";

$conn = mysqli_connect($host, $user, $pass, $db_name);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
