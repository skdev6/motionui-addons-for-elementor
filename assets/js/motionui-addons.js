;(function($){
    'use strict';

    function initScrollTrigger(trigger, settings){   
        let start = settings?.isWithScroll ? 'top 95%' : "top 80%";
        let end = settings?.isWithScroll ? 'top 5%' : "+=100%";
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
        return {
            duration:     toNum( settings[ prefix + 'muia_motion_duration' ], degaultDuration ),
            delay:        toNum( settings[ prefix + 'muia_motion_delay' ], degaultDelay ),
            stagger:      toNum( settings[ prefix + 'muia_motion_stagger' ], degaultStagger ),
            ease:         settings[ prefix + 'muia_motion_ease' ]        || degaultEase,
            isWithScroll: settings[ prefix + 'muia_motion_with_scroll' ] === 'yes',
        };
    }

    function afterLoad(fun){
        fun(); 
    }
    // Init Button
    function button(btn){  
        let buttonTextElement = btn.find('.muia-btn-text');
        console.log('Button', btn);
        
        if(btn.hasClass('muia-btn-reveal') || btn.hasClass('muia-btn-reveal-random')){
            var chars = new SplitType(buttonTextElement[0], {types:"chars"}).chars;    
            chars.forEach((el, index)=>{
                motionuiAni.set(el, {'--index':index}); 
            })
        }
    }
    // 

    // Text Animations
    function textAnimation( $scope, settings ) {
        var textElement = $scope.find( 'h1,h2,h3,h4,h5,h6,p' );
        var aniSettings = getAniSettings( settings, 'text' );
        var aniType     = settings && settings.muia_text_ani    ? settings.muia_text_ani    : '';
        var aniBy       = settings && settings.muia_text_ani_by ? settings.muia_text_ani_by : 'words';

        if ( ! textElement.length || ! aniType ) return;

        if ( ! textElement.hasClass( 'muia-split-initialized' ) ) {
            textElement.addClass( 'muia-split-initialized' );
            new SplitType( textElement[0], {
                types:      'lines, words, chars',
                lineClass:  'line-text',
                wordClass:  'word-text',
                charClass:  'char-text',
            } );
        }

        var selectorMap = {
            lines: '.line-text',
            words: '.word-text',
            chars: '.char-text',
        };

        var elements = textElement.find( selectorMap[ aniBy ] || '.word-text' );
        if ( ! elements.length ) return;

        $scope.removeClass( 'visibility__hidden' );

        var getDuration = function ( s ) { return s.duration || 0.8; };
        var getDelay    = function ( s ) { return s.delay    || 0;   };
        var getStagger  = function ( s ) { return s.stagger  || 0.04; };
        var getEase     = function ( s, fallback ) { return s.ease || fallback; };

        var animations = {

            'fade': function ( els, s ) {
                motionuiAni.set( els, { opacity: 0, translateY: '40px' } );
                motionuiAni.to( els, {
                    opacity:    1,
                    translateY: '0px',
                    duration:   getDuration( s ),
                    delay:      getDelay( s ),
                    stagger:    getStagger( s ),
                    ease:       getEase( s, 'power4.out' ),
                } );
            },

            'reveal': function ( els, s ) {
                els.each( function () {
                    var $el = $( this );
                    if ( ! $el.parent().hasClass( 'muia-reveal-wrap' ) ) {
                        $el.wrap( '<span class="muia-reveal-wrap" style="overflow:hidden;display:inline-block;"></span>' );
                    }
                } );
                motionuiAni.set( els.toArray(), { translateY: '110%' } );
                motionuiAni.to( els.toArray(), {
                    translateY: '0%',
                    duration:   getDuration( s ),
                    delay:      getDelay( s ),
                    stagger:    getStagger( s ),
                    ease:       getEase( s, 'expo.out' ),
                } );
            },
        };

        if ( animations[ aniType ] ) {
            motionuiAni.addScrollTrigger( $scope[0], {
                start: 'top 90%',
                once:  true,
                onEnter: function () {
                    animations[ aniType ]( elements, aniSettings );
                }
            } );
        }
    }
    function imageAnimation($scope, settings){
        let imgElement = $scope.find('img');
        let aniSettings = getAniSettings(settings, 'img', 1, 0, 'expo.out', 0.05);
        let wrap = $scope.find('.muia-ani-wrap');
        let direction = settings?.muia_ani_direction;

        if(!wrap.length) imgElement.wrap('<div class="muia-ani-wrap"></div>');
        $scope.removeClass('visibility__hidden');

        wrap = $scope.find('.muia-ani-wrap');

        motionuiAni.set(wrap, {
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

            motionuiAni.set(imgElement, { opacity: 0 });

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

            motionuiAni.set(gridItems, fromVars);

            // Trigger animation after load
            afterLoad(() => {
                motionuiAni.addScrollTrigger($scope, { 
                    start: 'top 75%',
                    onEnter(){
                        motionuiAni.to(gridItems, {
                            '--cb': '0%',
                            '--cr': '0%',
                            '--cl': '0%',
                            '--ct': '0%',
                            duration: aniSettings.duration,
                            delay: aniSettings.delay,
                            ease:aniSettings.isWithScroll ? 'none' : aniSettings.ease,
                            stagger: aniSettings.stagger,
                        });
                    }
                })
            });
        }
        if (isReveal){
            motionuiAni.set(imgElement, fromVars);
            let obj1 = {};
            let obj2 = {};
            obj1[Object.keys(fromVars)[0]] = revealToEmpty ? '0%' : '100%';
            obj2[Object.keys(fromVars)[1]] = revealToEmpty ? '0%' : '100%';
            
            afterLoad(() => {
                motionuiAni.addScrollTrigger($scope, { 
                    start: 'top 75%',
                    onEnter(){
                        motionuiAni.to(imgElement, {  
                            ...obj1,
                            duration: aniSettings.duration,
                            delay: aniSettings.delay,
                            ease: aniSettings.isWithScroll ? 'none' : aniSettings.ease,
                            stagger: aniSettings.stagger,
                        });
                        motionuiAni.to(imgElement, {  
                            ...obj2,
                            duration: aniSettings.duration,
                            ease:aniSettings.isWithScroll ? 'none' : aniSettings.ease,
                            delay: (aniSettings.delay + .05),
                            stagger: aniSettings.stagger,
                        });
                    }
                })
            }); 
        }
        function destroy(){
            motionuiAni.set(imgElement, {opacity:''})
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

       motionuiAni.set(bgs, {
            'transition':'none',
            '--cleft':i=> i === 0 ? '0%' : '100%',
            '--cright':i=> i === 0 ? '0%' : '100%',  
            translateX:(i)=> i === 0 ? '0%' : '50%', 
            scale:1
       })
       
        function goToSlide(nextIndex, direction){ 
            if(isSliding) return;
            isSliding = true;
            let slideItems = [bgsArray[currentindex], bgsArray[nextIndex]];
            let nextBg = bgMain.toArray()[nextIndex];
            let duration = 1;
            let ease = 'expo.inOut';
            // 
            motionuiAni.set(slideItems, {   
                autoAlpha:1,
                zIndex:i=> i === 0 ? 1 : 0,
                '--cleft': (i) => {
                    return direction === 'prev' ? (i === 0 ? '0%' : '50%') : '0%';
                },

                '--cright': (i) => {
                    return direction === 'next' ? (i === 0 ? '0%' : '50%') : '0%';
                },
                translateX:(i)=>{
                    if(direction === 'next'){
                        return i === 1 ? '50%' : '0%'
                    }else{
                        return i === 1 ? '-50%' : '0%'
                    }
                }
            });
            motionuiAni.set(nextBg, {scale:1.2});
            motionuiAni.to(nextBg, {scale:1, duration:3, delay:0, ease:'expo.out'});  
            motionuiAni.to(slideItems, {
                '--cleft': (i) => {
                    return direction === 'prev' ? (i === 0 ? '50%' : '0%') : '0%';
                },

                '--cright': (i) => {
                    return direction === 'next' ? (i === 0 ? '50%' : '0%') : '0%';
                },
                translateX:(i)=>{
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
            motionuiAni.to(pagiThumbs, {
                '--x':-nextIndex * 100+'%',
                '--gx':-nextIndex * gapX+'px',
                ease:'expo.out',
                duration,
            })
            motionuiAni.to(titles, {  
                '--x':-nextIndex * 100+'%',
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
        
    }
    //
    function gallery($scope){
        let gallery = $scope.find('.muia-gallery-wrap');

        if(gallery.hasClass('muia-layout-masonry')){
            let gap = parseInt(gallery.css('--gap')); 
            let gh = parseInt(gallery.css('height')); 
            
            gallery.isotope({
                itemSelector: '.muia-gallery-item',
                masonry: {
                    columnWidth: '.muia-gallery-item',
                    fitWidth: true,
                    gutter: gap
                }
            });

            setTimeout(() => {   
                let gallery = $scope.find('.muia-gallery-wrap');
                let afteGh = parseInt(gallery.css('height')); 
                gallery.css('min-height', afteGh < 50 ? gh : 'auto'); 
            }, 50);
        }
    }
    // Scroll Animation
    function scrollAnimation( $scope, settings ) {
        const wrapper     = $scope[0];
        const aniSettings = getAniSettings( settings );

        if ( ! wrapper ) return;

        const getVar = ( name, fallback = undefined ) => {
            const val = parseFloat( getComputedStyle( wrapper ).getPropertyValue( name ).trim() );
            return isNaN( val ) ? fallback : val;
        };

        const getVarWithUnit = ( name, fallback = undefined ) => {
            const val = getComputedStyle( wrapper ).getPropertyValue( name ).trim();
            return val !== '' ? val : fallback;
        };

        const fromVars = {
            x:       getVarWithUnit( '--mui-x' ),
            y:       getVarWithUnit( '--mui-y' ),
            rotateX: getVar( '--mui-rotate-x' ),
            rotateY: getVar( '--mui-rotate-y' ),
            rotateZ: getVar( '--mui-rotate-z' ),
            scaleX:  getVar( '--mui-scale-x' ),
            scaleY:  getVar( '--mui-scale-y' ),
            skewX:   getVar( '--mui-skew-x' ),
            skewY:   getVar( '--mui-skew-y' ),
            opacity: getVar( '--mui-opacity' ),
            transition:'none'
        };

        const toVars = {
            x:       getVarWithUnit( '--mui-x-to',       '0px' ),
            y:       getVarWithUnit( '--mui-y-to',        '0px' ),
            rotateX: getVar( '--mui-rotate-x-to', 0 ),
            rotateY: getVar( '--mui-rotate-y-to', 0 ),
            rotateZ: getVar( '--mui-rotate-z-to', 0 ),
            scaleX:  getVar( '--mui-scale-x-to',  1 ),
            scaleY:  getVar( '--mui-scale-y-to',  1 ),
            skewX:   getVar( '--mui-skew-x-to',   0 ),
            skewY:   getVar( '--mui-skew-y-to',   0 ),
            opacity: getVar( '--mui-opacity-to',   1 ),
            ease:       aniSettings.isWithScroll ? 'none' : aniSettings.ease,
            duration:   aniSettings.duration,
            delay:      aniSettings.delay,
        };

        Object.keys( fromVars ).forEach( key => {
            if ( fromVars[ key ] === undefined ) {
                delete fromVars[ key ];
                
                const cssToVar = '--mui-' + key.replace( /([A-Z])/g, '-$1' ).toLowerCase() + '-to';
                if ( getComputedStyle( wrapper ).getPropertyValue( cssToVar ).trim() === '' ) {
                    delete toVars[ key ];
                }
            }
        } );

        if ( ! Object.keys( fromVars ).length ) return;

        $scope.removeClass( 'visibility__hidden' );

        let animateEl = $scope.find('> *:not(.elementor-element-overlay,.ui-resizable-handle)')[0];
        afterLoad( () => {
            motionuiAni.fromTo( animateEl, fromVars, {
                ...toVars,
                scrollTrigger: initScrollTrigger(wrapper, aniSettings),
            } );
        } );
    }
    
    /**
     * Initialized all widgets
    */
    const widgets = {
        'muia-animated-slider.default':muiaSlide, 
        'muia-animated-gallery.default':gallery, 
    }
    // init elementor frontend
    $(window).on('elementor/frontend/init', function(){
        
        $.each(widgets, function(widget, fun){
           elementorFrontend.hooks.addAction('frontend/element_ready/' + widget, fun);
        });
          
        var widgetsAnimation = elementorModules.frontend.handlers.Base.extend({
            onInit: function() {
                if(typeof themeicMotionUiPro === 'undefined'){ 
                    console.log('Access');
                    
                    if(this.$element.hasClass('has-muia-text-animation')) textAnimation(this.$element, this.getElementSettings());
                    if(this.$element.hasClass('has-muia-img-ani')) imageAnimation(this.$element, this.getElementSettings());
                }
            },
            onElementChange: function onElementChange(e){
                if(typeof themeicMotionUiPro === 'undefined'){   
                    if(this.$element.hasClass('has-muia-text-animation')) textAnimation(this.$element, this.getElementSettings());
                    if(this.$element.hasClass('has-muia-img-ani')) imageAnimation(this.$element, this.getElementSettings());
                }
            },
            getReadySettings:function (){
                var settings = {
                    trigger: this.getElementSettings()
                };
                return $.extend({}, settings);
            }
        });
        
        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {

            if($scope.find('.muia-btn').length) $scope.find('.muia-btn').each(function(){button($(this))});  
            
            elementorFrontend.elementsHandler.addHandler(widgetsAnimation, {
                $element: $scope
            });
        });
    }); 
    
})(jQuery);