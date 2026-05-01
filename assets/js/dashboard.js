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
    $('.muia-dashboard-form').each(function(){
        let submitBtn = $(this).find('[type="submit"]');
        $(this).find('.th-switch-control input[type="checkbox"]').on('change', function(){
            submitBtn.removeAttr('disabled');
        });
    });
    
})(jQuery);