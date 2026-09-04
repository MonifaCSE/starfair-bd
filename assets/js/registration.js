// ==========================================
// STUDENT REGISTRATION CONTROLLER (PHP + MySQL Backend)
// ==========================================

document.addEventListener("DOMContentLoaded", () => {
    // URL Query Parameter Pre-selection for Courses (e.g. registration.html?course=modeling)
    const urlParams = new URLSearchParams(window.location.search);
    const selectedCourseParam = urlParams.get('course') || urlParams.get('slug') || urlParams.get('program');

    if (selectedCourseParam) {
        const paramLower = decodeURIComponent(selectedCourseParam).toLowerCase();
        const checkboxes = document.querySelectorAll('input[name="programmes[]"]');
        
        checkboxes.forEach(cb => {
            const valLower = cb.value.toLowerCase();
            const idLower = cb.id.toLowerCase();
            if (paramLower.includes(valLower) || valLower.includes(paramLower) || paramLower.includes(idLower)) {
                cb.checked = true;
                cb.closest('.form-check')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    const form = document.getElementById('admissionForm');
    const declaration = document.getElementById('declaration');
    const declFeedback = document.getElementById('declFeedback');
    
    if (!form) return;

    // Use existing Bootstrap Modal instance from registration.html
    const successModalEl = document.getElementById('successModal');
    let successModal = null;
    if (successModalEl && typeof bootstrap !== 'undefined') {
        successModal = new bootstrap.Modal(successModalEl);
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        
        let isValid = form.checkValidity();
        
        // Check declaration manually
        if (!declaration.checked) {
            declFeedback.style.display = 'block';
            isValid = false;
        } else {
            declFeedback.style.display = 'none';
        }

        if (!isValid) {
            event.stopPropagation();
            form.classList.add('was-validated');
            
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        form.classList.remove('was-validated');

        // Get the submit button and change to loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Uploading Documents & Submitting...`;

        try {
            // Build FormData containing all form inputs (text, checkboxes, files)
            const formData = new FormData(form);

            // POST to PHP Backend API
            const response = await fetch('backend/registration/submit.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Display success modal and reset form
                if (successModal) {
                    const noteEl = successModalEl.querySelector('.modal-body div');
                    if (noteEl) {
                        noteEl.textContent = result.message || "Application processed and saved successfully.";
                        noteEl.style.color = "#00e676";
                        noteEl.style.borderColor = "#00e676";
                        noteEl.style.background = "rgba(0, 230, 118, 0.1)";
                    }
                    successModal.show();
                } else {
                    alert(result.message || "Application submitted successfully!");
                }

                form.reset();
            } else {
                alert(result.message || "Failed to submit application.");
            }

        } catch (error) {
            console.error("STAR FAIR: Error submitting application:", error);
            alert("Failed to submit application: Server connection failed.");
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    }, false);

    // Watch checkbox changes
    declaration.addEventListener('change', function() {
        if (declaration.checked) {
            declFeedback.style.display = 'none';
        } else {
            declFeedback.style.display = 'block';
        }
    });
});
