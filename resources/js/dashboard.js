/**
 * Dashboard JavaScript
 * Sistem Informasi Pelanggaran Siswa SMP Negeri 7 Jember
 */

import './bootstrap';
import { initNotifications } from './notifications';

// Status labels removed

let chartData = { by_jenis: [], by_kategori: { ringan: 0, sedang: 0, berat: 0 } };
let mainChart = null;
let pieChart = null;

function animateNumber(id, target) {
    const el = document.getElementById(id);
    if (!el) return;

    let current = 0;
    const increment = Math.max(1, Math.ceil(target / 30));
    const stepTime = 800 / 30;

    const update = () => {
        current += increment;
        if (current < target) {
            el.textContent = current;
            setTimeout(update, stepTime);
        } else {
            el.textContent = target;
        }
    };

    update();
}

async function loadStats() {
    try {
        const { data } = await window.axios.get('/api/dashboard/stats');
        animateNumber('totalSiswa', data.total_siswa);
        animateNumber('totalPelanggaran', data.total_pelanggaran);
    } catch (error) {
        console.error('Gagal memuat statistik:', error);
    }
}

async function loadRecentTable() {
    const tbody = document.getElementById('recentTableBody');
    if (!tbody) return;

    try {
        const { data } = await window.axios.get('/api/dashboard/recent');
        tbody.innerHTML = '';

        if (data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:2rem;color:#94a3b8;">Belum ada data pelanggaran</td></tr>';
            return;
        }

        const badgeColor = { Ringan: 'badge-ringan', Sedang: 'badge-sedang', Berat: 'badge-berat' };

        data.data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${item.siswa}</strong></td>
                <td>${item.kelas}</td>
                <td>${item.pelanggaran}</td>
                <td><span class="badge-kategori ${badgeColor[item.kategori] || ''}">${item.kategori}</span></td>
                <td>${item.poin}</td>
                <td>${item.tanggal}</td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error('Gagal memuat pelanggaran terbaru:', error);
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:2rem;color:#ef4444;">Gagal memuat data</td></tr>';
    }
}

async function loadCharts() {
    try {
        const { data } = await window.axios.get('/api/dashboard/charts');
        chartData = data;
        renderCharts();
    } catch (error) {
        console.error('Gagal memuat chart:', error);
    }
}

function renderMainChart() {
    const canvas = document.getElementById('mainChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const labels = ['Ringan', 'Sedang', 'Berat'];
    const values = [
        chartData.by_kategori.ringan || 0,
        chartData.by_kategori.sedang || 0,
        chartData.by_kategori.berat || 0
    ];

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(51, 65, 85, 0.5)' : '#e2e8f0';

    if (mainChart) mainChart.destroy();

    mainChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Frekuensi Pelanggaran',
                data: values,
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)', // Ringan - Green
                    'rgba(245, 158, 11, 0.8)', // Sedang - Yellow
                    'rgba(239, 68, 68, 0.8)'   // Berat - Red
                ],
                borderRadius: 8,
                barPercentage: 0.7,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { color: textColor, stepSize: 1 },
                    grid: { color: gridColor }
                },
                x: { 
                    grid: { display: false },
                    ticks: { color: textColor }
                },
            },
        },
    });
}

function renderPieChart() {
    const canvas = document.getElementById('pieChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const { ringan, sedang, berat } = chartData.by_kategori;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const legendColor = isDark ? '#94a3b8' : '#64748b';
    const donutBorder = isDark ? '#1e293b' : '#ffffff';

    if (pieChart) pieChart.destroy();

    pieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Ringan', 'Sedang', 'Berat'],
            datasets: [{
                data: [ringan, sedang, berat],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 3,
                borderColor: donutBorder,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: { color: legendColor }
                } 
            },
            cutout: '65%',
        },
    });
}

function renderCharts() {
    renderMainChart();
    renderPieChart();
}

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

function initChartButtons() {
    document.querySelectorAll('.chart-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.chart-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            renderCharts();
        });
    });
}

// Notifikasi real-time ditangani oleh notifications.js

document.addEventListener('DOMContentLoaded', () => {
    loadStats();
    loadRecentTable();
    loadCharts();
    initSidebarToggle();
    initChartButtons();
    initNotifications(); // real-time notification (polling)

    // Re-render charts on theme change
    window.addEventListener('theme-changed', () => {
        if (chartData && (chartData.by_jenis || chartData.by_kategori)) {
            renderCharts();
        }
    });
});
