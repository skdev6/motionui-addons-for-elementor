;(function ($) {
    'use strict';

    /**
     * Custom animation's transform properties.
     *
     * Each entry maps a GSAP property to the CSS variable the From tab writes
     * (the To tab writes the same name with a `-to` suffix), the popover toggle
     * that switches it on, and the value it rests at when one side is unset.
     * Opacity has no toggle, so it counts as on whenever a value is set.
     */
    const CUSTOM_PROPS = [
        { prop: 'x',         css: '--muia-x',        toggle: 'muia_scroll_ani_translate_toggle', rest: 0 },
        { prop: 'y',         css: '--muia-y',        toggle: 'muia_scroll_ani_translate_toggle', rest: 0 },
        { prop: 'rotationX', css: '--muia-rotate-x', toggle: 'muia_scroll_ani_rotate_toggle',    rest: 0 },
        { prop: 'rotationY', css: '--muia-rotate-y', toggle: 'muia_scroll_ani_rotate_toggle',    rest: 0 },
        { prop: 'rotation',  css: '--muia-rotate-z', toggle: 'muia_scroll_ani_rotate_toggle',    rest: 0 },
        { prop: 'scaleX',    css: '--muia-scale-x',  toggle: 'muia_scroll_ani_scale_toggle',     rest: 1, number: true },
        { prop: 'scaleY',    css: '--muia-scale-y',  toggle: 'muia_scroll_ani_scale_toggle',     rest: 1, number: true },
        { prop: 'skewX',     css: '--muia-skew-x',   toggle: 'muia_scroll_ani_skew_toggle',      rest: 0 },
        { prop: 'skewY',     css: '--muia-skew-y',   toggle: 'muia_scroll_ani_skew_toggle',      rest: 0 },
        { prop: 'opacity',   css: '--muia-opacity',  toggle: null,                               rest: 1, number: true }
    ];

    // Read at call time, not once: the sliders are responsive, so the value
    // depends on the breakpoint the media queries currently match.
    function readVar($scope, name) {
        return getComputedStyle($scope[0]).getPropertyValue(name).trim();
    }

    /**
     * Elements to animate: the inner elements when "Animate Inner Elements"
     * names a selector that matches, otherwise the element itself.
     */
    function getTargets($scope, settings) {
        const selector = (settings.muia_child_selector || '').trim();

        if (settings.muia_animate_for_child !== 'yes' || !selector) {
            return $scope;
        }

        try {
            const $children = $scope.find(selector);
            return $children.length ? $children : $scope;
        } catch (e) {
            // Author-supplied selector; fall back on an invalid one.
            return $scope;
        }
    }

    /**
     * The Direction picker (Motion::get_derection_control, name 'motion_effect').
     * Offers left / right / top / bottom / center-v; the value is the side the
     * element comes in from. Unset means bottom, the usual "rise in".
     */
    function getDirection(settings) {
        return settings.motion_effectmuia_motion_direction || 'bottom';
    }

    /**
     * Travel distance as a CSS length. Fade and Slide take it from the
     * responsive "From" slider (muia_ani_from); the other presets pass their
     * own fallback.
     */
    function getDistance(handler, settings, fallback) {
        const value = (typeof handler.getCurrentDeviceSetting === 'function'
            ? handler.getCurrentDeviceSetting('muia_ani_from')
            : null) || settings.muia_ani_from;

        if (value && value.size !== '' && value.size !== undefined && value.size !== null) {
            return { size: Number(value.size), unit: value.unit || 'px' };
        }
        return fallback;
    }

    // Start offset for a direction: the element sits `distance` away on the
    // side it comes in from. center-v has no side, so no offset.
    function offsetFrom(direction, distance) {
        const d = distance.size + distance.unit;
        const n = -distance.size + distance.unit;

        switch (direction) {
            case 'left':   return { x: n };
            case 'right':  return { x: d };
            case 'top':    return { y: n };
            case 'bottom': return { y: d };
            default:       return {};
        }
    }

    // Transform origin on the side the element comes in from.
    const ORIGINS = {
        'left':     'left center',
        'right':    'right center',
        'top':      'center top',
        'bottom':   'center bottom',
        'center-v': 'center center'
    };

    // Rest state of everything the presets touch, so each `to` returns there.
    const REST = { x: 0, y: 0, opacity: 1, scale: 1, rotation: 0, rotationX: 0, rotationY: 0, skewX: 0, skewY: 0 };

    function restFor(from) {
        const to = {};
        Object.keys(from).forEach(key => {
            if (key in REST) to[key] = REST[key];
        });
        return to;
    }

    /**
     * Presets. Each returns { from, to, ease? } for the shared timeline in
     * play(); `ease` overrides the Easing setting where the motion IS the ease
     * (bounce, elastic).
     */
    const PRESETS = {

        // Fade in, travelling from the chosen side. Center: fade only.
        fade(handler, settings, direction) {
            const from = { opacity: 0, ...offsetFrom(direction, getDistance(handler, settings, { size: 100, unit: 'px' })) };
            return { from, to: restFor(from) };
        },

        // Travel in from the chosen side at full opacity. Center: grow from the middle.
        slide(handler, settings, direction) {
            const from = direction === 'center-v'
                ? { scale: 0, transformOrigin: ORIGINS['center-v'] }
                : offsetFrom(direction, getDistance(handler, settings, { size: 100, unit: 'px' }));
            return { from, to: restFor(from) };
        },

        // Scale up from the side it is anchored to.
        zoom(handler, settings, direction) {
            const from = { scale: 0.5, opacity: 0, transformOrigin: ORIGINS[direction] || ORIGINS['center-v'] };
            return { from, to: restFor(from) };
        },

        // Swing in on a hinge along the chosen edge. Center: turn on the vertical axis.
        flip(handler, settings, direction) {
            const angles = {
                'left':     { rotationY: -90 },
                'right':    { rotationY: 90 },
                'top':      { rotationX: 90 },
                'bottom':   { rotationX: -90 },
                'center-v': { rotationY: 90 }
            };
            const from = {
                ...(angles[direction] || angles['center-v']),
                opacity: 0,
                transformOrigin: ORIGINS[direction] || ORIGINS['center-v'],
                transformPerspective: 1000
            };
            return { from, to: { ...restFor(from), transformPerspective: 1000 } };
        },

        // Rotate into place around a corner on the chosen side. Center: spin in.
        rotate(handler, settings, direction) {
            const presets = {
                'left':     { rotation: -45, transformOrigin: 'left bottom' },
                'right':    { rotation: 45,  transformOrigin: 'right bottom' },
                'top':      { rotation: -45, transformOrigin: 'left top' },
                'bottom':   { rotation: 45,  transformOrigin: 'right bottom' },
                'center-v': { rotation: 180, scale: 0.5, transformOrigin: 'center center' }
            };
            const from = { ...(presets[direction] || presets['center-v']), opacity: 0 };
            return { from, to: restFor(from) };
        },

        // Lean in from the chosen side and straighten up.
        skew(handler, settings, direction) {
            const presets = {
                'left':     { skewX: 20,  x: -60 },
                'right':    { skewX: -20, x: 60 },
                'top':      { skewY: 10,  y: -60 },
                'bottom':   { skewY: -10, y: 60 },
                'center-v': { skewX: 20 }
            };
            const from = { ...(presets[direction] || presets['center-v']), opacity: 0 };
            return { from, to: restFor(from) };
        },

        // Drop in from the chosen side and bounce to rest. Center: pop up.
        bounce(handler, settings, direction) {
            const from = direction === 'center-v'
                ? { scale: 0, transformOrigin: ORIGINS['center-v'] }
                : offsetFrom(direction, { size: 150, unit: 'px' });
            return { from, to: restFor(from), ease: 'bounce.out' };
        },

        // Spring in from the chosen side and settle. Center: spring up from nothing.
        elastic(handler, settings, direction) {
            const from = direction === 'center-v'
                ? { scale: 0, transformOrigin: ORIGINS['center-v'] }
                : offsetFrom(direction, { size: 120, unit: 'px' });
            return { from, to: restFor(from), ease: 'elastic.out(1, 0.4)' };
        },

        // Come into focus, drifting a little from the chosen side.
        blur(handler, settings, direction) {
            const from = { opacity: 0, filter: 'blur(20px)', ...offsetFrom(direction, { size: 40, unit: 'px' }) };
            return { from, to: { ...restFor(from), filter: 'blur(0px)' } };
        },

        // Wipe in from the chosen edge. Center: open out from the middle.
        clip(handler, settings, direction) {
            const insets = {
                'left':     'inset(0% 100% 0% 0%)',
                'right':    'inset(0% 0% 0% 100%)',
                'top':      'inset(0% 0% 100% 0%)',
                'bottom':   'inset(100% 0% 0% 0%)',
                'center-v': 'inset(50% 0% 50% 0%)'
            };
            return {
                from: { clipPath: insets[direction] || insets['center-v'] },
                to:   { clipPath: 'inset(0% 0% 0% 0%)' }
            };
        }
    };

    /**
     * The one timeline every animation runs on: scroll-triggered by the
     * element, staggered across inner elements when there are several.
     */
    function play(handler, $scope, targets, settings, motion) {

        if (!motion || !Object.keys(motion.from).length) return;

        gsap.set(targets, { transition: 'none' });

        const to = {
            ...motion.to,
            stagger: targets.length > 1 ? settings.muiaTl.stagger : 0,
            force3D: true
        };

        if (motion.ease) to.ease = motion.ease;

        handler.addTimeline(gsap.timeline({
            defaults: { ...settings.muiaTl, delay: 0 },
            delay: settings.muiaTl.delay,
            scrollTrigger: {
                trigger: $scope,
                ...settings.muiaTrigger,
                invalidateOnRefresh: true
            },
            onStart: function () { gsap.set(targets, { willChange: 'transform, opacity' }); },
            onComplete: function () { gsap.set(targets, { willChange: 'auto' }); }
        })).fromTo(targets.toArray(), motion.from, to);
    }

    // Preset by name, run on the shared timeline.
    function buildPreset(handler, $scope, targets, settings) {
        const preset = PRESETS[ settings.muia_motion_effects_name ];
        play(handler, $scope, targets, settings, preset(handler, settings, getDirection(settings)));
    }

    /**
     * Custom: From / To values come straight from the CSS variables the
     * custom builder controls write on the wrapper.
     *
     * Values are functions so invalidateOnRefresh re-reads them, which keeps a
     * resize across breakpoints in step with the responsive controls.
     */
    function buildCustom(handler, $scope, targets, settings) {

        const from = {}, to = {};
        let uses3D = false;

        CUSTOM_PROPS.forEach(({ prop, css, toggle, rest, number }) => {

            // A popover that is off contributes nothing, whatever its sliders hold.
            if (toggle && settings[toggle] !== 'yes' && settings[toggle + '_to'] !== 'yes') {
                return;
            }

            const fromToggled = !toggle || settings[toggle] === 'yes';
            const toToggled   = !toggle || settings[toggle + '_to'] === 'yes';

            const read = (name, on) => () => {
                const raw = on ? readVar($scope, name) : '';
                if (raw === '') return rest;
                return number ? parseFloat(raw) : raw;
            };

            // Skip a property neither side has a value for, so it is left alone.
            if (readVar($scope, css) === '' && readVar($scope, css + '-to') === '') {
                return;
            }

            from[prop] = read(css, fromToggled);
            to[prop]   = read(css + '-to', toToggled);

            if (prop === 'rotationX' || prop === 'rotationY') uses3D = true;
        });

        if (uses3D) {
            from.transformPerspective = to.transformPerspective = 1000;
        }

        play(handler, $scope, targets, settings, { from, to });
    }

    const BUILDERS = { 'custom': buildCustom };

    Object.keys(PRESETS).forEach(name => { BUILDERS[ name ] = buildPreset; });

    function motionLib($scope, settings) {
        $scope.removeClass('visibility__hidden');

        const build = BUILDERS[ settings.muia_motion_effects_name ];

        // Switched off for phones: show the element as it is, no motion.
        if (!build || (muia.isMobileDevice() && !settings.isMobile)) {
            return;
        }

        const targets = getTargets($scope, settings);

        // Build first so the From state is already applied (fromTo renders
        // immediately), then reveal — otherwise the end state flashes.
        build(this, $scope, targets, settings);

        // clearProps for what the tweens wrote inline — killing a timeline
        // does not put those back.
        return function () {
            gsap.set(targets.toArray(), { clearProps: 'all' });
        };
    }

    muia.initElementorFrontend({
        extensions: {
            'muia-motion-lib-yes': motionLib
        }
    });

})(jQuery);
