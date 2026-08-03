;(function($){
    'use strict';

    var $navItems = $('.th-left-nav .th-das-navbar li');
    var $tabs     = $('.th-das-content .tab-content');

    // Restore the active tab from the URL hash (e.g. after the upload redirect)
    if (window.location.hash && $(window.location.hash).hasClass('tab-content')) {
        var $hashSec = $(window.location.hash);

        $tabs.removeClass('active');
        $hashSec.addClass('active');

        $navItems.removeClass('current-menu-item');
        $navItems.find('a[href="' + window.location.hash + '"]').closest('li').addClass('current-menu-item');
    }

    $navItems.find('a').on('click', function (e) {
        var href = $(this).attr('href');

        if (!href || href.charAt(0) !== '#') return;

        var $sec = $(href);
        if (!$sec.length) return;

        e.preventDefault();
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', href);
        }

        $tabs.removeClass('active');
        $sec.addClass('active');

        $navItems.removeClass('current-menu-item');
        $(this).closest('li').addClass('current-menu-item');
    });

    $('.import-widget-btn').click(function(){
        $(this).toggleClass('active');
        $('.upload-wrapper').toggleClass('active');
    });

    $('.muia-dashboard-form').on('submit', function(e){
        e.preventDefault();
        let type = $(this).data('type');
        let formData = $(this).serialize();
        let submitBtn = $(this).find('[type="submit"]');

        submitBtn.addClass('loading');
        submitBtn.append('<span class="spinner-border"></span>');
        
        $.post({
            url: muiaDashboard.ajaxUrl,
            data: {
                nonce: muiaDashboard.nonce,
                action: muiaDashboard.action,
                type:type,
                data:formData
            }, 
            success: function success(response) {
                submitBtn.removeClass('loading');
                submitBtn.find('.spinner-border').remove();
                submitBtn.prop('disabled', true);
            },
            error: function() {
                submitBtn.removeClass('loading');
                submitBtn.find('.spinner-border').remove();
            }
        });
    });

    

    $('.muia-enable-all').on('change', function() {  
        let isChecked = $(this).is(':checked');
        
        // Find all checkboxes EXCEPT the "Enable All" one itself
        let allCheckbox = $(this).closest('form')
            .find('.th-switch-control input[type="checkbox"]')
            .not('.pro-el')
            .not('.muia-enable-all');
        
        allCheckbox.prop('checked', isChecked);
        
        allCheckbox.trigger('change'); 
    });
    $('.muia-dashboard-form').each(function() {
        let $form = $(this);
        let submitBtn = $form.find('[type="submit"]');
        let enableCheckbox = $form.find('.muia-enable-all');
        let itemCheckboxes = $form.find('.th-switch-control input[type="checkbox"]').not('.muia-enable-all');

        itemCheckboxes.on('change', function() {
            submitBtn.removeAttr('disabled');

            let totalItems = itemCheckboxes.length;
            let checkedItems = itemCheckboxes.filter(':checked').length;

            if (totalItems === checkedItems) {
                enableCheckbox.prop('checked', true);
            } else {
                enableCheckbox.prop('checked', false);
            }
        });
    });

    $('.filter-navbar li a').on('click', function(){    
        let navbar = $(this).closest('.filter-navbar')
        let selector = $(navbar.data('area'));
        let visableItems = $(this).data('filter');
        let listItems = navbar.find('> li');
        let listItem = $(this).closest('li');

        listItems.removeClass('current-menu-item');
        listItem.addClass('current-menu-item');

        if(selector.length){
            let all = selector.find('> *');
            let filteredItems = visableItems === '*' ? all : $(visableItems);
            if(filteredItems.length){
                all.addClass('d-none');
                filteredItems.removeClass('d-none');
            }
        }
    });

    $('.is-pro.not-active-pro').on('click', function(e){   
        if(!$(e.target).hasClass('th-doc-link')){
            $('.muia-pro-popup-wrap').addClass('open');
        }else{
            setTimeout(() => {
                 $('.muia-pro-popup-wrap').addClass('open');
            }, 1000);
        }
    });

    $('.muia-close-btn,.muia-popup-wrap .backdrop').on('click', function(){
        $('.muia-pro-popup-wrap').removeClass('open');
    });

    // Delete custom widget: confirm via popup, then remove over AJAX.
    var $deletePopup = $('.muia-delete-popup-wrap');
    var deleteWidgetSlug = '';
    var $deleteWidgetCard = null;

    $('.delete-custom-widget').on('click', function(){
        deleteWidgetSlug  = $(this).data('widget');
        $deleteWidgetCard = $(this).closest('.th-widget-card');

        $deletePopup.find('.muia-delete-widget-name').text(
            $deleteWidgetCard.find('.title').text().trim()
        );
        $deletePopup.addClass('open');
    });

    $deletePopup.find('.muia-cancel-delete, .muia-close-btn, .backdrop').on('click', function(){
        $deletePopup.removeClass('open');
    });

    $deletePopup.find('.muia-confirm-delete').on('click', function(){
        var submitBtn = $(this);

        if (!deleteWidgetSlug || submitBtn.hasClass('loading')) return;

        submitBtn.addClass('loading');
        submitBtn.append('<span class="spinner-border"></span>');

        $.post(muiaDashboard.ajaxUrl, {
            action: 'muia_delete_custom_widget',
            nonce: muiaDashboard.nonce,
            widget: deleteWidgetSlug
        }).done(function(response){
            if (response && response.success && $deleteWidgetCard) {
                $deleteWidgetCard.remove();

                // Last widget deleted: show the empty state.
                if (!$('.muia-installed-custom-widgets .th-widget-card').length) {
                    $('.muia-installed-custom-widgets').remove();
                    $('.muia-no-custom-widgets').show();
                }
            }
        }).always(function(){
            submitBtn.removeClass('loading');
            submitBtn.find('.spinner-border').remove();
            $deletePopup.removeClass('open');
            deleteWidgetSlug  = '';
            $deleteWidgetCard = null;
        });
    });

})(jQuery);