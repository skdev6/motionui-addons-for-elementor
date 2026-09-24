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

    function muiaInView($el, className){
        var el = $el[0];
        if (!el) return null;

        var controller = muiaController();

        if (!controller) {
            $el.addClass(className);
            return null;
        }

        var scene = new ScrollMagic.Scene({
            triggerElement: el,
            triggerHook: 0.9,
            reverse: false
        });

        scene.on('enter', function(){
            $el.addClass(className);
        });

        return scene.addTo(controller);
    }
    /**
     * Widget Functions
    * */
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
        if(!btn.hasClass('muia-btn-reveal') && !btn.hasClass('muia-btn-reveal-random')) return;

        let buttonTextElement = btn.find('.muia-btn-text');

        if(!buttonTextElement.length || buttonTextElement.hasClass('muia-split-initialized')) return;

        buttonTextElement.addClass('muia-split-initialized');

        var chars = new SplitType(buttonTextElement[0], {types:"chars"}).chars || [];
        chars.forEach((el, index)=>{
            $(el).css('--index',index);
        })
    }
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
    function textAnimation( $scope, settings ) {

        $scope.removeClass( 'visibility__hidden' );

        var textElement = $scope.find( 'h1,h2,h3,h4,h5,h6,p,.elementor-heading-title' );
        var aniType     = settings && settings.muia_text_ani    ? settings.muia_text_ani    : '';
        var aniBy       = settings && settings.muia_text_ani_by ? settings.muia_text_ani_by : 'words';

        $scope.removeClass( function ( index, className ) {
            return ( className.match( /muia-text-by-\S+/g ) || [] ).join( ' ' );
        } ).removeClass( 'is-inview' );

        $scope.find( '.muia-reveal-wrap' ).children().unwrap();
        $scope.find( '.muia-text-piece' ).removeClass( 'muia-text-piece' ).css( '--index', '' );

        if ( ! textElement.length ) return;

        if ( aniType !== 'fade' && aniType !== 'reveal' ) return;

        textElement.each( function () {
            if ( this._muiaSplit ) return;

            this._muiaSplit = new SplitType( this, {
                types:     'lines, words, chars',
                lineClass: 'line-text',
                wordClass: 'word-text',
                charClass: 'char-text',
            } );
        } );

        var selectorMap = {
            lines: '.line-text',
            words: '.word-text',
            chars: '.char-text',
        };

        var aniSettings = getAniSettings( settings, 'text', 0.8, 0, 'expo.out', 0.04 );

        $scope.css( {
            '--text-ani-duration': aniSettings.duration + 's',
            '--text-ani-delay':    aniSettings.delay + 's',
            '--text-ani-stagger':  aniSettings.stagger + 's',
            '--text-ani-ease':     cssEase( aniSettings.ease )
        } );

        $scope.addClass( 'muia-text-by-' + aniBy );

        // Re-runnable: a re-split replaces the pieces, so the marking has to be
        // applied again afterwards.
        function markPieces() {
            var pieces = textElement.find( selectorMap[ aniBy ] || '.word-text' );
            if ( ! pieces.length ) return false;

            pieces.each( function ( index ) {
                $( this ).addClass( 'muia-text-piece' ).css( '--index', index );
            } );

            // A reveal slides each piece up from behind a clipped box, so it
            // needs one to be clipped by. Fade animates in place.
            if ( aniType === 'reveal' ) {
                pieces.each( function () {
                    var $piece = $( this );
                    if ( ! $piece.parent().hasClass( 'muia-reveal-wrap' ) ) {
                        $piece.wrap( '<span class="muia-reveal-wrap"></span>' );
                    }
                } );
            }

            return true;
        }

        if ( ! markPieces() ) return;

        if ( aniBy === 'lines' ) {
            muiaWatchLineResplit( $scope, textElement, markPieces );
        }

        muiaInView( $scope, 'is-inview' );
    }

    /**
     * Rebuild line boxes on resize.
     *
     * Registered per widget and driven from one shared, debounced window
     * listener; entries whose element Elementor has re-rendered away are
     * dropped on the next pass.
     */
    var muiaResplitTargets = [];
    var muiaResplitBound = false;

    function muiaWatchLineResplit( $scope, textElement, remark ) {
        var el = $scope[0];
        if ( ! el ) return;

        var isNew = ! el._muiaResplit;

        // Overwrite rather than stack: a panel change gives fresh closures.
        el._muiaResplit = { textElement: textElement, remark: remark };

        if ( isNew ) muiaResplitTargets.push( el );

        if ( muiaResplitBound ) return;
        muiaResplitBound = true;

        var timer;
        $( window ).on( 'resize.muiaSplit', function () {
            clearTimeout( timer );
            timer = setTimeout( function () {

                muiaResplitTargets = muiaResplitTargets.filter( function ( node ) {
                    return node.isConnected;
                } );

                muiaResplitTargets.forEach( function ( node ) {
                    var entry = node._muiaResplit;
                    if ( ! entry ) return;

                    // split() rebuilds innerHTML, so the wrappers go with it.
                    $( node ).find( '.muia-reveal-wrap' ).children().unwrap();

                    entry.textElement.each( function () {
                        if ( this._muiaSplit ) this._muiaSplit.split( {} );
                    } );

                    entry.remark();
                } );
            }, 200 );
        } );
    }
    
    function imageAnimation($scope, settings){

        $scope.removeClass('visibility__hidden');

        var imgElement = $scope.find('img');

        var aniSettings = getAniSettings(settings, 'img', 1, 0, 'expo.out', 0.05);
        var type        = settings && settings.muia_img_ani_type  ? settings.muia_img_ani_type  : '';
        var direction   = settings && settings.muia_ani_direction ? settings.muia_ani_direction : 'ttb';

        $scope.removeClass(function(index, className){
            return (className.match(/muia-img-dir-\S+/g) || []).join(' ');
        }).removeClass('is-inview');

        $scope.find('.muia-img-grid-reveal').remove();

        if (!imgElement.length) return;

        var wrap = $scope.find('.muia-ani-wrap');
        if (!wrap.length) {
            imgElement.wrap('<div class="muia-ani-wrap"></div>');
            wrap = $scope.find('.muia-ani-wrap');
        }

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

        $scope.addClass('muia-img-dir-' + direction);

        if (type === 'grid-reveal' || type === 'column-reveal') {

            var cols = 3;
            var rows = type === 'column-reveal' ? 1 : 2;

            wrap.each(function(){

                var $wrap = $(this);
                var src   = $wrap.find('img').attr('src');

                if (!src) return;

                var $grid = $('<div class="muia-img-grid-reveal"></div>')
                    .css({ '--cols': cols, '--rows': rows });

                for (var i = 0; i < cols * rows; i++) {
                    $('<span></span>')
                        .css({
                            '--col-index':  i % cols,
                            '--row-index':  Math.floor(i / cols),
                            '--tile-index': i,
                            'background-image': 'url("' + src.replace(/"/g, '\\"') + '")'
                        })
                        .appendTo($grid);
                }

                $wrap.append($grid);
            });
        }

        muiaWatchElementWidth(wrap);

        muiaInView($scope, 'is-inview');
    }
    
    var muiaWidthFallback = [];
    var muiaWidthFallbackBound = false;

    function muiaUpdateElementSize(el) {
        var $el = $(el);
        $el.css({
            '--elw': $el.innerWidth() + 'px',
            '--elh': $el.innerHeight() + 'px'
        });
    }

    function muiaWatchElementWidth($element) {
        if (!$element || !$element.length) return;

        $element.each(function () {
            var el = this;

            // Already watched: just refresh, do not subscribe twice.
            if (el._muiaSizeWatched) {
                muiaUpdateElementSize(el);
                return;
            }
            el._muiaSizeWatched = true;

            if (typeof ResizeObserver !== 'undefined') {
                new ResizeObserver(function () {
                    muiaUpdateElementSize(el);
                }).observe(el);
            } else {
                muiaWidthFallback.push(el);
                muiaBindWidthFallback();
            }

            muiaUpdateElementSize(el);
        });
    }

    function muiaBindWidthFallback() {
        if (muiaWidthFallbackBound) return;
        muiaWidthFallbackBound = true;

        var timer;
        $(window).on('resize.muiaSize', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                // Drop anything Elementor has since re-rendered away.
                muiaWidthFallback = muiaWidthFallback.filter(function (el) {
                    return el.isConnected;
                });
                muiaWidthFallback.forEach(muiaUpdateElementSize);
            }, 100);
        });
    }
    

    
})(jQuery);