function confirmLogout() {
  if (confirm("Apakah Anda yakin ingin keluar?")) {
    window.location.href = "logout.php";
  }
}
