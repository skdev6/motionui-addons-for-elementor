;(function ($) {
    'use strict';

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

        const img      = $scope.find('img');
        const duration = settings.muiaTl.duration;

        if (settings.muia_img_ani_type === 'reveal') {
            gsap.set(img, { scale: 1.2, transition: 'none', force3D: true });
            gsap.set($scope, { transition: 'none' });

            const tl = this.addTimeline(gsap.timeline({
                defaults: {
                    ...settings.muiaTl,
                    delay: 0
                },
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
            tl.fromTo($scope,
                { '--reveal-size': '100%' },
                { '--reveal-size': '0%' }
            )
            .to(img, {
                scale: 1,
                duration: duration * 1.25,
                force3D: true
            }, '<');
        }
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
