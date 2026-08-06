// ==========================================
// DYNAMIC MAGAZINE INTEGRATION (PHP + MYSQL)
// ==========================================

let magazines = [];

let pageFlip = null;
const flipSound = document.getElementById("flipSound");

// Initialize Magazine Section
document.addEventListener("DOMContentLoaded", () => {
    initializeMagazineApp();
});

async function initializeMagazineApp() {
    let fetchError = null;
    try {
        console.log("STAR FAIR: Fetching dynamic magazines from PHP Database...");
        const response = await fetch('backend/magazine/list.php');
        if (response.ok) {
            const fetchedMagazines = await response.json();
            if (Array.isArray(fetchedMagazines)) {
                magazines = fetchedMagazines;
                console.log(`STAR FAIR: Loaded ${magazines.length} magazines from database.`);
            } else {
                fetchError = "Invalid data format received from server.";
            }
        } else {
            fetchError = `Server returned status ${response.status}: ${response.statusText}`;
        }
    } catch (error) {
        console.error("STAR FAIR: Failed to query PHP backend:", error);
        fetchError = error.message || error;
    }

    if (fetchError) {
        // Display database loading error in the issues section container
        const container = document.querySelector(".issues-section .row");
        if (container) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-triangle-exclamation text-warning mb-3" style="font-size: 2.5rem; color: var(--gold);"></i>
                    <p class="text-gold" style="color: var(--gold); font-size: 1.2rem; font-weight: 500;">Failed to load magazines from database.</p>
                    <p class="text-muted small">${fetchError}</p>
                </div>
            `;
        }
        // Update the viewer as well
        const viewerTitle = document.getElementById("viewerTitle");
        const viewerDesc = document.getElementById("viewerDescription");
        if (viewerTitle) viewerTitle.textContent = "DATABASE ERROR";
        if (viewerDesc) viewerDesc.textContent = "Could not fetch publication data.";
        const viewerBox = document.querySelector(".viewer-box");
        if (viewerBox) {
            viewerBox.innerHTML = `
                <div class="text-center py-5">
                    <p class="text-muted">Error details: ${fetchError}</p>
                </div>
            `;
        }
        return;
    }

    // 2. Render dynamic grid cards
    renderMagazineCards();

    // 3. Load initial active issue (index 0)
    if (magazines.length > 0) {
        loadMagazine(magazines[0]);
        
        // Update hero cover image
        const heroCoverImg = document.getElementById("magazineHeroCover");
        if (heroCoverImg) {
            const defaultCover = "assets/images/magazine/page1.jpg";
            heroCoverImg.src = magazines[0].coverUrl || defaultCover;
            heroCoverImg.alt = magazines[0].title;
        }
    } else {
        const viewerTitle = document.getElementById("viewerTitle");
        const viewerDesc = document.getElementById("viewerDescription");
        if (viewerTitle) viewerTitle.textContent = "NO DIGITAL EDITIONS AVAILABLE";
        if (viewerDesc) viewerDesc.textContent = "There are no magazine issues published yet.";
        const viewerBox = document.querySelector(".viewer-box");
        if (viewerBox) {
            viewerBox.innerHTML = `
                <div class="text-center py-5">
                    <p class="text-muted">Once magazines are published from the Admin Panel, they will appear here.</p>
                </div>
            `;
        }
    }
}

// Render issues grid markup matching design styles
function renderMagazineCards() {
    const container = document.querySelector(".issues-section .row");
    if (!container) return;

    container.innerHTML = ""; // Wipe static HTML

    if (magazines.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <p class="text-gold" style="color: var(--gold); font-size: 1.2rem; font-weight: 500;">No magazine issues published yet.</p>
            </div>
        `;
        return;
    }

    magazines.forEach((mag, index) => {
        const col = document.createElement("div");
        col.className = "col-lg-3 col-md-6";

        const isLatest = index === 0;
        const defaultCover = `assets/images/magazine/page${(index % 4) + 1}.jpg`;

        col.innerHTML = `
            <div class="issue-card">
                <div class="issue-image">
                    <img src="${mag.coverUrl || defaultCover}" alt="${mag.title}">
                    ${isLatest ? '<span class="issue-badge">Latest</span>' : ''}
                </div>
                <div class="issue-content">
                    <small>${mag.dateText || "Released Edition"}</small>
                    <h4>${mag.title}</h4>
                    <p>${mag.description}</p>
                    <a href="#viewer" class="issue-btn" data-issue="${index}">
                        Read Now
                    </a>
                </div>
            </div>
        `;
        container.appendChild(col);
    });

    // Re-bind click event listeners to new dynamic buttons
    document.querySelectorAll(".issue-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const issueIndex = parseInt(btn.dataset.issue);
            const issue = magazines[issueIndex];
            
            if (flipSound) {
                flipSound.currentTime = 0;
                flipSound.volume = 0.4;
                flipSound.play().catch(() => {});
            }
            
            loadMagazine(issue);
            
            setTimeout(() => {
                const viewerSection = document.getElementById("viewer");
                if (viewerSection) {
                    viewerSection.scrollIntoView({ behavior: "smooth", block: "start" });
                }
            }, 100);
        });
    });

    // Refresh AOS animations if dynamic cards were added
    if (typeof AOS !== 'undefined') {
        AOS.refresh();
    }
}

// Load magazine pages using PDF.js and StPageFlip
async function loadMagazine(issue) {
    const viewerTitle = document.getElementById("viewerTitle");
    const viewerDesc = document.getElementById("viewerDescription");
    if (viewerTitle) viewerTitle.textContent = issue.title;
    if (viewerDesc) viewerDesc.textContent = issue.description;

    // Destroy existing PageFlip instance
    if (pageFlip) {
        try { pageFlip.destroy(); } catch(e) { console.warn("PageFlip destroy warning:", e); }
        pageFlip = null;
    }

    const viewerBox = document.querySelector(".viewer-box");
    if (!viewerBox) return;

    viewerBox.innerHTML = "";

    const loader = document.createElement("div");
    loader.id = "magazine-loader";
    loader.className = "text-center py-5";
    loader.innerHTML = `
        <div class="spinner-border text-gold" style="color: #D4AF37;" role="status"></div>
        <p class="mt-3" style="color: #D4AF37; font-weight: 600;">Loading magazine from PDF...</p>
        <p class="text-muted small" style="font-size:0.8rem;">Rendering high-resolution pages...</p>
    `;
    viewerBox.appendChild(loader);

    const newFlipbook = document.createElement("div");
    newFlipbook.id = "flipbook";
    viewerBox.appendChild(newFlipbook);

    try {
        if (typeof pdfjsLib === 'undefined') {
            throw new Error("PDF.js library is not loaded.");
        }

        const pdf = await pdfjsLib.getDocument(issue.pdfUrl).promise;
        const totalPages = pdf.numPages;

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
            newFlipbook.appendChild(pageDiv);
            pageDivs.push(pageDiv);
        }

        // Helper function for rendering a single page
        const renderSinglePage = async (pageNumber, containerDiv) => {
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
        await renderSinglePage(1, pageDivs[0]);

        // Hide the overall loader since the flipbook can be shown now
        loader.style.display = "none";

        // 3. Initialize St.PageFlip immediately!
        setTimeout(() => {
            initFlipbook(totalPages);
        }, 50);

        // 4. Render the remaining pages in the background sequentially so it doesn't block the UI
        (async () => {
            for (let i = 2; i <= totalPages; i++) {
                try {
                    await renderSinglePage(i, pageDivs[i - 1]);
                } catch (err) {
                    console.warn(`Background render failed for page ${i}:`, err);
                }
            }
        })();

    } catch (err) {
        console.error("PDF loading/rendering failed:", err);
        loader.innerHTML = `
            <i class="fa-solid fa-triangle-exclamation text-danger mb-3" style="font-size: 2.5rem;"></i>
            <p class="text-danger">Failed to load magazine PDF.</p>
            <p class="text-muted small">${err.message}</p>
        `;
    }
}

function initFlipbook(totalPages) {
    const el = document.getElementById("flipbook");
    if (!el || typeof St === 'undefined' || !St.PageFlip) {
        console.error("PageFlip elements or libraries not ready.");
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
            if (flipSound) {
                flipSound.currentTime = 0;
                flipSound.volume = 0.4;
                flipSound.play().catch(() => {});
            }
        });

        console.log("PageFlip initialized successfully with", totalPages, "pages");

    } catch(err) {
        console.error("PageFlip init failed:", err);
    }
}