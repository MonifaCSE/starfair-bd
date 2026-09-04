document.addEventListener("DOMContentLoaded", () => {
    const wrapper = document.getElementById("newsContentWrapper");
    if (!wrapper) return;

    // 1. Get the article ID from the URL query params
    const urlParams = new URLSearchParams(window.location.search);
    const articleId = urlParams.get("id");

    if (!articleId) {
        // Redirect to homepage if no ID is provided
        window.location.href = "index.html";
        return;
    }

    // 2. Fetch the single news article details from the database API
    fetch(`backend/get-news.php?id=${encodeURIComponent(articleId)}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.article) {
                renderError("Article not found or has been removed.");
                return;
            }

            const article = data.article;
            
            // Format published date
            const dateObj = new Date(article.published_date);
            const formattedDate = dateObj.toLocaleDateString("en-US", {
                year: "numeric",
                month: "long",
                day: "numeric"
            });

            // Convert double newlines in content to HTML paragraphs safely
            const contentHtml = article.content
                .split("\n\n")
                .map(para => `<p>${escapeHtml(para.trim())}</p>`)
                .join("");

            // Render details layout
            wrapper.innerHTML = `
                <div class="news-meta">
                    <span class="news-category-tag">${escapeHtml(article.category)}</span>
                    <span class="news-date"><i class="fa-regular fa-calendar me-2"></i>${formattedDate}</span>
                </div>
                <h1 class="news-title">${escapeHtml(article.title)}</h1>
                
                <img src="${article.image_path}" class="news-detail-img" alt="${escapeHtml(article.title)}">
                
                <div class="news-body-content">
                    ${contentHtml}
                </div>
                
                <a href="index.html#news" class="btn-back-news">
                    <i class="fa-solid fa-arrow-left"></i> Back to News Feed
                </a>
            `;
        })
        .catch(err => {
            console.error("Failed to load article detail:", err);
            renderError("Network error occurred. Unable to retrieve article details.");
        });

    function renderError(message) {
        wrapper.innerHTML = `
            <div class="text-center py-5">
                <i class="fa-solid fa-circle-exclamation fa-4x text-danger mb-4"></i>
                <h3 class="text-white">Oops! Article Error</h3>
                <p class="text-muted mt-2">${escapeHtml(message)}</p>
                <a href="index.html#news" class="btn-back-news mt-4">
                    <i class="fa-solid fa-arrow-left"></i> Back to Homepage
                </a>
            </div>
        `;
    }

    // Helper function to escape HTML special characters for security
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
