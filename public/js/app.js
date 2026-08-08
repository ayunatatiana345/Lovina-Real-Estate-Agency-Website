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
      .then(response => response.json())
      .then(data => {
        if (submitBtn) submitBtn.disabled = false;
        if (data.success) {
          inquiryForm.reset();
          if (successModal) {
            successModal.style.display = 'flex';
          }
        } else {
          alert('Submission error. Please check your form input.');
        }
      })
      .catch(error => {
        if (submitBtn) submitBtn.disabled = false;
        console.error('Inquiry error:', error);
        alert('An error occurred. Please try again.');
      });
    });
  }

  if (closeModalBtn && successModal) {
    closeModalBtn.addEventListener('click', function () {
      successModal.style.display = 'none';
    });
  }

  if (backHomeBtn && successModal) {
    backHomeBtn.addEventListener('click', function () {
      window.location.href = '/';
    });
  }
});
