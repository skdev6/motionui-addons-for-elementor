jQuery(window).on('elementor:init', function () {
    elementor.hooks.addAction('panel/open_editor/widget', function () {
        console.log('Hello');
    });
});