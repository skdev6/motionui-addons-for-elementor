(function () {
	'use strict';

	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
	if ('ontouchstart' in window) return; // keep native momentum scrolling on touch devices

	var target = window.scrollY;
	var current = window.scrollY;
	var running = false;
	var ease = 0.1;

	function maxScroll() {
		return document.documentElement.scrollHeight - window.innerHeight;
	}

	function tick() {
		current += (target - current) * ease;
		if (Math.abs(target - current) < 0.5) {
			current = target;
			running = false;
		}
		window.scrollTo(0, current);
		if (running) requestAnimationFrame(tick);
	}

	window.addEventListener('wheel', function (e) {
		if (e.ctrlKey || e.defaultPrevented) return;
		// Let scrollable inner elements (textareas, modals) scroll natively.
		var el = e.target;
		while (el && el !== document.body) {
			var s = getComputedStyle(el);
			if (/(auto|scroll)/.test(s.overflowY) && el.scrollHeight > el.clientHeight) return;
			el = el.parentElement;
		}
		e.preventDefault();
		target = Math.max(0, Math.min(maxScroll(), target + e.deltaY));
		if (!running) {
			current = window.scrollY;
			running = true;
			requestAnimationFrame(tick);
		}
	}, { passive: false });

	// Keep in sync when scroll is changed by anything else (keyboard, anchors).
	window.addEventListener('scroll', function () {
		if (!running) {
			target = current = window.scrollY;
		}
	}, { passive: true });
})();
