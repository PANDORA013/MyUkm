import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

// Set default headers for all AJAX requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Add a request interceptor to include CSRF token in all requests
window.axios.interceptors.request.use(
    config => {
        // Add CSRF token to all requests except GET, HEAD, OPTIONS
        if (!['get', 'head', 'options'].includes(config.method.toLowerCase())) {
            const token = document.head.querySelector('meta[name="csrf-token"]');
            if (token) {
                config.headers['X-CSRF-TOKEN'] = token.content;
            }
        }
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

// Pusher will be initialized in app.js to avoid duplicate initialization
