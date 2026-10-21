(function () {
    'use strict';

    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    function scrollToTop() {
        if (!window.location.hash) {
            window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
        }
    }

    scrollToTop();
    document.addEventListener('DOMContentLoaded', scrollToTop);
    window.addEventListener('load', scrollToTop);
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) scrollToTop();
    });

    var sidebar = document.getElementById('adminSidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    var toggle = document.getElementById('sidebarToggle');
    var adminNav = sidebar ? sidebar.querySelector('.admin-nav') : null;

    function revealNavLink(link) {
        if (!adminNav || !link) return;

        var navRect = adminNav.getBoundingClientRect();
        var linkRect = link.getBoundingClientRect();
        var offset = linkRect.top - navRect.top + adminNav.scrollTop - 8;

        adminNav.scrollTop = Math.max(0, offset);
    }

    function revealActiveNav() {
        if (!adminNav) return;
        revealNavLink(adminNav.querySelector('a.active'));
    }

    revealActiveNav();
    document.addEventListener('DOMContentLoaded', revealActiveNav);
    window.addEventListener('load', revealActiveNav);

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('show');
    }

    if (toggle && sidebar && backdrop) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('show');
        });
        backdrop.addEventListener('click', closeSidebar);
        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) closeSidebar();
                window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
                revealNavLink(link);
            });
        });
    }

    document.querySelectorAll('.pagination a, .page-link').forEach(function (link) {
        link.addEventListener('click', scrollToTop);
    });

    document.querySelectorAll('.alert[data-auto-dismiss]').forEach(function (alert) {
        window.setTimeout(function () {
            if (window.bootstrap && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 6000);
    });

    document.querySelectorAll('form[data-submit-once]').forEach(function (form) {
        form.addEventListener('submit', function () {
            var button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                window.setTimeout(function () { button.disabled = false; }, 12000);
            }
        });
    });

    document.querySelectorAll('[data-confirm]').forEach(function (element) {
        element.addEventListener('click', function (event) {
            if (!window.confirm(element.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-image-preview]').forEach(function (input) {
        var target = document.querySelector(input.getAttribute('data-image-preview'));
        if (!target) return;
        input.addEventListener('change', function () {
            if (input.files && input.files[0]) {
                target.src = URL.createObjectURL(input.files[0]);
                target.classList.remove('d-none');
            }
        });
    });

    var slugSource = document.querySelector('[data-slug-source]');
    var slugTarget = document.querySelector('[data-slug-target]');
    if (slugSource && slugTarget) {
        slugSource.addEventListener('input', function () {
            if (slugTarget.dataset.touched === '1') return;
            slugTarget.value = slugSource.value
                .toLowerCase()
                .trim()
                .replace(/[^\u0900-\u097Fa-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        });
        slugTarget.addEventListener('input', function () {
            slugTarget.dataset.touched = '1';
        });
    }
})();
