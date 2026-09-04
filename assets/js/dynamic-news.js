document.addEventListener("DOMContentLoaded", () => {
    const featuredContainer = document.getElementById("featuredNewsContainer");
    const sidebarList = document.getElementById("newsSidebarList");

    if (!featuredContainer || !sidebarList) return;

    let localNews = [];

    // 1. Fetch news articles from database
    fetch("backend/get-news.php")
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.news || data.news.length === 0) {
                // If no news, hide the section gracefully
                const newsSection = document.getElementById("news");
                if (newsSection) newsSection.style.display = "none";
                return;
            }

            localNews = data.news;

            // 2. Identify the featured news article
            let featuredItem = localNews.find(item => parseInt(item.is_featured) === 1);
            if (!featuredItem) {
                featuredItem = localNews[0]; // Fallback to the latest
            }

            // 3. Render featured block and sidebar items
            renderFeaturedNews(featuredItem);
            renderSidebarList(featuredItem.id);
        })
        .catch(err => {
            console.error("Failed to load homepage news feed:", err);
            // Hide section gracefully on network error
            const newsSection = document.getElementById("news");
            if (newsSection) newsSection.style.display = "none";
        });

    // Function to render the main spotlight news item on the left
    function renderFeaturedNews(news) {
        featuredContainer.style.opacity = 0; // Prepare for transition

        // Format published date
        const dateObj = new Date(news.published_date);
        const formattedDate = dateObj.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric"
        });

        // Update container HTML
        featuredContainer.innerHTML = `
            <div class="featured-news-card">
                <div class="featured-news-img-box">
                    <img src="${news.image_path}" alt="${escapeHtml(news.title)}" id="featuredNewsImg">
                    <div class="news-badge-meta">
                        <span class="news-badge-category">${escapeHtml(news.category)}</span>
                        <span class="news-badge-date"><i class="fa-regular fa-calendar me-2"></i>${formattedDate}</span>
                    </div>
                </div>
                <div class="featured-news-content">
                    <h3><a href="news-detail.html?id=${news.id}">${escapeHtml(news.title)}</a></h3>
                    <p>${escapeHtml(news.summary)}</p>
                    <a href="news-detail.html?id=${news.id}" class="btn-read-news">
                         বিস্তারিত পড়ুন <i class="fa-solid fa-arrow-right-long ms-1"></i>
                    </a>
                </div>
            </div>
        `;

        // Smooth fade-in animation
        let opacity = 0;
        const interval = setInterval(() => {
            opacity += 0.1;
            featuredContainer.style.opacity = opacity;
            if (opacity >= 1) clearInterval(interval);
        }, 30);
    }

    // Function to render the list of other articles in the scroll container
    function renderSidebarList(selectedId) {
        sidebarList.innerHTML = "";

        localNews.forEach(news => {
            const isCurrent = news.id === selectedId;

            // Format date
            const dateObj = new Date(news.published_date);
            const formattedDate = dateObj.toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric"
            });

            // Create sidebar link card
            const a = document.createElement("a");
            a.href = "#";
            a.className = `sidebar-news-item ${isCurrent ? 'active' : ''}`;
            a.innerHTML = `
                <div class="sidebar-news-thumb">
                    <img src="${news.image_path}" alt="${escapeHtml(news.title)}">
                </div>
                <div class="sidebar-news-info">
                    <h6>${escapeHtml(news.title)}</h6>
                    <div class="sidebar-news-meta">
                        <span class="meta-category">${escapeHtml(news.category)}</span>
                        <span><i class="fa-regular fa-calendar me-1"></i>${formattedDate}</span>
                    </div>
                </div>
            `;

            a.addEventListener("click", (e) => {
                e.preventDefault();

                // Highlight clicked sidebar item
                const allItems = sidebarList.querySelectorAll(".sidebar-news-item");
                allItems.forEach(item => item.classList.remove("active"));
                a.classList.add("active");

                // Dynamic swap transition in spotlight panel
                renderFeaturedNews(news);
            });

            sidebarList.appendChild(a);
        });
    }

    // Helper function to escape HTML special characters
    function escapeHtml(str) {
        if (!str) return "";
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
