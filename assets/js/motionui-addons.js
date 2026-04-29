;(function($){
    'use strict';

    gsap.registerPlugin(SplitText, ScrollTrigger);   
    // Init Button
    function button($scope){  
        let btn = $scope.find('.muia-btn');
        let buttonTextElement = $scope.find('.muia-btn-text');
        
        if(btn.hasClass('muia-btn-reveal')){
            var chars = new SplitType(buttonTextElement[0], {types:"chars"}).chars;    
            chars.forEach((el, index)=>{
                gsap.set(el, {'--index':index}); 
            })
        }
    }
    // Text Animations
    function textAnimation($scope){    
        let settings = $scope.data('settings');
        let textElement = $scope.find('.elementor-heading-title');

        if(!textElement.hasClass('muia-split-initialized')){
            textElement.addClass('muia-split-initialized');
            SplitText.create(textElement[0], {type:"chars", charsClass:"char-text"});   
        }
        let aniElements = textElement.find('.char-text'); 
        $scope.removeClass('visibility__hidden');
        gsap.set(aniElements, { opacity: 0, y : 20 });
        gsap.to(aniElements, { opacity: 1, y : 0, stagger: 0.05, ease: 'power4.out' });      
        
    }
    // Slide
    function muiaSlide($scope){
        let currentindex   = 0;
        let isSliding      = false;
        let bgs            = $scope.find('.slide-bg-item');
        let bgMain         = bgs.find('.sb-item');
        let bgsArray       = bgs.toArray();
        let titles         = $scope.find('.slide-title-item');
        let pagiThumbWrap  = $scope.find('.muia-thumb-pagi');
        let pagiThumbs     = pagiThumbWrap.find('.pagi-thumb');
        let prevBtn        = $scope.find('.muia-prev');
        let nextBtn        = $scope.find('.muia-next');
        let paginationDots = $scope.find('.muia-dot-pagi .dot-item');
        let totalSlide     = bgs.length - 1; 

        gsap.set(bgs, {        
            'transition':'none',
            '--cleft':i=> i === 0 ? '0%' : '100%',
            '--cright':i=> i === 0 ? '0%' : '100%',  
            x:(i)=> i === 0 ? '0%' : '50%',   
        });

        function goToSlide(nextIndex, direction){ 
            if(isSliding) return;
            isSliding = true;
            let slideItems = [bgsArray[currentindex], bgsArray[nextIndex]];
            let nextBg = bgMain.toArray()[nextIndex];
            let duration = 1;
            let ease = 'expo.inOut';
            // 
            gsap.set(slideItems, {   
                autoAlpha:1,
                zIndex:i=> i === 0 ? 1 : 0,
                '--cleft': (i) => {
                    return direction === 'prev' ? (i === 0 ? '0%' : '50%') : '0%';
                },

                '--cright': (i) => {
                    return direction === 'next' ? (i === 0 ? '0%' : '50%') : '0%';
                },
                x:(i)=>{
                    if(direction === 'next'){
                        return i === 1 ? '50%' : '0%'
                    }else{
                        return i === 1 ? '-50%' : '0%'
                    }
                }
            });
            gsap.set(nextBg, {scale:1.2});
            gsap.to(nextBg, {scale:1, duration:3, delay:0, ease:'expo.out'});  
            gsap.to(slideItems, {
                '--cleft': (i) => {
                    return direction === 'prev' ? (i === 0 ? '50%' : '0%') : '0%';
                },

                '--cright': (i) => {
                    return direction === 'next' ? (i === 0 ? '50%' : '0%') : '0%';
                },
                x:(i)=>{
                    if(direction === 'next'){
                        return i === 0 ? '-50%' : '0%'
                    }else{
                        return i === 0 ? '50%' : '0%'
                    }
                },
                ease:'expo.out',
                duration,
                scale:1,
                onComplete(){   
                    isSliding = false;  
                    currentindex = nextIndex; 
                }
            });  
            // 
            let gapX = parseInt(pagiThumbWrap.css('column-gap'));
            gsap.to(pagiThumbs, {
                '--x':-nextIndex * 100+'%',
                '--gx':-nextIndex * gapX+'px',
                ease:'expo.out',
                duration,
            })
            gsap.to(titles, {  
                x:-nextIndex * 100+'%',
                ease:'expo.out',
                duration,
            })
            titles.removeClass('active');
            bgs.removeClass('active');
            paginationDots.removeClass('active');
            pagiThumbs.removeClass('active');

            $(titles.toArray()[nextIndex]).addClass('active'); 
            $(bgs.toArray()[nextIndex]).addClass('active');
            $(pagiThumbs.toArray()[nextIndex]).addClass('active');
            $(paginationDots.toArray()[nextIndex]).addClass('active');
        }
        prevBtn.on('click', function(){  
            goToSlide(currentindex === 0 ? totalSlide : currentindex - 1, 'prev');
        });
        nextBtn.on('click', function(){
            goToSlide(currentindex === totalSlide ? 0 : currentindex + 1, 'next');
        });
    }
    /**
     * Initialized all widgets
    */
    const widgets = {
        'muia-animate-button.default':button,
        'muia-animated-slider.default':muiaSlide,
    }
    // init elementor frontend
    $(window).on('elementor/frontend/init', function(){

        $.each(widgets, function(widget, fun){
           elementorFrontend.hooks.addAction('frontend/element_ready/' + widget, fun);
        });
          
        var widgetsAnimation = elementorModules.frontend.handlers.Base.extend({
            onInit: function() {
                if(this.$element.hasClass('has-muia-text-animation')) textAnimation(this.$element);
            },
            onElementChange: function onElementChange(e){
                if(this.$element.hasClass('has-muia-text-animation')) textAnimation(this.$element);
            }
        });
        
        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(widgetsAnimation, {
                $element: $scope
            });
        });
    }); 
    
})(jQuery);