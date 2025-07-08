import '../css/app.css';
import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

document.addEventListener('alpine:init', () => {
    Alpine.data('dropdown', () => ({
        open: false,
        toggle() {
            this.open = !this.open;
        }
    }));
});

window.helpers = {
    confirmAction: (message, callback) => {
        if (confirm(message)) callback();
    }
};

// Simple form validation
document.addEventListener('DOMContentLoaded', function() {
    // Setup CSRF token for jQuery AJAX if jQuery is available
    if (typeof $ !== 'undefined') {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (csrfToken) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            console.log('jQuery CSRF token setup completed');
        } else {
            console.warn('CSRF token meta tag not found for jQuery');
        }
    }
    
    // Basic form interaction
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                setTimeout(() => {
                    submitBtn.disabled = false;
                }, 2000);
            }
        });
    });
});
