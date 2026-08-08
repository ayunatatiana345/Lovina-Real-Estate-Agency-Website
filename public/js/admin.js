/* PT Lovina North Bali Real Estate Agency - Admin Scripts */

document.addEventListener('DOMContentLoaded', function () {
  // Password Visibility Toggle
  const togglePasswordBtn = document.getElementById('togglePasswordBtn');
  const passwordInput = document.getElementById('password');

  if (togglePasswordBtn && passwordInput) {
    togglePasswordBtn.addEventListener('click', function () {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
    });
  }

  // Add Location Slide-in Panel Toggle
  const openLocationPanelBtn = document.getElementById('openAddLocationBtn');
  const closeLocationPanelBtn = document.getElementById('closeLocationPanelBtn');
  const cancelLocationBtn = document.getElementById('cancelLocationBtn');
  const locationSlidePanel = document.getElementById('locationSlidePanel');

  function openLocationPanel() {
    if (locationSlidePanel) {
      locationSlidePanel.classList.add('open');
    }
  }

  function closeLocationPanel() {
    if (locationSlidePanel) {
      locationSlidePanel.classList.remove('open');
    }
  }

  if (openLocationPanelBtn) {
    openLocationPanelBtn.addEventListener('click', openLocationPanel);
  }

  if (closeLocationPanelBtn) {
    closeLocationPanelBtn.addEventListener('click', closeLocationPanel);
  }

  if (cancelLocationBtn) {
    cancelLocationBtn.addEventListener('click', closeLocationPanel);
  }

  // Location Description Character Counter
  const locationDescInput = document.getElementById('locationDescInput');
  const charCounter = document.getElementById('charCounter');

  if (locationDescInput && charCounter) {
    locationDescInput.addEventListener('input', function () {
      const len = locationDescInput.value.length;
      charCounter.textContent = `${len} / 500`;
    });
  }

  // Logout Modal Confirmation Toggle
  const logoutBtn = document.getElementById('sidebarLogoutBtn');
  const logoutModal = document.getElementById('logoutModal');
  const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');

  if (logoutBtn && logoutModal) {
    logoutBtn.addEventListener('click', function (e) {
      e.preventDefault();
      logoutModal.style.display = 'flex';
    });
  }

  if (cancelLogoutBtn && logoutModal) {
    cancelLogoutBtn.addEventListener('click', function () {
      logoutModal.style.display = 'none';
    });
  }
});
