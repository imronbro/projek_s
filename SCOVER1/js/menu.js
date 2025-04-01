function toggleMenu() {
  const menuIcon = document.querySelector(".menu-icon");
  const navLinks = document.querySelector(".nav-links");

  menuIcon.classList.toggle("active");
  navLinks.classList.toggle("active");
}
