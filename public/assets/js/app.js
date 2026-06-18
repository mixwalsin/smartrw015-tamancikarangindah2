/**
 * Smart RW015 Taman Cikarang Indah 2
 * Custom JavaScript
 */

'use strict';

// ── Auto-dismiss alerts after 5 seconds ──
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });
});

// ── Confirm delete ──
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = el.getAttribute('data-confirm') || 'Apakah Anda yakin?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });
});

// ── Format Rupiah input ──
function formatRupiah(input) {
    let value = input.value.replace(/\D/g, '');
    input.value = new Intl.NumberFormat('id-ID').format(value);
}

// ── Active nav link ──
document.addEventListener('DOMContentLoaded', function () {
    const currentPath = window.location.pathname;
    document.querySelectorAll('.navbar-nav .nav-link').forEach(function (link) {
        if (link.getAttribute('href') && currentPath.startsWith(new URL(link.href).pathname)) {
            link.classList.add('active');
        }
    });
});
