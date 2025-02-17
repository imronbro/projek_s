<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "scover";

$conn = mysqli_connect($host, $user, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

// Cek apakah email ada di session
if ($email === null) {
    die("Anda belum login. Harap login terlebih dahulu.");
}

// Jika form disubmit, proses UPDATE data tambahan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sekolah = mysqli_real_escape_string($conn, $_POST['sekolah']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $ttl = mysqli_real_escape_string($conn, $_POST['ttl']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $nohp = mysqli_real_escape_string($conn, $_POST['nohp']);

    // Query untuk update data mahasiswa
    $query = "UPDATE siswa SET 
                sekolah = '$sekolah',
                kelas = '$kelas',
                ttl = '$ttl',
                alamat = '$alamat',
                nohp = '$nohp'
              WHERE email = '$email'";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Profil berhasil diperbarui!');
                window.location.href = 'profile.php';
              </script>";
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

// Ambil data user berdasarkan email untuk menampilkan form
$query = "SELECT * FROM siswa WHERE email = '$email'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Pastikan data ditemukan
if (!$data) {
    die("Data pengguna tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #2c3e50;
            color: #ecf0f1;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 50px auto;
            max-width: 600px;
            background-color: #34495e;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            color: #ecf0f1;
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        /* Form Styling */
        form {
            background-color: #3b4a5a;
            padding: 20px;
            border-radius: 8px;
            color: #ecf0f1;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .form-control {
            background-color: #2c3e50;
            color: #ecf0f1;
            border: 1px solid #34495e;
            border-radius: 5px;
            padding: 10px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        select.form-control {
            appearance: none;
            background: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ecf0f1"%3E%3Cpath d="M7 10l5 5 5-5H7z"/%3E%3C/svg%3E') no-repeat right 10px center;
            background-size: 1.5rem;
            padding-right: 2.5rem;
        }

        /* Button Styling */
        .btn {
            display: inline-block;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-success {
            background-color:rgba(85, 58, 133, 0.77);
        }

        .btn-success:hover {
            background-color:rgb(45, 149, 67);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color:rgb(55, 136, 206);
        }

        .btn-secondary:hover {
            background-color: #545b62;
            transform: translateY(-2px);
        }

        button:focus, .btn:focus {
            outline: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) 
            .container {
                margin: 20px;
                padding: 15px;
            }

            .form-control {
                font-size: 1rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Profil</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="sekolah" class="form-label">sekolah</label>
                <input type="text" id="sekolah" name="sekolah" value="<?= htmlspecialchars($data['sekolah']); ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="kelas" class="form-label">kelas</label>
                <input type="text" id="kelas" name="kelas" value="<?= htmlspecialchars($data['kelas']); ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="ttl" class="form-label">Tanggal Lahir</label>
                <input type="date" id="ttl" name="ttl" value="<?= htmlspecialchars($data['ttl']); ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat Rumah</label>
                <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($data['alamat']); ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nohp" class="form-label">No. HP</label>
                <input type="text" id="nohp" name="nohp" value="<?= htmlspecialchars($data['nohp']); ?>" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="profile.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
