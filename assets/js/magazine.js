// =========================
// MAGAZINE DATA
// =========================

const magazines = [
    {
        title: "Fashion Edition",
        description: "Fashion, Beauty & Lifestyle",
        pdfUrl: "assets/pdf/magazine/flipbook.pdf"
    },
    {
        title: "Beauty Edition",
        description: "Beauty & Pageantry",
        pdfUrl: "assets/pdf/magazine/flipbook.pdf"
    },
    {
        title: "Culture Edition",
        description: "Culture & Art",
        pdfUrl: "assets/pdf/magazine/flipbook.pdf"
    },
    {
        title: "Lifestyle Edition",
        description: "Lifestyle & Events",
        pdfUrl: "assets/pdf/magazine/flipbook.pdf"
    }
];

// =========================
// STATE
// =========================

let pageFlip = null;
const flipSound = document.getElementById("flipSound");

// =========================
// BUTTON CLICKS
// =========================

document.querySelectorAll(".issue-btn").forEach(btn => {
    btn.addEventListener("click", (e) => {
        e.preventDefault();
        const issueIndex = parseInt(btn.dataset.issue);
        const issue = magazines[issueIndex];
        
        // play flip sound
        flipSound.currentTime = 0;
        flipSound.volume = 0.4;
        flipSound.play().catch(() => {});
        
        loadMagazine(issue);
        setTimeout(() => {
            document.getElementById("viewer").scrollIntoView({ behavior: "smooth" });
        }, 100);
    });
});

// =========================
// LOAD MAGAZINE FROM PDF
// =========================

async function loadMagazine(issue) {
    document.getElementById("viewerTitle").textContent = issue.title;
    document.getElementById("viewerDescription").textContent = issue.description;

    // Step 1: Kill existing PageFlip safely
    if (pageFlip) {
        try { pageFlip.destroy(); } catch(e) { console.warn("destroy error:", e); }
        pageFlip = null;
    }

    // Step 2: Clear viewer-box and create a loader element
    const viewerBox = document.querySelector(".viewer-box");
    viewerBox.innerHTML = "";  // Wipe previous contents

    const loader = document.createElement("div");
    loader.id = "magazine-loader";
    loader.className = "text-center py-5";
    loader.innerHTML = `
        <div class="spinner-border text-gold" style="color: #D4AF37;" role="status"></div>
        <p class="mt-3" style="color: #D4AF37; font-weight: 600;">Loading magazine from PDF...</p>
        <p class="text-muted small" style="font-size:0.8rem;">Rendering high-resolution pages...</p>
    `;
    viewerBox.appendChild(loader);

    // Step 3: Create a brand new flipbook wrapper element (hidden initially)
    const newFlipbook = document.createElement("div");
    newFlipbook.id = "flipbook";
    newFlipbook.style.display = "none";
    viewerBox.appendChild(newFlipbook);

    try {
        // Step 4: Fetch and render PDF pages using PDF.js
        const pdf = await pdfjsLib.getDocument(issue.pdfUrl).promise;
        const totalPages = pdf.numPages;

        for (let i = 1; i <= totalPages; i++) {
            const page = await pdf.getPage(i);
            
            // Render at high resolution scale (1.5x)
            const viewport = page.getViewport({ scale: 1.5 });
            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");
            
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            await page.render({
                canvasContext: context,
                viewport: viewport
            }).promise;

            const pageDiv = document.createElement("div");
            pageDiv.className = "page";
            pageDiv.appendChild(canvas);
            newFlipbook.appendChild(pageDiv);
        }

        // Hide loader & show flipbook
        loader.style.display = "none";
        newFlipbook.style.display = "block";

        // Step 5: Force browser reflow and initialize StPageFlip
        void newFlipbook.offsetHeight;
        
        setTimeout(() => {
            initFlipbook(totalPages);
        }, 50);

    } catch (err) {
        console.error("PDF loading/rendering failed:", err);
        loader.innerHTML = `
            <i class="fa-solid fa-triangle-exclamation text-danger mb-3" style="font-size: 2.5rem;"></i>
            <p class="text-danger">Failed to load magazine PDF.</p>
            <p class="text-muted small">${err.message}</p>
        `;
    }
}

// =========================
// INIT FLIPBOOK
// =========================

function initFlipbook(totalPages) {
    const el = document.getElementById("flipbook");

    if (!el) {
        console.error("flipbook element not found");
        return;
    }

    try {
        pageFlip = new St.PageFlip(el, {
            width: 600,
            height: 800,
            size: "stretch",
            showCover: true,
            usePortrait: window.innerWidth < 768,
            mobileScrollSupport: true,
            drawShadow: true,
            maxShadowOpacity: 0.6,
            flippingTime: 900,
            minWidth: 260,
            maxWidth: 900,
            minHeight: 350,
            maxHeight: 900
        });

        pageFlip.loadFromHTML(document.querySelectorAll("#flipbook .page"));

        pageFlip.on("flip", () => {
            flipSound.currentTime = 0;
            flipSound.volume = 0.4;
            flipSound.play().catch(() => {});
        });

        console.log("PageFlip initialized successfully with", totalPages, "pages");

    } catch(err) {
        console.error("PageFlip init failed:", err);
    }
}

// Initialize the first issue on page load
document.addEventListener("DOMContentLoaded", () => {
    const defaultIssue = magazines[0];
    loadMagazine(defaultIssue);
});