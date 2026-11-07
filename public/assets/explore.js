(function () {
    var root = document.querySelector('[data-explore]');
    if (!root) {
        return;
    }

    var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
    var scroller = root.querySelector('[data-explore-scroller]');
    var cards = Array.prototype.slice.call(root.querySelectorAll('[data-explore-card]'));
    var prev = root.querySelector('[data-explore-prev]');
    var next = root.querySelector('[data-explore-next]');
    var live = root.querySelector('[data-explore-live]');
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function shownCards() {
        return cards.filter(function (card) {
            return !card.hidden;
        });
    }

    function updateNav() {
        if (!prev || !next) {
            return;
        }
        var max = scroller.scrollWidth - scroller.clientWidth;
        prev.disabled = scroller.scrollLeft <= 2;
        next.disabled = scroller.scrollLeft >= max - 2;
    }

    function select(tab) {
        tabs.forEach(function (item) {
            var on = item === tab;
            item.setAttribute('aria-selected', on ? 'true' : 'false');
            item.tabIndex = on ? 0 : -1;
        });

        var category = tab.getAttribute('data-category');
        cards.forEach(function (card) {
            card.hidden = category !== 'all' && card.getAttribute('data-category') !== category;
        });

        scroller.setAttribute('aria-labelledby', tab.id);
        scroller.scrollTo({ left: 0, behavior: reduce ? 'auto' : 'smooth' });

        if (live) {
            var count = shownCards().length;
            live.textContent = count + (count === 1 ? ' feature shown' : ' features shown');
        }

        window.requestAnimationFrame(updateNav);
    }

    tabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () {
            select(tab);
        });

        tab.addEventListener('keydown', function (event) {
            var nextIndex = null;
            if (event.key === 'ArrowRight') {
                nextIndex = (index + 1) % tabs.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (index - 1 + tabs.length) % tabs.length;
            } else if (event.key === 'Home') {
                nextIndex = 0;
            } else if (event.key === 'End') {
                nextIndex = tabs.length - 1;
            }

            if (nextIndex === null) {
                return;
            }

            event.preventDefault();
            tabs[nextIndex].focus();
            select(tabs[nextIndex]);
        });
    });

    function step() {
        var card = shownCards()[0];
        var width = card ? card.getBoundingClientRect().width : 300;
        var gap = 16;
        return width + gap;
    }

    function scrollByDir(direction) {
        scroller.scrollBy({
            left: direction * step(),
            behavior: reduce ? 'auto' : 'smooth'
        });
    }

    if (prev) {
        prev.addEventListener('click', function () {
            scrollByDir(-1);
        });
    }

    if (next) {
        next.addEventListener('click', function () {
            scrollByDir(1);
        });
    }

    scroller.addEventListener('scroll', updateNav, { passive: true });
    window.addEventListener('resize', updateNav);
    updateNav();
})();
