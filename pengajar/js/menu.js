function toggleMenu() {
  const navLinks = document.querySelector(".nav-links");
  const menuIcon = document.querySelector(".menu-icon");
  const notification = document.getElementById("logout-notification");

  navLinks.classList.toggle("active");
  menuIcon.classList.toggle("active");

  // Pastikan notifikasi berada di atas navbar saat menu aktif
  if (navLinks.classList.contains("active")) {
    notification.style.zIndex = "2000";
    // Tambahkan event listener untuk klik di luar navLinks
    document.addEventListener("click", closeMenuOnClickOutside);
  } else {
    notification.style.zIndex = "1000";
    document.removeEventListener("click", closeMenuOnClickOutside);
  }
}

function closeMenuOnClickOutside(e) {
  const navLinks = document.querySelector(".nav-links");
  const menuIcon = document.querySelector(".menu-icon");
  // Jika klik di luar navLinks dan menuIcon, tutup menu
  if (
    !navLinks.contains(e.target) &&
    !menuIcon.contains(e.target)
  ) {
    navLinks.classList.remove("active");
    menuIcon.classList.remove("active");
    document.removeEventListener("click", closeMenuOnClickOutside);
    // Reset z-index jika perlu
    const notification = document.getElementById("logout-notification");
    notification.style.zIndex = "1000";
  }
}

function confirmLogout() {
  const notification = document.getElementById("logout-notification");
  notification.style.display = "block";
}

function cancelLogout() {
  const notification = document.getElementById("logout-notification");
  notification.style.display = "none";
}