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
        showToast: function (message, type = 'success') {
            const container = document.getElementById('gpa-toast-container');
            if (!container) return;

            const toastEl = document.createElement('div');
            toastEl.className = 'toast align-items-center text-white border-0 show';
            toastEl.setAttribute('role', 'alert');

            const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-primary';
            toastEl.classList.add(bgClass);

            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            container.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
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
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        },
        toggleSidebar: function () {
            const sidebar = document.getElementById('gpa-sidebar');
            if (sidebar) sidebar.classList.toggle('show');
        },
    };

    document.addEventListener('DOMContentLoaded', function () {
        const flashSuccess = document.querySelector('[data-flash-success]');
        const flashError = document.querySelector('[data-flash-error]');
        if (flashSuccess) GpaApp.showToast(flashSuccess.textContent, 'success');
        if (flashError) GpaApp.showToast(flashError.textContent, 'error');
    });
})();
