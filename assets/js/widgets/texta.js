window.muia = window.muia || {};
;(function(){
    function textAni($scope){
        console.log('textAni', $scope);
        
    }
    muia.initElementorFrontend({
        extensions: {
            'muia-img-ani-yes': textAni
        }
    });
})(jQuery);  