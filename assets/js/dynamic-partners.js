document.addEventListener("DOMContentLoaded", () => {
    const grid = document.querySelector(".partners-grid");
    if (!grid) return;

    fetch("backend/get-partners.php")
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.partners || data.partners.length === 0) {
                return; // Fallback to static HTML if empty or failed
            }

            grid.innerHTML = ""; // Clear static items
            data.partners.forEach(partner => {
                const item = document.createElement("div");
                item.className = "partner-logo-item";
                item.dataset.field = "sponsor-logo";
                item.innerHTML = `<img src="${partner.image_path}" alt="${partner.name} Logo" loading="lazy">`;
                grid.appendChild(item);
            });
        })
        .catch(err => {
            console.warn("Dynamic partners list not available. Using static sponsor list fallback.", err);
        });
});
