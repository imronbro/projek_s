<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['mentor_id'])) {
    header("Location: login_mentor.php");
    exit();
}

$mentor_id = $_SESSION['mentor_id'];

$query = "SELECT js.id, js.tanggal, js.sesi, js.mata_pelajaran, s.full_name as nama_siswa 
          FROM jadwal_siswa js
          JOIN siswa s ON js.siswa_id = s.siswa_id
          WHERE js.pengajar_id = ? AND js.tanggal >= CURDATE()
          ORDER BY js.tanggal ASC, js.sesi ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$result = $stmt->get_result();

$current_date = null;
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mentor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins';
            background-color: #fff;
            color: #145375;
            margin: 0;
            padding: 0;
            padding-top: 100px;
            overflow-x: hidden;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .container h2 {
            text-align: center;
            color: #145375;
            margin-bottom: 30px;
        }

        .container table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }

        .container th,
        .container td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .container th {
            background-color: #145375;
            color: white;
            font-weight: bold;
        }

        .container tr:hover {
            background-color: #e9f3f9;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .container table,
            .container thead,
            .container tbody,
            .container th,
            .container td,
            .container tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 15px;
            }

            td {
                position: relative;
                padding-left: 50%;
            }

            td::before {
                position: absolute;
                left: 16px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
                color: #145375;
            }

            td:nth-of-type(1)::before {
                content: "Nama Siswa";
            }

            td:nth-of-type(2)::before {
                content: "Tanggal";
            }

            td:nth-of-type(3)::before {
                content: "Sesi";
            }

            td:nth-of-type(4)::before {
                content: "Mata Pelajaran";
            }
        }
    </style>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Mentor</h1>
        <ul class="nav-links">
            <li><a href="home_mentor.php">Jurnal</a></li>
            <li><a href="proses_presensi.php">Presensi Siswa</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php" class="active">Jadwal</a></li>
            <li><a href="kuis.php">Kuis</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <div class="container">
        <h2>Jadwal Anda</h2>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tanggal = date("d M Y", strtotime($row['tanggal']));

                // Jika tanggal baru, tampilkan judul dan mulai tabel baru
                if ($tanggal != $current_date) {
                    if ($current_date !== null) {
                        echo "</tbody></table><br>";
                    }

                    echo "<h3 style='margin-top: 40px;'>Tanggal: $tanggal</h3>";
                    echo "<table>
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>Tanggal</th>
                                <th>Sesi</th>
                                <th>Mata Pelajaran</th>
                            </tr>
                        </thead>
                        <tbody>";

                    $current_date = $tanggal;
                }

                // Isi baris tabel
                echo "<tr>
                    <td>" . htmlspecialchars($row['nama_siswa']) . "</td>
                    <td>" . htmlspecialchars($row['tanggal']) . "</td>
                    <td>" . htmlspecialchars($row['sesi']) . "</td>
                    <td>" . htmlspecialchars($row['mata_pelajaran']) . "</td>
                  </tr>";
            }
            echo "</tbody></table>"; // Tutup tabel terakhir
        } else {
            echo "<p>Tidak ada jadwal yang akan datang.</p>";
        }
        ?>
    </div>

    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>

    <script src="js/logout.js" defer></script>
    <script src="js/menu.js" defer></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>