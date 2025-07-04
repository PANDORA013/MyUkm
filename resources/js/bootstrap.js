import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;

// Set default headers for all AJAX requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Get CSRF token from meta tag
let token = document.head.querySelector('meta[name="csrf-token"]');

// If not found immediately, wait for DOM to load
if (!token) {
    document.addEventListener('DOMContentLoaded', function() {
        token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        } else {
            console.error('CSRF token not found: Make sure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your HTML head');
        }
    });
} else {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Add a request interceptor to include CSRF token in all requests
window.axios.interceptors.request.use(
    config => {
        // Add CSRF token to all requests except GET, HEAD, OPTIONS
        if (!['get', 'head', 'options'].includes(config.method.toLowerCase())) {
            let token = document.head.querySelector('meta[name="csrf-token"]');
            if (token) {
                config.headers['X-CSRF-TOKEN'] = token.content;
            } else {
                console.warn('CSRF token not found for request:', config.method, config.url);
            }
        }
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

// Pusher will be initialized in app.js to avoid duplicate initialization
