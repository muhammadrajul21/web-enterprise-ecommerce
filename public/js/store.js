(function () {
    'use strict';

    var body = document.body;

    /* ---------- Drawer mobile ---------- */
    var drawer = document.getElementById('nav-drawer');
    var openBtn = document.querySelector('[data-drawer-open]');

    function setDrawer(open) {
        if (!drawer) return;
        drawer.classList.toggle('is-open', open);
        drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
        body.classList.toggle('has-drawer', open);
        if (openBtn) {
            openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (!open) openBtn.focus();
        }
        if (open) {
            var close = drawer.querySelector('button[data-drawer-close]');
            if (close) close.focus();
        }
    }

    if (openBtn) openBtn.addEventListener('click', function () { setDrawer(true); });

    document.querySelectorAll('[data-drawer-close]').forEach(function (el) {
        el.addEventListener('click', function () { setDrawer(false); });
    });

    /* ---------- Search panel ---------- */
    var panel = document.getElementById('search-panel');
    var searchBtn = document.querySelector('[data-search-toggle]');

    function setSearch(open) {
        if (!panel || !searchBtn) return;
        panel.hidden = !open;
        searchBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) panel.querySelector('input').focus();
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            setSearch(panel.hidden);
        });
    }

    /* ---------- Esc menutup drawer / search ---------- */
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (drawer && drawer.classList.contains('is-open')) setDrawer(false);
        else if (panel && !panel.hidden) { setSearch(false); searchBtn.focus(); }
    });

    /* Drawer otomatis tertutup bila layar melebar ke desktop */
    window.matchMedia('(min-width: 1101px)').addEventListener('change', function (e) {
        if (e.matches && drawer && drawer.classList.contains('is-open')) setDrawer(false);
    });

    /* ---------- Footer accordion ----------
       Desktop: semua grup terbuka. Mobile: tertutup, bisa dibuka satu per satu.
       Tanpa JS, semua grup tetap terbuka. */
    var groups = document.querySelectorAll('.footer-group');
    var mq = window.matchMedia('(min-width: 641px)');

    function syncFooter() {
        groups.forEach(function (g) { g.open = mq.matches; });
    }

    if (groups.length) {
        syncFooter();
        mq.addEventListener('change', syncFooter);

        /* Di desktop summary tidak boleh menutup grup */
        groups.forEach(function (g) {
            g.querySelector('summary').addEventListener('click', function (e) {
                if (mq.matches) e.preventDefault();
            });
        });
    }
})();
