/* PT Lovina North Bali Real Estate Agency - Admin Scripts */

document.addEventListener('DOMContentLoaded', function () {
  // Password Visibility Toggle
  const togglePasswordBtn = document.getElementById('togglePasswordBtn');
  const passwordInput = document.getElementById('password');

  if (togglePasswordBtn && passwordInput) {
    togglePasswordBtn.addEventListener('click', function () {
      const isPassword = passwordInput.getAttribute('type') === 'password';
      passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
      const iconName = isPassword ? 'eye' : 'eye-off';
      togglePasswordBtn.innerHTML = `<i data-lucide="${iconName}" style="width: 18px; height: 18px;"></i>`;
      if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons({
          root: togglePasswordBtn
        });
      }
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
});
