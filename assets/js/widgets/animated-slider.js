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
        let dotPagi = $scope.find('.muia-dot-pagi')[0];
        let thumbPagiItems = $scope.find('.pagi-thumb');
        let titleWrap = $scope.find('.title-wrapper');
        let titles = titleWrap.find('.slide-title-item');
        let innerTitles = titles.find('.inner-title');
        let titlesOffsets = [];
        function titleFW(){
            titleWrap.css('--tf-w', titleWrap.find('.slide-title-item').eq(0).innerWidth()+'px');
            titlesOffsets = [];
            titles.each(function(){ 
                let halfw = ((titleWrap.innerWidth() - $(this).innerWidth()) / 2);  
                let mainOffset = titleWrap.offset().left - $(this).offset().left;
                titlesOffsets.push( mainOffset + halfw);   
            });
        }
        function setActiveTitle(index){
            titles.removeClass('active').eq(index).addClass('active');
        }
        var activeThumb = -1;
        function setActiveThumb(position){
            $scope.css('--tx', (position * -100) + '%');
            var index = Math.round(position);
            if (index === activeThumb) return;

            activeThumb = index;
            thumbPagiItems.removeClass('active').eq(index).addClass('active');
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
        ...(dotPagi ? {
            pagination: {
                el: dotPagi,
                clickable: true,
                bulletClass: 'dot-item',
                bulletActiveClass: 'active',
                modifierClass: 'muia-dot-pagi--',
                horizontalClass: 'muia-dot-pagi--horizontal',
                verticalClass: 'muia-dot-pagi--vertical',
                clickableClass: 'muia-dot-pagi--clickable',
                lockClass: 'muia-dot-pagi--lock',
                hiddenClass: 'muia-dot-pagi--hidden',
                renderBullet: function(index, className){
                    return '<div class="' + className + '"><span></span></div>';
                }
            }
        } : {}),
        on: {
            init: function(swiper){
                titleFW();
                setActiveTitle(swiper.realIndex);
            },
            slideChange: function(swiper){
                setActiveTitle(swiper.realIndex);
            },
            progress: function(swiper) {

                var titleTranslate = 0;
                var titleWeight    = 0;
                var thumbPosition  = 0;
                var thumbWeight    = 0;

                for (var i = 0; i < swiper.slides.length; i++) {
                    var slide         = swiper.slides[i];
                    var slideProgress = slide.progress;
                    var innerOffset   = swiper.width * interleaveOffset;
                    var innerTranslate = slideProgress * innerOffset;

                    slide.querySelector(".slide-bg-item").style.transform =
                    "translate3d(" + innerTranslate + "px, 0, 0)";

                    var weight = 1 - Math.abs(slideProgress);
                    if (weight <= 0) continue;

                    var realIndex = slide.getAttribute('data-swiper-slide-index');
                    realIndex = realIndex === null ? i : parseInt(realIndex, 10);

                    // Blending the index itself gives the fractional position
                    // of the strip, on the same weights the titles use.
                    thumbPosition += realIndex * weight;
                    thumbWeight   += weight;

                    // Kept separate: a widget with no titles still has thumbs.
                    var offset = titlesOffsets[realIndex];
                    if (typeof offset !== 'number') continue;

                    titleTranslate += offset * weight;
                    titleWeight    += weight;
                }

                if (titleWeight > 0) {
                    innerTitles.css('transform', 'translateX(' + (titleTranslate / titleWeight) + 'px)');
                }

                if (thumbWeight > 0) {
                    setActiveThumb(thumbPosition / thumbWeight);
                }
            },
            touchStart: function(swiper) {
                for (var i = 0; i < swiper.slides.length; i++) {
                    swiper.slides[i].style.transition = "";
                    swiper.slides[i].querySelector(".slide-bg-item").style.transition = "";
                }

                innerTitles.css('transition', '');
            },
            setTransition: function(swiper, speed) {
                for (var i = 0; i < swiper.slides.length; i++) {
                    swiper.slides[i].style.transition = speed + "ms";
                    swiper.slides[i].querySelector(".slide-bg-item").style.transition =
                    speed + "ms";
                }
                innerTitles.css('transition', 'transform ' + speed + 'ms');
                $scope.css('--txspeed', speed + 'ms');
            }
        }
        };

        var swiper = new Swiper(swiperEl, swiperOptions);

        thumbPagiItems.on('click', function(){
            var index = parseInt($(this).attr('data-go'), 10);
            if (isNaN(index)) return;
            swiper.slideToLoop(index);
        });
    }
    
})(jQuery);