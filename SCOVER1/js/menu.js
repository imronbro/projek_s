function toggleMenu() {
  const navLinks = document.querySelector(".nav-links");
  const menuIcon = document.querySelector(".menu-icon");
  navLinks.classList.toggle("active");
  menuIcon.classList.toggle("active");
}

function confirmLogout() {
  const notification = document.getElementById("logout-notification");
  notification.classList.remove("hide"); // Hapus kelas "hide" jika ada
  notification.style.display = "block"; // Tampilkan notifikasi
}

function cancelLogout() {
  const notification = document.getElementById("logout-notification");
  notification.classList.add("hide"); // Tambahkan kelas "hide" untuk animasi keluar
  setTimeout(() => {
    notification.style.display = "none"; // Sembunyikan notifikasi setelah animasi selesai
  }, 300); // Waktu harus sesuai dengan durasi animasi slideOut (0.3s)
}
