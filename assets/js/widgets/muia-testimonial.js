;(function ($) {
    'use strict';

    /**
     * Testimonial.
     *
     * Grid needs no JS — it is CSS grid and the panel sets the columns. This
     * only runs the Swiper for the slide view.
     *
     * Every option comes off the element itself: the Slide Controls trait marks
     * its controls frontend_available, so Elementor puts them in data-settings
     * and nothing has to be repeated in the markup.
     */

    if (!window.muia || typeof window.muia.initElementorFrontend !== 'function') {
        return;
    }

    window.muia.initElementorFrontend({
        widgets: {
            'themeic-testimonial.default': testimonialSlider
        }
    });

    // Where Elementor's own breakpoints land, so a slide count set for Tablet
    // in the panel changes at the width the panel means.
    var TABLET = 768;
    var DESKTOP = 1025;

    // A widget callback gets no teardown of its own, and the editor throws the
    // whole widget away on every change — so an autoplaying instance would sit
    // there ticking against a node that is no longer in the page. They are
    // kept here and the detached ones are shut down the next time one starts.
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

    function testimonialSlider($scope) {

        var el = $scope.find('.muia-testimonial-slider .swiper')[0];

        if (!el || typeof Swiper !== 'function') {
            return;
        }

        reap();

        // An earlier instance on the same node would fight this one for the
        // wrapper's transform.
        if (el.swiper) {
            el.swiper.destroy(true, true);
        }

        var settings = $scope.data('settings') || {};
        var p = 'muia_slide_';

        // Sliders and numbers arrive in different shapes, and an untouched
        // control arrives as an empty string.
        function num(value, fallback) {
            if (value && typeof value === 'object' && 'size' in value) {
                value = value.size;
            }
            value = parseFloat(value);
            return isNaN(value) ? fallback : value;
        }

        function perView(suffix, fallback) {
            return num(settings['themeic_testimonial_per_view' + suffix], fallback);
        }

        var navigation = settings[p + 'navigation'] || 'arrow';
        var wantsArrows = navigation === 'arrow' || navigation === 'both';
        var wantsDots = navigation === 'dots' || navigation === 'both';

        var isFade = settings[p + 'slides_transition'] === 'fade';
        var space = num(settings.themeic_testimonial_space, 24);

        var options = {
            direction: settings[p + 'vertical'] === 'yes' ? 'vertical' : 'horizontal',
            speed: num(settings[p + 'speed'], 300),
            // Infinite Loop is run as `rewind`, not `loop`. The bundled Swiper
            // (14.2.0) will not advance past the second slide with loop on —
            // measured with a bare instance and no options of ours, at every
            // slide count and slides-per-view. Rewind cycles 0>1>2>0 and is
            // the nearest thing that works; swap it back once Swiper is fixed.
            rewind: settings[p + 'loop'] === 'yes',
            spaceBetween: space,
            // Mobile first, then the two breakpoints above it.
            slidesPerView: isFade ? 1 : perView('_mobile', 1),
            watchOverflow: true,
            a11y: { enabled: true }
        };

        // Fade cross-fades one slide over another, so more than one in view
        // would stack them on top of each other.
        if (isFade) {
            options.effect = 'fade';
            options.fadeEffect = { crossFade: true };
        } else {
            options.breakpoints = {};
            options.breakpoints[TABLET] = { slidesPerView: perView('_tablet', 2), spaceBetween: space };
            options.breakpoints[DESKTOP] = { slidesPerView: perView('', 3), spaceBetween: space };
        }

        if (settings[p + 'autoplay'] === 'yes') {
            options.autoplay = {
                // The panel asks for seconds.
                delay: num(settings[p + 'autoplay_speed'], 2) * 1000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true
            };
        }

        if (wantsArrows) {
            options.navigation = {
                nextEl: $scope.find('.muia-testimonial-arrow.muia-next')[0],
                prevEl: $scope.find('.muia-testimonial-arrow.muia-prev')[0]
            };
        }

        if (wantsDots) {
            options.pagination = {
                el: $scope.find('.muia-testimonial-pagination')[0],
                clickable: true
            };
        }

        running.push(new Swiper(el, options));
    }

})(jQuery);
