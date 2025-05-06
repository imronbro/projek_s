document.addEventListener("DOMContentLoaded", function () {
  let today = new Date().toISOString().split("T")[0];
  document.getElementById("tanggal").value = today;
});

function toggleKomentar() {
  let kehadiran = document.getElementById("kehadiran").value;
  let komentarContainer = document.getElementById("komentar-container");

  if (kehadiran === "Izin" || kehadiran === "Sakit") {
    komentarContainer.style.display = "block";
  } else {
    komentarContainer.style.display = "none";
  }
}

const menuIcon = document.querySelector(".menu-icon");
const navLinks = document.querySelector(".nav-links");

menuIcon.addEventListener("click", () => {
  navLinks.classList.toggle("active");
  menuIcon.classList.toggle("active");
});

function confirmLogout() {
  if (confirm("Apakah Anda yakin ingin keluar?")) {
    window.location.href = "logout.php";
  }
  function toggleMenu() {
    const navLinks = document.querySelector(".nav-links");
    const menuIcon = document.querySelector(".menu-icon");

    navLinks.classList.toggle("active");
    menuIcon.classList.toggle("active");
  }
}
