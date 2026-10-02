window.muia = window.muia || {};
;(function($){  
    'use strict';

    let initSeq = 0;

    function initElementorFrontend(options = {}) {

        const handlerID = 'MuiaExtensionHandler' + (initSeq += 1);

        const {
            widgets = {},
            extensions = {},
            debounceDelay = 40,
            init = null,
            global = null,
            duration = 0.5,
            delay = 0,
            stagger = 0,
            ease = 'expo.out'
        } = options;

        const CSS_EASES = {
            'none': 'linear', 'expo.out': 'cubic-bezier(0.19, 1, 0.22, 1)', 'expo.in': 'cubic-bezier(0.95, 0.05, 0.795, 0.035)', 'expo.inOut': 'cubic-bezier(1, 0, 0, 1)', 'power1.out': 'cubic-bezier(0.25, 0.46, 0.45, 0.94)', 'power1.in': 'cubic-bezier(0.55, 0.085, 0.68, 0.53)', 'power1.inOut': 'cubic-bezier(0.455, 0.03, 0.515, 0.955)', 'power2.out': 'cubic-bezier(0.215, 0.61, 0.355, 1)', 'power2.in': 'cubic-bezier(0.55, 0.055, 0.675, 0.19)', 'power2.inOut': 'cubic-bezier(0.645, 0.045, 0.355, 1)', 'power3.out': 'cubic-bezier(0.165, 0.84, 0.44, 1)', 'power3.in': 'cubic-bezier(0.895, 0.03, 0.685, 0.22)', 'power3.inOut': 'cubic-bezier(0.77, 0, 0.175, 1)', 'power4.out': 'cubic-bezier(0.23, 1, 0.32, 1)', 'power4.in': 'cubic-bezier(0.755, 0.05, 0.855, 0.06)', 'power4.inOut': 'cubic-bezier(0.86, 0, 0.07, 1)', 'back.out(1.7)': 'cubic-bezier(0.175, 0.885, 0.32, 1.275)', 'back.in(1.7)': 'cubic-bezier(0.6, -0.28, 0.735, 0.045)', 'back.inOut(1.7)': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)', 'elastic.out(1, 0.3)': 'cubic-bezier(0.175, 0.885, 0.32, 1.275)', 'elastic.in(1, 0.3)': 'cubic-bezier(0.6, -0.28, 0.735, 0.045)', 'bounce.out': 'cubic-bezier(0.175, 0.885, 0.32, 1.275)', 'bounce.in': 'cubic-bezier(0.6, -0.28, 0.735, 0.045)'
        };

        const buildTimers = new Map();
        let handlerSeq = 0;

        const debounceFor = (key, fn, wait) => {
            clearTimeout(buildTimers.get(key));

            buildTimers.set(key, setTimeout(() => {
                buildTimers.delete(key);
                fn();
            }, wait));
        };

        const cancelDebounceFor = (key) => {
            clearTimeout(buildTimers.get(key));
            buildTimers.delete(key);
        };

        const killAnimation = (thing) => {

            if (!thing) {
                return;
            }

            if (typeof thing.kill === 'function') {
                thing.kill();
                return;
            }

            if (typeof thing.destroy === 'function') {
                thing.destroy(true);
            }

        };

        const getAniSettings = ($scope, settings = {}) => {

            const getNumber = (value, fallback) => {
                const rawValue = (value && typeof value === 'object' && 'size' in value) 
                    ? value.size 
                    : value;

                const num = Number(rawValue);
                if (Number.isNaN(num) || rawValue === null || rawValue === '') {
                    return fallback;
                }
                return num;
            };

            const DURATION_KEY = 'muia_motion_duration';

            const prefixKey = Object.keys(settings).find(key => key.endsWith(DURATION_KEY));

            const prefix = prefixKey
                ? prefixKey.slice(0, -DURATION_KEY.length)
                : '';

            const muiaDuration = getNumber(
                settings[`${prefix}muia_motion_duration`],
                duration
            );
            
            const directionKey = Object.keys(settings).find(key =>
                key.startsWith(prefix) && key.endsWith('muia_motion_direction')
            );

            const muiaDirection = (directionKey ? settings[directionKey] : '') || "none";
            const muiaTriggerPoint = settings[`${prefix}muia_motion_trigger_point`] || "custom";
            
            const muiaTriggerPointCustom = settings[`${prefix}muia_motion_trigger_point_custom`] || "top 80%";
            const isMobile = settings[`${prefix}muia_motion_mobile`] || "no";

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
                ease, TM = settings[`${prefix}muia_motion_trigger_mode`] || '';

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
                muiaDirection, muiaCSSEase, isMobile:isMobile === 'yes',isDesktop:window.innerWidth > 991,
                muiaTl:{ duration:muiaDuration, delay:muiaDelay, stagger:muiaStagger, ease:muiaEase },
                muiaTrigger:{
                    start: muiaTriggerPoint === 'custom' ? muiaTriggerPointCustom : muiaTriggerPoint, scrub: muiaLocal.hasPro ? TM === 'scroll' : false,  toggleActions: ( muiaLocal.hasPro && TM === 'reverse' ) ? 'play none none reverse' : 'play none none none'
                },
            };
        };

        $(window).on('elementor/frontend/init', function () {

            init?.();

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
             * Extension Handler
             */
            const ExtensionHandler = elementorModules.frontend.handlers.Base.extend({

                getConstructorID() {
                    return handlerID;
                },

                onInit() {

                    elementorModules.frontend.handlers.Base.prototype.onInit.apply(
                        this,
                        arguments
                    );

                    this.scrollTriggers = [];
                    this.timelines = [];
                    this.animations = [];
                    this.teardowns = [];

                    this.muiaKey = this.$element.data('model-cid')
                        || this.$element.data('id')
                        || this.$element[0]
                        || ('muia-handler-' + (handlerSeq += 1));

                    this.buildDebounced();

                },

                onElementChange() {

                    this.destroyAnimation();
                    this.buildDebounced();

                },

                onDestroy() {

                    cancelDebounceFor(this.muiaKey);

                    this.destroyAnimation();

                    elementorModules.frontend.handlers.Base.prototype.onDestroy.apply(
                        this,
                        arguments
                    );

                },

                buildDebounced() {

                    debounceFor(this.muiaKey, () => this.build(), debounceDelay);

                },

                destroyAnimation() {

                    this.scrollTriggers.forEach(killAnimation);
                    this.timelines.forEach(killAnimation);
                    this.animations.forEach(killAnimation);

                    this.teardowns.forEach(fn => {
                        try {
                            fn(this.$element);
                        } catch (e) {
                            // One extension's cleanup must not stop the rest.
                        }
                    });

                    this.scrollTriggers = [];
                    this.timelines = [];
                    this.animations = [];
                    this.teardowns = [];

                },

                build() {

                    if (typeof themeicMotionUiPro !== 'undefined') {
                        return;
                    }

                    const settings = this.getElementSettings();
                    const $scope = this.$element;

                    $.each(extensions, (className, callback) => {

                        if (!$scope.hasClass(className) || typeof callback !== 'function') {
                            return;
                        }

                        const teardown = callback.call(this, $scope, {
                            ...settings,
                            ...getAniSettings($scope, settings)
                        });

                        // An extension may just return its own cleanup instead
                        // of calling addTeardown().
                        this.addTeardown(teardown);

                    });

                },

                addTimeline(tl) {

                    if (!tl) {
                        return tl;
                    }

                    this.timelines.push(tl);

                    if (tl.scrollTrigger) {
                        this.scrollTriggers.push(tl.scrollTrigger);
                    }

                    return tl;

                },

                addScrollTrigger(st) {

                    if (!st) {
                        return st;
                    }

                    this.scrollTriggers.push(st);

                    return st;

                },

                addAnimation(anim) {

                    if (!anim) {
                        return anim;
                    }

                    this.animations.push(anim);

                    return anim;

                },

                addTeardown(fn) {

                    if (typeof fn !== 'function') {
                        return fn;
                    }

                    this.teardowns.push(fn);

                    return fn;

                }

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

                    if (hasExtension || elementorFrontend.isEditMode()) {

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