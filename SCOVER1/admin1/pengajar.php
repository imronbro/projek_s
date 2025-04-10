<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: loginadmin.php");
    exit();
}
$user_email = $_SESSION['user_email'];
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = "SELECT pengajar_id, full_name, gambar, mapel, nohp FROM mentor";
if (!empty($search)) {
    $query .= " WHERE full_name LIKE '%$search%'";
}

$result = mysqli_query($conn, $query);


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pengajar.css">
    <style>
        * {
           
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 100px;
            /* Memberikan ruang di bawah navbar */
            padding: 20px;
            text-align: center;
        }

        h2 {
            text-align: center;
            color: #145375;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input,
        select {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 10px 15px;
            background-color: #145375;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #145375;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #145375;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .btn-detail {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: rgb(13, 78, 135);
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-detail:hover {
            background-color: rgb(2, 65, 131);
        }

        .btn-group-vertical {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }
                    /* Form pencarian seperti filter-bar */
.search-form {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.search-form input[type="text"] {
    padding: 10px 15px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    width: 200px;
}

/* Tombol cari */
.search-form button {
    background-color: #f1c40f; /* kuning */
    color: #0b3c5d; /* biru tua */
    border: none;
    border-radius: 8px;
    padding: 10px 85px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.search-form button:hover {
    background-color: #d4ac0d;
}


        button {
            background-color: #e6c200;
            color: #145375;
            padding: 10px 100px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
        }

        button:hover {
            background-color: #145375;
            color: #fff;
        }

        /* Dropdown styles */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #145375;
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
        
        .star {
    font-size: 20px;
    display: inline-block;
    position: relative;
    color: #ccc;
}

.star.full {
    color: gold;
}

.star.partial {
    background: linear-gradient(90deg, gold var(--fill), #ccc var(--fill));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline-block;
}

.star.empty {
    color: #ccc;
}

    </style>
</head>
<script>
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
    document.addEventListener('click', function(event) {
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
            <img src="images/foto4.png" alt="Logo">
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

            <li><a href="pengajar.php" class="active">Pengajar</a></li>
            <li><a href="jadwal.php">Jadwal</a></li>
            <li><a href="profil.php">Siswa</a></li>
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
    <div class="container mt-5">
        <h2 class="text-center">Daftar Pengajar</h2>
        <!-- Form Pencarian -->
        <form action="pengajar.php" method="get" class="search-form">
            <input type="text" name="search" placeholder="Cari Nama Pengajar..." class="search-input">
            <button type="submit" class="button">Cari</button>
        </form>
            <div class="row mt-4">
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) {
    $imagePath = "../uploads/" . basename(htmlspecialchars($row['gambar']));
    $defaultImage = "../uploads1/default.png";
    $finalImage = (!empty($row['gambar']) && file_exists($imagePath)) ? $imagePath : $defaultImage;

    // Ambil rata-rata rating dari tabel rating_pengajar
    $pengajarId = $row['pengajar_id'];
    $sqlRating = "SELECT AVG(rating) AS rata_rating, COUNT(*) AS jumlah_rating FROM rating_pengajar WHERE pengajar_id = $pengajarId";
    $resultRating = mysqli_query($conn, $sqlRating);
    $rataRating = 0;
    $jumlahRating = 0;

    if ($resultRating && $dataRating = mysqli_fetch_assoc($resultRating)) {
        $rataRating = round($dataRating['rata_rating'], 1);
        $jumlahRating = $dataRating['jumlah_rating'];
    }
?>

                        <div class="col-md-4 mb-4">
                            <div class="card text-center p-3">
                                <img src="<?= $finalImage; ?>" alt="Foto Pengajar" class="profile-img mb-3">

                                <h4><?= htmlspecialchars($row['full_name']); ?></h4>
                                <p><strong>TUTOR <?= htmlspecialchars($row['mapel']); ?></strong></p>
<!-- Menampilkan rata-rata rating -->
<?php if ($jumlahRating > 0): ?>
    <div class="mb-2">
        <?php
            $average_rating = $rataRating;
            $fullStars = floor($average_rating); 
            $decimal = $average_rating - $fullStars;
            $emptyStars = 5 - ceil($average_rating); 

            // Bintang penuh
            for ($i = 0; $i < $fullStars; $i++) {
                echo '<span class="star full">★</span>';
            }

            // Bintang sebagian
            if ($decimal > 0) {
                $percentage = $decimal * 100;
                echo '<span class="star partial" style="--fill:' . $percentage . '%;">★</span>';
            }

            // Bintang kosong
            for ($i = 0; $i < $emptyStars; $i++) {
                echo '<span class="star empty">★</span>';
            }
        ?>
        <br>
        <span class="rating-text"><?= number_format($average_rating, 1); ?> / 5 (<?= $jumlahRating; ?> ulasan)</span>
    </div>
<?php else: ?>
    <div class="mb-2">
        <span class="rating-text">Belum ada ulasan</span>
    </div>
<?php endif; ?>


                                <div class="btn-group-vertical">
                                    <a href="detail_pengajar.php?id=<?= $row['pengajar_id']; ?>" class="btn btn-detail">Lihat Detail</a>
                                    <a href="https://wa.me/<?= htmlspecialchars($row['nohp']); ?>" target="_blank" class="btn btn-whatsapp">Hubungi via WhatsApp</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="col-12 text-center">
                        <p class="text-danger">Pengajar tidak ditemukan.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
</body>

</html>