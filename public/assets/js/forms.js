// API Base URL
const API_BASE_URL = '/api/v1';

// Utility function to show toast notifications
function showToast(message, type = 'success') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
        max-width: 350px;
    `;
    toast.textContent = message;

    // Add animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    document.body.appendChild(toast);

    // Remove toast after 5 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 5000);
}

// Utility function to disable button during submission
function setButtonLoading(button, isLoading) {
    if (isLoading) {
        button.dataset.originalText = button.textContent;
        button.textContent = 'Submitting...';
        button.disabled = true;
        button.style.opacity = '0.6';
        button.style.cursor = 'not-allowed';
    } else {
        button.textContent = button.dataset.originalText || button.textContent;
        button.disabled = false;
        button.style.opacity = '1';
        button.style.cursor = 'pointer';
    }
}

// Utility function to show field errors
function showFieldErrors(form, errors) {
    // Clear previous errors
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Show new errors
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-danger mt-1';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.textContent = errors[fieldName][0];
            field.parentElement.appendChild(errorDiv);
        }
    });
}

// Waitlist Form Handler
/*function initWaitlistForm() {
    const waitlistForms = document.querySelectorAll('form[action=""]');

    waitlistForms.forEach(form => {
        // Check if this is a waitlist form (in modal)
        if (form.closest('.modal')) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const submitButton = form.querySelector('button[type="submit"]');
                setButtonLoading(submitButton, true);

                // Get form data
                const formData = new FormData(form);
                const data = {
                    name: formData.get('name') || form.querySelector('input[placeholder="Your Name"]')?.value,
                    company_name: formData.get('company_name') || form.querySelector('input[placeholder="Company Name"]')?.value,
                    phone: formData.get('phone') || form.querySelector('input[placeholder="Mobile no."]')?.value,
                    email: formData.get('email') || form.querySelector('input[placeholder="Email address"]')?.value,
                    message: formData.get('message') || form.querySelector('textarea[placeholder="Message"]')?.value,
                };

                try {
                    const response = await fetch(`${API_BASE_URL}/waitlist`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(data),
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        showToast(result.message, 'success');
                        form.reset();

                        // Close modal if exists
                        const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                        if (modal) {
                            modal.hide();
                        }
                    } else {
                        if (result.errors) {
                            showFieldErrors(form, result.errors);
                        }
                        showToast(result.message || 'An error occurred. Please try again.', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Network error. Please check your connection and try again.', 'error');
                } finally {
                    setButtonLoading(submitButton, false);
                }
            });
        }
    });
}*/

// Contact Form Handler
function initContactForm() {
    const contactSection = document.querySelector('#get-touch');
    if (!contactSection) return;

    const contactForm = contactSection.querySelector('form');
    if (!contactForm) return;

    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitButton = contactForm.querySelector('button[type="submit"]');
        setButtonLoading(submitButton, true);

        // Get form data
        const inputs = contactForm.querySelectorAll('input, textarea');
        const data = {};

        inputs.forEach(input => {
            const placeholder = input.placeholder.toLowerCase();
            if (placeholder.includes('name') && !placeholder.includes('company')) {
                data.name = input.value;
            } else if (placeholder.includes('email')) {
                data.email = input.value;
            } else if (placeholder.includes('company')) {
                data.company_name = input.value;
            } else if (placeholder.includes('mobile')) {
                data.phone = input.value;
            } else if (placeholder.includes('message')) {
                data.message = input.value;
            }
        });

        try {
            const response = await fetch(`${API_BASE_URL}/contact`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (response.ok && result.success) {
                showToast(result.message, 'success');
                contactForm.reset();

                // Scroll to top of section
                contactSection.scrollIntoView({ behavior: 'smooth' });
            } else {
                if (result.errors) {
                    showFieldErrors(contactForm, result.errors);
                }
                showToast(result.message || 'An error occurred. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Network error. Please check your connection and try again.', 'error');
        } finally {
            setButtonLoading(submitButton, false);
        }
    });
}

// Initialize forms when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    initWaitlistForm();
    initContactForm();
});


$(document).ready(function(){
    var reserveForm = $('#reserveForm');
    $('#reserveForm').on('submit', function(e) {
        $('.form-spinner').css('visibility', 'visible');
        // $('#reserveForm button').prop('disabled', true);
        e.preventDefault();
        $.ajax({
            //url: '/reserve-form-email',
            url: API_BASE_URL+'/waitlist',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                $('.form-spinner').css('visibility', 'hidden');
                showToast(response.message, 'success');
                reserveForm[0].reset();
                // $('#reserveForm button').prop('disabled', false);
            },
            error: function (xhr) {
                $('.form-spinner').css('visibility', 'hidden');
                if (xhr.responseJSON.errors) {
                    //console.log(xhr.responseText.errors);
                    $('.form-spinner').css('visibility', 'hidden');
                    $.each(xhr.responseJSON.errors, function(index, value) {
                        showToast(value, 'error');
                        
                    });
                    // $('#reserveForm button').prop('disabled', false);
                }
                else{
                    showToast(xhr.responseJSON.message, 'error');
                    // $('#reserveForm button').prop('disabled', false);
                }
            }
        });
    });

    var contactForm = $('#contactForm');
    $('#contactForm').on('submit', function(e) {
        $('.form-spinner').css('visibility', 'visible');
        // $('#contactForm button').prop('disabled', true);
        e.preventDefault();
        $.ajax({
            //url: '/contact-form-email',
            url:  API_BASE_URL+'/contact',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                showToast(response.message, 'success');
                $('.form-spinner').css('visibility', 'hidden');
                $('#contactForm')[0].reset();
                // $('#contactForm button').prop('disabled', false);
            },
            error: function (xhr) {
                if (xhr.responseJSON.errors) {
                    //console.log(xhr.responseText.errors);
                    $.each(xhr.responseJSON.errors, function(index, value) {
                        showToast(value, 'error');
                    });
                    $('.form-spinner').css('visibility', 'hidden');
                    // $('#contactForm button').prop('disabled', false);
                }
                else{
                    showToast(xhr.responseJSON.message, 'error');
                    // $('#reserveForm button').prop('disabled', false);
                }
            }
        });
    });

    $('#pricing .btn').click(function(){
        setTimeout(() => {
            var plan_id = $(this).attr('data-plan-id');
            $('#exampleModalToggle #plan_id').val(plan_id);
        }, 2000);
    });

    $('#exampleModalToggle').on('shown.bs.modal', function (e) {
        $('#exampleModalToggle #plan_id').val("");
    });
});