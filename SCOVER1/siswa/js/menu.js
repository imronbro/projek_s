function toggleMenu() {
  const navLinks = document.querySelector(".nav-links");
  const menuIcon = document.querySelector(".menu-icon");
  const notification = document.getElementById("logout-notification");

  navLinks.classList.toggle("active");
  menuIcon.classList.toggle("active");

  // Pastikan notifikasi berada di atas navbar saat menu aktif
  if (navLinks.classList.contains("active")) {
    notification.style.zIndex = "2000"; // Z-index lebih tinggi dari navbar
  } else {
    notification.style.zIndex = "1000"; // Kembalikan z-index default
  }
}

function confirmLogout() {
  const notification = document.getElementById("logout-notification");
  notification.style.display = "block"; // Tampilkan notifikasi
}

function cancelLogout() {
  const notification = document.getElementById("logout-notification");
  notification.style.display = "none"; // Sembunyikan notifikasi
}
