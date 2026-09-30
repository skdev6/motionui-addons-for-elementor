;(function ($) {
    'use strict';

    function imageAni($scope, settings) {  

        const img = $scope.find('img');

        if (settings.muia_img_ani_type === 'reveal') {  

            gsap.set(img, { scale: 1.2, transition: 'none' });
            gsap.set($scope, { transition: 'none' });

            const tl = this.addTimeline(gsap.timeline({
                defaults:{
                    ...settings.muiaTl,
                    delay:0
                },
                delay:settings.muiaTl.delay,
                scrollTrigger: {
                    trigger: $scope,
                    ...settings.muiaTrigger,
                    markers:true
                }
            }));

            tl.to($scope, {
                '--reveal-size': '0%'
            })
            .to(img, { scale: 1 }, '<');
        } 

        $scope.removeClass('visibility__hidden'); 
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
