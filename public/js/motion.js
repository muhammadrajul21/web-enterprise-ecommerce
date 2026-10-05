(function () {
    'use strict';

    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var body = document.body;
    var header = document.querySelector('.site-header');
    var search = document.getElementById('search-panel');

    /* ---------- Header: bayangan + sembunyi saat scroll turun ---------- */
    if (header) {
        var lastY = window.scrollY, ticking = false;

        var update = function () {
            var y = window.scrollY;
            header.classList.toggle('is-scrolled', y > 8);

            var busy = body.classList.contains('has-drawer')
                || (search && !search.hidden)
                || header.contains(document.activeElement);

            if (busy || y < 200) {
                header.classList.remove('is-hidden');
                body.classList.remove('nav-hidden');
                lastY = y;
            } else if (Math.abs(y - lastY) > 6) {
                var down = y > lastY;
                header.classList.toggle('is-hidden', down);
                body.classList.toggle('nav-hidden', down);
                lastY = y;
            }
            ticking = false;
        };

        window.addEventListener('scroll', function () {
            if (!ticking) { ticking = true; requestAnimationFrame(update); }
        }, { passive: true });
    }

    /* ---------- Muncul saat di-scroll ----------
       Elemen yang sudah terlihat saat load tidak dianimasikan (tanpa kedip). */
    if (!reduce && 'IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            var n = 0;
            entries.forEach(function (e) {
                if (!e.isIntersecting) return;
                var el = e.target;
                el.style.transitionDelay = Math.min(n++, 5) * 70 + 'ms';
                el.classList.add('is-in');
                io.unobserve(el);

                // Lepas kelas setelah selesai agar transisi hover bawaan elemen kembali normal
                el.addEventListener('transitionend', function done(ev) {
                    if (ev.target !== el) return;
                    el.classList.remove('reveal', 'is-in');
                    el.style.transitionDelay = '';
                    el.removeEventListener('transitionend', done);
                });
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });

        document.querySelectorAll(
            '.product-card, .segment-card, .collection-item, .campaign-content, .catalog-empty'
        ).forEach(function (el) {
            if (el.getBoundingClientRect().top < window.innerHeight) return;
            el.classList.add('reveal');
            io.observe(el);
        });
    }

    /* ---------- Footer accordion (mobile) membuka/menutup halus ---------- */
    var desktop = window.matchMedia('(min-width: 641px)');

    document.querySelectorAll('.footer-group').forEach(function (g) {
        var summary = g.querySelector('summary');
        var anim = null;

        summary.addEventListener('click', function (e) {
            if (desktop.matches || reduce) return;
            e.preventDefault();
            if (anim) anim.cancel();

            var from = g.offsetHeight, closing = g.open, to;
            if (closing) {
                to = summary.offsetHeight;
            } else {
                g.open = true;
                to = g.offsetHeight;
            }

            g.style.overflow = 'hidden';
            anim = g.animate(
                { height: [from + 'px', to + 'px'] },
                { duration: 280, easing: 'cubic-bezier(.4, 0, .2, 1)' }
            );
            anim.onfinish = function () {
                if (closing) g.open = false;
                g.style.overflow = '';
                anim = null;
            };
        });
    });

    /* ---------- Katalog: beri umpan balik saat filter diganti ---------- */
    var grid = document.querySelector('.catalog-product-grid');
    if (grid) {
        document.querySelectorAll('.catalog-toolbar select').forEach(function (s) {
            s.addEventListener('change', function () { grid.classList.add('is-loading'); });
        });
    }
})();
