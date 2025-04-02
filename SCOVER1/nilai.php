<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user_email'];

$query = "SELECT pengajar_id, full_name FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$pengajar = $result->fetch_assoc();

if (!$pengajar) {
    die("Akun tidak ditemukan.");
}

$pengajar_id = $pengajar['pengajar_id'];
$pengajar_name = $pengajar['full_name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $siswa_id = $_POST["siswa_id"];
    $nama_kuis = $_POST["nama_kuis"];
    $nilai = $_POST["nilai"];

    // Validasi input
    if (empty($siswa_id) || empty($nama_kuis) || empty($nilai)) {
        echo "<script>alert('Harap isi semua kolom.'); window.history.back();</script>";
        exit();
    }

    $query = "INSERT INTO nilai_siswa (pengajar_id, siswa_id, nama_kuis, nilai) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisi", $pengajar_id, $siswa_id, $nama_kuis, $nilai);

    if ($stmt->execute()) {
        echo "<script>alert('Nilai berhasil disimpan!'); window.location.href = 'input_nilai.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
    $stmt->close();
}

$query = "SELECT siswa_id, full_name FROM siswa";
$result = mysqli_query($conn, $query);
$siswaList = [];
while ($row = mysqli_fetch_assoc($result)) {
    $siswaList[] = $row;
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai Siswa</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #003049;
            color: #fabe49;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        h2 {
            margin-top: 20px;
        }
        .container {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background-color: #0271ab;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
            position: relative;
        }
        input[type="text"], input[type="number"], button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            box-sizing: border-box;
        }
        button {
            background-color: #faaf1d;
            color: #003049;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #fabe49;
        }

        .autocomplete-suggestions {
            border: 1px solid #ccc;
            border-top: none;
            max-height: 150px;
            overflow-y: auto;
            background-color: #0271ab;
            position: absolute;
            width: calc(100% - 22px);
            z-index: 9999;
            left: 10px;
        }
        .autocomplete-suggestion {
            padding: 10px;
            cursor: pointer;
        }
        .autocomplete-suggestion:hover {
            background-color: #faaf1d;
            color: #003049;
        }

.back-button {
    display: inline-block;
    background-color: #faaf1d;
    color: #003049;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 20px;
    transition: background-color 0.3s;
}

.back-button:hover {
    background-color: #fabe49;
}

    </style>
</head>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="images/foto4.png" alt="Logo">
            <span class="logo-text">Scover Center</span>
        </div>
        <h1 class="title">Dashboard Mentor</h1>
    <ul class="nav-links">
        <li><a href="home_mentor.php">Presensi</a></li>
        <li><a href="siswa.php">Siswa</a></li>
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php">Kuis</a></li>
        <li><a href="nilai.php" class="active">Nilai</a></li>
        <li><a href="profile_mentor.php">Profil</a></li>
        <li><a href="kontak_mentor.php">Kontak</a></li>
        <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <h2>Input Nilai Siswa</h2>
    <p>Pengajar: <b><?php echo htmlspecialchars($pengajar_name); ?></b></p>
    <div class="container">
        <form action="" method="POST">
            <label for="searchStudent">Cari Siswa:</label>
            <input type="text" id="searchStudent" placeholder="Ketik nama siswa...">

            <input type="hidden" name="siswa_id" id="siswaId">
            <div id="autocomplete-list" class="autocomplete-suggestions"></div>
            
            <label for="nama_kuis">Nama Kuis:</label>
            <input type="text" name="nama_kuis" placeholder="Contoh: Kuis Matematika" required>
            
            <label for="nilai">Nilai:</label>
            <input type="number" name="nilai" min="0" max="100" placeholder="Masukkan nilai (0-100)" required>
            
            <button type="submit">Simpan Nilai</button>
        </form>
        <a href="home.php" class="back-button">Kembali</a>
        <a href="input_nilai.php" class="back-button">Riwayat</a>
    </div>
    <script src="js/logout.js" defer></script>
    <script src="js/home.js" defer></script>
    <script src="js/menu.js" defer></script>
    <script>
        const siswaList = <?php echo json_encode($siswaList); ?>;
        
        const searchInput = document.getElementById('searchStudent');
        const autocompleteList = document.getElementById('autocomplete-list');
        const siswaIdInput = document.getElementById('siswaId');
        
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            autocompleteList.innerHTML = ''; 
            
            if (!query) {
                return;
            }
            
            siswaList.forEach(function(siswa) {
                if (siswa.full_name.toLowerCase().includes(query)) {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.classList.add('autocomplete-suggestion');
                    suggestionItem.textContent = siswa.full_name;
                    suggestionItem.dataset.id = siswa.siswa_id;

                    suggestionItem.addEventListener('click', function() {
                        searchInput.value = siswa.full_name;
                        siswaIdInput.value = siswa.siswa_id;
                        autocompleteList.innerHTML = '';
                    });
                    
                    autocompleteList.appendChild(suggestionItem);
                }
            });
        });
        
        document.addEventListener('click', function(e) {
            if (e.target !== searchInput) {
                autocompleteList.innerHTML = '';
            }
        });
    </script>
</body>
</html>
