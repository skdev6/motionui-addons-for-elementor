;(function ($) {
    'use strict';
    function imageAni($scope, settings) {
        console.log("imageAni", $scope, settings);
        
    }

    muia.initElementorFrontend({
        extensions:{
            'muia-img-ani-yes': imageAni
        }
    });

})(jQuery);
