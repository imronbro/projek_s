<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

$user_email = $_SESSION['user_email'];

$query = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $pengajar_id = $row['pengajar_id'];
    $full_name = $row['full_name'];
} else {
    echo "<script>alert('Akun tidak ditemukan!'); window.location.href='login_mentor.php';</script>";
    exit();
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $siswa_ids = isset($_POST['siswa_ids']) ? explode(',', $_POST['siswa_ids']) : []; // Pisahkan string menjadi array
    $kelas = $_POST['kelas'];
    $mapel = $_POST['mapel'];
    $sekolah = !empty($_POST['sekolah']) ? $_POST['sekolah'] : "";
    $status = $_POST['status'];
    $alasan = !empty($_POST['alasan']) ? $_POST['alasan'] : "";

    if (!empty($siswa_ids)) {
        foreach ($siswa_ids as $siswa_id) {
            $insert = "INSERT INTO absensi_siswa (pengajar_id, siswa_id, kelas, mapel, sekolah, status, alasan) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert);
            $stmt_insert->bind_param("iisssss", $pengajar_id, $siswa_id, $kelas, $mapel, $sekolah, $status, $alasan);
            if (!$stmt_insert->execute()) {
    echo "Error insert: " . $stmt_insert->error;
    exit;
}

        }
        echo "<script>alert('Presensi berhasil disimpan!'); window.location.href='riwayat_absensi.php';</script>";
    } else {
        echo "<script>alert('Pilih setidaknya satu siswa!'); window.history.back();</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Siswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins';
    }

    .container {
        margin-top: 100px;
        /* Tambahkan margin agar konten tidak tertutup navbar */
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #145375;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        /* Gunakan berat font yang sesuai */
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    label {
        font-weight: bold;
        color: #333;
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
        /* Berat font normal */
    }

    select,
    input[type="text"],
    textarea {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        width: 100%;
        /* Pastikan elemen input memenuhi lebar kontainer */
        box-sizing: border-box;
        /* Pastikan padding tidak memengaruhi ukuran elemen */
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
        /* Berat font normal */
    }

    textarea {
        resize: vertical;
    }

    #siswa-list {
        margin-top: 10px;
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ccc;
        padding: 10px;
    }

    #selected-siswa {
        margin-top: 20px;
        padding: 10px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
    }

    .form-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-weight: bold;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        margin: 10px 5px;
        cursor: pointer;
        background-color: #e6c200;
        color: #145375;
        transition: 0.3s ease;
        text-align: center;
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
        /* Berat font normal */
    }

    .btn:hover {
        background-color: #145375;
        color: white;
    }

    #selected-siswa {
        margin-top: 20px;
        padding: 15px;
        border: 1px solid #ccc;
        background-color: #f0f8ff;
        border-radius: 8px;
    }

    #selected-siswa h4 {
        margin-bottom: 15px;
        font-size: 16px;
        color: #145375;
    }

    .siswa-card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .siswa-card {
        background-color: #ffffff;
        border: 2px solid #145375;
        border-radius: 12px;
        padding: 10px 15px;
        font-size: 14px;
        color: #145375;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 150px;
        max-width: 100%;
        flex: 1 1 calc(50% - 10px);
        /* Responsive 2 column */
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
    }

    .siswa-card button {
        background-color: transparent;
        border: none;
        color: #d9534f;
        font-weight: bold;
        cursor: pointer;
        margin-left: 10px;
        transition: 0.2s;
    }

    .siswa-card button:hover {
        color: #a94442;
    }

    /* Tambahkan ini agar responsif di layar kecil */
    @media (max-width: 500px) {
        .siswa-card {
            flex: 1 1 100%;
        }
    }


    /* Responsif untuk layar kecil */
    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }

        h2 {
            font-size: 18px;
        }

        select,
        input[type="text"],
        textarea {
            font-size: 12px;
            padding: 8px;
        }

        .btn {
            font-size: 12px;
            padding: 8px 15px;
        }
    }

    /* Responsif untuk layar sangat kecil */
    @media (max-width: 480px) {
        h2 {
            font-size: 16px;
        }

        select,
        input[type="text"],
        textarea {
            font-size: 10px;
            padding: 6px;
        }

        .btn {
            font-size: 10px;
            padding: 6px 10px;
        }
    }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
        </div>
        <h1 class="title">Dashboard Mentor</h1>
        <ul class="nav-links">
            <li><a href="home_mentor.php">Jurnal</a></li>
            <li><a href="proses_presensi.php" class="active">Presensi Siswa</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="kuis.php">Kuis</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>
    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
    <div class="container">
        <h2>Presensi Siswa</h2>
        <form action="proses_presensi.php" method="post" id="presensi-form">
            <label for="search-siswa">Cari Siswa (Nama):</label>
            <input type="text" id="search-siswa" name="search-siswa" placeholder="Masukkan Nama Siswa"
                onkeyup="cariSiswa()" autocomplete="off">

            <div id="siswa-list"
                style="margin-top: 10px; max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                <!-- Daftar siswa akan dimuat di sini melalui AJAX -->
            </div>

            <div id="selected-siswa"
                style="margin-top: 20px; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9;">
                <h4>Siswa yang Dipilih:</h4>
                <!-- Daftar siswa yang dipilih akan muncul di sini -->
            </div>

            <input type="hidden" id="selected-siswa-ids" name="siswa_ids" value="">

            <label for="kelas">Kelas:</label>
            <input type="text" id="kelas" name="kelas" placeholder="Masukkan Kelas" required>

            <label for="mapel">Mata Pelajaran:</label>
            <input type="text" id="mapel" name="mapel" placeholder="Masukkan Mata Pelajaran" required>

            <label for="sekolah">Sekolah (Opsional):</label>
            <input type="text" id="sekolah" name="sekolah" placeholder="Masukkan Nama Sekolah">

            <label for="status">Status Kehadiran:</label>
            <select id="status" name="status" required onchange="toggleAlasan()">
                <option value="Hadir">Hadir</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
            </select>

            <div id="alasan-container" style="display: none;">
                <label for="alasan">Alasan Izin/Sakit:</label>
                <textarea id="alasan" name="alasan" rows="3"
                    placeholder="Jelaskan alasan izin atau sakit..."></textarea>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn">Simpan Presensi</button>
                <a href="riwayat_absensi.php" class="btn">Riwayat Presensi</a>

            </div>
        </form>
    </div>
    <script src="js/menu.js" defer></script>
    <script>
    const selectedSiswa = {}; // Objek untuk menyimpan siswa yang dipilih

    function cariSiswa() {
        const keyword = document.getElementById('search-siswa').value;
        const siswaList = document.getElementById('siswa-list');

        if (keyword.length > 0) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `cari_siswa.php?keyword=${keyword}`, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    siswaList.innerHTML = this.responseText;
                    updateSelectedSiswa();
                }
            };
            xhr.send();
        } else {
            siswaList.innerHTML = '';
        }
    }

    function pilihSiswa(checkbox, siswaId, siswaName) {
        if (checkbox.checked) {
            selectedSiswa[siswaId] = siswaName; // Tambahkan siswa ke daftar
        } else {
            delete selectedSiswa[siswaId]; // Hapus siswa dari daftar
        }
        updateSelectedSiswa();
    }

    function updateSelectedSiswa() {
        const selectedSiswaContainer = document.getElementById('selected-siswa');
        const selectedSiswaIds = document.getElementById('selected-siswa-ids');
        selectedSiswaContainer.innerHTML = '<h4>Siswa yang Dipilih:</h4>';

        const siswaNames = [];
        const siswaIds = [];

        for (const [id, name] of Object.entries(selectedSiswa)) {
            siswaNames.push(`
            <div class="siswa-card">
                <span>${name}</span>
                <button type="button" onclick="hapusSiswa('${id}')">&times;</button>
            </div>
        `);
            siswaIds.push(id);
        }

        selectedSiswaContainer.innerHTML += `<div class="siswa-card-container">${siswaNames.join('')}</div>`;
        selectedSiswaIds.value = siswaIds.join(',');
    }


    function hapusSiswa(siswaId) {
        delete selectedSiswa[siswaId];
        updateSelectedSiswa();
        cariSiswa(); // Perbarui daftar siswa
    }

    function toggleAlasan() {
        const status = document.getElementById('status').value;
        const alasanContainer = document.getElementById('alasan-container');
        alasanContainer.style.display = (status === 'Izin' || status === 'Sakit') ? 'block' : 'none';
    }
    </script>
</body>

</html>