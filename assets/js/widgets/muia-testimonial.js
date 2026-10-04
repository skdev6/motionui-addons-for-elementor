;(function ($) {
    'use strict';

    /**
     * Testimonial.
     *
     * Grid needs no JS; this only runs Swiper for the slide view. The shared
     * Slide Controls trait writes responsive values as CSS custom properties
     * and behaviour into data-slide-settings.
     */

    if (!window.muia || typeof window.muia.initElementorFrontend !== 'function') {
        return;
    }

    window.muia.initElementorFrontend({
        widgets: {
            'themeic-testimonial.default': testimonialSlider
        }
    });

    var running = [];

    function reap() {
        running = running.filter(function (swiper) {
            if (swiper.destroyed) {
                return false;
            }

            if (!document.body.contains(swiper.el)) {
                swiper.destroy(true, true);
                return false;
            }

            return true;
        });
    }

    function readNumber(css, name, fallback) {
        var value = parseFloat(css.getPropertyValue(name));
        return isNaN(value) ? fallback : value;
    }

    function readMs(css, name, fallback) {
        var raw = css.getPropertyValue(name);
        var value = parseFloat(raw);

        if (isNaN(value)) {
            return fallback;
        }

        return raw.indexOf('ms') === -1 && raw.indexOf('s') !== -1 ? value * 1000 : value;
    }

    function getEffect(effect) {
        // Overlap is implemented by the shared slider helper, not by Swiper.
        return effect === 'overlay' ? 'slide' : (effect || 'slide');
    }

    function testimonialSlider($scope) {
        var wrap = $scope.find('.muia-testimonial-slider.themeic-slide-wrapper')[0];
        var el = wrap ? wrap.querySelector('.swiper') : null;

        if (!el || typeof Swiper !== 'function') {
            return;
        }

        reap();

        if (el.swiper) {
            el.swiper.destroy(true, true);
        }

        var settings = $(wrap).data('slideSettings') || {};
        var css = window.getComputedStyle(wrap);
        var effect = getEffect(settings.effect);
        var isFade = effect === 'fade';
        var pagination = wrap.querySelector('.themeic-slide-pagination');
        var paginationType = pagination ? pagination.getAttribute('data-pagination') || 'progressbar' : '';

        var options = {
            speed: readMs(css, '--slide-speed', 300),
            rewind: !!settings.loop,
            spaceBetween: readNumber(css, '--slide-gap', 24),
            slidesPerView: isFade ? 1 : readNumber(css, '--slide-per-view', 3),
            watchOverflow: true,
            a11y: { enabled: true }
        };

        if (isFade) {
            options.effect = 'fade';
            options.fadeEffect = { crossFade: true };
        } else if (effect !== 'slide') {
            options.effect = effect;
        }

        if (settings.autoplay) {
            options.autoplay = {
                delay: settings.autoplayDelay || 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            };
        }

        if (wrap.querySelector('.themeic-slide-prev') && wrap.querySelector('.themeic-slide-next')) {
            options.navigation = {
                nextEl: wrap.querySelector('.themeic-slide-next'),
                prevEl: wrap.querySelector('.themeic-slide-prev')
            };
        }

        if (pagination) {
            options.pagination = {
                el: pagination,
                type: paginationType,
                clickable: paginationType === 'bullets'
            };
        }

        running.push(new Swiper(el, options));
    }

})(jQuery);
