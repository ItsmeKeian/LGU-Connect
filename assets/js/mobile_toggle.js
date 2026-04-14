document.addEventListener('DOMContentLoaded', function () {

  const sidebar    = document.getElementById('sidebar');
  const menuToggle = document.getElementById('menuToggle');

  if (!sidebar || !menuToggle) {
    console.error('Sidebar or menuToggle not found!');
    return;
  }

  const overlay = document.createElement('div');
  overlay.id = 'sidebarOverlay';
  document.body.appendChild(overlay);

  function openSidebar() {
    sidebar.classList.add('sb-open');
    overlay.classList.add('active');
  }

  function closeSidebar() {
    sidebar.classList.remove('sb-open');
    overlay.classList.remove('active');
  }

  menuToggle.addEventListener('click', function () {
    sidebar.classList.contains('sb-open')
      ? closeSidebar()
      : openSidebar();
  });

  overlay.addEventListener('click', closeSidebar);

});