<?php
include '../koneksi.php';

$keyword = $_GET['keyword'] ?? '';
$keyword = trim($keyword);

if ($keyword !== '') {
    $stmt = $conn->prepare("SELECT siswa_id, full_name FROM siswa WHERE full_name LIKE ? ORDER BY full_name ASC LIMIT 30");
    $like = "%$keyword%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo '<label class="siswa-item">';
        echo '<input type="checkbox" id="cb-siswa-'.$row['siswa_id'].'" onclick="pilihSiswa(this, \''.$row['siswa_id'].'\', \''.htmlspecialchars($row['full_name'], ENT_QUOTES).'\')"> ';
        echo '<span class="siswa-nama">'.htmlspecialchars($row['full_name']).'</span>';
        echo '</label><br>';
    }
    $stmt->close();
}
$conn->close();
?>