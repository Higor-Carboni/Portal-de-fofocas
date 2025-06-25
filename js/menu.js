document.addEventListener('DOMContentLoaded', function() {
  const menuIcon = document.getElementById('menuIcon');
  const headerLinks = document.getElementById('headerLinks');
  menuIcon.addEventListener('click', function() {
    headerLinks.classList.toggle('ativo');
  });
});