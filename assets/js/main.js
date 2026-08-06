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
let pageFlip = null;

document.addEventListener("DOMContentLoaded", () => {
    initializeHomeFlipbook();
});

async function initializeHomeFlipbook() {
    const el = document.getElementById("flipbook");
    if (!el) return;

    try {
        console.log("STAR FAIR Home: Fetching latest magazine...");
        const response = await fetch('backend/magazine/list.php');
        if (response.ok) {
            const magazines = await response.json();
            if (Array.isArray(magazines) && magazines.length > 0) {
                const latest = magazines[0];
                
                // Update the text fields if they exist
                const magTitle = document.querySelector(".magazine-section .magazine-title");
                const magDesc = document.querySelector(".magazine-section .magazine-text");
                if (magTitle) magTitle.textContent = latest.title;
                if (magDesc) magDesc.textContent = latest.description;

                // Load pages from PDF
                await loadHomeFlipbookPDF(latest.pdfUrl);
                return;
            }
        }
    } catch (e) {
        console.warn("STAR FAIR Home: Failed to load dynamic flipbook, falling back to static html pages", e);
    }

    // Fallback: Initialize with whatever static HTML pages are in index.html
    initFlipbookFromHTML();
}

async function loadHomeFlipbookPDF(pdfUrl) {
    const el = document.getElementById("flipbook");
    if (!el) return;

    // Show a loading text or spinner
    el.innerHTML = `
        <div class="text-center py-5 w-100" style="color: #D4AF37;">
            <div class="spinner-border text-gold" style="color: #D4AF37; width: 3rem; height: 3rem;" role="status"></div>
            <p class="mt-3" style="font-weight: 600; color: #D4AF37;">Loading latest edition...</p>
        </div>
    `;

    try {
        if (typeof pdfjsLib === 'undefined') {
            throw new Error("PDF.js not loaded");
        }
        
        // Ensure worker is configured
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";

        const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
        const totalPages = pdf.numPages;

        el.innerHTML = ""; // Clear loader

        // 1. Create page divs immediately with placeholders
        const pageDivs = [];
        for (let i = 1; i <= totalPages; i++) {
            const pageDiv = document.createElement("div");
            pageDiv.className = "page";
            pageDiv.dataset.pageIndex = i;
            pageDiv.innerHTML = `
                <div class="page-placeholder text-center d-flex flex-column justify-content-center align-items-center" style="height: 100%; background: #1a1a1a; color: #D4AF37; padding: 20px;">
                    <div class="spinner-border spinner-border-sm text-gold" style="color: #D4AF37;" role="status"></div>
                    <span class="mt-2 text-muted small" style="font-size: 0.75rem;">Loading page ${i}...</span>
                </div>
            `;
            el.appendChild(pageDiv);
            pageDivs.push(pageDiv);
        }

        // Helper function for rendering a single page
        const renderHomeSinglePage = async (pageNumber, containerDiv) => {
            const page = await pdf.getPage(pageNumber);
            const viewport = page.getViewport({ scale: 1.1 });
            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");
            
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            await page.render({
                canvasContext: context,
                viewport: viewport
            }).promise;

            containerDiv.innerHTML = ""; // Clear placeholder
            containerDiv.appendChild(canvas);
        };

        // 2. Render Page 1 (Cover) immediately to show something to the user instantly
        await renderHomeSinglePage(1, pageDivs[0]);

        // 3. Initialize St.PageFlip immediately!
        initFlipbookFromHTML();

        // 4. Render the remaining pages in the background sequentially so it doesn't block the UI
        (async () => {
            for (let i = 2; i <= totalPages; i++) {
                try {
                    await renderHomeSinglePage(i, pageDivs[i - 1]);
                } catch (err) {
                    console.warn(`Home background render failed for page ${i}:`, err);
                }
            }
        })();

    } catch (err) {
        console.error("Home PDF load failed:", err);
        el.innerHTML = `
            <div class="text-center py-5 w-100 text-danger">
                <i class="fa-solid fa-triangle-exclamation mb-3" style="font-size: 2rem;"></i>
                <p>Failed to load PDF preview.</p>
                <p class="text-muted small">${err.message || err}</p>
            </div>
        `;
    }
}

function initFlipbookFromHTML() {
    const el = document.getElementById("flipbook");
    if (!el || !document.querySelectorAll("#flipbook .page").length) return;

    try {
        pageFlip = new St.PageFlip(el, {
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
        });

        pageFlip.loadFromHTML(document.querySelectorAll("#flipbook .page"));

        pageFlip.on("flip", () => {
            if (flipSound) {
                flipSound.currentTime = 0;
                flipSound.play().catch(() => {});
            }
        });
    } catch (err) {
        console.error("PageFlip init failed:", err);
    }
}



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