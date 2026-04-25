"use strict";

;(function($){


    function initMotionUIScrollAnimation($scope, $){
        
    }

    // init elementor frontend
    $(window).on('elementor/frontend/init', function(){

        var scrollAnimation = elementorModules.frontend.handlers.Base.extend({
            onInit: function() {
                if(this.$element.hasClass('mui-scroll-ani-yes')){
                    this.run();
                }
            },
            settings: function getReadySettings() {
                const triggerClassName = this.getElementSettings('mui_scroll_ani_trigger_class_name');
                const triggerChildSelector = this.getElementSettings('mui_scroll_ani_child_element_selector');
                const withScroll = this.getElementSettings('mui_scroll_ani_with_scroll');
                const ease = this.getElementSettings('mui_scroll_ani_ease');

                return {
                    triggerClassName: triggerClassName || false,
                    triggerChildSelector: triggerChildSelector || false,
                    withScroll: withScroll === 'yes',
                    ease: ease || false,
                };
            },
            onElementChange: function onElementChange(e){
                
            },
            run: function run() {
                let settings = this.settings();
                $element = this.$element;
                
                let y = $element.css('--mui-scroll-ani-y') || 0;
                let x = $element.css('--mui-scroll-ani-x') || 0;
                let rotate = $element.css('--mui-scroll-ani-rotate') || 0;
                let scale = $element.css('--mui-scroll-ani-scale') || 1;
                let opacity = $element.css('--mui-scroll-ani-opacity') || 1;
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(scrollAnimation, {
                $element: $scope
            });
        });
    }); 
    
})(jQuery);