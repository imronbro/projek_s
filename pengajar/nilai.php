<?php
session_start();
include '../koneksi.php';

// Ganti pengecekan session
if (!isset($_SESSION['user_email'])) {
    header("Location: login_mentor.php");
    exit();
}

// Ambil email dari session
$user_email = $_SESSION['user_email'];

// Ambil pengajar_id berdasarkan email
$query = "SELECT pengajar_id FROM mentor WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $pengajar_id = $row['pengajar_id'];
} else {
    header("Location: logout.php");
    exit();
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $siswa_id = $_POST["siswa_id"];
    $nama_kuis = $_POST["nama_kuis"];
    $nilai = $_POST["nilai"];

    // Validasi input
    if (empty($siswa_id) || empty($nama_kuis) || empty($nilai)) {
        $notification = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Harap isi semua kolom.'
        ];
    } else {
        $query = "INSERT INTO nilai_siswa (pengajar_id, siswa_id, nama_kuis, nilai) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisi", $pengajar_id, $siswa_id, $nama_kuis, $nilai);

        if ($stmt->execute()) {
            $notification = [
                'type' => 'success',
                'title' => 'Berhasil!',
                'message' => 'Nilai berhasil disimpan!'
            ];
        } else {
            $notification = [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Terjadi kesalahan: ' . mysqli_error($conn)
            ];
        }
        $stmt->close();
    }
}

// Ambil daftar siswa
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
    <title>Dashboard Mentor - Kuis</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins';
        background-color: #ffffff;
        color: #145375;
        margin: 0;
        padding: 0;
        padding-top: 100px;
        overflow-x: hidden;
    }

    h2, p {
    color: #145375;
    text-align: center;
}

    .container {
        width: 80%;
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        position: relative;
        color: #145375;
    }
    input[type="text"],
    input[type="number"],
    button {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 16px;
    }

    #autocomplete-list {
        margin-bottom: 0;
        border-bottom: none;
    }

    button {
        background-color: #e6c200;
        color:#145375;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #145375;
        color: #fff;
    }

    .back-button {
        display: inline-block;
        background-color: #e6c200;
        color: #145375;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 20px;
        margin-right: 10px;
        transition: background-color 0.3s;
        font-weight: bold;
    }

    .back-button:hover {
        background-color: #145375;
        color: #fff;
    }

    /* Autocomplete */
    .autocomplete-suggestions {
        border: 1px solid #ccc;
        border-top: none;
        max-height: 150px;
        overflow-y: auto;
        background-color: #ffffff;
        position: absolute;
        width: calc(100% - 22px);
        z-index: 9999;
        left: 10px;
        border-radius: 0 0 5px 5px;
    }

    .autocomplete-suggestion {
        padding: 10px;
        cursor: pointer;
        color: #145375;
    }

    .autocomplete-suggestion:hover {
        background-color: #faaf1d;
        color: #003049;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }
    }
     .notification-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .notification-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        width: 320px;
        padding: 30px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        text-align: center;
    }

    .notification-icon {
        font-size: 52px;
        margin-bottom: 20px;
    }

    .notification-popup.success .notification-icon {
        color: #28a745;
    }

    .notification-popup.error .notification-icon {
        color: #dc3545;
    }

    .notification-title {
        font-size: 20px;
        font-weight: 600;
        color: #145375;
        margin-bottom: 12px;
    }

    .notification-message {
        font-size: 15px;
        color: #666;
        margin-bottom: 25px;
        line-height: 1.6;
    }

    .notification-button {
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #145375;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .notification-button:hover {
        background: #0e3e5a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(20, 83, 117, 0.3);
    }

    .notification-overlay.show,
    .notification-popup.show {
        opacity: 1;
        visibility: visible;
    }

    .notification-popup.show {
        transform: translate(-50%, -50%) scale(1);
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
        <li><a href="jadwal.php">Jadwal</a></li>
        <li><a href="kuis.php">Kuis</a></li>
        <li><a href="nilai.php" class="active">Nilai</a></li>
        <li><a href="profile_mentor.php">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <h2>Input Nilai Siswa</h2>
    
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
        <a href="input_nilai.php" class="back-button">Riwayat</a>
    </div>
    <div id="logout-notification" class="notification">
        <p>Apakah Anda yakin ingin keluar?</p>
        <div class="notification-buttons">
            <button class="btn btn-secondary" onclick="cancelLogout()">Batal</button>
            <a href="logout.php" class="btn btn-danger">Keluar</a>
        </div>
    </div>
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
    <div class="notification-overlay" id="notificationOverlay"></div>
    <div class="notification-popup" id="notificationPopup">
        <div class="notification-icon">
            <i class="fas" id="notificationIcon"></i>
        </div>
        <div class="notification-title" id="notificationTitle"></div>
        <div class="notification-message" id="notificationMessage"></div>
        <button class="notification-button" id="notificationButton">OK</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('notificationOverlay');
            const popup = document.getElementById('notificationPopup');
            const title = document.getElementById('notificationTitle');
            const message = document.getElementById('notificationMessage');
            const button = document.getElementById('notificationButton');
            const icon = document.getElementById('notificationIcon');

            function showNotification(type, titleText, messageText) {
                popup.className = `notification-popup ${type}`;
                icon.className = `fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`;
                title.textContent = titleText;
                message.textContent = messageText;

                overlay.style.display = 'block';
                popup.style.display = 'block';

                setTimeout(() => {
                    overlay.classList.add('show');
                    popup.classList.add('show');
                }, 10);
            }

            button.onclick = function() {
                overlay.classList.remove('show');
                popup.classList.remove('show');

                setTimeout(() => {
                    overlay.style.display = 'none';
                    popup.style.display = 'none';
                    if (popup.classList.contains('success')) {
                        window.location.href = 'input_nilai.php'; // Redirect on success
                    } else {
                        window.history.back(); // Go back on error
                    }
                }, 300);
            };

            <?php if (isset($notification)): ?>
            showNotification(
                '<?php echo $notification['type']; ?>', 
                '<?php echo $notification['title']; ?>', 
                '<?php echo $notification['message']; ?>'
            );
            <?php endif; ?>
        });
    </script>
</body>
</html>
