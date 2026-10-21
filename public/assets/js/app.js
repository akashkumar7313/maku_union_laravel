(function () {
    'use strict';

    document.querySelectorAll('[data-confirm]').forEach(function (element) {
        element.addEventListener('click', function (event) {
            if (!window.confirm(element.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('form[data-submit-once]').forEach(function (form) {
        form.addEventListener('submit', function () {
            var button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                var label = button.getAttribute('data-loading-text');
                if (label) {
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>' + label;
                }
            }
        });
    });

    document.querySelectorAll('.alert[data-auto-dismiss]').forEach(function (alert) {
        window.setTimeout(function () {
            if (window.bootstrap && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 7000);
    });

    document.querySelectorAll('[data-copy]').forEach(function (button) {
        button.addEventListener('click', function () {
            var text = button.getAttribute('data-copy');
            if (!text || !navigator.clipboard) {
                return;
            }
            navigator.clipboard.writeText(text).then(function () {
                var original = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check2"></i> कॉपी हो गया';
                window.setTimeout(function () {
                    button.innerHTML = original;
                }, 1800);
            });
        });
    });

    document.querySelectorAll('[data-hero-slider]').forEach(function (slider) {
        var slides = Array.prototype.slice.call(slider.querySelectorAll('.hero-slide'));
        var prev = slider.querySelector('[data-hero-prev]');
        var next = slider.querySelector('[data-hero-next]');
        var dotsWrap = slider.querySelector('[data-hero-dots]');

        if (slides.length < 2) {
            [prev, next, dotsWrap].forEach(function (control) {
                if (control) control.hidden = true;
            });
            return;
        }

        var current = 0;
        var timer = null;
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function dots() {
            if (!dotsWrap) return;
            dotsWrap.innerHTML = '';
            slides.forEach(function (slide, index) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'hero-dot';
                dot.setAttribute('aria-label', 'बैनर ' + (index + 1));
                dot.addEventListener('click', function () {
                    show(index);
                    restart();
                });
                dotsWrap.appendChild(dot);
            });
        }

        function updateDots() {
            if (!dotsWrap) return;
            Array.prototype.forEach.call(dotsWrap.children, function (dot, index) {
                dot.classList.toggle('active', index === current);
                dot.setAttribute('aria-current', index === current ? 'true' : 'false');
            });
        }

        function show(index) {
            current = (index + slides.length) % slides.length;
            slides.forEach(function (slide, slideIndex) {
                slide.classList.toggle('active', slideIndex === current);
            });
            updateDots();
        }

        function stop() {
            if (timer) {
                window.clearInterval(timer);
                timer = null;
            }
        }

        function start() {
            if (reduceMotion || timer) return;
            timer = window.setInterval(function () {
                show(current + 1);
            }, 6000);
        }

        function restart() {
            stop();
            start();
        }

        if (prev) {
            prev.addEventListener('click', function () {
                show(current - 1);
                restart();
            });
        }

        if (next) {
            next.addEventListener('click', function () {
                show(current + 1);
                restart();
            });
        }

        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);
        slider.addEventListener('focusin', stop);
        slider.addEventListener('focusout', start);

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stop();
            } else {
                start();
            }
        });

        dots();
        show(0);
        start();
    });

    var yearNodes = document.querySelectorAll('[data-current-year]');
    yearNodes.forEach(function (node) {
        node.textContent = new Date().getFullYear();
    });
})();
