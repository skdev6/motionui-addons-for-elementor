const SLIDE_EFFECTS = {
    fade: {
        effect: 'fade',
        fadeEffect: { crossFade: true }
    },
    cube: {
        effect: 'cube',
        cubeEffect: { shadow: true, slideShadows: true, shadowOffset: 20, shadowScale: 0.94 }
    },
    coverflow: {
        effect: 'coverflow',
        coverflowEffect: { rotate: 30, stretch: 0, depth: 100, modifier: 1, slideShadows: true }
    },
    flip: {
        effect: 'flip',
        flipEffect: { slideShadows: true, limitRotation: true }
    },
    cards: {
        effect: 'cards',
        cardsEffect: { perSlideOffset: 8, perSlideRotate: 2, slideShadows: true }
    },
    creative: {
        effect: 'creative',
        creativeEffect: {
            prev: { shadow: true, translate: ['-20%', 0, -1] },
            next: { translate: ['100%', 0, 0] }
        }
    }
};
const SINGLE_SLIDE_EFFECTS = ['fade', 'cube', 'flip', 'cards', 'creative'];

function themeicSlide($scope, options = {}) {

    let root = $scope[0];
    
    if (!root || typeof Swiper === 'undefined') return;
    console.log(root);
    
    const {
        clickableSelector = '.themeic-slide-inner',
        dEffect = 'slide',
        loop,
        slideOptions = {}
    } = options;

    const slideSettings = $scope.find('[data-slide-settings]').data('slide-settings') || {};

    const overlay = slideSettings.effect === 'overlay';
    const effect = slideSettings.effect || dEffect;   
    const el = root.querySelector('.swiper');


    const prevBtn = root.querySelector('.themeic-slide-prev');
    const nextBtn = root.querySelector('.themeic-slide-next');
    const pagination = root.querySelector('.themeic-slide-pagination');

    let slidesPerView = readPx(root, '--slide-per-view', 3);
    let spaceBetween = readPx(root, '--slide-gap', 16);
    let speed = readPx(root, '--slide-speed', 600);

    let thumbsWidthPercent = readThumbsWidth();
    let loopExtra = readLoopExtra();
    let isDragging = false;

    const isLoop = slideSettings.loop;
  
    const manualNav = overlay && isLoop && (prevBtn || nextBtn);

    let swiper = null;

    if (el){
        root.classList.toggle('has-overlay-slide', overlay);

        swiper = new Swiper(el, {
            spaceBetween: spaceBetween,
            speed: speed,
            loop: isLoop,
            grabCursor: true,
            keyboard: { enabled: true },
            loopAdditionalSlides: loopExtra,
            loopAddBlankSlides: true,
            ...(overlay
                ? {
                    effect: 'fade',
                    fadeEffect: { crossFade: true },
                    watchSlidesProgress: true,
                }
                : {
                    ...(SLIDE_EFFECTS[effect] || {}),
                    slidesPerView: SINGLE_SLIDE_EFFECTS.indexOf(effect) > -1 ? 1 : slidesPerView,
                }),

            ...(!manualNav && (prevBtn || nextBtn)
                ? { navigation: { nextEl: nextBtn, prevEl: prevBtn } }
                : {}),
            ...(pagination
                ? {
                    pagination: {
                        el: pagination,
                        clickable: true,
                        type: pagination.dataset.pagination || 'bullets',
                    },
                }
                : {}),
            ...slideOptions,
            on: {
                touchStart() {
                    isDragging = true;
                    el.classList.add('slide-changing');
                },
                touchEnd() {
                    isDragging = false;
                    el.classList.remove('slide-changing');
                },
                resize(instance) {
                    syncTokens(instance);
                },
                orientationchange(instance) {
                    syncTokens(instance);
                },
                progress(swiper) {
                    if(!overlay) return;
                    const duration = isDragging ? 0 : 0.8;
                    
                    if(!isDragging) el.classList.add('slide-changing');

                    swiper.slides.forEach((slide, index) => {
                        const progress = slide.progress;
                        const slideInner = slide.querySelector('.themeic-slide-inner');
                        
                        slide.setAttribute('data-index', index.toString());
                        
                        if (!slideInner) return;

                        const stacked = Math.min(Math.abs(Math.min(progress, 0)), 1);

                        gsap.to(slide, {
                            '--sp': stacked,
                           
                            '--fs-scale': 1 - stacked * 0.5,
                            ease: 'expo.out',
                            duration,
                            overwrite: true,
                            onComplete(){
                                if(!isDragging){
                                    el.classList.remove('slide-changing');
                                }
                            }
                        });

                        if (progress >= 0) {
                            const clampedProgress = Math.min(progress, 1);

                            gsap.to(slideInner, {
                                width: '100%',
                                x: 0,
                                scale: 1 - (clampedProgress * 0.2),
                                opacity: 1 - clampedProgress,
                                ease: 'expo.out',
                                duration,
                                overwrite: true
                            });
                        } else {
                            const n    = Math.abs(progress);
                            const p    = Math.min(n, 1);
                            const full = slide.offsetWidth;
                            
                            const collapsed = full * thumbsWidthPercent / 100;

                            const x = p * (full + spaceBetween) + Math.max(n - 1, 0) * (collapsed + spaceBetween);

                            gsap.to(slideInner, {
                                width: `${100 - p * (100 - thumbsWidthPercent)}%`,
                                x,
                                scale: 1,
                                opacity: 1,
                                ease: 'expo.out',
                                duration,
                                overwrite: true
                            });
                        }
                    });
                },
                ...slideOptions.on,
            },
        });

        if(manualNav){
            
            const step = function(method){
                return function(e){
                    e.preventDefault();

                    if(!swiper || swiper.destroyed) return;

                    swiper.animating = false;
                    swiper[method]();
                };
            };

            if(nextBtn) nextBtn.addEventListener('click', step('slideNext'));
            if(prevBtn) prevBtn.addEventListener('click', step('slidePrev'));
        }

        if(overlay){
            el.addEventListener('click', function(e){
                const innerItem = e.target.closest(clickableSelector);
                if(!innerItem) return;

                const slideIndex = parseInt(innerItem.closest('.swiper-slide')?.getAttribute('data-index'), 10);
                if(Number.isInteger(slideIndex)) swiper.slideTo(slideIndex);
            });
        }
    }

    function syncTokens(instance) {
        thumbsWidthPercent = readThumbsWidth();

        const nextPerView = readPx(root, '--slide-per-view', 3);
        const nextGap = readPx(root, '--slide-gap', 16);

        const perViewChanged = nextPerView !== slidesPerView;
        const gapChanged = nextGap !== spaceBetween;

        slidesPerView = nextPerView;
        spaceBetween = nextGap;

        const nextLoopExtra = readLoopExtra();
        const loopExtraChanged = nextLoopExtra !== loopExtra;

        loopExtra = nextLoopExtra;

        if (!instance || instance.destroyed) return;
        if (!perViewChanged && !gapChanged && !loopExtraChanged) return;

        instance.params.spaceBetween = spaceBetween;

        if (!overlay) instance.params.slidesPerView = slidesPerView;

        if (loopExtraChanged && instance.params.loop && typeof instance.loopCreate === 'function') {
            instance.params.loopAdditionalSlides = loopExtra;
            instance.loopDestroy();
            instance.loopCreate(instance.realIndex);
        }

        instance.update();
    }

    function readThumbsWidth() {
        return gsap.utils.clamp(0, 100, readPx(root, '--thum-width-percent', 20));
    }

    function readLoopExtra() {
        const fallback = gsap.utils.clamp(1, 4, slidesPerView) - 1;

        return Math.max(0, Math.round(readPx(root, '--slide-loop-extra', fallback)));
    }

    function readPx(node, name, fallback) {
        const raw = getComputedStyle(node).getPropertyValue(name).trim();
        if (!raw) return fallback;

        const value = parseFloat(raw);
        if (isNaN(value)) return fallback;

        if (raw.endsWith('rem')) {
            return value * parseFloat(getComputedStyle(document.documentElement).fontSize);
        }

        if (raw.endsWith('vw')) {
            return (value / 100) * document.documentElement.clientWidth;
        }

        if (raw.endsWith('s') && !raw.endsWith('ms')) {
            return value * 1000;
        }

        return value;
    }

    return swiper;
}

muia.initElementorFrontend({
    widgets: {
        'themeic-testimonial.default': themeicSlide
    }
});