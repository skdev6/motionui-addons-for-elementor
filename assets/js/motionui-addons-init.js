;(function($){


    const frontend = window.motionUiAddonsFrontend;
    const widgets = {
        'muia-animate-button.default':frontend.button
    }
    // init elementor frontend
    $(window).on('elementor/frontend/init', function(){

        $.each(widgets, function(widget, fun){
           elementorFrontend.hooks.addAction('frontend/element_ready/' + widget, fun);
        });
          
        var widgetsAnimation = elementorModules.frontend.handlers.Base.extend({
            onInit: function() {
                if(this.$element.hasClass('has-muia-text-animation')) frontend.textAnimation(this.$element);
            },
            onElementChange: function onElementChange(e){
                if(this.$element.hasClass('has-muia-text-animation')) frontend.textAnimation(this.$element);
            }
        });
        
        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(widgetsAnimation, {
                $element: $scope
            });
        });
    }); 
    
})(jQuery);