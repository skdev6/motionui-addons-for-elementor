;(function ($) {
    'use strict';

    /**
     * A SLIDER value as { size, unit }, honouring the responsive variants.
     *
     * The From/To controls are responsive, so the tablet and mobile values live
     * under their own keys; getCurrentDeviceSetting picks the right one.
     */
    function sliderValue(handler, key, fallbackSize, fallbackUnit) {

        const value = handler.getCurrentDeviceSetting
            ? handler.getCurrentDeviceSetting(key)
            : null;

        const size = parseFloat(value && value.size);

        return {
            size: isNaN(size) ? fallbackSize : size,
            unit: (value && value.unit) || fallbackUnit
        };
    }

    /**
     * Reveal and corner reveal.
     *
     * Both drive --reveal-size from 100% to 0%; only the clip-path the
     * stylesheet builds out of it differs, so one builder covers the pair.
     */
    function buildMask(handler, $scope, img, settings) {

        gsap.set(img, { scale: 1.2, transition: 'none', force3D: true });
        gsap.set($scope, { transition: 'none' });

        const tl = handler.addTimeline(gsap.timeline({
            defaults: { ...settings.muiaTl, delay: 0 },
            delay: settings.muiaTl.delay,
            scrollTrigger: {
                trigger: $scope,
                ...settings.muiaTrigger,
                invalidateOnRefresh: true
            },
            onStart: function () {
                gsap.set($scope, { willChange: 'clip-path' });
                gsap.set(img, { willChange: 'transform' });
            },
            onComplete: function () {
                gsap.set([ $scope, img ], { willChange: 'auto' });
            }
        }));

        // fromTo, not to: on a rebuild the start would otherwise be read from
        // computed style, which mid-animation is whatever the last frame wrote.
        tl.fromTo($scope,
            { '--reveal-size': '100%' },
            { '--reveal-size': '0%' }
        )
        .to(img, {
            scale: 1,
            // Longer than the mask, so the image keeps drifting after the
            // reveal lands instead of both stopping together.
            duration: settings.muiaTl.duration * 1.25,
            force3D: true
        }, '<');
    }

    /**
     * Zoom — no mask, the image scales into place.
     */
    function buildZoom(handler, $scope, img, settings) {

        gsap.set(img, { transition: 'none', force3D: true });

        handler.addTimeline(gsap.timeline({
            defaults: { ...settings.muiaTl, delay: 0 },
            delay: settings.muiaTl.delay,
            scrollTrigger: {
                trigger: $scope,
                ...settings.muiaTrigger,
                invalidateOnRefresh: true
            },
            onStart: function () { gsap.set(img, { willChange: 'transform' }); },
            onComplete: function () { gsap.set(img, { willChange: 'auto' }); }
        })).fromTo(img,
            { scale: 1.3 },
            { scale: 1, force3D: true }
        );
    }

    /**
     * Parallax — the image travels while the frame stays put.
     *
     * The axis comes from the second direction picker (left is horizontal, top
     * is vertical) and the distance from the Travel From / Travel To pair.
     * Scrubbed across the element's whole pass through the viewport rather than
     * fired at the Trigger Point, which is what makes it read as parallax.
     */
    function buildParallax(handler, $scope, img, settings) {

        const vertical = settings.img_axismuia_motion_direction === 'top';

        const from = sliderValue(handler, 'imgmuia_motion_from', -15, '%');
        const to   = sliderValue(handler, 'imgmuia_motion_to', 15, '%');

        // GSAP takes percentages of the element through xPercent/yPercent; x
        // and y are always pixels, so the unit decides which property is used.
        const prop = from.unit === '%'
            ? (vertical ? 'yPercent' : 'xPercent')
            : (vertical ? 'y' : 'x');

        gsap.set(img, { transition: 'none', force3D: true, willChange: 'transform' });

        handler.addTimeline(gsap.timeline({
            scrollTrigger: {
                trigger: $scope,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true,
                invalidateOnRefresh: true
            }
        })).fromTo(img,
            { [ prop ]: from.size },
            { [ prop ]: to.size, ease: 'none', force3D: true }
        );
    }

    // One entry per animation type, keyed by the muia_img_ani_type value.
    const BUILDERS = {
        'reveal':        buildMask,
        'corner-reveal': buildMask,
        'zoom':          buildZoom,
        'prallax':       buildParallax
    };

    /**
     * @param {jQuery} $scope   The widget wrapper.
     * @param {Object} settings Element settings, plus the muia* values getAniSettings adds.
     *
     * `this` is the ExtensionHandler, so addTimeline / addScrollTrigger /
     * addAnimation / addTeardown are available. Whatever is registered there is
     * killed before the next build and on destroy.
     */
    function imageAni($scope, settings) {

        $scope.removeClass('visibility__hidden');

        const img   = $scope.find('img');
        const build = BUILDERS[ settings.muia_img_ani_type ];

        if (build && img.length) {
            build(this, $scope, img, settings);
        }

        // clearProps for what the tweens wrote inline — killing a timeline does
        // not put those back.
        return function () {
            gsap.set([ img, $scope ], { clearProps: 'all' });
        };
    }

    muia.initElementorFrontend({
        extensions: {
            'muia-img-ani-yes': imageAni
        }
    });

})(jQuery);
