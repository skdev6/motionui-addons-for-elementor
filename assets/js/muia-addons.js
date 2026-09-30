window.muia = window.muia || {};
;(function($){
    'use strict';

    function initElementorFrontend(options = {}) {
        const {
            widgets = {},
            extensions = {},
            debounceDelay = 40,
            init = null,
            global = null,
            duration = .5,
            delay = 0,
            stagger = 0.01,
            ease = 'expo.out'
        } = options;
        const CSS_EASES = {
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
        const debounce = (fn, wait = 50) => {
            let timeout;

            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn.apply(this, args), wait);
            };
        };
        const getAniSettings = ($scope, settings = {})=>{

            const getNumber = (value, fallback) => {
                const number = parseFloat(value?.size ?? value);
                return isNaN(number) ? fallback : number;
            };

            // Detect prefix automatically
            const durationKey = Object.keys(settings).find(key =>
                key.endsWith('muia_motion_duration')
            );

            const prefix = durationKey
                ? durationKey.replace('muia_motion_duration', '')
                : '';

            const muiaDuration = getNumber(
                settings[`${prefix}muia_motion_duration`],
                duration
            );

            const muiaDelay = getNumber(
                settings[`${prefix}muia_motion_delay`],
                delay
            );

            const muiaStagger = getNumber(
                settings[`${prefix}muia_motion_stagger`],
                stagger
            );

            const muiaEase =
                settings[`${prefix}muia_motion_ease`] ||
                ease;

            const muiaCSSEase =
                CSS_EASES[muiaEase] ||
                CSS_EASES['expo.out'];

            if ($scope?.length) {

                $scope.css({
                    '--muia-duration': `${muiaDuration}s`,
                    '--muia-delay': `${muiaDelay}s`,
                    '--muia-stagger': `${muiaStagger}s`,
                    '--muia-ease': muiaCSSEase
                });

            }
            return {
                muiaDuration,
                muiaDelay,
                muiaStagger,
                muiaEase,
                muiaCSSEase
            };
        }
        const initExtensions = ($element, settings = {}) => {

            $.each(extensions, function (className, callback) {

                if (!$element.hasClass(className)) {
                    return;
                }

                callback($element, {...getAniSettings($element, settings), ...settings});

            });

        };

        $(window).on('elementor/frontend/init', function () {  

            init?.();

            const ExtensionHandler = elementorModules.frontend.handlers.Base.extend({

                onInit() {
                    this.run();
                },

                onElementChange() {
                    this.run();
                },

                run: debounce(function () {

                    if (typeof themeicMotionUiPro !== 'undefined') {
                        return;
                    }

                    initExtensions(
                        this.$element,
                        this.getElementSettings()
                    );

                }, debounceDelay)

            });

            /**
             * Widgets
             */
            $.each(widgets, function (widget, callback) {

                elementorFrontend.hooks.addAction(
                    'frontend/element_ready/' + widget,
                    callback
                );

            });

            /**
             * Global Elements
             */
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/global',
                function ($scope) {

                    const hasExtension = Object.keys(extensions).some(className =>
                        $scope.hasClass(className)
                    );

                    if (hasExtension) {

                        elementorFrontend.elementsHandler.addHandler(
                            ExtensionHandler,
                            {
                                $element: $scope
                            }
                        );

                    }

                    global?.($scope);

                }
            );

        });

    }
    window.muia.initElementorFrontend = initElementorFrontend;
    initElementorFrontend({
        extensions: {
            'has-muia-img-ani': muia.imageAni,
            'has-muia-text-animation': muia.textAni
        },
        init() {
            initBurgerToggle();
        },
        global($scope) {
            $scope.find('.muia-btn').each(function () {
                button($(this));
            });
        }
    });

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
    
})(jQuery);