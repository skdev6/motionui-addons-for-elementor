;(function ($) {
    'use strict';

    const PIECE = {
        lines: '.muia-text-line',
        words: '.muia-text-word',
        chars: '.muia-text-char'
    };

    /**
     * The direction for this animation.
     *
     * getAniSettings resolves muiaDirection against the `text` prefix, so the
     * Slide picker arrives there. Text Reveal registers its own under
     * `orientation`, which that scope cannot see, so it is read by name.
     */
    function dirOf(settings, fallback) {

        if (settings.muiaDirection && settings.muiaDirection !== 'none') {
            return settings.muiaDirection;
        }

        return settings.orientationmuia_motion_direction || fallback;
    }

    /**
     * The elements to animate, from the Animate By setting.
     *
     * Everything is always split into lines, words and chars, whatever is being
     * animated — the line is what the masked effects clip against, and without
     * it there is nothing to hide the travel behind.
     */
    function piecesOf($scope, by) {
        return $scope.find(PIECE[ by ] || PIECE.words).toArray();
    }

    /**
     * Clip each line, so a piece travelling its own height disappears behind it.
     * Inline rather than in the stylesheet, so the teardown can lift it and the
     * text reflows normally.
     */
    function maskLines($scope) {

        const lines = $scope.find('.muia-text-line').toArray();

        gsap.set(lines, { overflow: 'hidden', display: 'block' });

        return function () {
            gsap.set(lines, { clearProps: 'overflow,display' });
        };
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

    function stagger(settings, from) {
        return { each: settings.muiaTl.stagger, from: from || 'start' };
    }

    /* ------------------------------------------------------------------
     * The animations. Each takes the handler, the scope, the settings and the
     * pieces, and may return cleanup of its own.
     * ---------------------------------------------------------------- */

    /** Slide — the pieces travel in from one edge, masked by their line. */
    function buildSlide(handler, $scope, settings, pieces) {

    }

    /**
     * Alternative Reveal — even and odd pieces drop in from opposite offsets.
     *
     * Vertical only, and no mask: the two offsets are the whole effect. They are
     * used exactly as the panel gives them, signs included, so Even 50px against
     * Odd -50px has alternate pieces rising and falling into the same line.
     */
    function buildAlt(handler, $scope, options) {

        const {  parts, settings, from, to } = options;

        parts.forEach((part) => {

            const wrap  = part.elements[0];
            const texts = wrap.querySelectorAll('.muia-text-char'); 

            if (!texts || !texts.length) {
                return;
            }

            // Index 0 counts as even, so the first piece takes the Even offset.
            gsap.set(texts, {
                y: (i) => (i % 2 === 0 ? from : to),
                opacity: 0
            });

            handler.addTimeline(gsap.timeline({
                defaults: { ...settings.muiaTl, delay: 0 },
                delay: settings.muiaTl.delay,
                scrollTrigger: {
                    trigger: wrap,
                    ...settings.muiaTrigger,
                    invalidateOnRefresh: true
                }
            }))
            .to(texts, {
                y: 0,
                opacity: 1,
                stagger: stagger(settings),
                force3D: true
            });
        });
    }
    /** Text Reveal — the classic masked rise, with its own orientation. */
    function buildReveal(handler, $scope, options) {

        const { textType, parts, settings, from, to } = options;

        const direction  = dirOf(settings, 'bottom');
        const isMask     = settings.muia_text_mask === 'yes';
        const horizontal = direction === 'left' || direction === 'right';
        const axis       = horizontal ? 'x' : 'y';

        const spaceFrom = isMask ? '100%' : from;
        const spaceTo   = isMask ? '0%'   : to;

        const start = (direction === 'left' || direction === 'bottom')
            ? spaceFrom
            : '-' + spaceFrom;

        parts.forEach((part) => {

            const texts = part[ textType ];
            const wrap  = part.elements[0];

            if (!texts || !texts.length) {
                return;
            }

            if (isMask) {
                $(texts).wrap('<span class="muia-text-mask"></span>');

                gsap.set($(wrap).find('.muia-text-mask').toArray(), {
                    display: 'inline-block',
                    overflow: 'hidden',
                    verticalAlign: 'top'
                });
            }

            gsap.set(texts, {
                [ axis ]: start,
                opacity: isMask ? 1 : 0
            });

            handler.addTimeline(gsap.timeline({
                defaults: { ...settings.muiaTl, delay: 0 },
                delay: settings.muiaTl.delay,
                scrollTrigger: {
                    trigger: wrap,
                    ...settings.muiaTrigger,
                    invalidateOnRefresh: true
                }
            }))
            .to(texts, {
                [ axis ]: spaceTo,
                opacity: 1,
                stagger: stagger(settings, direction === 'right' ? 'end' : 'start'),
                force3D: true
            });
        });
    }
    /** Smoky Reveal — the pieces resolve out of a blur as they drift up. */
    function buildSmoky(handler, $scope, options) {

        
    }
    /** Popup Reveal — each piece springs up off its own baseline. */
    function buildPopup(handler, $scope, options) {

    }

    /** Mixing Reveal — the pieces scatter in from everywhere and settle. */
    function buildMixing(handler, $scope, options) {
        
    }

    /** Scale — the pieces settle down out of being oversized. */
    function buildScale(handler, $scope, options) {

        const { settings, pieces } = options;

        gsap.set(pieces, { transformOrigin: '50% 50%' });

        timeline(handler, $scope, settings).fromTo(pieces,
            { autoAlpha: 0, scale: 1.8 },
            { autoAlpha: 1, scale: 1, stagger: stagger(settings), force3D: true }
        );
    }

    /** Text Flip — the pieces swing down into place on their top edge. */
    function buildFlip(handler, $scope, options) {
                
    }

    const BUILDERS = {
        'slide':          buildSlide,
        'reveal-alt':     buildAlt,
        'reveal-text':    buildReveal,
        'reveal-smoky':   buildSmoky,
        'reveal-popup':   buildPopup,
        'reveal-mixing':  buildMixing,
        'reveal-scale':   buildScale,
        'reveal-flip':    buildFlip
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

        const textElements = $scope.find('h1, h2, h3, h4, h5, h6, p').toArray();

        if (!textElements.length) {
            return null;
        }

        let textTypes = 'words, lines, chars';

        if(settings.muia_text_ani === 'reveal-text') textTypes = settings.muia_text_ani_by || textTypes;

        const parts = textElements.map((el) => new SplitType(el, {
            types: textTypes,
            lineClass: 'muia-text-line',
            wordClass: 'muia-text-word',
            charClass: 'muia-text-char'
        }));
        
        console.log(settings, parts);

        const from = $scope.css('--muia-from') || '50px';  
        const to   = $scope.css('--muia-to')   || '0px';  

        const cleanup = build(this, $scope, {textType:textTypes.split(',')[0],textTypes, parts, settings, from, to});    

        return function () {   
            if (typeof cleanup === 'function') { cleanup(); }

            parts.forEach((part) => part.revert());
        };
    }

    muia.initElementorFrontend({
        extensions: {
            'muia-text-ani-yes': textAni
        }
    });

})(jQuery);
