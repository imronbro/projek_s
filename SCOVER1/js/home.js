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
