(function () {
	'use strict';

	if (typeof Lenis === 'undefined') return;
	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

	function init() {
		var config   = (window.muiaGlobalEffect && window.muiaGlobalEffect['smooth-scroll']) || {};
		var settings = config.settings || {};
		var useGsap  = typeof gsap !== 'undefined' && gsap.ticker;

		var lenis = new Lenis({
			lerp: typeof settings.lerp === 'number' ? settings.lerp : 0.1,
			smoothWheel: true,
			anchors: true,
			autoRaf: !useGsap
		});

		window.muia = window.muia || {};
		window.muia.lenis = lenis;

		if (useGsap) {
			gsap.ticker.add(function (time) {
				lenis.raf(time * 1000);
			});
			gsap.ticker.lagSmoothing(0);
		}

		if (typeof ScrollTrigger !== 'undefined') {
			lenis.on('scroll', ScrollTrigger.update);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
