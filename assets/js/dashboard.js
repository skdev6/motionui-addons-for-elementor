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

    // Card filtering: the category select and the search box both narrow the
    // same area, so each change re-runs both rules rather than one hiding what
    // the other just showed.
    $(document).on('change', 'select[data-area]', function(){
        filterCards($(this).data('area'));
    });

    $(document).on('input', 'input[type="text"][data-area]', function(){
        filterCards($(this).data('area'));
    });

    function filterCards(area){
        let $area = $(area);

        if(!$area.length) return;

        let rawTerm  = ($('input[type="text"][data-area="' + area + '"]').val() || '').trim();
        let category = $('select[data-area="' + area + '"]').val() || '*';
        let term     = rawTerm.toLowerCase();
        let visible  = 0;

        $area.find('> *').each(function(){
            let $card = $(this);

            // data-title and data-category are printed by Dashboard::switch_card;
            // the category attribute holds readable names, not the slug classes.
            let haystack = (($card.data('title') || '') + ' ' + ($card.data('category') || '')).toLowerCase();

            let inCategory = category === '*' || $card.is(category);
            let inSearch   = term === '' || haystack.indexOf(term) !== -1;
            let matches    = inCategory && inSearch;

            if(matches) visible++;

            $card.toggleClass('d-none', !matches);
        });

        showNoResults(area, visible === 0, rawTerm);
    }

    // Nothing left after filtering: offer to build the widget instead of
    // leaving an empty screen.
    function showNoResults(area, isEmpty, term){
        let $empty = $('.muia-no-results[data-empty-for="' + area + '"]');

        if(!$empty.length) return;

        $empty.toggleClass('d-none', !isEmpty);

        if(!isEmpty) return;

        $empty.find('.muia-no-results-term').text(term ? '“' + term + '”' : '');

        // Carry the search term over so the request arrives with the widget
        // name already in it.
        let $btn = $empty.find('.muia-request-widget-btn');
        let base = $btn.data('base-url');

        if(base){
            $btn.attr('href', term ? base + (base.indexOf('?') === -1 ? '?' : '&') + 'widget=' + encodeURIComponent(term) : base);
        }
    }

    $(document).on('click', '.is-pro.not-active-pro', function(e){
        if(!$(e.target).hasClass('th-doc-link')){
            $('.muia-pro-popup-wrap').addClass('open');
        }else{
            setTimeout(() => {
                 $('.muia-pro-popup-wrap').addClass('open');
            }, 1000);
        }
    });

    // Closes whichever popup the backdrop or × belongs to, so every popup gets
    // the behaviour without needing its own handler.
    $(document).on('click', '.muia-popup-wrap .backdrop, .muia-popup-wrap .muia-close-btn', function(){
        $(this).closest('.muia-popup-wrap').removeClass('open');
    });

})(jQuery);