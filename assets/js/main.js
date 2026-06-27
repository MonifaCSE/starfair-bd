const heroSwiper = new Swiper(".heroSwiper", {

    loop: true,

    speed: 1800,

    effect: "fade",

    autoplay: {
        delay: 2000,
        disableOnInteraction: false
    },



    on: {

        init: function () {

            animateSlide();

        },

        slideChangeTransitionStart: function () {

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

/* AOS 

AOS.init({

    duration:1000,
    once:true,
    offset:100

});

*/


AOS.init({

    disable: window.innerWidth < 768

});


const filterButtons =
    document.querySelectorAll(".filter-btn");

const portfolioItems =
    document.querySelectorAll(".portfolio-item");

filterButtons.forEach(button => {

    button.addEventListener("click", () => {

        document
            .querySelector(".filter-btn.active")
            .classList.remove("active");

        button.classList.add("active");

        const filter =
            button.getAttribute("data-filter");

        portfolioItems.forEach(item => {

            if (
                filter === "all" ||
                item.classList.contains(filter)
            ) {

                item.style.display = "block";

            } else {

                item.style.display = "none";

            }

        });

    });

});




/* =========================
   FLIPBOOK
========================= */

const flipSound = document.getElementById("flipSound");

const pageFlip = new St.PageFlip(

    document.getElementById("flipbook"),

    {

        width: 550,
        height: 760,

        size: "stretch",

        showCover: true,

        usePortrait: false,

        mobileScrollSupport: true,

        drawShadow: true,

        maxShadowOpacity: 0.65,

        flippingTime: 900,

        startPage: 0,

        minWidth: 350,
        maxWidth: 900,

        minHeight: 500,
        maxHeight: 900

    }

);



/* =========================
   FLIP SOUND
========================= */

pageFlip.loadFromHTML(

    document.querySelectorAll("#flipbook .page")

);

pageFlip.on("flip", () => {

    flipSound.currentTime = 0;

    flipSound.play();

});



/* Courses Javascript*/


document.addEventListener("DOMContentLoaded", () => {

    const buttons = document.querySelectorAll(".details-btn");

    buttons.forEach(button => {

        button.addEventListener("click", () => {

            const card = button.closest(".program-card");

            const details = card.querySelector(".program-details");

            console.log("clicked");
            console.log(details);

            details.classList.toggle("active");

        });

    });

});