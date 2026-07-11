/**
 * notifications.js
 * Modul notifikasi real-time SIPS (Sistem Informasi Pelanggaran Siswa)
 * Digunakan bersama di semua halaman.
 *
 * Cara kerja:
 * - Saat halaman dimuat, langsung fetch pelanggaran hari ini → tampilkan di badge & dropdown.
 * - Polling setiap POLL_INTERVAL ms; hanya ambil data baru (after_id).
 * - Jika ada pelanggaran baru → badge bertambah + animasi guncang lonceng + toast pop-up.
 */

const POLL_INTERVAL = 30000; // 30 detik

let latestId = 0;        // ID pelanggaran tertinggi yang sudah diketahui
let totalToday = 0;      // Total pelanggaran hari ini
let unreadCount = 0;     // Jumlah pelanggaran belum dibaca
let pollingTimer = null;
let dropdownOpen = false;

const badgeColor = {
    Ringan: '#10b981',
    Sedang: '#f59e0b',
    Berat:  '#ef4444',
};

function getThemeColors() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    return {
        bgCard: isDark ? '#1e293b' : '#ffffff',
        textPrimary: isDark ? '#f8fafc' : '#1e293b',
        textSecondary: isDark ? '#94a3b8' : '#64748b',
        textMuted: isDark ? '#64748b' : '#94a3b8',
        border: isDark ? '#334155' : '#e2e8f0',
        bgFooter: isDark ? '#0f172a' : '#f8f9fc',
        accent: isDark ? '#60a5fa' : '#4e73df',
    };
}

// ─────────────────────────────────────────────
// DOM Helpers
// ─────────────────────────────────────────────

function getBadgeEl() {
    return document.querySelector('.notification .badge');
}

function getBellEl() {
    return document.querySelector('.notification');
}

function updateBadge(count) {
    const badge = getBadgeEl();
    if (!badge) return;

    badge.textContent = count;
    badge.style.display = count > 0 ? 'inline-block' : 'none';
}

function shakeBell() {
    const bell = getBellEl();
    if (!bell) return;

    bell.classList.add('bell-shake');
    setTimeout(() => bell.classList.remove('bell-shake'), 800);
}

// ─────────────────────────────────────────────
// Toast Notification
// ─────────────────────────────────────────────

function showToast(item) {
    const existing = document.getElementById('sips-toast-container');
    const container = existing || (() => {
        const div = document.createElement('div');
        div.id = 'sips-toast-container';
        div.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
            z-index: 9999;
            max-width: 340px;
        `;
        document.body.appendChild(div);
        return div;
    })();

    const color = badgeColor[item.kategori] || '#10b981';
    const theme = getThemeColors();
    const toast = document.createElement('div');
    toast.style.cssText = `
        background: ${theme.bgCard};
        border-radius: 10px;
        padding: 14px 16px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        border-left: 4px solid ${color};
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        animation: sips-slide-in 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    `;
    toast.innerHTML = `
        <div style="display:flex; align-items:flex-start; gap:10px;">
            <div style="width:10px; height:10px; border-radius:50%; background:${color}; flex-shrink:0; margin-top:3px;"></div>
            <div style="flex:1; min-width:0;">
                <div style="font-weight:700; color:${theme.textPrimary}; margin-bottom:2px;">
                    🔔 Pelanggaran Baru Masuk!
                </div>
                <div style="color:${theme.accent}; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    ${item.siswa} (${item.kelas})
                </div>
                <div style="color:${theme.textSecondary}; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    ${item.pelanggaran}
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:6px; font-size:11px; color:${theme.textMuted};">
                    <span>${item.tanggal} • ${item.waktu}</span>
                    <span style="font-weight:700; color:#ef4444;">+${item.poin} Poin</span>
                </div>
            </div>
            <button style="position:absolute; top:8px; right:10px; background:none; border:none; cursor:pointer; color:#94a3b8; font-size:16px; line-height:1;" onclick="this.closest('[id]').parentElement && this.closest('div[style]').remove()">×</button>
        </div>
        <div class="toast-progress" style="
            position:absolute; bottom:0; left:0; height:3px;
            background:${color}; width:100%;
            animation: sips-progress 5s linear forwards;
        "></div>
    `;

    toast.addEventListener('click', () => {
        window.location.href = '/pelanggaran';
    });

    container.appendChild(toast);

    // Auto remove setelah 5 detik
    setTimeout(() => {
        toast.style.animation = 'sips-slide-out 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// ─────────────────────────────────────────────
// CSS Animations (inject sekali)
// ─────────────────────────────────────────────

function injectStyles() {
    if (document.getElementById('sips-notif-styles')) return;

    const style = document.createElement('style');
    style.id = 'sips-notif-styles';
    style.textContent = `
        @keyframes sips-slide-in {
            from { opacity: 0; transform: translateX(60px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes sips-slide-out {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(60px); }
        }
        @keyframes sips-progress {
            from { width: 100%; }
            to   { width: 0%; }
        }
        @keyframes bell-shake {
            0%,100% { transform: rotate(0deg); }
            15%      { transform: rotate(-20deg); }
            30%      { transform: rotate(20deg); }
            45%      { transform: rotate(-15deg); }
            60%      { transform: rotate(15deg); }
            75%      { transform: rotate(-8deg); }
            90%      { transform: rotate(8deg); }
        }
        .bell-shake .fa-bell,
        .bell-shake {
            animation: bell-shake 0.8s ease !important;
        }
        .notification-dropdown::-webkit-scrollbar { width: 4px; }
        .notification-dropdown::-webkit-scrollbar-track { background: #f1f5f9; }
        .notification-dropdown::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }
    `;
    document.head.appendChild(style);
}

// ─────────────────────────────────────────────
// Render Dropdown Items
// ─────────────────────────────────────────────

function renderDropdown(list, container) {
    const theme = getThemeColors();
    container.innerHTML = '';

    if (!list || list.length === 0) {
        container.innerHTML = `
            <div style="padding:24px 16px; text-align:center; color:${theme.textMuted}; font-size:13px;">
                <i class="fas fa-bell-slash" style="font-size:24px; margin-bottom:8px; display:block; opacity:0.5;"></i>
                Belum ada pelanggaran hari ini
            </div>`;
        return;
    }

    list.forEach(item => {
        const color = badgeColor[item.kategori] || '#10b981';
        const div = document.createElement('div');
        div.style.cssText = `
            padding: 12px 16px;
            border-bottom: 1px solid ${theme.border};
            cursor: pointer;
            transition: background 0.15s;
            font-size: 13px;
        `;
        div.addEventListener('mouseenter', () => div.style.backgroundColor = theme.bgFooter);
        div.addEventListener('mouseleave', () => div.style.backgroundColor = 'transparent');
        div.innerHTML = `
            <div style="display:flex; gap:10px; align-items:flex-start;">
                <div style="width:9px; height:9px; border-radius:50%; background:${color}; margin-top:4px; flex-shrink:0;"></div>
                <div style="flex:1; min-width:0;">
                    <strong style="color:${theme.accent}; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        ${item.siswa} <span style="color:${theme.textMuted}; font-weight:400;">(${item.kelas})</span>
                    </strong>
                    <div style="color:${theme.textSecondary}; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        ${item.pelanggaran}
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:5px; font-size:11px; color:${theme.textMuted};">
                        <span><i class="fas fa-clock" style="margin-right:3px;"></i>${item.waktu} • ${item.tanggal}</span>
                        <span style="font-weight:700; color:#ef4444; font-size:12px;">+${item.poin} Poin</span>
                    </div>
                </div>
            </div>
        `;
        div.addEventListener('click', () => { window.location.href = '/pelanggaran'; });
        container.appendChild(div);
    });
}

// ─────────────────────────────────────────────
// Fetch & Poll Logic
// ─────────────────────────────────────────────

async function fetchTodayNotifications(isIncremental = false) {
    try {
        const url = isIncremental && latestId > 0
            ? `/api/notifications/today?after_id=${latestId}`
            : '/api/notifications/today';

        const { data: res } = await window.axios.get(url);

        const isFirstLoad = !isIncremental;
        const hasNew = isIncremental && res.data && res.data.length > 0;

        // Update total & badge
        if (isFirstLoad || !isIncremental) {
            totalToday = res.count || 0;

            // Hitung unreadCount berdasarkan localStorage
            const lastSeenId = localStorage.getItem('sips_last_seen_id');
            if (lastSeenId === null) {
                unreadCount = res.count || 0;
            } else {
                const parsedLastSeen = parseInt(lastSeenId, 10);
                unreadCount = (res.data || []).filter(item => item.id > parsedLastSeen).length;
            }
        } else if (hasNew) {
            totalToday += res.data.length;
            if (dropdownOpen) {
                unreadCount = 0;
                if (res.latest_id) {
                    localStorage.setItem('sips_last_seen_id', res.latest_id);
                }
            } else {
                unreadCount += res.data.length;
            }
        }

        updateBadge(unreadCount);

        // Update latest_id
        if (res.latest_id && res.latest_id > latestId) {
            latestId = res.latest_id;
        }

        // Jika ada pelanggaran baru saat polling → animasi & toast
        if (hasNew) {
            shakeBell();
            // Tampilkan toast untuk setiap item baru (max 3)
            res.data.slice(0, 3).forEach((item, i) => {
                setTimeout(() => showToast(item), i * 300);
            });

            // Reload isi dropdown jika sedang terbuka
            const list = document.getElementById('sips-notif-list');
            if (list && dropdownOpen) {
                await reloadDropdownContent(list);
            }
        }

        // Pada first load, isi dropdown jika sudah terbuka
        if (isFirstLoad) {
            const list = document.getElementById('sips-notif-list');
            if (list) renderDropdown(res.data || [], list);
        }

    } catch (err) {
        console.warn('[SIPS Notif] Gagal fetch notifikasi:', err);
    }
}

async function reloadDropdownContent(listEl) {
    try {
        const { data: res } = await window.axios.get('/api/notifications/today');
        renderDropdown(res.data || [], listEl);

        // Perbarui header count
        const countEl = document.getElementById('sips-notif-count');
        if (countEl) countEl.textContent = `${res.count} Hari Ini`;
    } catch (err) {
        console.warn('[SIPS Notif] Gagal reload dropdown:', err);
    }
}

function startPolling() {
    if (pollingTimer) clearInterval(pollingTimer);
    pollingTimer = setInterval(() => fetchTodayNotifications(true), POLL_INTERVAL);
}

// ─────────────────────────────────────────────
// Build Dropdown UI
// ─────────────────────────────────────────────

function buildDropdown(notifEl) {
    // Hapus dropdown lama jika ada
    const old = notifEl.querySelector('.notification-dropdown');
    if (old) old.remove();

    const theme = getThemeColors();
    const dropdown = document.createElement('div');
    dropdown.className = 'notification-dropdown';
    dropdown.style.cssText = `
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 340px;
        background: ${theme.bgCard};
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid ${theme.border};
        display: none;
        z-index: 1050;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    `;

    // Header
    const header = document.createElement('div');
    header.style.cssText = `
        padding: 14px 16px;
        background: linear-gradient(135deg, #4e73df, #6366f1);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    `;
    header.innerHTML = `
        <div style="display:flex; align-items:center; gap:8px; font-weight:700; font-size:14px;">
            <i class="fas fa-bell"></i>
            Pelanggaran Hari Ini
        </div>
        <small id="sips-notif-count" style="background:rgba(255,255,255,0.2); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;">
            0 Hari Ini
        </small>
    `;
    dropdown.appendChild(header);

    // Live indicator
    const liveBar = document.createElement('div');
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    liveBar.style.cssText = `
        padding: 6px 16px;
        background: ${isDark ? 'rgba(34, 197, 94, 0.1)' : '#f0fdf4'};
        border-bottom: 1px solid ${isDark ? 'rgba(34, 197, 94, 0.2)' : '#dcfce7'};
        font-size: 11px;
        color: ${isDark ? '#4ade80' : '#16a34a'};
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    `;
    liveBar.innerHTML = `
        <span style="width:7px; height:7px; background:#22c55e; border-radius:50%; display:inline-block; animation: sips-pulse 1.5s infinite;"></span>
        Memperbarui otomatis setiap 30 detik
    `;
    dropdown.appendChild(liveBar);

    // Add pulse animation to style
    const pStyle = document.getElementById('sips-notif-styles');
    if (pStyle && !pStyle.textContent.includes('sips-pulse')) {
        pStyle.textContent += `
            @keyframes sips-pulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.5; transform: scale(0.85); }
            }
        `;
    }

    // List area
    const list = document.createElement('div');
    list.id = 'sips-notif-list';
    list.style.cssText = 'max-height: 300px; overflow-y: auto;';
    list.innerHTML = `
        <div style="padding:24px 16px; text-align:center; color:${theme.textMuted}; font-size:13px;">
            <i class="fas fa-spinner fa-spin" style="margin-bottom:8px; display:block;"></i>
            Memuat...
        </div>`;
    dropdown.appendChild(list);

    // Footer
    const footer = document.createElement('div');
    footer.style.cssText = `
        padding: 10px 16px;
        text-align: center;
        border-top: 1px solid ${theme.border};
        background: ${theme.bgFooter};
    `;
    footer.innerHTML = `
        <a href="/pelanggaran" style="color:${theme.accent}; text-decoration:none; font-size:12px; font-weight:600;">
            <i class="fas fa-list"></i> Lihat Semua Pelanggaran
        </a>`;
    dropdown.appendChild(footer);

    notifEl.appendChild(dropdown);
    return { dropdown, list };
}

// ─────────────────────────────────────────────
// Init — dipanggil dari tiap halaman
// ─────────────────────────────────────────────

export function initNotifications() {
    injectStyles();

    const notifEl = document.querySelector('.notification');
    if (!notifEl) return;

    notifEl.style.cursor = 'pointer';
    notifEl.style.position = 'relative';

    const { dropdown, list } = buildDropdown(notifEl);

    // First load: ambil semua data hari ini
    fetchTodayNotifications(false).then(() => {
        // Perbarui header count di dropdown
        const countEl = document.getElementById('sips-notif-count');
        if (countEl) countEl.textContent = `${totalToday} Hari Ini`;
    });

    // Mulai polling
    startPolling();

    // Toggle dropdown saat klik lonceng
    notifEl.addEventListener('click', async (e) => {
        if (e.target.closest('.notification-dropdown')) return;
        e.stopPropagation();

        const isOpen = dropdown.style.display === 'block';

        // Tutup semua dropdown lain
        document.querySelectorAll('.notification-dropdown').forEach(d => d.style.display = 'none');

        if (!isOpen) {
            dropdown.style.display = 'block';
            dropdownOpen = true;

            // Reset badge count to 0 and save latestId as read
            unreadCount = 0;
            updateBadge(unreadCount);
            if (latestId > 0) {
                localStorage.setItem('sips_last_seen_id', latestId);
            }

            // Reload isi terbaru saat dibuka
            await reloadDropdownContent(list);
            const countEl = document.getElementById('sips-notif-count');
            if (countEl) countEl.textContent = `${totalToday} Hari Ini`;
        } else {
            dropdown.style.display = 'none';
            dropdownOpen = false;
        }
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', () => {
        dropdown.style.display = 'none';
        dropdownOpen = false;
    });
}
