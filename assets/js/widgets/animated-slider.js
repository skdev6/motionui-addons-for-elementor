;(function($){
    'use strict';


    /**
     * Initialized all widgets
    */
    const widgets = {
        'themeic-animated-slider.default':animatedSlide, 
    }
    // init elementor frontend
    $(window).on('elementor/frontend/init', function(){
        $.each(widgets, function(widget, fun){
           elementorFrontend.hooks.addAction('frontend/element_ready/' + widget, fun);
        });
    }); 

    function animatedSlide($scope){

        let swiperEl = $scope.find('.slide-bg-wrap .swiper')[0];
        let next = $scope.find(".muia-next")[0];
        let prev = $scope.find(".muia-prev")[0];
        let titleWrap = $scope.find('.title-wrapper');
        let titles = titleWrap.find('.slide-title-item');
        
        function titleFW(){
            titleWrap.css('--tf-w', titleWrap.find('.slide-title-item').eq(0).innerWidth()+'px');
        }
        titleFW();
        $(window).on('resize', titleFW); 

        var interleaveOffset = 0.5;

        var swiperOptions = {
        loop: true,
        speed: 1000,
        grabCursor: true,
        watchSlidesProgress: true,
        mousewheelControl: true,
        keyboardControl: true,
        navigation: {
            nextEl: next,
            prevEl:prev 
        },
        on: {
            progress: function() {
                var swiper = this;
                for (var i = 0; i < swiper.slides.length; i++) {
                    var slideProgress = swiper.slides[i].progress;
                    var innerOffset = swiper.width * interleaveOffset;
                    var innerTranslate = slideProgress * innerOffset;
                    swiper.slides[i].querySelector(".slide-bg-item").style.transform =
                    "translate3d(" + innerTranslate + "px, 0, 0)";

                    let currentTitle = $(titles.toArray()[i]);
                    
                    if (currentTitle.length) {
                        let titleOriginalLeft = currentTitle.offset().left - (currentTitle.css('transform') === 'none' ? 0 : parseFloat(currentTitle.css('transform').split(',')[4] || 0));
                        let centeredLeftPosition = containerCenter - (currentTitle.outerWidth() / 2);
                        
                        let baseCenterTranslate = centeredLeftPosition - titleOriginalLeft;
                        
                        let progressTranslate = baseCenterTranslate - (slideProgress * swiper.width * 0.5); 

                        currentTitle.css('transform', 'translateX(' + progressTranslate + 'px)');
                    }
                }      
            },
            touchStart: function(swiper) {
                for (var i = 0; i < swiper.slides.length; i++) {
                    swiper.slides[i].style.transition = "";
                }
            },
            setTransition: function(swiper, speed) {
                for (var i = 0; i < swiper.slides.length; i++) {
                    swiper.slides[i].style.transition = speed + "ms";
                    swiper.slides[i].querySelector(".slide-bg-item").style.transition =
                    speed + "ms";
                }
            }
        }
        };

        var swiper = new Swiper(swiperEl, swiperOptions);
    }
    
})(jQuery);