;(function($){
    'use strict';

    var $navItems = $('.th-left-nav .th-das-navbar li');
    var $tabs     = $('.th-das-content .tab-content');

    $navItems.find('a').on('click', function (e) {
        var href = $(this).attr('href');

        // Only handle hash links
        if (!href || href.charAt(0) !== '#') return;

        var $sec = $(href);
        if (!$sec.length) return;

        e.preventDefault();

        $tabs.removeClass('active');
        $sec.addClass('active');

        $navItems.removeClass('current-menu-item');
        $(this).closest('li').addClass('current-menu-item');
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
                submitBtn.attr('disabled', 'true');
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
                all.css('display', 'none');
                filteredItems.css('display', '');
            }
        }
    })
    
})(jQuery);