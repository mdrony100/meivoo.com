/* Meivoo UI behaviour: theme, toasts, search, small UX niceties.
   Progressive enhancement only — every page works without it. */
(function () {
    'use strict';

    var root = document.documentElement;
    var THEME_KEY = 'meivoo.theme';

    /* ---------------- theme ---------------- */
    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
        try { localStorage.setItem(THEME_KEY, theme); } catch (e) { }
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', theme === 'dark' ? '#0b0c16' : '#ffffff');
        document.querySelectorAll('[data-mv-theme-toggle]').forEach(function (btn) {
            btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
            btn.title = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
        });
        document.dispatchEvent(new CustomEvent('mv:theme', { detail: { theme: theme } }));
    }

    function currentTheme() {
        return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    }

    function adoptTheme() {
        var theme = root.getAttribute('data-theme') || 'light';
        applyTheme(theme);
    }

    function initTheme() {
        document.querySelectorAll('[data-mv-theme-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
            });
        });
    }

    /* ---------------- toasts ---------------- */
    function toastHost() {
        var host = document.querySelector('.mv-toast-host');
        if (!host) {
            host = document.createElement('div');
            host.className = 'mv-toast-host';
            host.setAttribute('role', 'status');
            host.setAttribute('aria-live', 'polite');
            document.body.appendChild(host);
        }
        return host;
    }

    function toast(message, type, timeout) {
        if (!message) return;
        var el = document.createElement('div');
        el.className = 'mv-toast ' + (type || 'info');
        el.innerHTML = '<span class="mv-toast-dot"></span><span></span>';
        el.lastElementChild.textContent = message;
        toastHost().appendChild(el);
        var life = timeout || 4200;
        setTimeout(function () {
            el.classList.add('leaving');
            setTimeout(function () { el.remove(); }, 220);
        }, life);
    }

    window.MvToast = toast;

    function initServerToasts() {
        document.querySelectorAll('[data-mv-toast]').forEach(function (node) {
            toast(node.getAttribute('data-mv-toast'), node.getAttribute('data-mv-toast-type') || 'success');
            node.remove();
        });
    }

    /* ---------------- reveal on scroll ---------------- */
    function initReveal() {
        var items = document.querySelectorAll('.mv-reveal');
        if (!items.length) return;
        if (!('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('in'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    io.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -60px 0px', threshold: 0.08 });
        items.forEach(function (el) { io.observe(el); });
    }

    /* ---------------- back to top ---------------- */
    function initBackTop() {
        var btn = document.querySelector('.mv-backtop');
        if (!btn) {
            btn = document.createElement('button');
            btn.className = 'mv-backtop';
            btn.type = 'button';
            btn.setAttribute('aria-label', 'Back to top');
            btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>';
            document.body.appendChild(btn);
        }
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        var onScroll = function () {
            btn.classList.toggle('show', window.scrollY > 420);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    /* ---------------- avatar fallback ---------------- */
    function initAvatars() {
        document.querySelectorAll('img[data-mv-avatar]').forEach(function (img) {
            img.addEventListener('error', function () {
                var span = document.createElement('span');
                var name = img.getAttribute('alt') || 'U';
                span.className = (img.className || '') + ' user-avatar-placeholder';
                span.textContent = name.trim().charAt(0).toUpperCase() || 'U';
                span.style.width = img.getAttribute('width') ? img.getAttribute('width') + 'px' : '';
                span.style.height = img.getAttribute('height') ? img.getAttribute('height') + 'px' : '';
                img.replaceWith(span);
            }, { once: true });
        });
    }

    /* ---------------- autosize textareas + counters ---------------- */
    function initComposers() {
        document.querySelectorAll('textarea[data-mv-autosize]').forEach(function (area) {
            var resize = function () {
                area.style.height = 'auto';
                area.style.height = Math.min(area.scrollHeight, 220) + 'px';
            };
            area.addEventListener('input', resize);
            resize();
        });

        document.querySelectorAll('[data-mv-counter]').forEach(function (host) {
            var target = document.querySelector(host.getAttribute('data-mv-counter'));
            if (!target) return;
            var max = parseInt(target.getAttribute('maxlength') || '0', 10);
            var update = function () {
                var len = target.value.length;
                host.textContent = max ? len + '/' + max : String(len);
                host.classList.toggle('text-danger', max > 0 && len > max * 0.92);
            };
            target.addEventListener('input', update);
            update();
        });
    }

    /* ---------------- global search ---------------- */
    function initSearch() {
        var input = document.querySelector('[data-mv-search]');
        if (!input) return;
        var url = input.getAttribute('data-mv-search');
        var box = input.parentElement.querySelector('.mv-search-results');
        if (!box) return;

        var timer = null;
        var lastQuery = '';

        function close() {
            box.classList.remove('open');
            box.innerHTML = '';
        }

        function render(groups) {
            var html = '';
            var count = 0;
            groups.forEach(function (group) {
                if (!group.items || !group.items.length) return;
                count += group.items.length;
                html += '<div class="mv-search-group">' + group.label + '</div>';
                group.items.forEach(function (item) {
                    html += '<a class="mv-search-item" href="' + item.url + '">' +
                        '<span class="mv-search-item-icon">' + (item.icon || '•') + '</span>' +
                        '<span>' + item.title + (item.meta ? ' <span class="text-muted small">· ' + item.meta + '</span>' : '') + '</span>' +
                        '</a>';
                });
            });
            if (!count) {
                html = '<div class="mv-search-empty">No matches. Try a name, post or event.</div>';
            }
            box.innerHTML = html;
            box.classList.add('open');
        }

        function search(q) {
            if (!q || q.length < 2) { close(); return; }
            fetch(url + '?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (r) { return r.ok ? r.json() : { groups: [] }; })
                .then(function (data) { if (q === lastQuery) render(data.groups || []); })
                .catch(function () { close(); });
        }

        input.addEventListener('input', function () {
            lastQuery = input.value.trim();
            clearTimeout(timer);
            timer = setTimeout(function () { search(lastQuery); }, 220);
        });

        input.addEventListener('focus', function () {
            if (input.value.trim().length >= 2) search(input.value.trim());
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { close(); input.blur(); }
        });

        document.addEventListener('click', function (e) {
            if (!input.parentElement.contains(e.target)) close();
        });

        document.addEventListener('keydown', function (e) {
            var tag = (document.activeElement && document.activeElement.tagName) || '';
            var typing = /INPUT|TEXTAREA|SELECT/.test(tag) || (document.activeElement && document.activeElement.isContentEditable);
            if (e.key === '/' && !typing) {
                e.preventDefault();
                input.focus();
            }
        });
    }

    /* ---------------- notification summary ---------------- */
    function initSummary() {
        var nodes = document.querySelectorAll('[data-mv-summary]');
        if (!nodes.length) return;
        var url = nodes[0].getAttribute('data-mv-summary');
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data) return;
                nodes.forEach(function (node) {
                    Object.keys(data).forEach(function (key) {
                        var target = node.querySelector('[data-mv-summary-item="' + key + '"]');
                        if (!target) return;
                        if (typeof data[key] === 'number') {
                            target.textContent = data[key] > 99 ? '99+' : String(data[key]);
                            target.classList.toggle('d-none', data[key] <= 0);
                        }
                    });
                });
                var dot = document.querySelectorAll('[data-mv-unread-dot]');
                var total = (data.messages || 0) + (data.events || 0) + (data.birthdays || 0);
                dot.forEach(function (d) { d.classList.toggle('d-none', total <= 0); });
            })
            .catch(function () { });
    }

    /* ---------------- boot ---------------- */
    function boot() {
        adoptTheme();
        initTheme();
        initServerToasts();
        initReveal();
        initBackTop();
        initAvatars();
        initComposers();
        initSearch();
        initSummary();

        // keep icons consistent for anything injected after load
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
