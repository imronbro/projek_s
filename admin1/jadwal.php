<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}
$user_email = $_SESSION['user_email'];

// Fungsi untuk mendapatkan nama hari dalam bahasa Indonesia
function getHari($tanggal)
{
    $hari = date('l', strtotime($tanggal));
    $hariIndonesia = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu'
    ];
    return $hariIndonesia[$hari];
}

// Proses tambah jadwal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['siswa_id'])) {
    $siswa_ids = $_POST['siswa_id']; // Ini adalah array
    $tanggal = $_POST['tanggal'];
    $sesi = $_POST['sesi'];
    $mata_pelajaran = $_POST['mata_pelajaran'];
    $pengajar_id = $_POST['pengajar_id'];

    // Loop untuk memasukkan setiap siswa
    foreach ($siswa_ids as $siswa_id) {
        $query = "INSERT INTO jadwal_siswa (siswa_id, tanggal, sesi, mata_pelajaran, pengajar_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssi", $siswa_id, $tanggal, $sesi, $mata_pelajaran, $pengajar_id);

        if (!$stmt->execute()) {
            echo "<script>alert('Gagal menambahkan jadwal untuk siswa ID: $siswa_id');</script>";
        }
    }
    echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location.href='jadwal.php';</script>";
    $stmt->close();
}

// Ambil daftar siswa
$siswa_result = $conn->query("SELECT siswa_id, full_name FROM siswa");

// Ambil daftar pengajar
$pengajar_result = $conn->query("SELECT pengajar_id, full_name FROM mentor");

// Ambil jadwal
$today = date('Y-m-d');
$jadwal_query = "SELECT j.id, s.full_name AS siswa_name, j.tanggal, j.sesi, j.mata_pelajaran, m.full_name AS pengajar_name 
                 FROM jadwal_siswa j 
                 JOIN siswa s ON j.siswa_id = s.siswa_id 
                 JOIN mentor m ON j.pengajar_id = m.pengajar_id
                 ORDER BY 
                    CASE 
                        WHEN j.tanggal = CURDATE() THEN 0
                        WHEN j.tanggal > CURDATE() THEN 1
                        ELSE 2
                    END,
                 j.tanggal ASC, j.sesi ASC";
$jadwal_result = $conn->query($jadwal_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {

        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins';
        background-color: #f4f4f4;
        color: #145375;
        margin: 0;
        padding: 0;
        padding-top: 100px;
        overflow-x: hidden;

    }

    .container {
        margin-top: 35px;
        padding: 20px;
        width: 90%;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 1s ease-in-out;
    }
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    background-color: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    color: #145375;
}

form label {
    font-weight: 600;
    color: #145375;
}



button[type="submit"] {
    background-color: #e6c200;
    color: #fff;
    font-weight: bold;
    border: none;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #145375;
}

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    h2 {
        text-align: center;
        color: #145375;
        margin: 40px;
        font-size: 2.5em;
    }

    p {
        text-align: center;
        font-size: 1.2em;
        margin-bottom: 30px;
    }


    table {
        width: 90%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fabe49;
        color: #003049;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        margin: auto;
    }

    th,
    td {
        padding: 15px;
        border: 1px solid #003049;
        text-align: center;
        font-size: 1em;
    }

    th {
        background-color: #faaf1d;
        color: #003049;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #e0e0e0;
    }

    tr:nth-child(odd) {
        background-color: #ffffff;
    }

    tr:hover {
        background-color: #faaf1d;
        color: #003049;
    }

    @media (max-width: 768px) {
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-form input,
        .filter-form button {
            width: 100%;
        }

        h2 {
            font-size: 2em;
        }

        th,
        td {
            font-size: 0.9em;
            padding: 10px;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 1.8em;
        }

        th,
        td {
            font-size: 0.8em;
            padding: 8px;
        }

        .filter-form input,
        .filter-form button {
            font-size: 0.9em;
            padding: 8px;
        }
    }


        /* Dropdown styles */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #0271ab;
            min-width: 180px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            padding: 0;
            margin: 5px;
            left: -35px;
            list-style: none;

            /* <- tambahkan border */
        }

        .dropdown-menu li a {
            color: #fff !important;
            /* pastikan warnanya terlihat */
            padding: 12px 16px;
            text-decoration: none;
            display: block;

            font-weight: bold;
            /* opsional biar lebih terlihat */
        }

        .dropdown-menu li a:hover {
            background-color: #e6c200;
            color: #145375;
        }


        .arrow {
            font-size: 12px;
            margin-left: 5px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0;
            width: 100%;
        }

            .notification {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #f9f9f9;
        color: #145375;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
        /* Default: disembunyikan */
        text-align: center;
        width: 300px;
    }

    .notification p {
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: bold;
    }

    /* Tombol di dalam notifikasi */
    .notification-buttons {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .notification-buttons .btn {
        flex: 1;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s ease;
        text-decoration: none;
        /* Hilangkan garis bawah */
    }

    /* Tombol Batal */
    .notification-buttons .btn-secondary {
        background-color: #145375;
        color: white;
    }

    .notification-buttons .btn-secondary:hover {
        background-color: #e6c200;
        color: #145375;
    }

    /* Tombol Keluar */
    .notification-buttons .btn-danger {
        background-color: #e74c3c;
        /* Warna merah */
        color: white;
        border: none;
    }

    .notification-buttons .btn-danger:hover {
        background-color: #c0392b;
        /* Warna merah lebih gelap saat hover */
        transform: scale(1.05);
        /* Efek zoom saat hover */
    }
        /* Modal latar belakang */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(20, 83, 117, 0.6); /* #145375 transparan */
}

/* Isi modal */
.modal-content {
    background-color: #fff; /* putih */
    margin: 5% auto;
    padding: 30px;
    border: 2px solid #145375;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    color: #145375;
    font-family: 'Segoe UI', sans-serif;
}

/* Tombol close */
.close {
    color: #145375;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #e6c200;
    text-decoration: none;
}

/* Judul modal */
.modal-content h2 {
    margin-top: 0;
    color: #145375;
}

/* Label */
.modal-content label {
    display: block;
    margin-top: 15px;
    font-weight: 600;
    color: #145375;
}

/* Input dan Select */
.modal-content input[type="text"],
.modal-content input[type="date"],
.modal-content select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #145375;
    border-radius: 8px;
    font-size: 1em;
    background-color: #fff;
    color: #145375;
}

.modal-content select:focus,
.modal-content input:focus {
    border-color: #e6c200;
    outline: none;
    box-shadow: 0 0 5px #e6c200;
}

/* Tombol submit di dalam modal */
.modal-content button[type="submit"] {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #e6c200;
    border: none;
    border-radius: 10px;
    color: #145375;
    font-weight: bold;
    font-size: 1em;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.modal-content button[type="submit"]:hover {
    background-color: #d4b200;
}

/* Tombol di luar modal */
.container > button {
    background-color: #145375;
    color: #fff;
    padding: 10px 16px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.3s ease;
    margin-bottom: 20px;
}

.container > button:hover {
    background-color: #0e3a5d;
}

/* Judul halaman */
.container h2 {
    color: #145375;
    margin-bottom: 10px;
}
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

form label {
    font-weight: bold;
    color: #145375;
}

form input,
form select,
.select2-container {
    width: 100%;
    padding: 10px;
    font-size: 1em;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
}

button[type="submit"] {
    background-color: #e6c200;
    color: #fff;
    font-weight: bold;
    border: none;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #145375;
}


    </style>
</head>
<script>
    function confirmLogout() {
        if (confirm("Apakah kamu yakin ingin keluar?")) {
            window.location.href = "logout.php"; // ganti sesuai nama file logout-mu
        }
    }

    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }

    function toggleDropdown(event) {
        event.preventDefault(); // supaya gak reload atau pergi ke #
        const dropdown = event.currentTarget.nextElementSibling;
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    function toggleDropdown(event) {
        event.preventDefault();
        const link = event.currentTarget;
        const dropdown = link.nextElementSibling;
        const arrow = link.querySelector('#arrow');

        const isOpen = dropdown.style.display === 'block';
        dropdown.style.display = isOpen ? 'none' : 'block';
        arrow.innerHTML = isOpen ? '&#9660;' : '&#9650;'; // ▼ / ▲
    }


    // Tutup dropdown kalau klik di luar menu
    document.addEventListener('click', function (event) {
        const dropdownMenus = document.querySelectorAll('.dropdown-menu');
        dropdownMenus.forEach(menu => {
            if (!menu.parentElement.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    });
</script>

<body>
    <nav class="navbar">
        <div class="logo">
            <a href="home.php">
                <img src="images/foto4.png" alt="Logo" class="logo-image">
            </a>
        </div>
        <h1 class="title">Dashboard Admin</h1>
        <ul class="nav-links">
            <li class="dropdown">
                <a href="#" onclick="toggleDropdown(event)">Presensi <span id="arrow" class="arrow">&#9660;</span></a>
                <ul class="dropdown-menu">
                    <li><a href="home.php">Presensi Siswa</a></li>
                    <li><a href="presensipengajar.php">Presensi Pengajar</a></li>
                </ul>
            </li>

            <li><a href="pengajar.php">Pengajar</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php" class="active">Jadwal</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="rating.php">Rating</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <div class="container">
        <h2>Atur Jadwal Siswa</h2>
        <button onclick="openModal()" style="margin: 40px;">Tambah Jadwal</button>
        <div id="jadwalModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Tambah Jadwal</h2>
                <form action="" method="post">
    <!-- Siswa -->
    <label for="siswa_id">Siswa:</label>
    <select name="siswa_id[]" id="siswa_id" class="select2" multiple required>
        <?php while ($row = $siswa_result->fetch_assoc()) { ?>
            <option value="<?= $row['siswa_id'] ?>">
                <?= htmlspecialchars($row['full_name']) ?>
            </option>
        <?php } ?>
    </select>

    <!-- Tanggal -->
    <label for="tanggal">Tanggal:</label>
    <input type="date" name="tanggal" id="tanggal" required>

    <!-- Sesi -->
    <label for="sesi">Sesi:</label>
    <select name="sesi" id="sesi" required>
        <option value="Sesi 1 (09:00-10:30)">Sesi 1 (09:00-10:30)</option>
        <option value="Sesi 2 (10:30-12:00)">Sesi 2 (10:30-12:00)</option>
        <option value="Sesi 3 (13:00-14:30)">Sesi 3 (13:00-14:30)</option>
        <option value="Sesi 4 (14:30-16:00)">Sesi 4 (14:30-16:00)</option>
        <option value="Sesi 5 (16:00-17:30)">Sesi 5 (16:00-17:30)</option>
        <option value="Sesi 6 (18:00-19:30)">Sesi 6 (18:00-19:30)</option>
        <option value="Sesi 7 (19:30-21:00)">Sesi 7 (19:30-21:00)</option>
    </select>

    <!-- Mata Pelajaran -->
    <label for="mata_pelajaran">Mata Pelajaran:</label>
    <input type="text" name="mata_pelajaran" id="mata_pelajaran" required>

    <!-- Pengajar -->
    <label for="pengajar_id">Pengajar:</label>
    <select name="pengajar_id" id="pengajar_id" class="select2" required>
        <option value="" disabled selected>Pilih Pengajar</option>
        <?php while ($row = $pengajar_result->fetch_assoc()) { ?>
            <option value="<?= $row['pengajar_id'] ?>">
                <?= htmlspecialchars($row['full_name']) ?>
            </option>
        <?php } ?>
    </select>
    

    <!-- Tombol Submit -->
    <button type="submit">Tambah Jadwal</button>
</form>


            </div>
        </div>

        <!-- Tabel Jadwal -->
        <h3>Jadwal yang Telah Diatur</h3>
        <table>
            <tr>
                <th>Siswa</th>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Sesi</th>
                <th>Mata Pelajaran</th>
                <th>Pengajar</th>
                <th>Aksi</th>
            </tr>
            <?php
            if ($jadwal_result->num_rows > 0) {
                while ($row = $jadwal_result->fetch_assoc()) {
                    $hari = getHari($row['tanggal']);
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['siswa_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                    echo "<td>" . htmlspecialchars($hari) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sesi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['mata_pelajaran']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['pengajar_name']) . "</td>";
                    echo "<td><a href='edit_jadwal.php?id=" . $row['id'] . "'>Edit</a> | 
                      <a href='hapus_jadwal.php?id=" . $row['id'] . "' onclick='return confirm(\"Hapus jadwal ini?\")'>Hapus</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Tidak ada data jadwal.</td></tr>";
            }
            ?>
        </table>

    </div>

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // Inisialisasi Select2
            $('.select2').select2({
                placeholder: "Pilih...",
                allowClear: true
            });
        });
    </script>
<script>
    function openModal() {
        document.getElementById('jadwalModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('jadwalModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('jadwalModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

    <script>$(document).ready(function() {
    $('.select2').select2({
        width: '100%' // Agar lebar sesuai dengan input lainnya
    });
});
</script>
<div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
</body>
<script src="js/menu.js" defer></script>

</html>