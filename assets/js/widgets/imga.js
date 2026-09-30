window.muia = window.muia || {};

;(function ($) {
    'use strict';

    var KEYWORDS = { top: 0, center: 0.5, bottom: 1, left: 0, right: 1 };

    // Scenes are cheap, controllers are not: one per scroll container.
    var controllers = [];
    var instances   = [];

    function getController(container, vertical) {
        var found = controllers.filter(function (entry) {
            return entry.container === container && entry.vertical === vertical;
        })[0];

        if (found) {
            return found.controller;
        }

        var controller = new ScrollMagic.Controller({
            container: container || window,
            vertical: vertical
        });

        controllers.push({ container: container, vertical: vertical, controller: controller });

        return controller;
    }

    function parseToken(token) {
        if (Object.prototype.hasOwnProperty.call(KEYWORDS, token)) {
            return { fraction: KEYWORDS[token], px: 0 };
        }

        if (/%\s*$/.test(token)) {
            return { fraction: (parseFloat(token) || 0) / 100, px: 0 };
        }

        return { fraction: 0, px: parseFloat(token) || 0 };
    }

    function parsePlacement(value, fallback) {
        var raw = (value === undefined || value === null || value === '') ? fallback : value;

        var parts = String(raw).trim().split(/\s+/);

        return {
            el: parseToken(parts[0]),
            vp: parseToken(parts.length > 1 ? parts[1] : 'top')
        };
    }

    function measure(controller, element) {
        return {
            // info('size') is the scroll container's height, or the window's.
            viewport: controller ? controller.info('size') : window.innerHeight,
            element: element ? (element.offsetHeight || 0) : 0
        };
    }
    function elementPoint(placement, size) {
        return placement.el.fraction * size.element + placement.el.px;
    }
    function viewportPoint(placement, size) {
        return placement.vp.fraction * size.viewport + placement.vp.px;
    }

    // Only a fractional element point (center, bottom, 40%) depends on the
    // element's height; a pixel offset does not, and neither does 'top'.
    function usesElementHeight(placement) {
        return !!placement && placement.el.fraction !== 0;
    }

    function resolveElement(trigger) {
        if (typeof trigger === 'string') {
            return document.querySelector(trigger);
        }

        if (trigger && trigger.jquery) {
            return trigger[0];
        }

        return trigger || null;
    }

    function InitScrollMagic(options) {

        var opts = options || {};

        if (typeof ScrollMagic === 'undefined') {
            // No library, no scroll behaviour. Run the enter callback once so
            // whatever was going to animate still lands in its final state
            // rather than staying hidden.
            if (typeof opts.onEnter === 'function') {
                opts.onEnter({ progress: 1, isActive: true, direction: 1, scene: null });
            }
            return null;
        }

        var element = resolveElement(opts.trigger);

        if (!element) {
            return null;
        }

        var vertical   = opts.horizontal !== true;
        var controller = opts.controller || getController(opts.scroller || opts.container || null, vertical);

        var start = parsePlacement(opts.start, 'top bottom');
        var end   = opts.end;

        // '+=300' and '+=50%' are a distance rather than a placement.
        var endDistance = null;

        if (typeof end === 'number') {
            endDistance = { px: end, fraction: 0 };
        } else if (typeof end === 'string' && end.indexOf('+=') === 0) {
            var rest = end.slice(2);

            endDistance = /%\s*$/.test(rest)
                ? { px: 0, fraction: (parseFloat(rest) || 0) / 100 }
                : { px: parseFloat(rest) || 0, fraction: 0 };
        }

        var endPlacement = (!endDistance && end) ? parsePlacement(end, 'top bottom') : null;

        function computeDuration() {
            var size = measure(controller, element);

            if (endDistance) {
                return Math.max(0, endDistance.fraction * size.viewport + endDistance.px);
            }

            if (!endPlacement) {
                return 0;
            }

            // Each placement sits at elementTop + elementPoint - viewportPoint,
            // so subtracting one from the other cancels the element's own top
            // and leaves just the scroll distance between them.
            var delta = (elementPoint(endPlacement, size) - elementPoint(start, size))
                - (viewportPoint(endPlacement, size) - viewportPoint(start, size));

            return Math.max(0, delta);
        }

        function computeHook() {
            var size = measure(controller, element);

            return size.viewport ? viewportPoint(start, size) / size.viewport : 0;
        }

        var scene = new ScrollMagic.Scene({
            triggerElement: element,
            triggerHook: computeHook(),
            // Passed as a function, so ScrollMagic re-evaluates it on refresh.
            duration: computeDuration,
            offset: elementPoint(start, measure(controller, element)),
            reverse: opts.once !== true
        });

        if (opts.pin) {
            scene.setPin(opts.pin === true ? element : opts.pin, {
                pushFollowers: opts.pinSpacing !== false
            });
        }

        // The indicators plugin is a separate file, so only wire markers up
        // when it has actually been enqueued.
        if (opts.markers && typeof scene.addIndicators === 'function') {
            scene.addIndicators({ name: opts.id || 'muia' });
        }

        var api = {
            scene: scene,
            progress: 0,
            isActive: false,
            direction: 1,

            /** Re-measure after a layout change. */
            refresh: function () {
                var size = measure(controller, element);

                scene.offset(elementPoint(start, size));
                scene.triggerHook(computeHook());
                scene.refresh();               // re-runs the duration function

                return api;
            },
            /** ScrollTrigger calls it kill; ScrollMagic calls it destroy. */
            kill: function (reset) {
                var at = instances.indexOf(api);

                if (at !== -1) {
                    instances.splice(at, 1);
                }

                if (api.observer) {
                    api.observer.disconnect();
                    api.observer = null;
                }

                clearTimeout(sizeTimer);
                scene.destroy(reset !== false);

                return null;
            }
        };

        // Any placement measured as a fraction of the element needs the
        // element's height, and at element_ready an image has usually not
        // loaded, so the wrapper still measures 0. ScrollMagic resolves the
        // duration function once and caches the number, so without a re-measure
        // an `end` of 'bottom top' stays where 'top top' would have been.
        var sizeTimer;

        function watchSize() {
            if (!usesElementHeight(start) && !usesElementHeight(endPlacement)) {
                return;
            }

            if (typeof ResizeObserver === 'undefined') {
                // No observer: catch the common case, images finishing.
                $(window).one('load.muiaScrollMagic', function () {
                    api.refresh();
                });
                return;
            }

            api.observer = new ResizeObserver(function () {
                clearTimeout(sizeTimer);
                sizeTimer = setTimeout(function () {
                    api.refresh();
                }, 50);
            });

            api.observer.observe(element);
        }

        function sync(event) {
            if (event && typeof event.progress === 'number') {
                api.progress = event.progress;
            }

            api.isActive  = !!event && event.state === 'DURING';
            api.direction = (event && event.scrollDirection === 'REVERSE') ? -1 : 1;
        }

        function call(fn) {
            if (typeof fn === 'function') {
                fn.call(api, api);
            }
        }

        // ScrollMagic fires enter and leave in both directions and says which
        // in scrollDirection; ScrollTrigger splits that into four callbacks.
        scene.on('enter', function (event) {
            sync(event);
            call(api.direction === -1 ? opts.onEnterBack : opts.onEnter);
            call(opts.onToggle);
        });

        scene.on('leave', function (event) {
            sync(event);
            call(api.direction === -1 ? opts.onLeaveBack : opts.onLeave);
            call(opts.onToggle);
        });

        scene.on('progress', function (event) {
            sync(event);
            call(opts.onUpdate);
        });

        if (typeof opts.onRefresh === 'function') {
            scene.on('shift', function (event) {
                sync(event);
                call(opts.onRefresh);
            });
        }

        scene.addTo(controller);
        instances.push(api);
        watchSize();

        return api;
    }
    InitScrollMagic.refreshAll = function () {
        instances.slice().forEach(function (instance) {
            instance.refresh();
        });
    };
    InitScrollMagic.killAll = function (reset) {
        instances.slice().forEach(function (instance) {
            instance.kill(reset);
        });
    };

    InitScrollMagic.getController = getController;

    InitScrollMagic.create = InitScrollMagic;

    var resizeTimer;

    $(window).on('resize.muiaScrollMagic', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(InitScrollMagic.refreshAll, 150);
    });

    window.muia.initScrollMagic = InitScrollMagic;

    function imageAni($scope, settings) {

        $scope.removeClass('visibility__hidden');

        var trigger = InitScrollMagic.create({
            trigger: $scope,
            start: settings.muiaTriggerPoint || 'top 80%',
            markers: true,
            onEnter: function () {
                $scope.addClass('muia-start');
            }
        });

        return function () {
            $scope.removeClass('muia-start');

            if (trigger) {
                trigger.kill();
            }
        };
    }

    window.muia.imageAni = imageAni;

})(jQuery);
