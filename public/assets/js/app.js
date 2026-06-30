(function () {
    'use strict';

    window.GpaApp = {
        showLoading: function () {
            const overlay = document.getElementById('gpa-loading-overlay');
            if (overlay) overlay.classList.remove('hidden');
        },
        hideLoading: function () {
            const overlay = document.getElementById('gpa-loading-overlay');
            if (overlay) overlay.classList.add('hidden');
        },
        showToast: function (message, type) {
            type = type || 'success';
            const container = document.getElementById('gpa-toast-container');
            if (!container) return;

            const toastEl = document.createElement('div');
            toastEl.className = 'toast align-items-center text-white border-0 show';
            const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : type === 'warning' ? 'bg-warning text-dark' : 'bg-primary';
            toastEl.classList.add(bgClass);
            toastEl.innerHTML = '<div class="d-flex"><div class="toast-body">' + message + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
            container.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', function () { toastEl.remove(); });
        },
        confirmDelete: function (form) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        },
        toggleSidebar: function () {
            const sidebar = document.getElementById('gpa-sidebar');
            if (sidebar) sidebar.classList.toggle('show');
        },
        initTheme: function () {
            const saved = localStorage.getItem('gpa-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', saved);
            this.updateThemeIcon(saved);
        },
        toggleTheme: function () {
            const current = document.documentElement.getAttribute('data-theme') || 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('gpa-theme', next);
            this.updateThemeIcon(next);
        },
        updateThemeIcon: function (theme) {
            const icon = document.getElementById('gpa-theme-icon');
            if (icon) icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        },
        initSecureBanner: function () {
            const banner = document.getElementById('gpa-secure-banner');
            if (!banner) return;
            const isSecure = window.isSecureContext || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
            if (!isSecure) banner.classList.add('show');
        },
        loadNotifications: function () {
            const badge = document.getElementById('gpa-notif-count');
            const list = document.getElementById('gpa-notif-list');
            if (!badge) return;

            fetch('/notifications/unread', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'flex' : 'none';
                if (list && data.items) {
                    list.innerHTML = data.items.length === 0
                        ? '<div class="gpa-notif-item text-muted text-center">Tidak ada notifikasi baru</div>'
                        : data.items.map(function (item) {
                            return '<div class="gpa-notif-item"><div class="fw-semibold"><i class="bi ' + item.icon + ' text-' + item.color + ' me-1"></i>' + item.title + '</div><div class="text-muted">' + item.message + '</div><small class="text-muted">' + item.time + '</small></div>';
                        }).join('');
                }
            })
            .catch(function () {});
        },
        markAllNotificationsRead: function () {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            }).then(function () { GpaApp.loadNotifications(); });
        },
    };

    document.addEventListener('DOMContentLoaded', function () {
        GpaApp.initTheme();
        GpaApp.initSecureBanner();
        GpaApp.loadNotifications();
        setInterval(function () { GpaApp.loadNotifications(); }, 60000);

        const flashSuccess = document.querySelector('[data-flash-success]');
        const flashError = document.querySelector('[data-flash-error]');
        if (flashSuccess) GpaApp.showToast(flashSuccess.textContent, 'success');
        if (flashError) GpaApp.showToast(flashError.textContent, 'error');

        const clockEl = document.getElementById('gpa-live-clock');
        if (clockEl) {
            const updateClock = function () {
                clockEl.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            };
            updateClock();
            setInterval(updateClock, 1000);
        }
    });
})();
