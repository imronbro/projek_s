<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Learnings Platform</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <!-- Link CSS -->
    <link rel="stylesheet" href="css/indexhome.css">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body{
            font-family: 'Poppins';
            
        }
        .hero-image img {
            opacity: 0;
            transform: scale(0.8);
            animation: zoomIn 1s ease-out 0.8s forwards;
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="left-side">
                <div class="logo">
                    <img src="images/register.png" alt="Learnings Logo" width="40"> Scover Learning Center
                </div>
                <div class="social-media">
                    <a href="https://www.instagram.com/scover.idn/"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.youtube.com/@scover.indonesia"><i class="bi bi-youtube"></i></a>
                    <a href="https://api.whatsapp.com/send?phone=6289697053591"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            <!-- Login & Register -->
            <div class="login-register">
                <!-- Login Dropdown -->
                <div class="dropdown">
                    <button class="btn login-btn dropdown-toggle" type="button" id="loginDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Login
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="loginDropdown">
                        <li><a class="dropdown-item" href="siswa/login">Login Siswa</a></li>
                        <li><a class="dropdown-item" href="pengajar/login_mentor">Login Pengajar</a></li>
                    </ul>
                </div>

                <!-- Register Dropdown -->
                <div class="dropdown">
                    <button class="btn register-btn dropdown-toggle" type="button" id="registerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Register
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="registerDropdown">
                        <li><a class="dropdown-item" href="siswa/register">Register Siswa</a></li>
                        <li><a class="dropdown-item" href="pengajar/register_mentor">Register Pengajar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Scover: Unlock Your <span style="color: #faaf1d;"> Potential </span>, One Lesson at a Time!</h1>
                <p>Temukan potensi terbaikmu, belajar jadi lebih seru! Scover hadir untuk membantumu belajar dengan cara yang lebih mudah, efektif, dan menyenangkan! Kami percaya bahwa setiap siswa memiliki potensi luar biasa yang hanya perlu ditemukan dan dikembangkan.</p>

            </div>
            <div class="hero-image">
                <img src="images/herofix.png" alt="Belajar Online">
            </div>

    </section>
    <!-- Footer Section -->
    <!-- Footer Section -->
    <footer class="footer">
        <div>
            <p>&copy; 2025 Scover Learning Center Indonesia. All Rights Reserved.</p>
        </div>
    </footer>


</body>

</html>