var comparisonInitialized = false;

function initAOS() {
    if (typeof AOS === 'undefined') {
        return;
    }

    AOS.init({
        once: true,
        offset: 60,
        disable: function () {
            return window.innerWidth < 768;
        }
    });
}

function initComparison() {
    if (comparisonInitialized || typeof window.jQuery === 'undefined' || !$.fn.twentytwenty) {
        return;
    }

    $('.before_after').each(function () {
        $(this).twentytwenty();
    });

    comparisonInitialized = true;
}

function imageLoaded(img) {
    img.style.display = 'block';

    var container = img.closest('.loader-container');
    if (!container) {
        return;
    }

    var images = container.querySelectorAll('.before_after img');
    var allLoaded = true;

    images.forEach(function (image) {
        if (!image.complete) {
            allLoaded = false;
        }
    });

    if (allLoaded) {
        var loader = container.querySelector('.loader');
        if (loader) {
            loader.style.display = 'none';
        }

        initComparison();
    }
}

window.imageLoaded = imageLoaded;

initAOS();

$(window).on('scroll', function () {
    if ($(this).scrollTop() > 40) {
        $('.navbar-custom').css('box-shadow', '0 4px 24px rgba(124,58,237,0.12)');
    } else {
        $('.navbar-custom').css('box-shadow', '0 2px 20px rgba(124,58,237,0.06)');
    }
});

$(function () {
    document.querySelectorAll('.before_after img').forEach(function (img) {
        if (img.complete) {
            imageLoaded(img);
        }
    });
});
