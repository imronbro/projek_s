<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['mentor_id'])) {
    header("Location: login_mentor.php");
    exit();
}

$mentor_id = $_SESSION['mentor_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $siswa_ids = $_POST["siswa_id"]; // ini array
    $nama_kuis = $_POST["nama_kuis"];
    $file_kuis = $_FILES["file_kuis"];

    // Validasi input
    if (empty($siswa_ids) || empty($nama_kuis) || empty($file_kuis)) {
        echo "<script>alert('Harap isi semua kolom.'); window.history.back();</script>";
        exit();
    }

    // Upload file kuis (sama sekali saja)
    $target_dir = "../uploads1/";
    $file_name = basename($file_kuis["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (move_uploaded_file($file_kuis["tmp_name"], $target_file)) {
        $query = "INSERT INTO kuis (pengajar_id, siswa_id, nama, file_kuis) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        foreach ($siswa_ids as $siswa_id) {
            $stmt->bind_param("iiss", $mentor_id, $siswa_id, $nama_kuis, $target_file);
            $stmt->execute();
        }

        $stmt->close();

        echo "<script>alert('Kuis berhasil disimpan!'); window.location.href = 'kuis.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupload file.'); window.history.back();</script>";
    }
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
    <title>Dashboard Mentor - Kuis</title>
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

    h2,
    p {
        color: #145375;
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
    input[type="file"],
    button {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 16px;
    }

    button {
        background-color: #e6c200;
        color: #145375;
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

    .autocomplete-suggestions {
        border: none;
        max-height: 150px;
        overflow-y: auto;
        background-color: #ffffff;
        position: absolute;
        width: calc(100% - 22px);
        z-index: 9999;
        left: 10px;
        border-radius: 0 0 5px 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* opsional buat kesan melayang */
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

    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-weight: bold;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        margin: 10px 5px;
        cursor: pointer;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 20px;
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
            <li><a href="proses_presensi.php">Presensi Siswa</a></li>
            <li><a href="siswa.php">Siswa</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="kuis.php" class="active">Kuis</a></li>
            <li><a href="nilai.php">Nilai</a></li>
            <li><a href="profile_mentor.php">Profil</a></li>
            <li><button class="logout-btn" onclick="confirmLogout()">Keluar</button></li>
        </ul>
        <div class="menu-icon" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
    <h2 style="text-align: center;">Input Kuis</h2>

    <div class="container">
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="searchStudent">Cari Siswa:</label>
            <input type="text" id="searchStudent" placeholder="Ketik nama siswa...">

            <!-- Container pilihan siswa yang sudah dipilih -->
            <div id="selectedStudents" style="margin:10px 0; min-height:30px;"></div>

            <!-- Hidden inputs array untuk kirim id siswa -->
            <div id="hiddenInputs"></div>

            <div id="autocomplete-list" class="autocomplete-suggestions"></div>


            <label for="nama_kuis">Nama Kuis:</label>
            <input type="text" name="nama_kuis" placeholder="Contoh: Kuis Matematika" required>

            <label for="file_kuis">Upload File Kuis (pdf):</label>
            <input type="file" name="file_kuis" accept=".pdf" required>

            <button type="submit">Simpan Kuis</button>
        </form>

        <a href="riwayat_kuis.php" class="back-button">Riwayat Kuis</a>
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
    const selectedStudentsDiv = document.getElementById('selectedStudents');
    const hiddenInputsDiv = document.getElementById('hiddenInputs');

    let selectedStudents = []; // Array { siswa_id, full_name }

    function renderSelectedStudents() {
        selectedStudentsDiv.innerHTML = '';
        hiddenInputsDiv.innerHTML = '';

        selectedStudents.forEach(student => {
            const studentElem = document.createElement('span');
            studentElem.style.cssText =
                'display:inline-block; background:#e6c200; color:#145375; padding:5px 10px; margin:3px; border-radius:5px; font-weight:bold; cursor:default;';
            studentElem.textContent = student.full_name;

            const removeBtn = document.createElement('button');
            removeBtn.textContent = '×';
            removeBtn.style.cssText =
                'margin-left:8px; border:none; background:none; font-weight:bold; cursor:pointer; color:#145375;';
            removeBtn.title = 'Hapus';
            removeBtn.addEventListener('click', () => {
                selectedStudents = selectedStudents.filter(s => s.siswa_id !== student.siswa_id);
                renderSelectedStudents();
            });

            studentElem.appendChild(removeBtn);
            selectedStudentsDiv.appendChild(studentElem);

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'siswa_id[]'; // array supaya multiple terkirimm
            hiddenInput.value = student.siswa_id;
            hiddenInputsDiv.appendChild(hiddenInput);
        });
    }

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        autocompleteList.innerHTML = '';

        if (!query) return;

        siswaList.forEach(siswa => {
            if (
                siswa.full_name.toLowerCase().includes(query) &&
                !selectedStudents.find(s => s.siswa_id === siswa.siswa_id) // cegah duplikat
            ) {
                const suggestionItem = document.createElement('div');
                suggestionItem.classList.add('autocomplete-suggestion');
                suggestionItem.textContent = siswa.full_name;
                suggestionItem.dataset.id = siswa.siswa_id;

                suggestionItem.addEventListener('click', function() {
                    selectedStudents.push({
                        siswa_id: siswa.siswa_id,
                        full_name: siswa.full_name
                    });
                    renderSelectedStudents();
                    searchInput.value = '';
                    autocompleteList.innerHTML = '';
                    searchInput.focus();
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