/**
 * motionuiAni - Lightweight animation helper
 * License: GPL-2.0-or-later
 * @version 1.0.0
 */
var motionuiAni = (function () {
    'use strict';

    // ─────────────────────────────────────────────
    // Easing functions
    // ─────────────────────────────────────────────
    var easings = {
        'linear':          function (t) { return t; },
        'expo.out':        function (t) { return t === 1 ? 1 : 1 - Math.pow(2, -10 * t); },
        'expo.in':         function (t) { return t === 0 ? 0 : Math.pow(2, 10 * t - 10); },
        'expo.inOut':      function (t) { return t === 0 ? 0 : t === 1 ? 1 : t < 0.5 ? Math.pow(2, 20 * t - 10) / 2 : (2 - Math.pow(2, -20 * t + 10)) / 2; },
        'power2.out':      function (t) { return 1 - Math.pow(1 - t, 2); },
        'power2.in':       function (t) { return t * t; },
        'power2.inOut':    function (t) { return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2; },
        'power3.out':      function (t) { return 1 - Math.pow(1 - t, 3); },
        'power3.in':       function (t) { return t * t * t; },
        'power3.inOut':    function (t) { return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; },
        'power4.out':      function (t) { return 1 - Math.pow(1 - t, 4); },
        'power4.in':       function (t) { return t * t * t * t; },
        'power4.inOut':    function (t) { return t < 0.5 ? 8 * t * t * t * t : 1 - Math.pow(-2 * t + 2, 4) / 2; },
        'sine.out':        function (t) { return Math.sin((t * Math.PI) / 2); },
        'sine.in':         function (t) { return 1 - Math.cos((t * Math.PI) / 2); },
        'sine.inOut':      function (t) { return -(Math.cos(Math.PI * t) - 1) / 2; },
        'back.out':        function (t) { var c1 = 1.70158; var c3 = c1 + 1; return 1 + c3 * Math.pow(t - 1, 3) + c1 * Math.pow(t - 1, 2); },
        'back.in':         function (t) { var c1 = 1.70158; var c3 = c1 + 1; return c3 * t * t * t - c1 * t * t; },
        'back.inOut':      function (t) { var c1 = 1.70158; var c2 = c1 * 1.525; return t < 0.5 ? (Math.pow(2 * t, 2) * ((c2 + 1) * 2 * t - c2)) / 2 : (Math.pow(2 * t - 2, 2) * ((c2 + 1) * (t * 2 - 2) + c2) + 2) / 2; },
        'elastic.out':     function (t) { var c4 = (2 * Math.PI) / 3; return t === 0 ? 0 : t === 1 ? 1 : Math.pow(2, -10 * t) * Math.sin((t * 10 - 0.75) * c4) + 1; },
        'bounce.out':      function (t) { var n1 = 7.5625; var d1 = 2.75; if (t < 1/d1) { return n1*t*t; } else if (t < 2/d1) { t -= 1.5/d1; return n1*t*t+0.75; } else if (t < 2.5/d1) { t -= 2.25/d1; return n1*t*t+0.9375; } else { t -= 2.625/d1; return n1*t*t+0.984375; } },
        'none':            function (t) { return t; },
    };

    function getEasing(ease) {
        if (typeof ease === 'function') return ease;
        return easings[ease] || easings['linear'];
    }

    // ─────────────────────────────────────────────
    // Target resolver — accepts selector, element,
    // NodeList, jQuery object, or array
    // ─────────────────────────────────────────────
    function resolveTargets(target) {
        if (!target) return [];
        if (typeof target === 'string')      return Array.from(document.querySelectorAll(target));
        if (target instanceof Element)       return [target];
        if (target instanceof NodeList)      return Array.from(target);
        if (Array.isArray(target))           return target.filter(Boolean);
        if (target.jquery)                   return target.toArray(); // jQuery
        return [target];
    }

    // ─────────────────────────────────────────────
    // Property parser — converts prop/value pairs
    // into { prop, from, to, isCSS, isVar, unit }
    // ─────────────────────────────────────────────
    var transformProps = ['translateX','translateY','translateZ','rotate','rotateX','rotateY','rotateZ','scaleX','scaleY','scale','skewX','skewY'];
    var unitDefaults   = { translateX:'px', translateY:'px', translateZ:'px', rotate:'deg', rotateX:'deg', rotateY:'deg', rotateZ:'deg', skewX:'deg', skewY:'deg' };

    function parseValue(val) {
        if (typeof val === 'number') return { num: val, unit: '' };
        var match = String(val).match(/^(-?[\d.]+)(%|px|deg|rem|em|vh|vw)?$/);
        if (match) return { num: parseFloat(match[1]), unit: match[2] || '' };
        return { num: parseFloat(val) || 0, unit: '' };
    }

    function getCurrentValue(el, prop) {
        // CSS variable
        if (prop.startsWith('--')) {
            return getComputedStyle(el).getPropertyValue(prop).trim() || '0';
        }
        // Transform
        if (transformProps.indexOf(prop) !== -1) {
            var current = el._muiTransform || {};
            return current[prop] !== undefined ? current[prop] : (unitDefaults[prop] ? '0' + unitDefaults[prop] : '0');
        }
        // Regular CSS
        return getComputedStyle(el).getPropertyValue(
            prop.replace(/([A-Z])/g, function(m) { return '-' + m.toLowerCase(); })
        ).trim() || '0';
    }

    // ─────────────────────────────────────────────
    // Apply transform to element
    // ─────────────────────────────────────────────
    function applyTransform(el) {
        var t = el._muiTransform || {};
        var str = '';
        if (t.translateX !== undefined) str += 'translateX(' + t.translateX + ') ';
        if (t.translateY !== undefined) str += 'translateY(' + t.translateY + ') ';
        if (t.translateZ !== undefined) str += 'translateZ(' + t.translateZ + ') ';
        if (t.rotateX    !== undefined) str += 'rotateX('    + t.rotateX    + ') ';
        if (t.rotateY    !== undefined) str += 'rotateY('    + t.rotateY    + ') ';
        if (t.rotateZ    !== undefined) str += 'rotateZ('    + t.rotateZ    + ') ';
        if (t.rotate     !== undefined) str += 'rotate('     + t.rotate     + ') ';
        if (t.scaleX     !== undefined) str += 'scaleX('     + t.scaleX     + ') ';
        if (t.scaleY     !== undefined) str += 'scaleY('     + t.scaleY     + ') ';
        if (t.scale      !== undefined) str += 'scale('      + t.scale      + ') ';
        if (t.skewX      !== undefined) str += 'skewX('      + t.skewX      + ') ';
        if (t.skewY      !== undefined) str += 'skewY('      + t.skewY      + ') ';
        el.style.transform = str.trim();
    }

    // ─────────────────────────────────────────────
    // Apply a single property value to element
    // ─────────────────────────────────────────────
    function applyProp(el, prop, value) {
        // CSS variable
        if (prop.startsWith('--')) {
            el.style.setProperty(prop, value);
            return;
        }
        // Transform property
        if (transformProps.indexOf(prop) !== -1) {
            if (!el._muiTransform) el._muiTransform = {};
            el._muiTransform[prop] = value;
            applyTransform(el);
            return;
        }
        // opacity / autoAlpha
        if (prop === 'opacity') {
            el.style.opacity = value;
            return;
        }
        if (prop === 'autoAlpha') {
            el.style.opacity    = value;
            el.style.visibility = parseFloat(value) === 0 ? 'hidden' : 'visible';
            return;
        }
        // zIndex
        if (prop === 'zIndex') {
            el.style.zIndex = value;
            return;
        }
        // transition
        if (prop === 'transition') {
            el.style.transition = value;
            return;
        }
        // Regular camelCase → kebab-case CSS
        var cssProp = prop.replace(/([A-Z])/g, function (m) { return '-' + m.toLowerCase(); });
        el.style.setProperty(cssProp, value);
    }

    // ─────────────────────────────────────────────
    // motionui.set(target, props)
    // Instantly apply properties — no animation
    // ─────────────────────────────────────────────
    function set(target, props) {
        var els = resolveTargets(target);
        els.forEach(function (el, elIndex) {
            Object.keys(props).forEach(function (prop) {
                var val = props[prop];
                // Pass index first (like GSAP), then element
                if (typeof val === 'function') val = val(elIndex, el);
                var parsed = parseValue(val);
                var unit   = parsed.unit || unitDefaults[prop] || '';
                var final  = typeof val === 'number' || (typeof val === 'string' && !isNaN(val))
                    ? parsed.num + unit
                    : val;
                applyProp(el, prop, final);
            });
        });
    }

    // ─────────────────────────────────────────────
    // motionui.to(target, props)
    // Animate to target values using rAF
    // props: { duration, delay, ease, stagger, onComplete, onUpdate, ...cssProps }
    // ─────────────────────────────────────────────
    function to(target, props) {
        var els      = resolveTargets(target);
        var duration = (props.duration || 1) * 1000;
        var delay    = (props.delay    || 0) * 1000;
        var stagger  = (props.stagger  || 0) * 1000;
        var easeFn   = getEasing(props.ease || 'power2.out');

        // Separate animation props from control props
        var controlKeys = { duration:1, delay:1, ease:1, stagger:1, onComplete:1, onUpdate:1, scrollTrigger:1 };
        var animProps   = {};
        Object.keys(props).forEach(function (k) {
            if (!controlKeys[k]) animProps[k] = props[k];
        });

        var totalCompleted = 0;

        els.forEach(function (el, elIndex) {
            var elDelay    = delay + (stagger * elIndex);
            var startTime  = null;

            // Build from/to for each prop
            var tweens = Object.keys(animProps).map(function (prop) {
                var toVal    = typeof animProps[prop] === 'function' ? animProps[prop](elIndex, el) : animProps[prop];
                var fromRaw  = getCurrentValue(el, prop);
                var fromP    = parseValue(fromRaw);
                var toP      = parseValue(toVal);
                var unit     = toP.unit || fromP.unit || unitDefaults[prop] || '';
                return { prop: prop, from: fromP.num, to: toP.num, unit: unit };
            });

            function tick(timestamp) {
                if (!startTime) startTime = timestamp;
                var elapsed  = timestamp - startTime;
                var progress = Math.min(elapsed / duration, 1);
                var eased    = easeFn(progress);

                tweens.forEach(function (tween) {
                    var current = tween.from + (tween.to - tween.from) * eased;
                    var value   = tween.unit ? (current + tween.unit) : current;
                    applyProp(el, tween.prop, value);
                });

                if (typeof props.onUpdate === 'function') props.onUpdate(progress, el);

                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    // Snap to exact final values
                    tweens.forEach(function (tween) {
                        applyProp(el, tween.prop, tween.unit ? (tween.to + tween.unit) : tween.to);
                    });
                    totalCompleted++;
                    if (typeof props.onComplete === 'function' && totalCompleted === els.length) {
                        props.onComplete();
                    }
                }
            }

            if (elDelay > 0) {
                setTimeout(function () { requestAnimationFrame(tick); }, elDelay);
            } else {
                requestAnimationFrame(tick);
            }
        });
    }

    // ─────────────────────────────────────────────
    // motionui.fromTo(target, fromProps, toProps)
    // ─────────────────────────────────────────────
    function fromTo(target, fromProps, toProps) {
        set(target, fromProps);
        // Small delay to ensure set() is painted before animating
        requestAnimationFrame(function () {
            to(target, toProps);
        });
    }

    // ─────────────────────────────────────────────
    // motionui.stagger(value, options)
    // Returns a function for per-element stagger delay
    // Usage: stagger: motionui.stagger(0.1)
    // ─────────────────────────────────────────────
    function stagger(value, options) {
        var start = (options && options.start) || 0;
        return function (el, index) {
            return start + (index * value);
        };
    }

    // ─────────────────────────────────────────────
    // motionui.addScrollTrigger(trigger, options)
    //
    // options = {
    //   start      : 'top 80%'   — when to fire  (default: 'top 80%')
    //   end        : 'top 20%'   — when to fire end / scrub end
    //   scrub      : false       — tie progress to scroll (like GSAP scrub)
    //   once       : true        — fire onEnter only once
    //   onEnter    : fn(el)      — fires when start is crossed going down
    //   onLeave    : fn(el)      — fires when end is crossed going down
    //   onEnterBack: fn(el)      — fires when end is crossed going up
    //   onLeaveBack: fn(el)      — fires when start is crossed going up
    //   onUpdate   : fn(progress, el) — fires every scroll tick (scrub mode)
    // }
    //
    // start/end format: 'elementEdge viewportEdge'
    //   elementEdge  : 'top' | 'center' | 'bottom' | '50%' | '200px'
    //   viewportEdge : 'top' | 'center' | 'bottom' | '80%' | '200px'
    //
    // Returns { kill } to remove the scroll listener
    // ─────────────────────────────────────────────
    function addScrollTrigger(trigger, options) {
        var el   = resolveTargets(trigger)[0];
        var opts = options || {};
        if (!el) return { kill: function () {} };

        var once       = opts.once !== false; // default true
        var scrub      = opts.scrub || false;
        var onEnter    = opts.onEnter     || null;
        var onLeave    = opts.onLeave     || null;
        var onEnterBack= opts.onEnterBack || null;
        var onLeaveBack= opts.onLeaveBack || null;
        var onUpdate   = opts.onUpdate    || null;

        // ── Parse a position string like 'top 80%' ───────────────
        // Returns a function(rect, wh) → pixel position
        function parsePosition(str, fallbackEl, fallbackVp) {
            str = str || (fallbackEl + ' ' + fallbackVp);
            var parts  = str.trim().split(/\s+/);
            var elPart = parts[0] || fallbackEl;
            var vpPart = parts[1] || fallbackVp;

            function resolvePart(part, size) {
                if (part === 'top')    return 0;
                if (part === 'center') return size / 2;
                if (part === 'bottom') return size;
                if (/%$/.test(part))   return (parseFloat(part) / 100) * size;
                if (/px$/.test(part))  return parseFloat(part);
                return parseFloat(part) || 0;
            }

            return function (rect, wh) {
                var elOffset = resolvePart(elPart, rect.height);
                var vpOffset = resolvePart(vpPart, wh);
                // Pixel position from top of page where trigger fires
                return (rect.top + window.scrollY + elOffset) - vpOffset;
            };
        }

        var getStartPx = parsePosition(opts.start, 'top',    '80%');
        var getEndPx   = parsePosition(opts.end,   'bottom', '20%');

        var fired    = false;
        var inView   = false;
        var ticking  = false;

        function onScroll() {
            if (!ticking) {
                requestAnimationFrame(update);
                ticking = true;
            }
        }

        function update() {
            ticking = false;
            var rect    = el.getBoundingClientRect();
            var wh      = window.innerHeight;
            var scrollY = window.scrollY;

            var startPx = getStartPx(rect, wh);
            var endPx   = getEndPx(rect, wh);

            var passedStart = scrollY >= startPx;
            var passedEnd   = scrollY >= endPx;

            // ── Scrub mode — pass 0-1 progress to onUpdate ───────
            if (scrub && typeof onUpdate === 'function') {
                var range    = endPx - startPx;
                var progress = range > 0
                    ? Math.min(Math.max((scrollY - startPx) / range, 0), 1)
                    : 0;
                onUpdate(progress, el);
            }

            // ── Enter (crossed start going down) ──────────────────
            if (passedStart && !passedEnd && !inView) {
                inView = true;
                if (typeof onEnter === 'function') {
                    onEnter(el);
                    if (once) {
                        window.removeEventListener('scroll', onScroll);
                        return;
                    }
                }
            }

            // ── Leave (crossed end going down) ────────────────────
            if (passedEnd && inView) {
                inView = false;
                if (typeof onLeave === 'function') onLeave(el);
            }

            // ── EnterBack (crossed end going up) ──────────────────
            if (!passedEnd && !inView && fired) {
                inView = true;
                if (typeof onEnterBack === 'function') onEnterBack(el);
            }

            // ── LeaveBack (crossed start going up) ────────────────
            if (!passedStart && inView) {
                inView = false;
                if (typeof onLeaveBack === 'function') onLeaveBack(el);
            }

            fired = true;
        }

        window.addEventListener('scroll', onScroll, { passive: true });

        // Run once immediately to catch elements already in view
        update();

        // Return kill method to clean up listener
        return {
            kill: function () {
                window.removeEventListener('scroll', onScroll);
            }
        };
    }

    // ─────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────
    return {
        set:             set,
        to:              to,
        fromTo:          fromTo,
        stagger:         stagger,
        easings:         easings,
        addScrollTrigger: addScrollTrigger,
    };

})();

// Expose globally so other files can access it
window.motionuiAni = motionuiAni;