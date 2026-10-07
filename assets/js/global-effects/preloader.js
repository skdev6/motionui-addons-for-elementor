(function () {
	'use strict';

	var loader = document.querySelector('.muia-preloader');
	if (!loader) return;

	function hide() {
		loader.classList.add('is-hidden');
		setTimeout(function () {
			if (loader.parentNode) loader.parentNode.removeChild(loader);
		}, 600);
	}

	if (document.readyState === 'complete') {
		hide();
	} else {
		window.addEventListener('load', hide);
		// Never trap visitors behind a slow asset.
		setTimeout(hide, 8000);
	}
})();
