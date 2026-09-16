/* PT Lovina North Bali Real Estate Agency - Public Frontend Scripts */

document.addEventListener('DOMContentLoaded', function () {
  // Contact & Inquiry Form AJAX Handling
  const inquiryForm = document.getElementById('inquiryForm');
  const successModal = document.getElementById('successModal');
  const closeModalBtn = document.getElementById('closeSuccessModalBtn');
  const backHomeBtn = document.getElementById('backHomeBtn');

  if (inquiryForm) {
    inquiryForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(inquiryForm);
      const submitBtn = inquiryForm.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;

      fetch(inquiryForm.action, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: formData,
      })
      .then(async response => {
        if (submitBtn) submitBtn.disabled = false;
        const data = await response.json().catch(() => null);

        if (response.ok && data && data.success) {
          inquiryForm.reset();
          if (successModal) {
            successModal.style.display = 'flex';
          }
        } else if (response.status === 422 && data && data.errors) {
          const firstError = Object.values(data.errors)[0][0] || 'Please verify your input fields.';
          alert(firstError);
        } else {
          const msg = (data && data.message) ? data.message : 'An error occurred. Please try again.';
          alert(msg);
        }
      })
      .catch(error => {
        if (submitBtn) submitBtn.disabled = false;
        console.error('Inquiry error:', error);
        alert('Unable to submit inquiry. Please check your connection and try again.');
      });
    });
  }

  if (closeModalBtn && successModal) {
    closeModalBtn.addEventListener('click', function () {
      successModal.style.display = 'none';
    });
  }

  if (successModal) {
    successModal.addEventListener('click', function (e) {
      if (e.target === successModal) {
        successModal.style.display = 'none';
      }
    });
  }

  if (backHomeBtn && successModal) {
    backHomeBtn.addEventListener('click', function () {
      window.location.href = '/';
    });
  }
});
