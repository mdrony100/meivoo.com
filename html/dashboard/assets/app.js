$(function () {
    /* ── AOS ── */
    AOS.init({
        once: true,
        offset: 40,
        duration: 500
    });

    /* ── Mobile sidebar toggle ── */
    $('#sidebarToggle').on('click', function () {
        $('#sidebar').toggleClass('mobile-open');
        $('#sidebarOverlay').toggleClass('show');
    });

    $('#sidebarOverlay').on('click', function () {
        $('#sidebar').removeClass('mobile-open');
        $(this).removeClass('show');
    });

    /* ── Desktop sidebar collapse ── */
    $('#sidebarCollapseBtn').on('click', function () {
        var $sidebar = $('#sidebar');
        var $mainWrapper = $('#mainWrapper');
        var $icon = $(this).find('i');
        var collapsed = !$sidebar.hasClass('is-collapsed');

        $sidebar.toggleClass('is-collapsed', collapsed);
        $mainWrapper.toggleClass('is-sidebar-collapsed', collapsed);

        if (collapsed) {
            $sidebar.css('width', '68px');
            $mainWrapper.css('margin-left', '68px');
            $icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
            $sidebar.find('.sidebar-brand-text, .nav-item-custom span, .nav-badge, .user-name, .user-email').css({
                opacity: 0,
                width: 0,
                overflow: 'hidden'
            });
        } else {
            $sidebar.css('width', 'var(--sidebar-w)');
            $mainWrapper.css('margin-left', 'var(--sidebar-w)');
            $icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
            $sidebar.find('.sidebar-brand-text, .nav-item-custom span, .nav-badge, .user-name, .user-email').css({
                opacity: '',
                width: '',
                overflow: ''
            });
        }
    });

    /* ── Comment toggle ── */
    $(document).on('click', '.btn-toggle-comments', function () {
        var targetId = $(this).data('target');
        $('#' + targetId).toggleClass('show');
    });

    /* ── React toggle ── */
    $(document).on('click', '.btn-react-toggle', function () {
        var $btn = $(this);
        var $icon = $btn.find('i').first();
        var reacted = !$btn.hasClass('is-reacted');

        $btn.toggleClass('is-reacted', reacted);
        $icon.toggleClass('fa-regular', !reacted).toggleClass('fa-solid', reacted);
    });

    /* ── Native share ── */
    $(document).on('click', '.btn-share', function () {
        var $btn = $(this);
        var postText = $btn.closest('.post-card').find('.post-body').text().trim();

        if (navigator.share) {
            navigator.share({
                title: 'Family Legacy – A Family Memory',
                text: postText,
                url: window.location.href
            }).catch(function () {});
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(window.location.href).then(function () {
                var originalHtml = $btn.html();
                $btn.html('<i class="fa-solid fa-check fa-sm"></i> Copied!');
                setTimeout(function () {
                    $btn.html(originalHtml);
                }, 2000);
            }).catch(function () {});
        }
    });

    /* ── Compose textarea auto-expand ── */
    $('#composeInput').on('focus', function () {
        $(this).css({
            minHeight: '90px',
            height: 'auto'
        }).attr('rows', 4);
    });

    $('#composeInput').on('blur', function () {
        if (!$.trim($(this).val())) {
            $(this).css({
                minHeight: '42px',
                height: ''
            }).attr('rows', 1);
        }
    });

    $('#composeInput').on('input', function () {
        $(this).css('height', 'auto');
        this.style.height = this.scrollHeight + 'px';
    });

    /*  Family Tree page  */
    if ($('#treeInner').length) {
        var $treeInner = $('#treeInner');
        var scale = 1;
        var activeNode = null;

        function setActiveNode($node) {
            if (activeNode && activeNode.length && (!$node || !activeNode.is($node))) {
                activeNode.removeClass('open');
            }

            activeNode = $node && $node.length ? $node : null;

            if (activeNode) {
                activeNode.addClass('open');
            }
        }

        $(document).on('click', '.tree-inner .node-col', function () {
            setActiveNode($(this));
        });

        $(document).on('keydown', '.tree-inner .node-col', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                $(this).trigger('click');
            }
        });

        $('#memberOffcanvas').on('hidden.bs.offcanvas', function () {
            setActiveNode(null);
        });
    }
});

// Family Gallery Lightbox
(function ($) {
$.fn.familyGalleryLightbox = function (options) {
var settings = $.extend({
    overlay: '#familyPhotoLightbox'
}, options || {});

return this.each(function () {
    var $gallery = $(this);
    var $overlay = $(settings.overlay);
    var $items = $gallery.find('[data-family-photo]');
    var $image = $overlay.find('.gallery-lightbox-image');
    var currentIndex = 0;

    function showItem(index) {
    if (!$items.length) {
        return;
    }

    currentIndex = (index + $items.length) % $items.length;

    var $item = $items.eq(currentIndex);
    $image.attr({
        src: $item.attr('href'),
        alt: $item.find('img').attr('alt') || $item.data('title') || 'Photo'
    });
    }

    function open(index) {
    showItem(index);
    $overlay.addClass('show').attr('aria-hidden', 'false');
    $('body').css('overflow', 'hidden');
    }

    function close() {
    $overlay.removeClass('show').attr('aria-hidden', 'true');
    $('body').css('overflow', '');
    }

    $gallery.on('click', '[data-family-photo]', function (event) {
    event.preventDefault();
    open($items.index(this));
    });

    $overlay.on('click', '[data-lightbox-close], [data-lightbox-backdrop]', function () {
    close();
    });

    $overlay.on('click', '[data-lightbox-prev]', function (event) {
    event.preventDefault();
    showItem(currentIndex - 1);
    });

    $overlay.on('click', '[data-lightbox-next]', function (event) {
    event.preventDefault();
    showItem(currentIndex + 1);
    });

    $(document).on('keydown.familyGalleryLightbox', function (event) {
    if (!$overlay.hasClass('show')) {
        return;
    }

    if (event.key === 'Escape') {
        close();
    } else if (event.key === 'ArrowLeft') {
        showItem(currentIndex - 1);
    } else if (event.key === 'ArrowRight') {
        showItem(currentIndex + 1);
    }
    });
});
};
})(jQuery);

$(function () {
$('#tab-photos .photos-grid').familyGalleryLightbox();
});
