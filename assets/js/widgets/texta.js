;(function ($) {
    'use strict';

    const PIECE = {
        lines: '.muia-text-line',
        words: '.muia-text-word',
        chars: '.muia-text-char'
    };
    function dirOf(settings, fallback) {

        if (settings.muiaDirection && settings.muiaDirection !== 'none') {
            return settings.muiaDirection;
        }

        return settings.orientationmuia_motion_direction || fallback;
    }

    function num(value, fallback) {
        const n = parseFloat(value);
        return Number.isNaN(n) ? fallback : n;
    }

    function stagger(settings, from) {
        return { each: settings.muiaTl.stagger, from: from || 'start' };
    }

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
    
    function buildSmoky(handler, $scope, options) {

        const { textType, parts, settings } = options;

        const offset = num(settings.muia_text_v_offset, 70);
        const blur   = num(settings.muia_text_blur, 70);

        // The control reads as a percentage, 5 to 100, so 70 means 0.7.
        const scale = settings.muia_text_scale_from;

        // GSAP understands start, end, center, edges and random. Anything else
        // would be read as an index, which is not what the panel is offering.
        const FROM = ['start', 'end', 'center', 'edges', 'random'];
        const staggerFrom = FROM.includes(settings.muia_text_stagger_from)
            ? settings.muia_text_stagger_from
            : 'start';

        parts.forEach((part) => {

            const texts = part[ textType ];
            const wrap  = part.elements[0];

            if (!texts || !texts.length) {
                return;
            }

            gsap.set(texts, {
                y: offset,
                scale: scale,
                autoAlpha: 0,
                filter: 'blur(' + blur + 'px)',
                transformOrigin: '50% 50%'
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
                scale: 1,
                autoAlpha: 1,
                filter: 'blur(0px)',
                stagger: stagger(settings, staggerFrom),
                force3D: true
            });
        });

        // filter is not in GSAP's transform set, so clearProps on the pieces
        // does not take it off. Left behind, a stale blur(0px) keeps the text on
        // its own compositing layer and softens the glyphs.
        return function () {
            parts.forEach((part) => {
                const texts = part[ textType ];
                if (texts && texts.length) {
                    gsap.set(texts, { clearProps: 'filter' });
                }
            });
        };
    }

    const BUILDERS = {
        'reveal-alt':     buildAlt,
        'reveal-text':    buildReveal,
        'reveal-smoky':   buildSmoky
    };

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
        if(settings.muia_text_ani === 'reveal-smoky') textTypes = settings.muia_text_ani_by || textTypes;

        const parts = textElements.map((el) => new SplitType(el, {
            types: textTypes,
            lineClass: 'muia-text-line',
            wordClass: 'muia-text-word',
            charClass: 'muia-text-char'
        }));

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
