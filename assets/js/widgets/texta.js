;(function ($) {
    'use strict';

    // SplitType puts every piece inside a .line-text, so masking the line is
    // enough for a reveal — no extra wrapper per word or character.
    const PIECE = {
        lines: '.line-text',
        words: '.word-text',
        chars: '.char-text'
    };

    /**
     * Split the widget text and hand back the pieces to animate.
     *
     * Always splits lines as well, whatever the Animate By setting, because the
     * line is what a reveal clips against.
     */
    function split($scope, by) {

        const targets = $scope.find('h1, h2, h3, h4, h5, h6, p, .elementor-heading-title');

        if (!targets.length) {
            return null;
        }

        const instance = new SplitType(targets.toArray(), {
            types: 'lines, words, chars',
            lineClass: 'line-text',
            wordClass: 'word-text',
            charClass: 'char-text'
        });

        const pieces = $scope.find(PIECE[ by ] || PIECE.words);

        return pieces.length ? { instance: instance, pieces: pieces } : null;
    }

    /**
     * Which axis a direction moves on, and from how far.
     *
     * Percentages of the piece itself, so a reveal always travels exactly its
     * own height or width no matter the font size.
     */
    function offset(direction) {
        switch (direction) {
            case 'top':   return { yPercent: -100 };
            case 'left':  return { xPercent: -100 };
            case 'right': return { xPercent: 100 };
            default:      return { yPercent: 100 };   // bottom
        }
    }

    function timeline(handler, $scope, settings) {
        return handler.addTimeline(gsap.timeline({
            defaults: { ...settings.muiaTl, delay: 0 },
            delay: settings.muiaTl.delay,
            scrollTrigger: {
                trigger: $scope,
                ...settings.muiaTrigger,
                invalidateOnRefresh: true
            }
        }));
    }

    /**
     * Fade — the pieces drift in and up, staggered.
     */
    function buildFade(handler, $scope, settings, parts) {

        const from = offset(settings.textmuia_motion_direction);

        // A fade travels a short way, not the piece's full size.
        Object.keys(from).forEach(function (key) { from[ key ] = from[ key ] * 0.4; });

        timeline(handler, $scope, settings).fromTo(parts.pieces.toArray(),
            { ...from, autoAlpha: 0 },
            {
                xPercent: 0,
                yPercent: 0,
                autoAlpha: 1,
                stagger: settings.muiaTl.stagger,
                force3D: true
            }
        );
    }

    /**
     * Reveal — the pieces slide out from behind their own line.
     */
    function buildReveal(handler, $scope, settings, parts) {

        // The mask. Inline rather than in the stylesheet, so the teardown can
        // clear it and the text reflows normally when the effect is switched
        // off or the widget is rebuilt.
        gsap.set($scope.find('.line-text'), { overflow: 'hidden', display: 'block' });

        timeline(handler, $scope, settings).fromTo(parts.pieces.toArray(),
            offset(settings.textmuia_motion_direction),
            {
                xPercent: 0,
                yPercent: 0,
                stagger: settings.muiaTl.stagger,
                force3D: true
            }
        );
    }

    // One entry per animation type, keyed by the muia_text_ani value. The Pro
    // types are absent on purpose: build() bails when Pro is present and Pro
    // registers its own handlers for them.
    const BUILDERS = {
        'fade':   buildFade,
        'reveal': buildReveal
    };

    /**
     * @param {jQuery} $scope   The widget wrapper.
     * @param {Object} settings Element settings, plus the muia* values getAniSettings adds.
     *
     * `this` is the ExtensionHandler, so addTimeline / addScrollTrigger /
     * addAnimation / addTeardown are available. Whatever is registered there is
     * killed before the next build and on destroy.
     */
    function textAni($scope, settings) {

        $scope.removeClass('visibility__hidden');

        const build = BUILDERS[ settings.muia_text_ani ];

        if (!build) {
            return null;
        }

        const parts = split($scope, settings.muia_text_ani_by);

        if (!parts) {
            return null;
        }

        build(this, $scope, settings, parts);

        // revert() puts the original markup back. Without it every rebuild —
        // and the editor rebuilds on every slider step — splits the already
        // split text again, nesting spans until the markup is unusable.
        return function () {
            gsap.set(parts.pieces.toArray(), { clearProps: 'all' });
            gsap.set($scope.find('.line-text'), { clearProps: 'all' });
            parts.instance.revert();
        };
    }

    muia.initElementorFrontend({
        extensions: {
            'muia-text-ani-yes': textAni
        }
    });

})(jQuery);
