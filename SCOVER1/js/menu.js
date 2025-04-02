function toggleMenu() {
  const navLinks = document.querySelector(".nav-links");
  const menuIcon = document.querySelector(".menu-icon");
  navLinks.classList.toggle("active");
  menuIcon.classList.toggle("active");
}

function confirmLogout() {
  if (confirm("Apakah Anda yakin ingin keluar?")) {
    window.location.href = "logout.php";
  }
}
