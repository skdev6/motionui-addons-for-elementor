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

        checkHasParentMenuItem();
    }
    function checkHasParentMenuItem(){   
        $(".th-das-navbar li").removeClass('current-menu-parent');
        $(".th-das-navbar li.current-menu-item").closest('.submenu').parent('li').addClass('current-menu-parent');   
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

        checkHasParentMenuItem();
    });

    $('.import-widget-btn').click(function(){
        $(this).toggleClass('active');
        $('.upload-wrapper').toggleClass('active');
    });

    $(document).on('submit', '.muia-dashboard-form', function(e){
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

    

    $(document).on('change', '.muia-enable-all', function() {
        let isChecked = $(this).is(':checked');

        // Find all checkboxes EXCEPT the "Enable All" one itself
        let allCheckbox = $(this).closest('form')
            .find('.th-switch-control input[type="checkbox"]')
            .not('.pro-el')
            .not('.muia-enable-all');

        allCheckbox.prop('checked', isChecked);

        allCheckbox.trigger('change');
    });
    // Delegated, and everything resolved at event time rather than captured on
    // load, so this keeps working after the widget list is replaced with the
    // markup returned by the catalog refresh.
    $(document).on('change', '.muia-dashboard-form .th-switch-control input[type="checkbox"]', function() {
        let $checkbox = $(this);

        if ($checkbox.hasClass('muia-enable-all')) return;

        let $form = $checkbox.closest('.muia-dashboard-form');
        if (!$form.length) return;

        let itemCheckboxes = $form.find('.th-switch-control input[type="checkbox"]').not('.muia-enable-all');

        $form.find('[type="submit"]').removeAttr('disabled');

        let totalItems   = itemCheckboxes.length;
        let checkedItems = itemCheckboxes.filter(':checked').length;

        $form.find('.muia-enable-all').prop('checked', totalItems === checkedItems);
    });

    $(document).on('click', '.filter-navbar li a', function(){
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

    // $(document).on('click', '.is-pro.not-active-pro', function(e){
    //     if(!$(e.target).hasClass('th-doc-link')){
    //         $('.muia-pro-popup-wrap').addClass('open');
    //     }else{
    //         setTimeout(() => {
    //              $('.muia-pro-popup-wrap').addClass('open');
    //         }, 1000);
    //     }
    // });
    $(document).on('click', '.th-widget-card .get_widget-btn', function(e){
        let allWidget = $('.themeic-das-wrap').data('all-widget');
        let urlWidget = $(this).attr('href');
        // The name comes from the card that was clicked, not from the popup —
        // the popup's own heading is what we are about to fill in.
        let title = $(this).closest('.th-widget-card').find('.title').text().trim();

        // No bundle URL configured: leave the button as a plain link so the
        // card still goes somewhere useful.
        if(!allWidget) return;

        e.preventDefault();

        let $popup = $('.muia-get-widget-popup-wrap');

        // The popup is shared by every card, so its buttons and title are
        // filled in per click rather than rendered per widget.
        $popup.find('.muia-get-this-widget').attr('href', urlWidget);
        $popup.find('.muia-get-all-widgets').attr('href', allWidget);
        $popup.find('.muia-get-widget-name').text(title);
        $popup.find('.widget-title-text').text(title);

        $popup.addClass('open');
    });

    // Closes whichever popup the backdrop or × belongs to, so every popup gets
    // the behaviour without needing its own handler.
    $(document).on('click', '.muia-popup-wrap .backdrop, .muia-popup-wrap .muia-close-btn', function(){
        $(this).closest('.muia-popup-wrap').removeClass('open');
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

    function refreshWidgetCatalog(){
        if (!muiaDashboard.catalogUrl || !muiaDashboard.catalogAction) return;

        $.ajax({
            url: muiaDashboard.catalogUrl,
            method: 'GET',
            dataType: 'json',
            cache: false,
            success: function (data) {
                if (!data) return;
                if(data.get_all_widgets_url) $('.themeic-das-wrap').attr('data-all-widget', data.get_all_widgets_url);
                $.post(muiaDashboard.ajaxUrl, {
                    action: muiaDashboard.catalogAction,
                    nonce: muiaDashboard.nonce,
                    catalog: JSON.stringify(data)
                },
                function({data, success}) {
                    console.log(data);
                    
                    if(data.updated){
                        $('#muia-widgets').html(data.html);
                    }
                }
                ).fail(function () {
                    console.error('Could not store the widget catalog.');
                })
            },
            error: function (xhr, status, error) {
                // Not fatal: the stored catalog keeps being used.
                console.error('Failed to fetch the widget catalog:', error);
            }
        });
    }

    $(window).on('load', refreshWidgetCatalog);

})(jQuery);