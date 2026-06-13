const heroSwiper = new Swiper(".heroSwiper", {

    loop:true,

    speed:1800,

    effect:"fade",

    autoplay:{
        delay:2000,
        disableOnInteraction:false
    },

    

    on:{

        init:function(){

            animateSlide();

        },

        slideChangeTransitionStart:function(){

            animateSlide();

        }

    }

});

function animateSlide() {

    gsap.killTweensOf(".hero-title");
    gsap.killTweensOf(".hero-line");
    gsap.killTweensOf(".hero-desc");
    gsap.killTweensOf(".hero-btn");

    gsap.set(".hero-title", { opacity: 0, y: 80 });
    gsap.set(".hero-line", { width: 0 });
    gsap.set(".hero-desc", { opacity: 0, y: 50 });
    gsap.set(".hero-btn", { opacity: 0, y: 50 });

    gsap.to(".swiper-slide-active .hero-title", {
        opacity: 1,
        y: 0,
        duration: 1
    });

    gsap.to(".swiper-slide-active .hero-line", {
        width: 220,
        duration: 1,
        delay: .5
    });

    gsap.to(".swiper-slide-active .hero-desc", {
        opacity: 1,
        y: 0,
        duration: 1,
        delay: .8
    });

    gsap.to(".swiper-slide-active .hero-btn", {
        opacity: 1,
        y: 0,
        duration: 1,
        delay: 1.2
    });

}