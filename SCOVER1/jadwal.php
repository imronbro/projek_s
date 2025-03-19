<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="css/jadwal.css" />
    <link rel="stylesheet" href="css/logout.css" />
  </head>
  <body>
    <nav class="navbar">
      <div class="logo">
        <img src="images/foto4.png" alt="Logo" />
      </div>
      <h1 class="title">Dashboard Siswa</h1>
      <ul class="nav-links">
        <li><a href="home.php">Presensi</a></li>
        <li><a href="pengajar.php">Pengajar</a></li>
        <li><a href="jadwal.php" class="active">Jadwal</a></li>
        <li><a href="nilai_siswa.php">Nilai</a></li>
        <li><a href="profile.php">Profil</a></li>
        <li><a href="kontak.php">Kontak</a></li>
        <li>
          <button class="logout-btn" onclick="confirmLogout()">Keluar</button>
        </li>
      </ul>
      <div class="menu-icon" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </nav>

    <div class="container">
      <h2>Pilih Kelas</h2>
      <select id="classSelector" onchange="updateClassSections()">
        <option value="">-- Pilih Kelas --</option>
        <option value="10">Kelas 10</option>
        <option value="11">Kelas 11</option>
        <option value="12">Kelas 12</option>
      </select>

      <h2>Pilih Sub Kelas</h2>
      <select id="sectionSelector" onchange="updateDays()" disabled>
        <option value="">-- Pilih Sub Kelas --</option>
      </select>

      <h2>Pilih Hari</h2>
      <select id="daySelector" onchange="showSchedule()" disabled>
        <option value="">-- Pilih Hari --</option>
        <option value="monday">Senin</option>
        <option value="tuesday">Selasa</option>
        <option value="wednesday">Rabu</option>
        <option value="thursday">Kamis</option>
        <option value="friday">Jumat</option>
        <option value="saturday">Sabtu</option>
      </select>

      <div id="scheduleContainer" class="schedule"></div>
    </div>

    <script>
      const schedules = {
        10: {
          Aspira: {
            monday: [{ time: "08:00 - 10:00", subject: "Matematika" }],
            tuesday: [{ time: "08:00 - 09:30", subject: "Fisika" }],
            wednesday: [{ time: "08:00 - 09:30", subject: "Kimia" }],
            thursday: [{ time: "08:00 - 09:30", subject: "Biologi" }],
            friday: [{ time: "08:00 - 09:30", subject: "Bahasa Indonesia" }],
            saturday: [{ time: "08:00 - 09:30", subject: "Bahasa Inggris" }],
          },
          Elevate: {
            monday: [{ time: "08:00 - 10:00", subject: "Sastra" }],
            tuesday: [{ time: "08:00 - 09:30", subject: "Ekonomi" }],
            wednesday: [{ time: "08:00 - 09:30", subject: "Kimia" }],
            thursday: [{ time: "08:00 - 09:30", subject: "Biologi" }],
            friday: [{ time: "08:00 - 09:30", subject: "Bahasa Indonesia" }],
            saturday: [{ time: "08:00 - 09:30", subject: "Bahasa Inggris" }],
          },
        },
        11: {
          Aspira: {
            monday: [{ time: "08:00 - 10:00", subject: "Kimia" }],
            tuesday: [{ time: "08:00 - 09:30", subject: "Biologi" }],
            wednesday: [{ time: "08:00 - 09:30", subject: "Kimia" }],
            thursday: [{ time: "08:00 - 09:30", subject: "Biologi" }],
            friday: [{ time: "08:00 - 09:30", subject: "Bahasa Indonesia" }],
            saturday: [{ time: "08:00 - 09:30", subject: "Bahasa Inggris" }],
          },
          Elevate: {
            monday: [{ time: "08:00 - 10:00", subject: "Kimia" }],
            tuesday: [{ time: "08:00 - 09:30", subject: "Biologi" }],
            wednesday: [{ time: "08:00 - 09:30", subject: "Kimia" }],
            thursday: [{ time: "08:00 - 09:30", subject: "Biologi" }],
            friday: [{ time: "08:00 - 09:30", subject: "Bahasa Indonesia" }],
            saturday: [{ time: "08:00 - 09:30", subject: "Bahasa Inggris" }],
          },
        },
        12: {
          Aspira: {
            monday: [{ time: "08:00 - 10:00", subject: "Sejarah" }],
            tuesday: [{ time: "08:00 - 09:30", subject: "Geografi" }],
            wednesday: [{ time: "08:00 - 09:30", subject: "Kimia" }],
            thursday: [{ time: "08:00 - 09:30", subject: "Biologi" }],
            friday: [{ time: "08:00 - 09:30", subject: "Bahasa Indonesia" }],
            saturday: [{ time: "08:00 - 09:30", subject: "Bahasa Inggris" }],
          },
          Elevate: {
            monday: [{ time: "08:00 - 10:00", subject: "Sejarah" }],
            tuesday: [{ time: "08:00 - 09:30", subject: "Geografi" }],
            wednesday: [{ time: "08:00 - 09:30", subject: "Kimia" }],
            thursday: [{ time: "08:00 - 09:30", subject: "Biologi" }],
            friday: [{ time: "08:00 - 09:30", subject: "Bahasa Indonesia" }],
            saturday: [{ time: "08:00 - 09:30", subject: "Bahasa Inggris" }],
          },
        },
      };

      function updateClassSections() {
        const classValue = document.getElementById("classSelector").value;
        const sectionSelector = document.getElementById("sectionSelector");
        sectionSelector.innerHTML =
          '<option value="">-- Pilih Sub Kelas --</option>';
        sectionSelector.disabled = true;

        if (classValue && schedules[classValue]) {
          Object.keys(schedules[classValue]).forEach((section) => {
            const option = document.createElement("option");
            option.value = section;
            option.textContent = `Kelas ${classValue} ${section}`;
            sectionSelector.appendChild(option);
          });
          sectionSelector.disabled = false;
        }
        updateDays();
      }

      function updateDays() {
        const sectionValue = document.getElementById("sectionSelector").value;
        const daySelector = document.getElementById("daySelector");
        daySelector.disabled = !sectionValue;
      }

      function showSchedule() {
        const classValue = document.getElementById("classSelector").value;
        const sectionValue = document.getElementById("sectionSelector").value;
        const day = document.getElementById("daySelector").value;
        const container = document.getElementById("scheduleContainer");
        container.innerHTML = "";

        if (
          classValue &&
          sectionValue &&
          day &&
          schedules[classValue] &&
          schedules[classValue][sectionValue] &&
          schedules[classValue][sectionValue][day]
        ) {
          schedules[classValue][sectionValue][day].forEach((item, index) => {
            setTimeout(() => {
              const card = document.createElement("div");
              card.classList.add("card");
              card.innerHTML = `<strong>${item.time}</strong><br>${item.subject}`;
              container.appendChild(card);
              setTimeout(() => card.classList.add("fade-in"), 10);
            }, index * 150);
          });
          container.classList.add("show");
        } else {
          container.classList.remove("show");
        }
      }
    </script>
    <script src="js/logout.js" defer></script>
    <script src="js/home.js" defer></script>
    <script src="js/menu.js" defer></script>
  </body>
</html>
