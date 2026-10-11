/**
 * Live preview of the Custom CSS extension inside the Elementor editor.
 *
 * The saved CSS is printed by PHP (Custom_CSS::add_post_css); this adds the
 * same rules to the editor's own stylesheet as the control changes, so the
 * preview doesn't wait for a save.
 */
(function () {
	'use strict';

	if (typeof elementor === 'undefined' || !elementor.hooks) return;

	elementor.hooks.addFilter('editor/style/styleText', function (css, context) {
		if (!context || !context.model) return css;

		var custom = context.model.get('settings').get('muia_custom_css');
		if (!custom) return css;

		var selector = '.elementor-' + elementor.config.document.id +
			' .elementor-element.elementor-element-' + context.model.get('id');

		// The sheet lives in a <style> tag, so no markup may get through.
		custom = String(custom).replace(/<\/?[a-z!][^>]*>/gi, '');

		return css + custom.replace(/selector/g, selector);
	});
})();
