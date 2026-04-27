;(function($){

    gsap.registerPlugin(SplitText, ScrollTrigger);   
    // Init Button
    function button($scope){  
        let btn = $scope.find('.muia-btn');
        let buttonTextElement = $scope.find('.muia-btn-text');
        
        if(btn.hasClass('muia-btn-reveal')){
            var chars = new SplitType(buttonTextElement[0], {types:"chars"}).chars;    
            chars.forEach((el, index)=>{
                gsap.set(el, {'--index':index}); 
            })
        }
    }
    function textAnimation($scope){    
        let settings = $scope.data('settings');
        let textElement = $scope.find('.elementor-heading-title');

        if(!textElement.hasClass('muia-split-initialized')){
            textElement.addClass('muia-split-initialized');
            SplitText.create(textElement[0], {type:"chars", charsClass:"char-text"});   
        }
        let aniElements = textElement.find('.char-text'); 
        $scope.removeClass('visibility__hidden');
        gsap.set(aniElements, { opacity: 0, y : 20 });
        gsap.to(aniElements, { opacity: 1, y : 0, stagger: 0.05, ease: 'power4.out' });      
        console.log('Text Animation');
        
    }
    window.motionUiAddonsFrontend = {
        button,
        textAnimation
    };
      
})(jQuery);