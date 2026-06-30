// =========================
// MAGAZINE DATA
// =========================

const magazines = [
    {
        title: "Fashion Edition",
        description: "Fashion, Beauty & Lifestyle",
        folder: "issue/issue1",
        totalPages: 6
    },
    {
        title: "Beauty Edition",
        description: "Beauty & Pageantry",
        folder: "issue/issue2",
        totalPages: 6
    },
    {
        title: "Culture Edition",
        description: "Culture & Art",
        folder: "issue/issue3",
        totalPages: 6
    },
    {
        title: "Lifestyle Edition",
        description: "Lifestyle & Events",
        folder: "issue/issue4",
        totalPages: 6
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
        const issue = magazines[parseInt(btn.dataset.issue)];
        flipSound.currentTime = 0;
        flipSound.play().catch(() => {});
        loadMagazine(issue);
        setTimeout(() => {
            document.getElementById("viewer").scrollIntoView({ behavior: "smooth" });
        }, 100);
    });
});

// =========================
// LOAD MAGAZINE
// =========================

function loadMagazine(issue) {
    document.getElementById("viewerTitle").textContent = issue.title;
    document.getElementById("viewerDescription").textContent = issue.description;

    // Step 1: Kill existing PageFlip safely
    if (pageFlip) {
        try { pageFlip.destroy(); } catch(e) { console.warn("destroy error:", e); }
        pageFlip = null;
    }

    // Step 2: Get the viewer-box and fully reset it
    const viewerBox = document.querySelector(".viewer-box");
    viewerBox.innerHTML = "";  // Wipe EVERYTHING including the corrupted flipbook node

    // Step 3: Create a brand new flipbook wrapper
    const newFlipbook = document.createElement("div");
    newFlipbook.id = "flipbook";
    viewerBox.appendChild(newFlipbook);

    // Step 4: Force browser reflow so the element has real dimensions
    void newFlipbook.offsetHeight;

    // Step 5: Build pages
    for (let i = 1; i <= issue.totalPages; i++) {
        const page = document.createElement("div");
        page.className = "page";
        const img = document.createElement("img");
        img.src = `assets/images/magazine/${issue.folder}/page${i}.jpg`;
        img.alt = `Page ${i}`;
        page.appendChild(img);
        newFlipbook.appendChild(page);
    }

    // Step 6: Small delay so DOM settles, then init
    setTimeout(() => {
        initFlipbook();
    }, 50);
}

// =========================
// INIT FLIPBOOK
// =========================

function initFlipbook() {
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

        console.log("PageFlip initialized successfully");

    } catch(err) {
        console.error("PageFlip init failed:", err);
    }
}