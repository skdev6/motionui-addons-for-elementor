;(function($){
    'use strict';

    /**
     * Initialized all widgets
    */
    const widgets = {}
    const extensions = {
        'has-muia-img-ani':imageAnimation,
        'has-muia-text-animation':textAnimation
    }
    // init elementor frontend
    $(window).on('elementor/frontend/init', function(){

        initBurgerToggle();

        $.each(widgets, function(widget, fun){
           elementorFrontend.hooks.addAction('frontend/element_ready/' + widget, fun);
        });
          

        const debouncedExtensions = new Map();

        function debounce(func, wait = 80) {
            let timeoutId;
            return function executedFunction(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                }, wait);
            };
        }

        function initExtension(element, getSettings) {  
            const elementId = element.data('id') || element.attr('data-id') || 'unknown';
            $.each(extensions, function(extension, fun) {
                if (!element.hasClass(extension)) return;

                const key = `${elementId}_${extension}`;

                if (!debouncedExtensions.has(key)) {
                    const debouncedFn = debounce((latestSettings) => {

                        afterLoad(() =>fun(element, latestSettings));

                    }, 50);

                    debouncedExtensions.set(key, debouncedFn);
                }

                // Pass fresh settings every time
                debouncedExtensions.get(key)(getSettings);
            });
        }

        var widgetsAnimation = elementorModules.frontend.handlers.Base.extend({
            onInit: function() {
                if(typeof themeicMotionUiPro === 'undefined'){ 
                    initExtension(this.$element, this.getElementSettings());
                }
            },
            onElementChange: function onElementChange(e){
                if(typeof themeicMotionUiPro === 'undefined'){   
                    initExtension(this.$element, this.getElementSettings());
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

    function afterLoad(fun){
        fun();
    }

    /* -----------------------------------------------------------------
     * ScrollMagic helpers
     * -------------------------------------------------------------- */

    // The motion controls offer GSAP ease names; CSS wants a timing function.
    // These are the standard Penner cubic-bezier equivalents.
    var CSS_EASES = {
        'none':                'linear',
        'expo.out':            'cubic-bezier(0.19, 1, 0.22, 1)',
        'expo.in':             'cubic-bezier(0.95, 0.05, 0.795, 0.035)',
        'expo.inOut':          'cubic-bezier(1, 0, 0, 1)',
        'power1.out':          'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        'power1.in':           'cubic-bezier(0.55, 0.085, 0.68, 0.53)',
        'power1.inOut':        'cubic-bezier(0.455, 0.03, 0.515, 0.955)',
        'power2.out':          'cubic-bezier(0.215, 0.61, 0.355, 1)',
        'power2.in':           'cubic-bezier(0.55, 0.055, 0.675, 0.19)',
        'power2.inOut':        'cubic-bezier(0.645, 0.045, 0.355, 1)',
        'power3.out':          'cubic-bezier(0.165, 0.84, 0.44, 1)',
        'power3.in':           'cubic-bezier(0.895, 0.03, 0.685, 0.22)',
        'power3.inOut':        'cubic-bezier(0.77, 0, 0.175, 1)',
        'power4.out':          'cubic-bezier(0.23, 1, 0.32, 1)',
        'power4.in':           'cubic-bezier(0.755, 0.05, 0.855, 0.06)',
        'power4.inOut':        'cubic-bezier(0.86, 0, 0.07, 1)',
        'back.out(1.7)':       'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
        'back.in(1.7)':        'cubic-bezier(0.6, -0.28, 0.735, 0.045)',
        'back.inOut(1.7)':     'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
        // A cubic-bezier cannot oscillate, so these take the nearest
        // single-overshoot curve rather than quietly flattening to linear.
        'elastic.out(1, 0.3)': 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
        'elastic.in(1, 0.3)':  'cubic-bezier(0.6, -0.28, 0.735, 0.045)',
        'bounce.out':          'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
        'bounce.in':           'cubic-bezier(0.6, -0.28, 0.735, 0.045)'
    };

    function cssEase(name){
        return CSS_EASES[name] || CSS_EASES['expo.out'];
    }

    // One controller for the whole page; scenes are cheap, controllers are not.
    var muiaScrollController = null;

    function muiaController(){
        if (typeof ScrollMagic === 'undefined') return null;
        if (!muiaScrollController) muiaScrollController = new ScrollMagic.Controller();
        return muiaScrollController;
    }

    /**
     * Add `className` once the element's top reaches 90% down the viewport.
     *
     * triggerHook 0.9 is ScrollMagic's spelling of "top 90%". The scene is torn
     * down on entry: these are reveals, and replaying one every time the
     * element passes back through is not what the author asked for.
     */
    function muiaInView($el, className){
        var el = $el[0];
        if (!el) return null;

        var controller = muiaController();

        // Without ScrollMagic, reveal immediately rather than leaving the
        // element stuck in its starting state.
        if (!controller) {
            $el.addClass(className);
            return null;
        }

        var scene = new ScrollMagic.Scene({
            triggerElement: el,
            triggerHook: 0.9
        });

        scene.on('enter', function(){
            $el.addClass(className);
            scene.destroy(true);
        });

        // addTo() updates the scene straight away, so something already past
        // the trigger point reveals here instead of waiting for a scroll.
        return scene.addTo(controller);
    }
    /**
     * Widget Functions
    * */
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
    // Init Button
    function button(btn){
        let buttonTextElement = btn.find('.muia-btn-text');
        if(btn.hasClass('muia-btn-reveal') || btn.hasClass('muia-btn-reveal-random')){
            var chars = new SplitType(buttonTextElement[0], {types:"chars"}).chars;    
            chars.forEach((el, index)=>{
                $(el).css('--index',index); 
            })
        }
    }
    // Burger toggle. Delegated once from the document, so a button that
    // Elementor re-renders in the editor keeps working without re-binding.
    function initBurgerToggle(){
        $(document).on('click', '[data-muia-burger]', function(){
            var $btn    = $(this).toggleClass('is-open');
            var isOpen  = $btn.hasClass('is-open');
            var target  = $btn.attr('data-muia-target');

            $btn.attr('aria-expanded', isOpen ? 'true' : 'false');

            if(target){
                try{ $(target).toggleClass('is-open', isOpen); }
                catch(e){ /* Author-supplied selector; ignore an invalid one. */ }
            }
        });
    }
    /**
     * Text reveal.
     *
     * Same split of duties as imageAnimation: SplitType breaks the text up,
     * this marks the pieces and publishes the timing, ScrollMagic adds
     * `is-inview` at top 90%, and `components/text-ani.scss` owns the motion.
     *
     * Classes it works from:
     *
     *     .muia-text-fade | .muia-text-reveal          type, from prefix_class
     *     .muia-text-by-lines | -words | -chars        split level
     *     .is-inview                                   play
     *
     * and these custom properties on the widget:
     *
     *     --text-ani-duration  --text-ani-delay
     *     --text-ani-stagger   --text-ani-ease
     *
     * Every animated piece gets `.muia-text-piece` and its own `--index`, so
     * the stylesheet staggers with
     * `calc(var(--text-ani-delay) + var(--index) * var(--text-ani-stagger))`
     * and never has to care which split level is in play.
     */
    function textAnimation( $scope, settings ) {

        var textElement = $scope.find( 'h1,h2,h3,h4,h5,h6,p' );
        var aniType     = settings && settings.muia_text_ani    ? settings.muia_text_ani    : '';
        var aniBy       = settings && settings.muia_text_ani_by ? settings.muia_text_ani_by : 'words';

        if ( ! textElement.length ) return;

        // Undo the previous pass: Elementor re-runs this whenever the panel
        // changes, and the split level may have moved.
        $scope.removeClass( function ( index, className ) {
            return ( className.match( /muia-text-by-\S+/g ) || [] ).join( ' ' );
        } ).removeClass( 'is-inview' );

        $scope.find( '.muia-reveal-wrap' ).children().unwrap();
        $scope.find( '.muia-text-piece' ).removeClass( 'muia-text-piece' ).css( '--index', '' );

        $scope.removeClass( 'visibility__hidden' );

        // Only the two free types are handled here; the Pro build takes over
        // for wave, scramble and auto scroll.
        if ( aniType !== 'fade' && aniType !== 'reveal' ) return;

        // Split once, with every level, so changing "Animate By" later only
        // moves the marker class instead of rebuilding the DOM. Filtered so a
        // second heading in the same widget is not skipped.
        var unsplit = textElement.filter( function () {
            return ! $( this ).hasClass( 'muia-split-initialized' );
        } );

        if ( unsplit.length ) {
            unsplit.addClass( 'muia-split-initialized' );
            new SplitType( unsplit.toArray(), {
                types:     'lines, words, chars',
                lineClass: 'line-text',
                wordClass: 'word-text',
                charClass: 'char-text',
            } );
        }

        var selectorMap = {
            lines: '.line-text',
            words: '.word-text',
            chars: '.char-text',
        };

        var pieces = textElement.find( selectorMap[ aniBy ] || '.word-text' );
        if ( ! pieces.length ) return;

        var aniSettings = getAniSettings( settings, 'text', 0.8, 0, 'expo.out', 0.04 );

        $scope.css( {
            '--text-ani-duration': aniSettings.duration + 's',
            '--text-ani-delay':    aniSettings.delay + 's',
            '--text-ani-stagger':  aniSettings.stagger + 's',
            '--text-ani-ease':     cssEase( aniSettings.ease )
        } );

        $scope.addClass( 'muia-text-by-' + aniBy );

        pieces.each( function ( index ) {
            $( this ).addClass( 'muia-text-piece' ).css( '--index', index );
        } );

        // A reveal slides each piece up from behind a clipped box, so it needs
        // one to be clipped by. Fade animates in place and needs no wrapper.
        if ( aniType === 'reveal' ) {
            pieces.each( function () {
                var $piece = $( this );
                if ( ! $piece.parent().hasClass( 'muia-reveal-wrap' ) ) {
                    $piece.wrap( '<span class="muia-reveal-wrap"></span>' );
                }
            } );
        }

        muiaInView( $scope, 'is-inview' );
    }
    
    function imageAnimation($scope, settings){

        var imgElement = $scope.find('img');
        if (!imgElement.length) return;

        var aniSettings = getAniSettings(settings, 'img', 1, 0, 'expo.out', 0.05);
        var type        = settings && settings.muia_img_ani_type  ? settings.muia_img_ani_type  : '';
        var direction   = settings && settings.muia_ani_direction ? settings.muia_ani_direction : 'ttb';

        $scope.removeClass(function(index, className){
            return (className.match(/muia-img-dir-\S+/g) || []).join(' ');
        }).removeClass('is-inview');

        $scope.find('.muia-img-grid-reveal').remove();

        var wrap = $scope.find('.muia-ani-wrap');
        if (!wrap.length) {
            imgElement.wrap('<div class="muia-ani-wrap"></div>');
            wrap = $scope.find('.muia-ani-wrap');
        }

        $scope.removeClass('visibility__hidden');

        // No animation picked: leave the image as it is.
        if (!type) {
            muiaWatchElementWidth(wrap);
            return;
        }

        // Seconds, matching how the controls express them.
        $scope.css({
            '--img-ani-duration': aniSettings.duration + 's',
            '--img-ani-delay':    aniSettings.delay + 's',
            '--img-ani-stagger':  aniSettings.stagger + 's',
            '--img-ani-ease':     cssEase(aniSettings.ease)
        });

        // `muia-img-<type>` is already on the widget from the control's
        // prefix_class; the direction control has none, so it is added here.
        $scope.addClass('muia-img-dir-' + direction);

        // A grid or column reveal animates tiles rather than the image, so the
        // tiles are built here and the image is hidden by the stylesheet.
        if (type === 'grid-reveal' || type === 'column-reveal') {

            var cols = 3;
            var rows = type === 'column-reveal' ? 1 : 2;
            var src  = imgElement.attr('src');
            var tiles = '';

            for (var i = 0; i < cols * rows; i++) {
                tiles += '<span style="' +
                    '--col-index:'  + (i % cols) + ';' +
                    '--row-index:'  + Math.floor(i / cols) + ';' +
                    '--tile-index:' + i + ';' +
                    'background-image:url(' + src + ');"></span>';
            }

            wrap.append(
                '<div class="muia-img-grid-reveal" style="--cols:' + cols + ';--rows:' + rows + ';">' +
                tiles +
                '</div>'
            );
        }

        muiaWatchElementWidth(wrap);

        muiaInView($scope, 'is-inview');
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
    

    
})(jQuery);