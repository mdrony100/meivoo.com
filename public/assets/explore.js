(function () {
    var root = document.querySelector('[data-explore]');
    if (!root) {
        return;
    }

    var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
    var viewport = root.querySelector('[data-explore-viewport]');
    var track = root.querySelector('[data-explore-track]');
    var prev = root.querySelector('[data-explore-prev]');
    var next = root.querySelector('[data-explore-next]');
    var toggle = root.querySelector('[data-explore-toggle]');
    var toggleLabel = root.querySelector('[data-explore-toggle-label]');
    var toggleIcon = root.querySelector('[data-explore-toggle-icon]');
    var live = root.querySelector('[data-explore-live]');
    var motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    var reduced = motionQuery.matches;
    var hoverPaused = false;
    var focusPaused = false;
    var userPaused = false;
    var index = 0;
    var looping = false;
    var moving = false;
    var timer = null;
    var DWELL = 4800;
    var TRAVEL = 600;

    function realCards() {
        return Array.prototype.slice.call(track.querySelectorAll('[data-explore-card]:not([data-explore-clone])'));
    }

    function shown() {
        return realCards().filter(function (card) {
            return !card.hidden;
        });
    }

    function columnsFor(width) {
        if (width < 540) {
            return 1;
        }
        if (width < 840) {
            return 2;
        }
        if (width < 1080) {
            return 3;
        }
        return 4;
    }

    function gap() {
        var styles = window.getComputedStyle(track);
        var value = parseFloat(styles.columnGap || styles.gap);
        return isNaN(value) ? 16 : value;
    }

    function stepSize() {
        var card = shown()[0];
        if (!card) {
            return 0;
        }
        return card.getBoundingClientRect().width + gap();
    }

    function canSlide() {
        return looping && stepSize() > 0;
    }

    function wantsAutoplay() {
        return canSlide() && !reduced && !userPaused && !hoverPaused && !focusPaused && document.visibilityState !== 'hidden';
    }

    function setToggle() {
        if (!toggle) {
            return;
        }
        var paused = reduced || userPaused;
        toggle.setAttribute('aria-pressed', paused ? 'true' : 'false');
        if (reduced) {
            toggle.setAttribute('aria-disabled', 'true');
        } else {
            toggle.removeAttribute('aria-disabled');
        }
        if (toggleLabel) {
            toggleLabel.textContent = reduced
                ? 'Automatic slides stay off while reduced motion is on'
                : (paused ? 'Play automatic slides' : 'Pause automatic slides');
        }
        if (toggleIcon) {
            toggleIcon.className = 'fa-solid ' + (paused ? 'fa-play' : 'fa-pause');
        }
    }

    function setNav() {
        var enabled = canSlide();
        [prev, next].forEach(function (button) {
            if (!button) {
                return;
            }
            button.setAttribute('aria-disabled', enabled ? 'false' : 'true');
        });
    }

    function clearClones() {
        track.querySelectorAll('[data-explore-clone]').forEach(function (node) {
            node.remove();
        });
    }

    function makeClone(card) {
        var clone = card.cloneNode(true);
        clone.setAttribute('data-explore-clone', 'true');
        clone.setAttribute('aria-hidden', 'true');
        clone.inert = true;
        clone.querySelectorAll('a').forEach(function (link) {
            link.tabIndex = -1;
        });
        return clone;
    }

    function logicalIndex() {
        var count = shown().length;
        if (!looping || count === 0) {
            return 0;
        }
        return (index - count + count * 8) % count;
    }

    function layout(keepPlace) {
        var place = keepPlace ? logicalIndex() : 0;
        clearClones();
        var list = shown();
        var count = list.length;
        var columns = columnsFor(viewport.clientWidth);
        viewport.style.setProperty('--mv-cards', String(columns));
        looping = count > columns;

        if (looping) {
            var prefix = document.createDocumentFragment();
            var suffix = document.createDocumentFragment();
            list.forEach(function (card) {
                prefix.appendChild(makeClone(card));
                suffix.appendChild(makeClone(card));
            });
            track.insertBefore(prefix, track.firstChild);
            track.appendChild(suffix);
            index = count + place;
        } else {
            index = 0;
        }

        apply(false);
        setNav();
        setToggle();
        start();
    }

    function apply(animate) {
        var distance = -(index * stepSize());
        track.style.transition = animate && !reduced ? 'transform ' + TRAVEL + 'ms ease' : 'none';
        track.style.transform = 'translate3d(' + distance + 'px, 0, 0)';
        if (!animate || reduced) {
            if (normalize()) {
                track.style.transition = 'none';
                track.style.transform = 'translate3d(' + (-(index * stepSize())) + 'px, 0, 0)';
            }
            moving = false;
            syncFocusable();
        }
    }

    function normalize() {
        if (!looping) {
            return false;
        }
        var count = shown().length;
        var nextIndex = index;
        if (index >= count * 2) {
            nextIndex = index - count;
        } else if (index < count) {
            nextIndex = index + count;
        }
        if (nextIndex === index) {
            return false;
        }
        index = nextIndex;
        return true;
    }

    function syncFocusable() {
        var view = viewport.getBoundingClientRect();
        realCards().forEach(function (card) {
            if (card.hidden) {
                card.setAttribute('aria-hidden', 'true');
                card.querySelectorAll('a').forEach(function (link) {
                    link.tabIndex = -1;
                });
                return;
            }
            var rect = card.getBoundingClientRect();
            var onScreen = rect.right > view.left + 12 && rect.left < view.right - 12;
            var focused = card.contains(document.activeElement);
            card.setAttribute('aria-hidden', onScreen || focused ? 'false' : 'true');
            card.querySelectorAll('a').forEach(function (link) {
                if (onScreen || focused) {
                    link.removeAttribute('tabindex');
                } else {
                    link.tabIndex = -1;
                }
            });
        });
    }

    function announce(text) {
        if (live) {
            live.textContent = text;
        }
    }

    function move(direction, fromUser) {
        if (!canSlide() || moving) {
            return;
        }
        moving = true;
        index += direction;
        apply(true);
        if (fromUser) {
            var headings = shown();
            var current = headings[logicalIndex()];
            var title = current ? current.querySelector('h3') : null;
            if (title) {
                announce(title.textContent.trim());
            }
            start();
        }
        window.setTimeout(function () {
            if (!moving) {
                return;
            }
            if (normalize()) {
                apply(false);
            }
            moving = false;
            syncFocusable();
        }, reduced ? 0 : TRAVEL + 80);
    }

    function stop() {
        if (timer) {
            window.clearInterval(timer);
            timer = null;
        }
    }

    function start() {
        stop();
        if (!wantsAutoplay()) {
            return;
        }
        timer = window.setInterval(function () {
            move(1, false);
        }, DWELL);
    }

    function select(tab) {
        var active = document.activeElement;
        tabs.forEach(function (item) {
            var on = item === tab;
            item.setAttribute('aria-selected', on ? 'true' : 'false');
            item.tabIndex = on ? 0 : -1;
        });

        var category = tab.getAttribute('data-category');
        realCards().forEach(function (card) {
            var match = category === 'all' || card.getAttribute('data-category') === category;
            card.hidden = !match;
        });

        if (active && active.closest && active.closest('[data-explore-card]') && active.closest('[data-explore-card]').hidden) {
            tab.focus();
        }

        viewport.setAttribute('aria-labelledby', tab.id);
        layout(false);
        announce(shown().length + (shown().length === 1 ? ' feature' : ' features'));
    }

    tabs.forEach(function (tab, tabIndex) {
        tab.addEventListener('click', function () {
            select(tab);
        });
        tab.addEventListener('keydown', function (event) {
            var nextIndex = null;
            if (event.key === 'ArrowRight') {
                nextIndex = (tabIndex + 1) % tabs.length;
            } else if (event.key === 'ArrowLeft') {
                nextIndex = (tabIndex - 1 + tabs.length) % tabs.length;
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

    function manual(direction) {
        if (!canSlide()) {
            return;
        }
        move(direction, true);
    }

    if (prev) {
        prev.addEventListener('click', function () {
            manual(-1);
        });
    }
    if (next) {
        next.addEventListener('click', function () {
            manual(1);
        });
    }
    if (toggle) {
        toggle.addEventListener('click', function () {
            if (reduced || toggle.getAttribute('aria-disabled') === 'true') {
                return;
            }
            userPaused = !userPaused;
            setToggle();
            start();
        });
    }

    track.addEventListener('transitionend', function (event) {
        if (event.target !== track || event.propertyName !== 'transform') {
            return;
        }
        if (normalize()) {
            apply(false);
        } else {
            moving = false;
            syncFocusable();
        }
    });

    root.addEventListener('mouseenter', function () {
        if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
            return;
        }
        hoverPaused = true;
        stop();
    });
    root.addEventListener('mouseleave', function () {
        hoverPaused = false;
        start();
    });
    root.addEventListener('focusin', function () {
        focusPaused = true;
        stop();
    });
    root.addEventListener('focusout', function () {
        window.setTimeout(function () {
            focusPaused = root.contains(document.activeElement);
            if (!focusPaused) {
                start();
            }
        }, 0);
    });

    var touchX = 0;
    var touchY = 0;
    viewport.addEventListener('touchstart', function (event) {
        if (!event.touches[0]) {
            return;
        }
        touchX = event.touches[0].clientX;
        touchY = event.touches[0].clientY;
        stop();
    }, { passive: true });
    viewport.addEventListener('touchmove', function (event) {
        if (!event.touches[0]) {
            return;
        }
        var dx = event.touches[0].clientX - touchX;
        var dy = event.touches[0].clientY - touchY;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 10) {
            event.preventDefault();
        }
    }, { passive: false });
    viewport.addEventListener('touchend', function (event) {
        var touch = event.changedTouches[0];
        if (!touch) {
            start();
            return;
        }
        var dx = touch.clientX - touchX;
        if (Math.abs(dx) > 48) {
            manual(dx < 0 ? 1 : -1);
        } else {
            start();
        }
    });

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') {
            stop();
        } else {
            start();
        }
    });

    window.addEventListener('resize', function () {
        layout(true);
    });

    if (motionQuery.addEventListener) {
        motionQuery.addEventListener('change', function () {
            reduced = motionQuery.matches;
            layout(true);
        });
    }

    layout(false);
})();
