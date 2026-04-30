;(function($){
    'use strict';

    gsap.registerPlugin(SplitText, ScrollTrigger);   
    

    function initScrollTrigger(trigger, settings){   
        let start = settings?.isWithScroll ? 'top 98%' : "top 80%";
        let end = settings?.isWithScroll ? 'top 15%' : "+=100%";
        return{   
            trigger,
            start,
            end,
            scrub:settings?.isWithScroll ?? false
        }
    }
    function getAniSettings( settings, prefix = '', degaultDuration = 1, degaultDelay = 0, degaultEase = 'expo.out', degaultStagger = 0.01 ) {
        const toNum = ( val, fallback ) => {
            const n = parseFloat( val?.size ?? val );
            return isNaN( n ) ? fallback : n;
        };
        console.log(settings);
        
        return {
            duration:     toNum( settings[ prefix + 'mui_motion_duration' ], degaultDuration ),
            delay:        toNum( settings[ prefix + 'mui_motion_delay' ], degaultDelay ),
            stagger:      toNum( settings[ prefix + 'mui_motion_stagger' ], degaultStagger ),
            ease:         settings[ prefix + 'mui_motion_ease' ]        || degaultEase,
            isWithScroll: settings[ prefix + 'mui_motion_with_scroll' ] === 'yes',
        };
    }
    function afterLoad(fun){
        fun(); 
    }
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
    function textAnimation( $scope, settings ) {
        let textElement  = $scope.find( '.elementor-heading-title' );
        let aniSettings  = getAniSettings( settings );

        if ( ! textElement.hasClass( 'muia-split-initialized' ) ) {
            textElement.addClass( 'muia-split-initialized' );
            SplitText.create( textElement[0], { type: 'chars', charsClass: 'char-text' } );
        }

        let aniElements = textElement.find( '.char-text' );
        $scope.removeClass( 'visibility__hidden' );

        gsap.set( aniElements, { opacity: 0, y: 20 } );

        afterLoad(function(){
            gsap.to( aniElements, {
                opacity:  1,
                y:        0,
                stagger:  aniSettings.stagger || 0.05,
                ease:     aniSettings.ease    || 'power4.out',
                duration: aniSettings.duration,
                delay:    aniSettings.delay,
            });
        });
    }
    
    function imageAnimation($scope, settings){    
        // console.log(settings);
        let imgElement = $scope.find('img');
        let aniSettings = getAniSettings(settings, 'img', 1, 0, 'expo.out', 0.05);
        let wrap = $scope.find('.muia-ani-wrap');
        let direction = settings?.muia_ani_direction;

        if(!wrap.length) imgElement.wrap('<div class="muia-ani-wrap"></div>');
        $scope.removeClass('visibility__hidden');

        wrap = $scope.find('.muia-ani-wrap');

        gsap.set(wrap, {
            'overflow':'hidden',
            'display':'inline-block',
        })
        
        let isColumn = settings?.muia_img_ani_type === 'column-reveal';
        let isGrid   = settings?.muia_img_ani_type === 'grid-reveal';
        let isReveal   = settings?.muia_img_ani_type === 'reveal';

        // Determine animation starting values based on direction
        let fromVars = {};
        let revealToEmpty = true;
        switch (direction) { // ← fixed typo  
            case 'ltr':
                if(!isReveal){
                    if (!isColumn) fromVars['--cb'] = '100%';
                    fromVars['--cr'] = '100%';
                }else{
                    fromVars['--cl1'] = '0%';
                    fromVars['--cl2'] = '0%';
                    revealToEmpty = false
                }
                break;

            case 'rtl':
                if(!isReveal){
                    if (!isColumn) fromVars['--cb'] = '100%';
                    fromVars['--cl'] = '100%';
                }else{
                    fromVars['--cr1'] = '100%';
                    fromVars['--cr2'] = '100%';
                    revealToEmpty = true
                }
                break;

            case 'btt':   // bottom to top
                if(!isReveal){
                    fromVars['--ct'] = '100%';
                }else{
                    fromVars['--cb1'] = '100%';
                    fromVars['--cb2'] = '100%';
                    revealToEmpty = true
                }
                break;

            case 'ttb':   // top to bottom
                if(!isReveal){
                    fromVars['--cb'] = '100%';
                }else{
                    fromVars['--ct1'] = '0%';
                    fromVars['--ct2'] = '0%';
                    revealToEmpty = false
                }
                break;

            default:
                if(!isReveal){
                    fromVars['--cb'] = '100%';
                }
        }

        if (isGrid || isColumn) {
            // Clean up previous grid if exists
            $scope.find('.muia-img-grid-reveal').remove();

            gsap.set(imgElement, { opacity: 0 });

            const src = imgElement.attr('src');
            const cols = 3;
            const rows = isColumn ? 1 : 2;           // Adjust as needed
            const totalItems = cols * rows;
            
            let spansHTML = '';
            for (let i = 0; i < totalItems; i++) {
                const col = i % cols;
                const row = Math.floor(i / cols);
                spansHTML += `<span style="--col-index: ${col}; --row-index: ${row}; background-image: url(${src});"></span>`;
            }

            // Create the container once
            const gridHTML = `<div class="muia-img-grid-reveal">${spansHTML}</div>`;
            wrap.append(gridHTML);

            const gridItems = $scope.find('.muia-img-grid-reveal span');

            gsap.set(gridItems, fromVars);

            // Trigger animation after load
            afterLoad(() => {
                gsap.to(gridItems, {
                    '--cb': '0%',
                    '--cr': '0%',
                    '--cl': '0%',
                    '--ct': '0%',
                    duration: aniSettings.duration,
                    delay: aniSettings.delay,
                    ease:aniSettings.isWithScroll ? 'none' : aniSettings.ease,
                    stagger: aniSettings.stagger,
                    scrollTrigger:initScrollTrigger(imgElement, aniSettings)    
                });
            });
        }
        if (isReveal){
            gsap.set(imgElement, fromVars);
            let obj1 = {};
            let obj2 = {};
            obj1[Object.keys(fromVars)[0]] = revealToEmpty ? '0%' : '100%';
            obj2[Object.keys(fromVars)[1]] = revealToEmpty ? '0%' : '100%';
            
            afterLoad(() => {
                gsap.to(imgElement, {  
                    ...obj1,
                    duration: aniSettings.duration,
                    delay: aniSettings.delay,
                    ease: aniSettings.isWithScroll ? 'none' : aniSettings.ease,
                    stagger: aniSettings.stagger,
                    scrollTrigger:initScrollTrigger(imgElement, aniSettings)
                });
                gsap.to(imgElement, {  
                    ...obj2,
                    duration: aniSettings.duration,
                    ease:aniSettings.isWithScroll ? 'none' : aniSettings.ease,
                    delay: (aniSettings.delay + .05),
                    stagger: aniSettings.stagger,
                    scrollTrigger:initScrollTrigger(imgElement, aniSettings)   
                });
            }); 
        }
        function destroy(){
            gsap.set(imgElement, {opacity:''})
        }
        if (settings?.muia_img_ani_type === '') destroy();    
        muiaWatchElementWidth(wrap); 
    }
    function muiaWatchElementWidth($element) {
        if (!$element || !$element.length) {
            return;
        }
        function updateWidth() {  
            const width = $element.innerWidth();
            const height = $element.innerHeight();
            $element.css('--elw', width+'px');
            $element.css('--elh', height+'px');
        }
        // Initial call
        updateWidth();
        // Update on window resize (with debounce for better performance)
        let resizeTimer;
        $(window).on('resize.muia', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(updateWidth, 100);
        });
        // Optional: Also update when Elementor frontend is ready
        $(window).on('elementor/frontend/init', updateWidth);
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
        $scope.find('[data-go]').on('click', function(){
            let index = parseInt($(this).data('go'));
            if(currentindex !== index) goToSlide(index, index > currentindex ? 'next' : 'prev');  
        });
        Observer.create({ 
            target: $scope.find('.muia-slide-basic')[0],
            type: "touch,pointer",
            onLeft: () => {
                if (isSliding) return;
                const nextIndex = (currentindex + 1) % totalSlide;
                goToSlide(nextIndex, 'next');
            },

            onRight: () => {
                if (isSliding) return;
                const prevIndex = (currentindex - 1 + totalSlide) % totalSlide;
                goToSlide(prevIndex, 'prev');
            }
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
                if(this.$element.hasClass('has-muia-text-animation')) textAnimation(this.$element, this.getElementSettings());
                if(this.$element.hasClass('has-muia-img-ani')) imageAnimation(this.$element, this.getElementSettings());
            },
            onElementChange: function onElementChange(e){
                if(this.$element.hasClass('has-muia-text-animation')) textAnimation(this.$element, this.getElementSettings());
                if(this.$element.hasClass('has-muia-img-ani')) imageAnimation(this.$element, this.getElementSettings());
            },
            getReadySettings:function (){
                var settings = {
                    trigger: this.getElementSettings()
                };
                return $.extend({}, settings);
            }
        });
        
        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(widgetsAnimation, {
                $element: $scope
            });
        });
    }); 
    
})(jQuery);