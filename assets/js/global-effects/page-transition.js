(function () {
	'use strict';

	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

	document.addEventListener('click', function (e) {
		var a = e.target.closest && e.target.closest('a[href]');
		if (!a || e.defaultPrevented || e.button !== 0) return;
		if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
		if (a.target && a.target !== '_self') return;
		if (a.hasAttribute('download')) return;

		var url;
		try { url = new URL(a.href, location.href); } catch (err) { return; }

		if (url.origin !== location.origin) return;
		if (!/^https?:$/.test(url.protocol)) return;
		if (url.pathname === location.pathname && url.search === location.search) return; // same-page/anchor
		if (/\/wp-admin\/|\/wp-login\.php/.test(url.pathname)) return;

		e.preventDefault();
		document.body.classList.add('muia-page-leaving');
		setTimeout(function () { location.href = url.href; }, 350);
	});

	// Back/forward cache restores the faded-out state otherwise.
	window.addEventListener('pageshow', function (e) {
		if (e.persisted) document.body.classList.remove('muia-page-leaving');
	});
})();
