/**
 * Pelanggaran Page JavaScript
 * Real-time notification via shared notifications.js module
 */

import './bootstrap';
import { initNotifications } from './notifications';

function initSidebarToggle() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (!menuToggle || !sidebar || !overlay) return;

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSidebarToggle();
    initNotifications(); // real-time notification (polling)
    initSearch(); // add search functionality
});

function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const tableRows = document.querySelectorAll('.recent-table tbody tr');

        tableRows.forEach(row => {
            // Skip the "no data" row if it exists
            if (row.querySelector('td[colspan]')) return; 

            // Search across all text in the row
            const textContent = row.textContent.toLowerCase();
            if (textContent.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}
