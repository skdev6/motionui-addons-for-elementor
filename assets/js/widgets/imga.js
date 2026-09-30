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
            duration: settings.muiaTl.duration * 1.25,
            force3D: true
        }, '<');
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
            { scale: .3, transformOrigin:settings.muiaDirection, opacity:0 },
            { scale: 1, force3D: true, opacity:1 }
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
        'zoom':          buildZoom,
        'prallax':       buildParallax
    };

    function imageAni($scope, settings) {    
        $scope.removeClass('visibility__hidden');

        if(!settings.isDesktop && !settings.isMobile) return; 
        
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
