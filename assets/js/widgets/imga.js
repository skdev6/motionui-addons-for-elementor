;(function ($) {
    'use strict';

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
                gsap.set($scope, { willChange: 'clip-path', overflow: 'hidden' });
                gsap.set(img, { willChange: 'transform' });
            },
            onComplete: function () {
                gsap.set([ $scope, img ], { willChange: 'auto', overflow: '' });
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
            duration: settings.muiaTl.duration * 1.25,
            force3D: true
        }, '<');
    }
    /**
     * Tiles reveal.
     *
     * One image cut into --ts slices, each a div with the same background
     * offset to its own share, then staggered in. The layout lives in
     * image-ani.scss; this sets the two custom properties it reads and runs the
     * motion.
     */
    function buildTiles(handler, $scope, img, settings) {  

        let direction = settings.muiaDirection || 'top'; 
        let tiles     = Math.max(2, parseInt(settings.img_muia_num_of_tiles, 10) || 5);
        // left, right and center-h cut into columns; the rest cut into rows.
        const horizontal = direction === 'left' || direction === 'right' || direction === 'center-h';


        const $wrap = $('<div class="muia-img-tiles-wrap"></div>').css('--ts', tiles);
        const src   = img.attr('src');

        for (let i = 0; i < tiles; i++) {
            $('<div class="muia-img-tile"></div>')
                .css({
                    'background-image': 'url("' + src + '")',
                    '--p': (i / (tiles - 1)) * 100,
                    '--index': i
                })
                .appendTo($wrap);
        }

        $scope.append($wrap);

        const $tiles = $wrap.find('.muia-img-tile');

        // The tiles carry the picture now, so the image underneath only has to
        // hold the box open. Set from here rather than the stylesheet, so a
        // widget whose script never runs still shows its image.
        gsap.set(img, { autoAlpha: 0 });

        const origin = {
            'left':     'left center',
            'right':    'right center',
            'top':      'center top',
            'bottom':   'center bottom',
            'center-h': 'center center',
            'center-v': 'center center'
        }[ direction ] || 'center center';

        // Which end the stagger starts from, so the tiles sweep the same way
        // the direction reads.
        const from = (direction === 'right' || direction === 'bottom') ? 'end'
            : (direction === 'center') ? 'center'
            : 'start';

        const axis = horizontal ? 'scaleX' : 'scaleY';

        gsap.set($tiles.toArray(), { transformOrigin: origin, force3D: true });

        handler.addTimeline(gsap.timeline({
            defaults: { ...settings.muiaTl, delay: 0 },
            delay: settings.muiaTl.delay,
            scrollTrigger: {
                trigger: $scope,
                ...settings.muiaTrigger,
                invalidateOnRefresh: true
            }
        })).fromTo($tiles.toArray(),
            { [ axis ]: 0 },
            {
                [ axis ]: 1,
                force3D: true,
                stagger: { each: settings.muiaTl.stagger, from: from }
            }
        );

        // The wrapper is positioned, not laid out, so it has no size of its
        // own — it takes the image's.
        function adjustDimansions() {    
            $wrap.css({ width: img.width() + 'px', height: img.height() + 'px' });
        }

        adjustDimansions();
        $(window).on('resize.muiaTiles', adjustDimansions);

        // The image may still be loading, in which case width() was 0 above.
        img.one('load', adjustDimansions);

        return () => {
            $(window).off('resize.muiaTiles', adjustDimansions);
            img.off('load', adjustDimansions);
            // Remove what was injected, or every rebuild stacks another wrapper
            // of tiles on top of the last.
            $wrap.remove();
            gsap.set(img, { clearProps: 'all' });
        };
    }
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
            { scale: 0, transformOrigin:settings.muiaDirection === "center-v" || settings.muiaDirection === "center-h" ? "center" : settings.muiaDirection },
            { scale: 1, force3D: true }
        );
    }

    function buildParallax(handler, $scope, img, settings) {

        const vertical = settings.img_axismuia_motion_direction === 'top';

        const from = $scope.css('--muia-from') || '0%';
        const to   = $scope.css('--muia-to')   || '0%';
        
        const axis  = vertical ? 'y' : 'x';
        const fromV = { [ axis ]: from };
        const toV   = { [ axis ]: to };

        gsap.set(img, { transition: 'none', force3D: true, willChange: 'transform' });

        handler.addTimeline(gsap.timeline({
            scrollTrigger: {
                trigger: $scope,
                ...settings.muiaTrigger,
                end: 'bottom top',
                scrub: true,
                invalidateOnRefresh: true
            }
        })).fromTo(img,
            fromV,
            { ...toV, ease: 'none', force3D: true }
        );
    }

    const BUILDERS = {
        'reveal':        buildMask,
        'corner-reveal': buildMask,
        'poly-reveal':   buildMask,
        'circle-reveal':   buildMask,
        'tiles-reveal':   buildTiles,
        'zoom':          buildZoom,
        'prallax':       buildParallax
    };

    function imageAni($scope, settings) {    
        $scope.removeClass('visibility__hidden');

        if(!settings.isDesktop && !settings.isMobile) return; 
        
        const img   = $scope.find('img');
        const build = BUILDERS[ settings.muia_img_ani_type ];

        // A builder may hand back cleanup of its own: injected markup, a
        // listener, a scene. Dropping it meant buildTiles left a wrapper
        // behind on every rebuild.
        let cleanup = null;

        if (build && img.length) {
            cleanup = build(this, $scope, img, settings);
        }

        // clearProps for what the tweens wrote inline — killing a timeline does
        // not put those back.
        return function () {
            if (typeof cleanup === 'function') { cleanup(); }

            gsap.set([ img, $scope ], { clearProps: 'all' });
        };
    }

    muia.initElementorFrontend({
        extensions: {
            'muia-img-ani-yes': imageAni
        }
    });

})(jQuery);
