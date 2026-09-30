window.muia = window.muia || {};
;(function(){

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
            triggerHook: 0.8,
            reverse: false
        });

        scene.on('enter', function(){
            $el.addClass(className);
        });

        return scene.addTo(controller);
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
    

    function textAni($scope){
        console.log('textAni', $scope);
        
    }

    window.muia.textAni = textAni;
})(jQuery);  