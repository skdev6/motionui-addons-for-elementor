/**
 * Theme Builder display conditions popup for the Elementor editor.
 */
(function ($) {
	'use strict';

	var $popup = $('#muia-tb-conditions-popup');

	if (!$popup.length || typeof muiaThemeBuilder === 'undefined') {
		return;
	}

	var cfg = muiaThemeBuilder;
	var i18n = cfg.i18n;
	var registry = cfg.registry;
	var rows = (cfg.conditions || []).slice();
	var initialStatus = cfg.status;
	var lastOpened = 0;
	var acknowledged = false;

	var $rows = $popup.find('.muia-tb-condition-rows');
	var $notice = $popup.find('.muia-tb-notice');
	var $save = $popup.find('.muia-tb-save');
	var searchTimer = null;
	var cache = {};
	var PAGE_SIZE = 30;

	function open() {
		lastOpened = Date.now();
		acknowledged = false;
		$notice.removeClass('is-error is-warning').text('');
		$save.prop('disabled', false).text(i18n.save);
		render();
		$popup.addClass('open');
	}

	function close() {
		$popup.removeClass('open');
	}

	function label(name) {
		var item = registry[name];

		if (!item) {
			return name;
		}

		return item.sub_conditions && item.sub_conditions.length ? item.all_label : item.label;
	}

	function option(value, text, selected) {
		return $('<option>').val(value).text(text).prop('selected', selected);
	}

	function subSelect(row) {
		var $select = $('<select class="muia-form-control muia-tb-sub">');
		var parent = registry[row.name];

		$select.append(option('', parent ? parent.all_label : '', !row.sub_name));

		((parent && parent.sub_conditions) || []).forEach(function (child) {
			var item = registry[child];

			if (!item) {
				return;
			}

			if (item.sub_conditions && item.sub_conditions.length) {
				var $group = $('<optgroup>').attr('label', item.label);

				$group.append(option(child, item.all_label, row.sub_name === child));
				item.sub_conditions.forEach(function (grandchild) {
					if (registry[grandchild]) {
						$group.append(option(grandchild, registry[grandchild].label, row.sub_name === grandchild));
					}
				});
				$select.append($group);
				return;
			}

			$select.append(option(child, item.label, row.sub_name === child));
		});

		return $select;
	}

	function picker(row, $row) {
		var $wrap = $('<div class="muia-tb-picker">');
		var $input = $('<input type="text" class="muia-form-control" autocomplete="on">').attr('placeholder', i18n.search);
		var $list = $('<ul class="muia-tb-results">');
		var $chosen = $('<div class="muia-tb-chosen">');

		function showChosen() {
			$chosen.empty();

			if (row.sub_id) {
				$chosen.append($('<span>').text(row.sub_label || '#' + row.sub_id));
				$chosen.append($('<button type="button" class="muia-tb-clear">').attr('aria-label', i18n.clear).text('×'));
			}

			$input.toggle(!row.sub_id);

			// No specific object means "All": say so in the field instead of leaving it blank.
			if (!row.sub_id) {
				$input.val(i18n.all);
			}
		}

		// Fill the list: "All" first (it means no specific object), then the results.
		function renderList(results) {
			$list.empty().append($('<li class="is-all">').toggleClass('active', !row.sub_id).text(i18n.all).data('result', { id: 0, text: '' }));

			if (results && !results.length) {
				$list.append($('<li class="is-empty">').text(i18n.noResults));
				return;
			}

			(results || []).forEach(function (result) {
				$('<li>').toggleClass('active', row.sub_id === result.id).text(result.text).data('result', result).appendTo($list);
			});
		}

		// Load the results for a term, asking the server only for what has not been
		// fetched yet. A full first page (fewer rows than the server limit) is the
		// complete set, so every later search is answered from memory.
		function load(term) {
			var key = row.sub_name + '|' + term.toLowerCase();
			var all = cache[row.sub_name + '|'];

			if (cache[key]) {
				return $.Deferred().resolve(cache[key]).promise();
			}

			if (all && all.length < PAGE_SIZE) {
				cache[key] = all.filter(function (result) {
					return result.text.toLowerCase().indexOf(term.toLowerCase()) !== -1;
				});

				return $.Deferred().resolve(cache[key]).promise();
			}

			return $.post(cfg.ajaxUrl, {
				action: 'muia_theme_builder_search_objects',
				nonce: cfg.nonce,
				condition: row.sub_name,
				search: term
			}).then(function (response) {
				cache[key] = response && response.success ? response.data.results : [];

				return cache[key];
			});
		}

		$input.on('focus', function () {
			$input.val('');
			renderList();

			load('').done(function (results) {
				if ($input.is(':focus') && '' === $.trim($input.val())) {
					renderList(results);
				}
			});
		});

		// Delayed so a click on a list item lands before the list is cleared.
		$input.on('blur', function () {
			setTimeout(function () {
				$list.empty();

				if (!row.sub_id) {
					$input.val(i18n.all);
				}
			}, 150);
		});

		$input.on('input', function () {
			var term = $.trim($input.val());

			clearTimeout(searchTimer);

			searchTimer = setTimeout(function () {
				load(term).done(function (results) {
					// Ignore an answer for a term the user has since changed.
					if (term === $.trim($input.val())) {
						renderList(results);
					}
				});
			}, 250);
		});

		$list.on('click', 'li', function () {
			var result = $(this).data('result');

			if (!result) {
				return;
			}

			row.sub_id = result.id;
			row.sub_label = result.text;
			$list.empty();
			showChosen();
		});

		$chosen.on('click', '.muia-tb-clear', function () {
			row.sub_id = 0;
			row.sub_label = '';
			showChosen();
			$input.trigger('focus');
		});

		showChosen();

		return $wrap.append($chosen, $input, $list);
	}

	function renderRow(row, index) {
		var $row = $('<div class="muia-tb-condition-row">');
		var $type = $('<select class="muia-form-control muia-tb-type">')
			.append(option('include', i18n.include, 'exclude' !== row.type))
			.append(option('exclude', i18n.exclude, 'exclude' === row.type));
		var $name = $('<select class="muia-form-control muia-tb-name">');

		cfg.topLevel.forEach(function (item) {
			$name.append(option(item.name, item.label, row.name === item.name));
		});

		$type.on('change', function () {
			row.type = $type.val();
		});

		$name.on('change', function () {
			row.name = $name.val();
			row.sub_name = '';
			row.sub_id = 0;
			row.sub_label = '';
			render();
		});

		$row.append($type, $name);

		if ('general' !== row.name) {
			var $sub = subSelect(row);

			$sub.on('change', function () {
				row.sub_name = $sub.val();
				row.sub_id = 0;
				row.sub_label = '';
				render();
			});

			$row.append($sub);

			if (row.sub_name && registry[row.sub_name] && registry[row.sub_name].has_source) {
				$row.append(picker(row, $row));
			}
		}

		$row.append(
			$('<button type="button" class="muia-tb-remove eicon-trash">')
				.attr('aria-label', i18n.removeLabel)
				.on('click', function () {
					rows.splice(index, 1);
					render();
				})
		);

		return $row;
	}

	function render() {
		$rows.empty();
		rows.forEach(function (row, index) {
			$rows.append(renderRow(row, index));
		});
	}

	function payload() {
		return rows.map(function (row) {
			return {
				type: row.type,
				name: row.name,
				sub_name: row.sub_name || '',
				sub_id: row.sub_id || 0
			};
		});
	}

	function save() {
		if (acknowledged) {
			close();
			return;
		}

		$notice.removeClass('is-error is-warning').text('');
		$save.prop('disabled', true).text(i18n.saving);

		$.post(cfg.ajaxUrl, {
			action: 'muia_theme_builder_save_conditions',
			nonce: cfg.nonce,
			template_id: cfg.templateId,
			conditions: payload()
		}).done(function (response) {
			if (!response || !response.success) {
				$notice.addClass('is-error').text(response && response.data && response.data.message ? response.data.message : i18n.error);
				$save.prop('disabled', false).text(i18n.save);
				return;
			}

			rows = response.data.conditions || [];

			if (response.data.conflicts && response.data.conflicts.length) {
				acknowledged = true;
				$notice.addClass('is-warning').text(i18n.conflict.replace('%s', response.data.conflicts.join(', ')));
				$save.prop('disabled', false).text('OK');
				return;
			}

			close();
		}).fail(function () {
			$notice.addClass('is-error').text(i18n.error);
			$save.prop('disabled', false).text(i18n.save);
		});
	}

	$popup.on('click', '.backdrop, .muia-close-btn, .muia-close-btn-text', close);
	$popup.on('click', '.muia-tb-save', save);
	$popup.on('click', '.muia-tb-add-condition', function () {
		rows.push({ type: 'include', name: 'general', sub_name: '', sub_id: 0, sub_label: '' });
		render();
	});

	// Publishing is the moment the template needs to know where it goes: open the
	// popup the first time it is published, and whenever it still has no conditions.
	function maybeOpenAfterPublish() {
		if ($popup.hasClass('open') || Date.now() - lastOpened < 3000) {
			return;
		}

		if ('publish' !== initialStatus || !rows.length) {
			open();
		}
	}

	$(document).on('click', '#elementor-panel-saver-button-publish', function () {
		maybeOpenAfterPublish();
	});

	function onPanelInit() {
		if (elementor.saver && elementor.saver.on) {
			elementor.saver.on('after:save', function (data) {
				if (data && 'publish' === data.status) {
					maybeOpenAfterPublish();
					initialStatus = 'publish';
				}
			});
		}

		var footer = elementor.getPanelView && elementor.getPanelView().footer;

		if (footer && footer.currentView && footer.currentView.addSubMenuItem) {
			footer.currentView.addSubMenuItem('saver-options', {
				before: 'save-draft',
				name: 'muia-conditions',
				icon: 'themeic-muia-logo',
				title: i18n.menuTitle,
				callback: open
			});
		}
	}

	if ('undefined' !== typeof elementor) {
		elementor.on('panel:init', onPanelInit);
	}

	// Editor v2 app bar.
	var appBar = window.elementorV2 && window.elementorV2.editorAppBar;

	if (appBar && appBar.documentOptionsMenu) {
		appBar.documentOptionsMenu.registerAction({
			id: 'muia-template-conditions',
			group: ['save'],
			priority: 10,
			props: {
				icon: function () {
					return window.React ? window.React.createElement('i', { className: 'themeic-muia-logo' }) : null;
				},
				title: i18n.menuTitle,
				visible: true,
				onClick: function (event) {
					event.preventDefault();
					open();
				}
			}
		});
	}
})(jQuery);
