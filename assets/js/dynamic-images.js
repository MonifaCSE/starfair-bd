/**
 * STAR FAIR - Dynamic Images Loader
 * Dynamically resolves base paths and fetches custom image overrides from the database.
 * Fails gracefully back to default hardcoded image if API is unavailable (e.g., GitHub Pages).
 */
document.addEventListener("DOMContentLoaded", () => {
    // 1. Get current page name from filename (fallback to index.html if empty)
    const pathParts = window.location.pathname.split('/');
    const page = pathParts.pop() || 'index.html';
    
    // 2. Select all manageable images on the page
    const dynamicImages = document.querySelectorAll("img[data-key]");
    const dynamicBgs = document.querySelectorAll("[data-bg-key]");
    if (dynamicImages.length === 0 && dynamicBgs.length === 0) return;
    
    // 3. Dynamically compute the base path for API requests
    const basePath = pathParts.join('/') + '/';
    const apiUrl = `${basePath}backend/get-images.php?page=${encodeURIComponent(page)}`;
    
    // 4. Fetch dynamic image mappings
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.images) {
                dynamicImages.forEach(img => {
                    const key = img.getAttribute("data-key");
                    if (data.images[key]) {
                        const imgData = data.images[key];
                        const newSrc = `${basePath}${imgData.path}?v=${imgData.t}`;
                        img.src = newSrc;
                        
                        // Check if the image has a parent gallery-item with a data-src attribute (used for lightboxes)
                        const parentItem = img.closest(".gallery-item");
                        if (parentItem && parentItem.hasAttribute("data-src")) {
                            parentItem.setAttribute("data-src", newSrc);
                        }
                    }
                });
                // Check if the page has custom mappings for elements using images as backgrounds (e.g. hero sections)
                const dynamicBgs = document.querySelectorAll("[data-bg-key]");
                dynamicBgs.forEach(el => {
                    const key = el.getAttribute("data-bg-key");
                    if (data.images[key]) {
                        const imgData = data.images[key];
                        const newSrc = `${basePath}${imgData.path}?v=${imgData.t}`;
                        // Re-apply standard linear gradient overlay along with dynamic image URL
                        el.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('${newSrc}')`;
                    }
                });
            }
        })
        .catch(err => {
            // Graceful fallback: Do nothing and leave the original hardcoded images visible
            console.warn("Dynamic Image API unavailable (standard fallback active):", err.message);
        });
});
