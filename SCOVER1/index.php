<html>
  <head>
    <title>Scover</title>
    <link rel="stylesheet" href="css/style1.css" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    />


<style>
<style>
  .menu-container {
    position: relative;
    display: inline-block;
  }

  .hamburger-menu {
    font-size: 24px;
    cursor: pointer;
    position: absolute;
    top: 10px;
    right: 10px;
    background: #007bff;
    color: white;
    padding: 10px;
    border-radius: 5px;
  }

  .login-options {
    display: none;
    position: absolute;
    top: 10px;
    right: 50px; /* Menggeser menu ke kiri */
    background-color: #fff;
    border: 1px solid #ccc;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 10px;
    border-radius: 8px;
    z-index: 1000;
    min-width: 150px;
  }

  .login-options a {
    display: block;
    text-decoration: none;
    color:rgb(255, 255, 255);
    padding: 5px 10px;
    border-radius: 4px;
    transition: background-color 0.3s;
  }

  .login-options a:hover {
    background-color: #007bff;
    color: #fff;
  }
</style>
</style>

</head>

  <body>
  <div class="hero">
  <nav>
    <img src="images/foto4.png" class="logo" />
    <div class="menu-container">
      <div class="hamburger-menu" onclick="toggleMenu()">
        &#9776;
      </div>
      <div class="login-options" id="loginOptions">
        <!-- Opsi Daftar -->
        <a href="register.php" class="login-btn">Daftar Siswa</a>
        <a href="register_mentor.php" class="login-btn">Daftar Mentor</a>
        
        <!-- Opsi Login -->
        <a href="login.php" class="register-btn">Login Siswa</a>
        <a href="login_mentor.php" class="register-btn">Login Mentor</a>
        <a href="admin1/loginadmin.php" class="register-btn">Login Admin</a>
      </div>
    </div>
  </nav>
</div>

    <section class="home-header">
      <div class="container">
        <div class="row justify-content-center">
          <div>
            <div class="promo-text-box">
              <h1>Scover - Study and Discover</h1>
              <p>
                Temukan potensi terbaikmu, belajar jadi lebih seru! Scover hadir
                untuk membantumu belajar dengan cara yang lebih mudah, efektif,
                dan menyenangkan! Kami percaya bahwa setiap siswa memiliki
                potensi luar biasa yang hanya perlu ditemukan dan dikembangkan.
              </p>
            </div>
            <img src="images/herofix.png" class="features-img anim" />
          </div>
        </div>
      </div>
    </section>
    <script>
  function toggleMenu() {
    var menu = document.getElementById("loginOptions");
    if (menu.style.display === "block") {
      menu.style.display = "none";
    } else {
      menu.style.display = "block";
    }
  }
</script>
  </body>
</html>
