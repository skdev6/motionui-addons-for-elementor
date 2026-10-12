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

        if (!Object.keys(from).length) return;

        if (uses3D) {
            from.transformPerspective = to.transformPerspective = 1000;
        }

        gsap.set(targets, { transition: 'none' });

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
        })).fromTo(targets.toArray(), from, {
            ...to,
            stagger: targets.length > 1 ? settings.muiaTl.stagger : 0,
            force3D: true
        });
    }

    // Presets join this map as they are written.
    const BUILDERS = {
        'custom': buildCustom
    };

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

        $scope.removeClass('visibility__hidden');

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
