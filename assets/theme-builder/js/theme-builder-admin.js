/**
 * Theme Builder templates list: the "Add New Template" popup.
 */
(function () {
	'use strict';

	var popup = document.getElementById('muia-tb-new-popup');

	if (!popup || typeof muiaThemeBuilder === 'undefined') {
		return;
	}

	var form = popup.querySelector('#muia-tb-new-form');
	var notice = popup.querySelector('.muia-tb-notice');
	var submit = form.querySelector('button[type="submit"]');
	var typeSelect = form.querySelector('select[name="template_type"]');
	var i18n = muiaThemeBuilder.i18n;

	function open() {
		notice.textContent = '';
		popup.classList.add('open');
		typeSelect.focus();
	}

	function close() {
		popup.classList.remove('open');
	}

	function showError(message) {
		notice.textContent = message;
		submit.disabled = false;
	}

	// Both the header button and the empty-state link point at post-new.php.
	document.addEventListener('click', function (event) {
		var link = event.target.closest('a[href*="post-new.php?post_type=muia_theme_builder"]');

		if (link) {
			event.preventDefault();
			open();
		}
	});

	popup.addEventListener('click', function (event) {
		if (event.target.closest('.backdrop, .muia-close-btn, .muia-close-btn-text')) {
			close();
		}
	});

	document.addEventListener('keydown', function (event) {
		if ('Escape' === event.key) {
			close();
		}
	});

	form.addEventListener('submit', function (event) {
		event.preventDefault();

		if (!typeSelect.value) {
			showError(i18n.chooseType);
			return;
		}

		var body = new FormData(form);
		body.append('action', 'muia_theme_builder_create_template');
		body.append('nonce', muiaThemeBuilder.nonce);

		notice.textContent = '';
		submit.disabled = true;
		submit.textContent = i18n.creating;

		fetch(muiaThemeBuilder.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
			.then(function (response) {
				return response.json();
			})
			.then(function (response) {
				if (response && response.success && response.data.edit_url) {
					window.location.href = response.data.edit_url;
					return;
				}

				showError(response && response.data && response.data.message ? response.data.message : i18n.error);
			})
			.catch(function () {
				showError(i18n.error);
			});
	});

	if (/[?&]muia_add_new=1/.test(window.location.search)) {
		open();
	}
})();
