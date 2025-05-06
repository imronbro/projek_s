<?php
include '../koneksi.php';

if (isset($_GET['keyword'])) {
    $keyword = "%" . $_GET['keyword'] . "%";

    $query = "SELECT siswa_id, full_name FROM siswa WHERE full_name LIKE ? LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $keyword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div>
                    <input type="checkbox" id="siswa-' . $row['siswa_id'] . '" 
                           onchange="pilihSiswa(this, \'' . $row['siswa_id'] . '\', \'' . htmlspecialchars($row['full_name']) . '\')">
                    ' . htmlspecialchars($row['full_name']) . '
                  </div>';
        }
    } else {
        echo '<p>Tidak ada siswa ditemukan.</p>';
    }

    $stmt->close();
    $conn->close();
}
?>