window.muia = window.muia || {};
function InitScrollMagic(options = {}) {
    const { trigger = null, duration = 0, start = 0, offset = 0, reverse = true, controller = null, onEnter = null,onLeave = null,onProgress = null, onUpdate = null, ...sceneOptions } = options;

    const smController = controller || new ScrollMagic.Controller();

    const scene = new ScrollMagic.Scene({
        triggerElement: trigger,
        duration,
        triggerHook: start,
        offset,
        reverse,
        ...sceneOptions
    });
    if (typeof onEnter === 'function') {
        scene.on('enter', onEnter);
    }
    if (typeof onLeave === 'function') {
        scene.on('leave', onLeave);
    }
    if (typeof onProgress === 'function') {
        scene.on('progress', onProgress);
    }
    if (typeof onUpdate === 'function') {
        scene.on('update', onUpdate);
    }
    scene.addTo(smController);
    return scene;
}
window.muia.initScrollMagic = InitScrollMagic;  


;(function(){

    function imageAni($scope, settings){
        console.log('imageAni', $scope, settings, 'This', this);
        
    }

    window.muia.imageAni = imageAni;
})(jQuery);