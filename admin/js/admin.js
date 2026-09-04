// ==========================================
// STAR FAIR ADMIN CONTROLLER (PHP + MySQL Backend)
// ==========================================

// Global state variables for client-side search and filtering
let allRegistrations = [];

document.addEventListener("DOMContentLoaded", () => {
    const isLoginPage = document.getElementById("loginForm") !== null;
    const isDashboardPage = document.getElementById("registrationsTableBody") !== null;

    // --- 1. Login Handler ---
    if (isLoginPage) {
        const loginForm = document.getElementById("loginForm");
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const loginBtn = document.getElementById("loginBtn");

            loginBtn.disabled = true;
            loginBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Authenticating...`;

            try {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('password', password);

                const response = await fetch('../backend/admin/login.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = "dashboard.php";
                } else {
                    alert(result.message || "Sign-in failed.");
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = "Sign In";
                }
            } catch (err) {
                console.error("Login request failed:", err);
                alert("An error occurred during authentication. Please try again.");
                loginBtn.disabled = false;
                loginBtn.innerHTML = "Sign In";
            }
        });
    }

    // --- 2. Dashboard Init ---
    if (isDashboardPage) {
        initializeDashboard();
    }
});

// ==========================================
// DASHBOARD VIEW HANDLERS
// ==========================================

function initializeDashboard() {
    loadRegistrations();
    loadMagazines();

    // Bind reload/refresh buttons
    const refreshRegsBtn = document.getElementById("refreshRegsBtn");
    if (refreshRegsBtn) {
        refreshRegsBtn.addEventListener("click", () => loadRegistrations());
    }

    // Bind search and filter events
    const regSearchInput = document.getElementById("regSearchInput");
    const regProgramFilter = document.getElementById("regProgramFilter");

    if (regSearchInput) {
        regSearchInput.addEventListener("input", filterAndRenderRegistrations);
    }
    if (regProgramFilter) {
        regProgramFilter.addEventListener("change", filterAndRenderRegistrations);
    }

    // Bind Magazine form submit
    const magazineForm = document.getElementById("magazineForm");
    if (magazineForm && !magazineForm.dataset.listenerBound) {
        magazineForm.dataset.listenerBound = "true";
        magazineForm.addEventListener("submit", handleMagazineUpload);
    }
}

// Fetch registrations list from PHP endpoint
async function loadRegistrations() {
    const tableBody = document.getElementById("registrationsTableBody");
    if (!tableBody) return;

    tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-gold" role="status"></div>
                <p class="mt-2 text-muted mb-0">Querying records from MySQL database...</p>
            </td>
        </tr>
    `;

    try {
        const response = await fetch('../backend/admin/registrations.php');
        if (response.status === 403) {
            // Session expired or unauthorized
            window.location.href = "index.php";
            return;
        }

        allRegistrations = await response.json();
        filterAndRenderRegistrations();

    } catch (err) {
        console.error("Failed to load registrations:", err);
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger py-4">Failed to load: ${err.message}</td>
            </tr>
        `;
    }
}

// Search and program filter logic
function filterAndRenderRegistrations() {
    const tableBody = document.getElementById("registrationsTableBody");
    if (!tableBody) return;

    const searchQuery = (document.getElementById("regSearchInput")?.value || "").toLowerCase().trim();
    const programFilter = document.getElementById("regProgramFilter")?.value || "";

    // Clear previous rows
    tableBody.innerHTML = "";

    // Apply filters
    const filteredRegs = allRegistrations.filter(reg => {
        // Program filter (checks if program name is inside the registrations JSON string or row)
        // Wait, registrations API returns lists, we need to inspect the details if loaded, but the list row contains basic fields.
        const matchesSearch = 
            (reg.name || "").toLowerCase().includes(searchQuery) ||
            (reg.email || "").toLowerCase().includes(searchQuery) ||
            (reg.mobile || "").toLowerCase().includes(searchQuery);

        let matchesProgram = true;
        if (programFilter) {
            const lowerProgram = (reg.programmes || "").toLowerCase();
            matchesProgram = lowerProgram.includes(programFilter.toLowerCase());
        }

        return matchesSearch && matchesProgram;
    });

    if (filteredRegs.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4" style="color: var(--gold); font-weight: 500;">
                    <i class="fa-solid fa-circle-info me-2"></i> No student registrations found.
                </td>
            </tr>
        `;
        return;
    }

    filteredRegs.forEach(data => {
        const dateStr = data.created_at ? new Date(data.created_at).toLocaleDateString() : "N/A";
        
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><strong>${escapeHtml(data.name)}</strong></td>
            <td>${escapeHtml(data.mobile)}</td>
            <td>${escapeHtml(data.email)}</td>
            <td>${escapeHtml(data.gender)}</td>
            <td>${dateStr}</td>
            <td class="text-center">
                <button class="btn btn-outline-gold btn-sm me-2 view-reg-btn" data-id="${data.id}">
                    <i class="fa-solid fa-eye"></i> View
                </button>
                <button class="btn btn-danger btn-sm delete-reg-btn" data-id="${data.id}">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </td>
        `;
        tableBody.appendChild(tr);
    });

    // Re-bind View and Delete events
    document.querySelectorAll(".view-reg-btn").forEach(btn => {
        btn.addEventListener("click", () => openRegistrationModal(btn.dataset.id));
    });

    document.querySelectorAll(".delete-reg-btn").forEach(btn => {
        btn.addEventListener("click", () => deleteRegistration(btn.dataset.id));
    });
}

// Fetch active issues inside the magazines table
async function loadMagazines() {
    const tableBody = document.getElementById("magazinesTableBody");
    if (!tableBody) return;

    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4">
                <div class="spinner-border text-gold" role="status"></div>
                <p class="mt-2 text-muted mb-0">Querying editions...</p>
            </td>
        </tr>
    `;

    try {
        const response = await fetch('../backend/admin/magazines.php');
        if (response.status === 403) {
            window.location.href = "index.php";
            return;
        }

        const magazines = await response.json();
        tableBody.innerHTML = "";

        if (magazines.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4" style="color: var(--gold); font-weight: 500;">
                        <i class="fa-solid fa-circle-info me-2"></i> No published magazines found.
                    </td>
                </tr>
            `;
            return;
        }

        magazines.forEach(data => {
            const coverUrl = data.coverUrl || "../assets/images/magazine/page1.jpg";
            
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td><img src="${coverUrl}" style="height:60px; width:42px; object-fit:cover; border-radius:3px; border:1px solid #333;"></td>
                <td><strong>${escapeHtml(data.title)}</strong><br><small class="text-muted">${escapeHtml(data.description)}</small></td>
                <td>${escapeHtml(data.dateText)}</td>
                <td>${data.order}</td>
                <td class="text-center">
                    <a href="${data.pdfUrl}" target="_blank" class="btn btn-outline-gold btn-sm me-2"><i class="fa-solid fa-file-pdf"></i> PDF</a>
                    <button class="btn btn-danger btn-sm delete-mag-btn" data-id="${data.id}">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        // Bind Delete buttons
        document.querySelectorAll(".delete-mag-btn").forEach(btn => {
            btn.addEventListener("click", () => deleteMagazine(btn.dataset.id));
        });

    } catch (err) {
        console.error("Failed to load magazines:", err);
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-danger py-4">Failed to load: ${err.message}</td>
            </tr>
        `;
    }
}

// Open and populate application details
async function openRegistrationModal(id) {
    const modalBody = document.getElementById("regModalBody");
    const modalEl = document.getElementById("regDetailModal");
    if (!modalBody || !modalEl) return;

    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-gold" role="status"></div>
            <p class="mt-2 text-muted">Retrieving detailed record...</p>
        </div>
    `;

    const detailModal = new bootstrap.Modal(modalEl);
    detailModal.show();

    try {
        const response = await fetch(`../backend/admin/registration-view.php?id=${id}`);
        if (!response.ok) throw new Error("Could not retrieve registration record from server.");

        const data = await response.json();
        
        const dateStr = data.created_at ? new Date(data.created_at).toLocaleString() : "N/A";
        const placeholderImg = '../assets/images/advisor-trainers/user-placeholder.png';
        
        modalBody.innerHTML = `
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        <img src="${data.photoUrl || placeholderImg}" 
                             class="img-fluid rounded border" 
                             style="max-height:220px; object-fit:cover; border-color:var(--border-color) !important;"
                             onerror="this.src='${placeholderImg}'">
                    </div>
                    <a href="${data.photoUrl}" target="_blank" class="btn btn-gold btn-sm w-100 ${data.photoUrl ? '' : 'disabled'}">
                        <i class="fa-solid fa-download"></i> Download Photo
                    </a>
                </div>
                
                <div class="col-md-8">
                    <h4 class="text-gold mb-3">${escapeHtml(data.name)}</h4>
                    <div class="detail-grid">
                        <div class="detail-item"><div class="detail-label">DOB</div><div class="detail-value">${escapeHtml(data.dob)}</div></div>
                        <div class="detail-item"><div class="detail-label">Gender</div><div class="detail-value">${escapeHtml(data.gender)}</div></div>
                        <div class="detail-item"><div class="detail-label">Blood Group</div><div class="detail-value">${escapeHtml(data.blood_group)}</div></div>
                        <div class="detail-item"><div class="detail-label">Nationality</div><div class="detail-value">${escapeHtml(data.nationality)}</div></div>
                        <div class="detail-item"><div class="detail-label">Mobile</div><div class="detail-value">${escapeHtml(data.mobile)}</div></div>
                        <div class="detail-item"><div class="detail-label">Alt Mobile</div><div class="detail-value">${escapeHtml(data.alt_mobile || 'N/A')}</div></div>
                        <div class="detail-item"><div class="detail-label">Email</div><div class="detail-value">${escapeHtml(data.email)}</div></div>
                        <div class="detail-item"><div class="detail-label">Education</div><div class="detail-value">${escapeHtml(data.education)}</div></div>
                        <div class="detail-item"><div class="detail-label">Occupation</div><div class="detail-value">${escapeHtml(data.occupation)}</div></div>
                        <div class="detail-item"><div class="detail-label">Date Submitted</div><div class="detail-value">${dateStr}</div></div>
                    </div>
                </div>

                <div class="col-12">
                    <h5 class="text-gold border-bottom pb-2">Family & Emergency Contacts</h5>
                    <div class="detail-grid">
                        <div class="detail-item"><div class="detail-label">Father's Name</div><div class="detail-value">${escapeHtml(data.father_name)}</div></div>
                        <div class="detail-item"><div class="detail-label">Mother's Name</div><div class="detail-value">${escapeHtml(data.mother_name)}</div></div>
                        <div class="detail-item"><div class="detail-label">Guardian Name</div><div class="detail-value">${escapeHtml(data.guardian_name)}</div></div>
                        <div class="detail-item"><div class="detail-label">Guardian Mobile</div><div class="detail-value">${escapeHtml(data.guardian_mobile)}</div></div>
                        <div class="detail-item"><div class="detail-label">Emergency Contact</div><div class="detail-value">${escapeHtml(data.emergency_name)} (${escapeHtml(data.emergency_relation)})</div></div>
                    </div>
                </div>

                <div class="col-12">
                    <h5 class="text-gold border-bottom pb-2">Addresses</h5>
                    <div class="detail-grid">
                        <div class="detail-item detail-fullwidth"><div class="detail-label">Present Address</div><div class="detail-value">${escapeHtml(data.present_address)}</div></div>
                        <div class="detail-item detail-fullwidth"><div class="detail-label">Permanent Address</div><div class="detail-value">${escapeHtml(data.permanent_address)}</div></div>
                    </div>
                </div>

                <div class="col-12">
                    <h5 class="text-gold border-bottom pb-2">Selected Programmes & Events</h5>
                    <div class="detail-grid">
                        <div class="detail-item"><div class="detail-label">Programmes</div><div class="detail-value">${escapeHtml(data.programmes.join(', ') || 'None')}</div></div>
                        <div class="detail-item"><div class="detail-label">Events</div><div class="detail-value">${escapeHtml(data.events.join(', ') || 'None')}</div></div>
                    </div>
                </div>

                <div class="col-12">
                    <h5 class="text-gold border-bottom pb-2">Statement Details & Uploaded Files</h5>
                    <div class="detail-grid">
                        <div class="detail-item detail-fullwidth"><div class="detail-label">Why Join?</div><div class="detail-value">${escapeHtml(data.why_join)}</div></div>
                        <div class="detail-item"><div class="detail-label">Previous Experience</div><div class="detail-value">${escapeHtml(data.previous_experience || 'None')}</div></div>
                        <div class="detail-item"><div class="detail-label">Medical Conditions</div><div class="detail-value">${escapeHtml(data.medical_conditions || 'None')}</div></div>
                        <div class="detail-item"><div class="detail-label">Special Skills</div><div class="detail-value">${escapeHtml(data.special_skills || 'None')}</div></div>
                        
                        <!-- Uploaded files links (Proxy routes) -->
                        <div class="detail-item">
                            <div class="detail-label">NID / Birth Certificate</div>
                            <div class="detail-value">
                                <a href="${data.nidBcUrl}" target="_blank" class="btn btn-outline-gold btn-sm mt-1 w-100 ${data.nidBcUrl ? '' : 'disabled'}">
                                    <i class="fa-solid fa-file-pdf"></i> View / Download Document
                                </a>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Portfolio Document</div>
                            <div class="detail-value">
                                <a href="${data.portfolioUrl}" target="_blank" class="btn btn-outline-gold btn-sm mt-1 w-100 ${data.portfolioUrl ? '' : 'disabled'}">
                                    <i class="fa-solid fa-file-pdf"></i> View / Download Portfolio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } catch(err) {
        console.error("Failed to fetch application details:", err);
        modalBody.innerHTML = `<div class="alert alert-danger">Error retrieving record: ${err.message}</div>`;
    }
}

// Delete student registration
async function deleteRegistration(id) {
    if (!confirm("Are you sure you want to permanently delete this registration record? This action is irreversible.")) return;

    try {
        const formData = new FormData();
        formData.append('id', id);

        const response = await fetch('../backend/admin/registration-delete.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            alert("Registration record successfully deleted.");
            loadRegistrations();
        } else {
            alert(result.message || "Failed to delete registration.");
        }
    } catch (err) {
        console.error("Failed to delete registration record:", err);
        alert("Failed to delete record: " + err.message);
    }
}

// Handle Magazine form submit and files uploads
async function handleMagazineUpload(e) {
    e.preventDefault();

    const submitBtn = document.getElementById("magSubmitBtn");
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Uploading & Publishing...`;

    try {
        // Collect form data with files directly
        const form = document.getElementById("magazineForm");
        const formData = new FormData(form);

        const response = await fetch('../backend/admin/magazine-upload.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message || "Magazine edition published successfully.");
            form.reset();
            loadMagazines();
        } else {
            alert(result.message || "Failed to upload magazine.");
        }

    } catch (err) {
        console.error("Failed to upload magazine:", err);
        alert("Failed to publish magazine: " + err.message);
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// Delete magazine issue
async function deleteMagazine(id) {
    if (!confirm("Are you sure you want to delete this magazine issue? Both the database record and storage PDF files will be destroyed.")) return;

    try {
        const formData = new FormData();
        formData.append('id', id);

        const response = await fetch('../backend/admin/magazine-delete.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert("Magazine issue successfully deleted.");
            loadMagazines();
        } else {
            alert(result.message || "Failed to delete magazine issue.");
        }
    } catch (err) {
        console.error("Failed to delete magazine issue:", err);
        alert("Failed to delete issue: " + err.message);
    }
}

// Utility to escape HTML and prevent XSS
function escapeHtml(text) {
    if (!text) return "";
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// --- DYNAMIC IMAGE MANAGER LOGIC ---

const pageStructure = {
    global: {
        "global": {
            title: "Global Website Configurations",
            slots: [
                { key: "logo", label: "Website Global Logo", default: "assets/images/logo/logo.jpg" }
            ]
        }
    },
    main: {
        "index.html": {
            title: "Homepage",
            slots: [
                { key: "hero_slide_1", label: "Hero Slider 1", default: "assets/images/hero/fasion-show.jpg" },
                { key: "hero_slide_2", label: "Hero Slider 2", default: "assets/images/hero/campaign-shoot.jpg" },
                { key: "hero_slide_3", label: "Hero Slider 3", default: "assets/images/hero/registration.jpg" },
                { key: "about_img", label: "About Section Image", default: "assets/images/about/about.jpg" },
                { key: "program_fashion", label: "Program Category: Fashion", default: "assets/images/programs/fasion.jpg" },
                { key: "program_grooming", label: "Program Category: Grooming", default: "assets/images/programs/grooming.jpg" },
                { key: "program_acting", label: "Program Category: Acting", default: "assets/images/programs/acting.jpg" },
                { key: "program_culture", label: "Program Category: Culture", default: "assets/images/programs/culture.jpg" },
                { key: "portfolio_model_1", label: "Portfolio: Models 1", default: "assets/images/portfolio/model1.jpg" },
                { key: "portfolio_model_2", label: "Portfolio: Models 2", default: "assets/images/portfolio/model2.jpg" },
                { key: "portfolio_kids_1", label: "Portfolio: Kids 1", default: "assets/images/portfolio/kids1.jpg" },
                { key: "portfolio_kids_2", label: "Portfolio: Kids 2", default: "assets/images/portfolio/kids2.jpg" },
                { key: "portfolio_teenage_1", label: "Portfolio: Teenagers 1", default: "assets/images/portfolio/teenage1.jpg" },
                { key: "portfolio_teenage_2", label: "Portfolio: Teenagers 2", default: "assets/images/portfolio/teenage2.jpg" },
                { key: "event_highlight_1", label: "Event Highlight: Kids Modeling", default: "assets/images/event/event1.jpg" },
                { key: "event_highlight_2", label: "Event Highlight: Fashion Show", default: "assets/images/event/event2.jpg" },
                { key: "event_highlight_3", label: "Event Highlight: Campaign Shoot", default: "assets/images/event/event3.jpg" },
                { key: "founder_img", label: "Founder Section Image", default: "assets/images/mentors/founder.jpg" }
            ]
        },
        "advisor-trainers.html": {
            title: "Advisor & Trainers Directory",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/communication.jpg" }
            ]
        },
        "course.html": {
            title: "Courses Directory",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/programs-hero.jpg" },
                { key: "course_fashion", label: "Course card: Fashion Modeling", default: "assets/images/programs/fasion.jpg" },
                { key: "course_acting", label: "Course card: Acting & Performance", default: "assets/images/programs/acting.jpg" },
                { key: "course_hiphop", label: "Course card: Hip Hop Dance", default: "assets/images/programs/hiphop_dance.png" },
                { key: "course_culture", label: "Course card: Classical Dance", default: "assets/images/programs/culture.jpg" },
                { key: "course_art_1", label: "Course card: Fine Arts", default: "assets/images/programs/art.jpg" },
                { key: "course_poetry", label: "Course card: Poetry Recitation", default: "assets/images/programs/poetry_recitation.png" },
                { key: "course_hosting", label: "Course card: Professional Hosting", default: "assets/images/programs/hosting.png" },
                { key: "course_pageant", label: "Course card: Pageant Grooming", default: "assets/images/programs/pageant_grooming.png" },
                { key: "course_photography", label: "Course card: Photography", default: "assets/images/programs/photo.jpg" },
                { key: "course_makeup", label: "Course card: Prosthetic Makeup", default: "assets/images/programs/prosthetic_makeup.png" },
                { key: "course_communication", label: "Course card: Life Skills & Comm", default: "assets/images/programs/communication.jpg" },
                { key: "course_grooming", label: "Course card: Personal Grooming", default: "assets/images/programs/grooming.jpg" },
                { key: "course_art_2", label: "Course card: Fine Arts (Certificate)", default: "assets/images/programs/art.jpg" },
                { key: "course_marketing", label: "Course card: Digital Marketing", default: "assets/images/programs/digital_marketing.png" },
                { key: "gallery_closeup", label: "Highlight Gallery: Closeup", default: "assets/images/programs/closeup.jpg" },
                { key: "gallery_formal", label: "Highlight Gallery: Formal", default: "assets/images/programs/formal.jpg" },
                { key: "gallery_bridal", label: "Highlight Gallery: Bridal", default: "assets/images/programs/bridal.jpg" }
            ]
        },
        "registration.html": {
            title: "Admission Form",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/hero/registration.jpg" }
            ]
        },
        "magazine.html": {
            title: "Magazine flipbook",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/magazine/page1.jpg" },
                { key: "magazine_hero_cover", label: "Hero Highlight Cover", default: "assets/images/magazine/page1.jpg" }
            ]
        },
        "contact.html": {
            title: "Contact page",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/hero/registration.jpg" }
            ]
        },
        "award.html": {
            title: "Awards page",
            slots: [
                { key: "hero_slide", label: "Hero Side Banner", default: "assets/images/award/5.jpeg" },
                { key: "about_awards", label: "About Awards Banner", default: "assets/images/award/11.jpeg" },
                { key: "gallery_1", label: "Gallery Image 1", default: "assets/images/award/10.jpeg" },
                { key: "gallery_2", label: "Gallery Image 2", default: "assets/images/award/1.jpeg" },
                { key: "gallery_3", label: "Gallery Image 3", default: "assets/images/award/2.jpeg" },
                { key: "gallery_4", label: "Gallery Image 4", default: "assets/images/award/18.jpeg" },
                { key: "gallery_5", label: "Gallery Image 5", default: "assets/images/award/6.jpeg" },
                { key: "gallery_6", label: "Gallery Image 6", default: "assets/images/award/fasion1.jpeg" },
                { key: "gallery_7", label: "Gallery Image 7", default: "assets/images/award/9.jpeg" },
                { key: "gallery_8", label: "Gallery Image 8", default: "assets/images/award/8.jpeg" },
                { key: "lifetime_award", label: "Dedication Award Image", default: "assets/images/award/3.jpeg" },
                { key: "award_winners", label: "Winners Showcase Image", default: "assets/images/award/award2.jpeg" },
                { key: "panel_1", label: "Panel Judge 1 Photo", default: "assets/images/award/panel1.jpeg" },
                { key: "panel_2", label: "Panel Judge 2 Photo", default: "assets/images/award/panel2.jpeg" },
                { key: "panel_3", label: "Panel Judge 3 Photo", default: "assets/images/award/panel4.jpeg" },
                { key: "panel_4", label: "Panel Judge 4 Photo", default: "assets/images/award/panel6.jpeg" }
            ]
        }
    },
    courses: {
        "modeling.html": {
            title: "Modeling Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/fasion.jpg" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/modeling1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/modeling2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/modeling3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/modeling4.jpg" }
            ]
        },
        "acting.html": {
            title: "Acting Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/acting.jpg" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/acting1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/acting2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/acting3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/acting4.png" }
            ]
        },
        "hiphop-dance.html": {
            title: "Hip Hop Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/hiphop_dance.png" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/hiphop1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/hiphop2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/hiphop3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/hiphop4.png" }
            ]
        },
        "classical-dance.html": {
            title: "Classical Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/culture.jpg" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/classical1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/classical2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/classical3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/classical4.png" }
            ]
        },
        "photography.html": {
            title: "Photography Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/photo.jpg" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/photography1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/photography2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/photography3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/photography4.png" }
            ]
        },
        "pageant.html": {
            title: "Pageant Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/pageant_grooming.png" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/pageant1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/pageant2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/pageant3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/pageant4.png" }
            ]
        },
        "makeup.html": {
            title: "Makeup Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/prosthetic_makeup.png" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/makeup1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/makeup2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/makeup3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/makeup4.png" }
            ]
        },
        "fine-arts.html": {
            title: "Fine Arts Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/art.jpg" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/arts1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/arts2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/arts3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/arts4.png" }
            ]
        },
        "communication.html": {
            title: "Communication Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/communication.jpg" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/communication1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/communication2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/communication3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/communication4.png" }
            ]
        },
        "grooming.html": {
            title: "Grooming Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/grooming.jpg" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/grooming1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/grooming2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/grooming3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/grooming4.png" }
            ]
        },
        "poetry.html": {
            title: "Poetry Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/poetry_recitation.png" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/poetry1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/poetry2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/poetry3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/poetry4.png" }
            ]
        },
        "hosting.html": {
            title: "Hosting Details",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/hosting.png" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/hosting1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/hosting2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/hosting3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/hosting4.png" }
            ]
        },
        "digital-marketing.html": {
            title: "Digital Marketing",
            slots: [
                { key: "hero_img", label: "Hero Banner Background", default: "assets/images/programs/digital_marketing.png" },
                { key: "gallery_1", label: "Gallery Highlight 1", default: "assets/images/course-gallery/marketing1.png" },
                { key: "gallery_2", label: "Gallery Highlight 2", default: "assets/images/course-gallery/marketing2.png" },
                { key: "gallery_3", label: "Gallery Highlight 3", default: "assets/images/course-gallery/marketing3.png" },
                { key: "gallery_4", label: "Gallery Highlight 4", default: "assets/images/course-gallery/marketing4.png" }
            ]
        }
    },
    events: {
        "kids-modelling.html": {
            title: "Kids Modelling Dropdown",
            slots: [
                { key: "hero_img", label: "Hero Banner Image", default: "assets/images/kids/kids-hero1.jpg" },
                { key: "about_img", label: "About Section Image", default: "assets/images/kids/kids-hero.webp" },
                { key: "highlight_1", label: "Highlight 1 (Runway Walk)", default: "assets/images/kids/highlight1.jpg" },
                { key: "highlight_2", label: "Highlight 2 (Creative Performance)", default: "assets/images/kids/highlight2.jpg" },
                { key: "highlight_3", label: "Highlight 3 (Award Ceremony)", default: "assets/images/kids/highlight3.jpg" },
                { key: "gallery_1", label: "Gallery Image 1", default: "assets/images/kids/gallery1.jpg" },
                { key: "gallery_2", label: "Gallery Image 2", default: "assets/images/kids/gallery2.jpg" },
                { key: "gallery_3", label: "Gallery Image 3", default: "assets/images/kids/gallery3.jpg" },
                { key: "gallery_4", label: "Gallery Image 4", default: "assets/images/kids/gallery4.jpg" }
            ]
        },
        "fashion-show.html": {
            title: "Fashion Show Dropdown",
            slots: [
                { key: "hero_img", label: "Hero Banner Image", default: "assets/images/fashion-show/fashion-hero.jpg" },
                { key: "about_img", label: "About Section Image", default: "assets/images/fashion-show/about.jpg" },
                { key: "highlight_1", label: "Highlight 1 (Runway Performance)", default: "assets/images/fashion-show/highlight1.jpg" },
                { key: "highlight_2", label: "Highlight 2 (Designer Collection)", default: "assets/images/fashion-show/highlight3.jpg" },
                { key: "highlight_3", label: "Highlight 3 (Media Coverage)", default: "assets/images/fashion-show/highlight2.jpg" },
                { key: "gallery_1", label: "Gallery Image 1", default: "assets/images/fashion-show/gallery1.jpg" },
                { key: "gallery_2", label: "Gallery Image 2", default: "assets/images/fashion-show/gallery2.jpg" },
                { key: "gallery_3", label: "Gallery Image 3", default: "assets/images/fashion-show/gallery3.jpg" },
                { key: "gallery_4", label: "Gallery Image 4", default: "assets/images/fashion-show/gallery4.jpg" }
            ]
        },
        "campaign-shoot.html": {
            title: "Campaign Shoot Dropdown",
            slots: [
                { key: "hero_img", label: "Hero Banner Image", default: "assets/images/campaign/campaign-hero.jpg" },
                { key: "about_img", label: "About Section Image", default: "assets/images/campaign/about.jpg" },
                { key: "style_1", label: "Style 1 (Fashion Editorial)", default: "assets/images/campaign/style1.jpg" },
                { key: "style_2", label: "Style 2 (Corporate Look)", default: "assets/images/campaign/style2.jpg" },
                { key: "style_3", label: "Style 3 (Magazine Cover)", default: "assets/images/campaign/style3.jpg" },
                { key: "bts_img", label: "Behind the Scenes Image", default: "assets/images/campaign/bts.jpg" },
                { key: "gallery_1", label: "Gallery Image 1", default: "assets/images/campaign/gallery1.jpg" },
                { key: "gallery_2", label: "Gallery Image 2", default: "assets/images/campaign/gallery2.jpg" },
                { key: "gallery_3", label: "Gallery Image 3", default: "assets/images/campaign/gallery3.jpg" },
                { key: "gallery_4", label: "Gallery Image 4", default: "assets/images/campaign/gallery4.jpg" },
                { key: "gallery_5", label: "Gallery Image 5", default: "assets/images/campaign/gallery5.jpg" },
                { key: "gallery_6", label: "Gallery Image 6", default: "assets/images/campaign/gallery6.jpg" },
                { key: "gallery_7", label: "Gallery Image 7", default: "assets/images/campaign/gallery7.jpg" },
                { key: "gallery_8", label: "Gallery Image 8", default: "assets/images/campaign/gallery8.jpg" }
            ]
        }
    },
    gallery: {
        "photo-gallery.html": {
            title: "Photo Gallery Dropdown",
            slots: [
                { key: "hero_img", label: "Hero Banner Image", default: "assets/images/programs/photo.jpg" },
                { key: "fashion_1", label: "Fashion Show 1", default: "assets/images/fashion-show/gallery1.jpg" },
                { key: "fashion_2", label: "Fashion Show 2", default: "assets/images/fashion-show/gallery2.jpg" },
                { key: "fashion_3", label: "Fashion Show 3", default: "assets/images/fashion-show/gallery3.jpg" },
                { key: "fashion_4", label: "Fashion Show 4", default: "assets/images/fashion-show/gallery4.jpg" },
                { key: "award_1", label: "Award 1", default: "assets/images/award/1.jpeg" },
                { key: "award_2", label: "Award 2", default: "assets/images/award/2.jpeg" },
                { key: "award_3", label: "Award 3", default: "assets/images/award/3.jpeg" },
                { key: "award_4", label: "Award 4", default: "assets/images/award/4.jpeg" },
                { key: "training_1", label: "Training 1", default: "assets/images/campaign/gallery1.jpg" },
                { key: "training_2", label: "Training 2", default: "assets/images/campaign/gallery2.jpg" },
                { key: "training_3", label: "Training 3", default: "assets/images/campaign/gallery3.jpg" },
                { key: "training_4", label: "Training 4", default: "assets/images/campaign/gallery4.jpg" },
                { key: "event_1", label: "Event 1", default: "assets/images/event/event1.jpg" },
                { key: "event_2", label: "Event 2", default: "assets/images/event/event2.jpg" },
                { key: "event_3", label: "Event 3", default: "assets/images/event/event3.jpg" },
                { key: "kids_1", label: "Kids 1", default: "assets/images/kid/kid1.jpg" },
                { key: "kids_2", label: "Kids 2", default: "assets/images/kid/kid2.jpg" },
                { key: "kids_3", label: "Kids 3", default: "assets/images/kid/kid3.jpg" },
                { key: "kids_4", label: "Kids 4", default: "assets/images/kid/kid4.jpg" }
            ]
        },
        "kids-portfolio.html": {
            title: "Kids Portfolio Dropdown",
            slots: [
                { key: "hero_img", label: "Hero Banner Image", default: "assets/images/kids/kids-hero.webp" },
                { key: "gallery_1", label: "Portfolio Image 1", default: "assets/images/kid/kid1.jpg" },
                { key: "gallery_2", label: "Portfolio Image 2", default: "assets/images/kid/kid2.jpg" },
                { key: "gallery_3", label: "Portfolio Image 3", default: "assets/images/kid/kid3.jpg" },
                { key: "gallery_4", label: "Portfolio Image 4", default: "assets/images/kid/kid4.jpg" },
                { key: "gallery_5", label: "Portfolio Image 5", default: "assets/images/kid/kid5.jpg" },
                { key: "gallery_6", label: "Portfolio Image 6", default: "assets/images/kid/kid6.jpg" },
                { key: "gallery_7", label: "Portfolio Image 7", default: "assets/images/kid/kid7.jpg" },
                { key: "gallery_8", label: "Portfolio Image 8", default: "assets/images/kid/kid8.jpg" }
            ]
        },
        "teenager-portfolio.html": {
            title: "Teenager Portfolio Dropdown",
            slots: [
                { key: "hero_img", label: "Hero Banner Image", default: "assets/images/teen/teen-hero.jpg" },
                { key: "gallery_1", label: "Portfolio Image 1", default: "assets/images/teen/gallery1.jpg" },
                { key: "gallery_2", label: "Portfolio Image 2", default: "assets/images/teen/gallery2.jpg" },
                { key: "gallery_3", label: "Portfolio Image 3", default: "assets/images/teen/gallery3.jpg" },
                { key: "gallery_4", label: "Portfolio Image 4", default: "assets/images/teen/gallery4.jpg" },
                { key: "gallery_5", label: "Portfolio Image 5", default: "assets/images/teen/gallery5.jpg" },
                { key: "gallery_6", label: "Portfolio Image 6", default: "assets/images/teen/gallery6.jpg" },
                { key: "gallery_7", label: "Portfolio Image 7", default: "assets/images/teen/gallery7.jpg" },
                { key: "gallery_8", label: "Portfolio Image 8", default: "assets/images/teen/gallery8.jpg" }
            ]
        },
        "models-portfolio.html": {
            title: "Models Portfolio Dropdown",
            slots: [
                { key: "hero_img", label: "Hero Banner Image", default: "assets/images/models/hero.jpg" },
                { key: "gallery_1", label: "Portfolio Image 1", default: "assets/images/models/gallery1.jpg" },
                { key: "gallery_2", label: "Portfolio Image 2", default: "assets/images/models/gallery2.jpg" },
                { key: "gallery_3", label: "Portfolio Image 3", default: "assets/images/models/gallery3.jpg" },
                { key: "gallery_4", label: "Portfolio Image 4", default: "assets/images/models/gallery4.jpg" },
                { key: "gallery_5", label: "Portfolio Image 5", default: "assets/images/models/gallery5.jpg" },
                { key: "gallery_6", label: "Portfolio Image 6", default: "assets/images/models/gallery6.jpg" },
                { key: "gallery_7", label: "Portfolio Image 7", default: "assets/images/models/gallery7.jpg" },
                { key: "gallery_8", label: "Portfolio Image 8", default: "assets/images/models/gallery8.jpg" }
            ]
        }
    }
};

let allTeamMembers = { mentor: [], advisor: [], trainer: [] };
let selectedMemberId = null;

let allPartners = [];
let selectedPartnerId = null;

let allNews = [];
let selectedNewsId = null;

document.addEventListener("DOMContentLoaded", () => {
    const sectionSelect = document.getElementById("imageSectionSelect");
    const pageSelect = document.getElementById("imagePageSelect");

    if (sectionSelect && pageSelect) {
        sectionSelect.addEventListener("change", handleSectionChange);
        pageSelect.addEventListener("change", handlePageChange);
        
        // Initial setup load
        handleSectionChange();
    }

    // Initialize team profile management controller
    initializeTeamManager();

    // Initialize partners management controller
    initializePartnersManager();

    // Initialize news management controller
    initializeNewsManager();
});

function handleSectionChange() {
    const sectionSelect = document.getElementById("imageSectionSelect");
    const pageSelect = document.getElementById("imagePageSelect");
    const section = sectionSelect.value;
    
    // Clear page selection options
    pageSelect.innerHTML = "";
    
    if (pageStructure[section]) {
        for (const filename in pageStructure[section]) {
            const pageData = pageStructure[section][filename];
            const opt = document.createElement("option");
            opt.value = filename;
            opt.textContent = pageData.title;
            pageSelect.appendChild(opt);
        }
    }
    
    handlePageChange();
}

function handlePageChange() {
    const section = document.getElementById("imageSectionSelect").value;
    const page = document.getElementById("imagePageSelect").value;
    
    if (!page) {
        document.getElementById("pageSlotsContainer").innerHTML = '<p class="text-muted text-center py-4">No page selected.</p>';
        return;
    }
    
    loadPageSlots(section, page);
}

async function loadPageSlots(section, page) {
    const slotsContainer = document.getElementById("pageSlotsContainer");
    const titleHeader = document.getElementById("selectedPageTitle");
    
    titleHeader.innerHTML = `<i class="fa-solid fa-images me-2"></i> Manage Images: ${pageStructure[section][page].title}`;
    slotsContainer.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-gold" role="status"></div>
            <p class="text-muted mt-2">Loading image slots...</p>
        </div>
    `;

    try {
        const response = await fetch(`../backend/admin/get-page-images.php?page=${encodeURIComponent(page)}`);
        const data = await response.json();
        
        if (!data.success) {
            slotsContainer.innerHTML = `<div class="alert alert-danger">${escapeHtml(data.message)}</div>`;
            return;
        }

        const customImages = data.images || {};
        const pageData = pageStructure[section][page];
        
        slotsContainer.innerHTML = "";
        
        pageData.slots.forEach(slot => {
            const isCustomized = !!customImages[slot.key];
            // Resolve preview image path. Fallback to original default asset in local root.
            const currentSrc = isCustomized ? `../${customImages[slot.key]}` : `../${slot.default}`;
            
            // Build cache prevention version
            const currentPreviewSrc = `${currentSrc}?v=${Date.now()}`;

            const card = document.createElement("div");
            card.className = "card bg-dark text-white border-secondary mb-3";
            card.innerHTML = `
                <div class="row g-0 align-items-center p-3">
                    <div class="col-md-3 text-center">
                        <img src="${currentPreviewSrc}" class="img-thumbnail bg-black border-gold img-fluid" style="max-height: 120px; object-fit: contain;">
                    </div>
                    <div class="col-md-9">
                        <div class="card-body py-1">
                            <h6 class="card-title text-gold">${escapeHtml(slot.label)}</h6>
                            <div class="input-group mb-2">
                                <input type="file" class="form-control bg-secondary text-white border-dark" id="file-${slot.key}" accept="image/*">
                                <button class="btn btn-gold btn-sm" type="button" id="btn-upload-${slot.key}" onclick="uploadSlotImage('${section}', '${page}', '${slot.key}', '${slot.default}')">
                                    <i class="fa-solid fa-upload"></i> Upload
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Default: ${escapeHtml(slot.default)}</span>
                                ${isCustomized ? `
                                    <button class="btn btn-sm btn-outline-danger py-0" style="border-radius:20px; font-size:11px;" onclick="resetSlotImage('${section}', '${page}', '${slot.key}')">
                                        <i class="fa-solid fa-arrow-rotate-left"></i> Restore Default
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            slotsContainer.appendChild(card);
        });

    } catch (err) {
        console.error("Failed to load page slots:", err);
        slotsContainer.innerHTML = `<div class="alert alert-danger">Failed to load slots: ${escapeHtml(err.message)}</div>`;
    }
}

async function uploadSlotImage(section, page, key, defaultPath) {
    const fileInput = document.getElementById(`file-${key}`);
    const uploadBtn = document.getElementById(`btn-upload-${key}`);
    
    if (!fileInput || fileInput.files.length === 0) {
        alert("Please select an image file to upload first.");
        return;
    }

    const file = fileInput.files[0];
    if (file.size > 5 * 1024 * 1024) {
        alert("The image file size exceeds the 5MB limit.");
        return;
    }

    const formData = new FormData();
    formData.append("section", section);
    formData.append("page", page);
    formData.append("image_key", key);
    formData.append("imageFile", file);

    const originalHtml = uploadBtn.innerHTML;
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

    try {
        const response = await fetch("../backend/admin/image-upload.php", {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message || "Image replaced successfully.");
            loadPageSlots(section, page);
        } else {
            alert(result.message || "Upload failed.");
        }
    } catch (err) {
        console.error("Upload error occurred:", err);
        alert("An error occurred during upload: " + err.message);
    } finally {
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = originalHtml;
    }
}

async function resetSlotImage(section, page, key) {
    if (!confirm("Are you sure you want to restore the default image? The custom image file will be deleted.")) return;

    const formData = new FormData();
    formData.append("section", section);
    formData.append("page", page);
    formData.append("image_key", key);

    try {
        const response = await fetch("../backend/admin/image-reset.php", {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message || "Slot restored to default.");
            loadPageSlots(section, page);
        } else {
            alert(result.message || "Failed to restore default.");
        }
    } catch (err) {
        console.error("Reset error occurred:", err);
        alert("An error occurred: " + err.message);
    }
}

// ==========================================
// MENTOR & ADVISOR PROFILE CONTROLLERS
// ==========================================

function initializeTeamManager() {
    const typeSelect = document.getElementById("teamTypeSelect");
    const editForm = document.getElementById("memberEditForm");
    const btnResetPhoto = document.getElementById("btnResetMemberPhoto");

    if (typeSelect) {
        typeSelect.addEventListener("change", renderTeamDirectory);
    }

    if (editForm) {
        editForm.addEventListener("submit", handleSaveMember);
    }

    if (btnResetPhoto) {
        btnResetPhoto.addEventListener("click", handleResetMemberPhoto);
    }

    // Load team members directory list from database
    loadTeamMembers();
}

async function loadTeamMembers() {
    const listGroup = document.getElementById("teamMembersList");
    if (!listGroup) return;

    listGroup.innerHTML = `<div class="text-center p-3 text-muted"><div class="spinner-border text-gold spinner-border-sm me-2"></div> Loading profile data...</div>`;

    try {
        const response = await fetch("../backend/get-team.php");
        const data = await response.json();

        if (data.success) {
            allTeamMembers.mentor = data.mentors || [];
            allTeamMembers.advisor = data.advisors || [];
            allTeamMembers.trainer = data.trainers || [];
            renderTeamDirectory();
        } else {
            listGroup.innerHTML = `<div class="alert alert-danger py-2 m-2">Error: ${escapeHtml(data.message)}</div>`;
        }
    } catch (err) {
        console.error("Failed to load team members:", err);
        listGroup.innerHTML = `<div class="alert alert-danger py-2 m-2">Network Error</div>`;
    }
}

function renderTeamDirectory() {
    const listGroup = document.getElementById("teamMembersList");
    const filterType = document.getElementById("teamTypeSelect").value;
    if (!listGroup) return;

    listGroup.innerHTML = "";
    const list = allTeamMembers[filterType] || [];

    if (list.length === 0) {
        listGroup.innerHTML = `<div class="text-center p-3 text-muted">No profile entries in this category.</div>`;
        return;
    }

    list.forEach(member => {
        const item = document.createElement("button");
        item.type = "button";
        item.className = "list-group-item list-group-item-action text-white bg-dark border-secondary py-2";
        if (selectedMemberId === member.id) {
            item.classList.add("active");
            item.style.backgroundColor = "var(--gold)";
            item.style.borderColor = "var(--gold)";
        }
        item.innerHTML = `
            <div class="fw-bold text-truncate">${escapeHtml(member.name)}</div>
            <div class="small text-muted text-truncate" style="font-size: 11px;">${escapeHtml(member.designation)}</div>
        `;
        item.addEventListener("click", () => {
            // Deselect previous
            const activeItems = listGroup.querySelectorAll(".active");
            activeItems.forEach(ai => {
                ai.classList.remove("active");
                ai.style.backgroundColor = "";
                ai.style.borderColor = "";
            });
            // Select current
            item.classList.add("active");
            item.style.backgroundColor = "var(--gold)";
            item.style.borderColor = "var(--gold)";
            
            selectedMemberId = member.id;
            selectTeamMember(member);
        });
        listGroup.appendChild(item);
    });

    // Keep editing card updated if members list reloaded
    if (selectedMemberId) {
        const currentMember = list.find(m => m.id === selectedMemberId);
        if (currentMember) {
            selectTeamMember(currentMember);
        } else {
            // Member not in current category filter list
            document.getElementById("memberEditCard").style.display = "none";
            document.getElementById("memberEditPlaceholder").style.display = "block";
        }
    }
}

function selectTeamMember(member) {
    document.getElementById("memberEditPlaceholder").style.display = "none";
    document.getElementById("memberEditCard").style.display = "block";

    document.getElementById("editingMemberTitle").innerHTML = `<i class="fa-solid fa-user-edit me-2"></i> Edit Profile: ${escapeHtml(member.name)}`;
    document.getElementById("editMemberId").value = member.id;
    document.getElementById("editMemberName").value = member.name;
    document.getElementById("editMemberDesignation").value = member.designation;
    document.getElementById("editMemberBio").value = member.bio;

    const previewImg = document.getElementById("editMemberPhotoPreview");
    const placeholderIcon = document.getElementById("editMemberPhotoPlaceholder");
    const fileInput = document.getElementById("editMemberPhotoFile");

    // Reset file input element
    if (fileInput) fileInput.value = "";

    if (member.image_path) {
        previewImg.src = `../${member.image_path}?v=${Date.now()}`;
        previewImg.style.display = "block";
        placeholderIcon.style.display = "none";
    } else {
        previewImg.src = "";
        previewImg.style.display = "none";
        placeholderIcon.style.display = "block";
    }
}

async function handleSaveMember(e) {
    e.preventDefault();

    const saveBtn = document.getElementById("btnSaveMember");
    const form = document.getElementById("memberEditForm");
    const formData = new FormData(form);

    const originalHtml = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...`;

    try {
        const response = await fetch("../backend/admin/save-mentor.php", {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message || "Profile details saved successfully.");
            // Reload database directory list
            await loadTeamMembers();
        } else {
            alert(result.message || "Save operation failed.");
        }
    } catch (err) {
        console.error("Save profile error:", err);
        alert("An error occurred: " + err.message);
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalHtml;
    }
}

async function handleResetMemberPhoto() {
    if (!selectedMemberId) return;
    if (!confirm("Are you sure you want to restore the default photo for this profile? The custom image file will be deleted.")) return;

    const btnReset = document.getElementById("btnResetMemberPhoto");
    const originalHtml = btnReset.innerHTML;

    btnReset.disabled = true;
    btnReset.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Resetting...`;

    try {
        const formData = new FormData();
        formData.append("id", selectedMemberId);

        const response = await fetch("../backend/admin/reset-mentor-image.php", {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message || "Reverted to default profile photo.");
            await loadTeamMembers();
        } else {
            alert(result.message || "Failed to restore default photo.");
        }
    } catch (err) {
        console.error("Reset photo error:", err);
        alert("An error occurred: " + err.message);
    } finally {
        btnReset.disabled = false;
        btnReset.innerHTML = originalHtml;
    }
}

// --- COLLABORATION PARTNERS MANAGER LOGIC ---

function initializePartnersManager() {
    const btnAddNew = document.getElementById("btnAddNewPartner");
    const partnerForm = document.getElementById("partnerForm");
    const btnDelete = document.getElementById("btnDeletePartner");
    const logoInput = document.getElementById("partnerLogo");

    if (btnAddNew) {
        btnAddNew.addEventListener("click", resetPartnerForm);
    }

    if (partnerForm) {
        partnerForm.addEventListener("submit", handleSavePartner);
    }

    if (btnDelete) {
        btnDelete.addEventListener("click", handleDeletePartner);
    }

    if (logoInput) {
        logoInput.addEventListener("change", handlePartnerLogoPreview);
    }

    // Load partners grid list
    loadPartners();
}

async function loadPartners() {
    const listGroup = document.getElementById("partnersList");
    if (!listGroup) return;

    listGroup.innerHTML = `<div class="text-center p-3 text-muted"><div class="spinner-border text-gold spinner-border-sm me-2"></div> Loading partner logos...</div>`;

    try {
        const response = await fetch("../backend/get-partners.php");
        const data = await response.json();

        if (data.success) {
            allPartners = data.partners || [];
            renderPartnersList();
        } else {
            listGroup.innerHTML = `<div class="alert alert-danger py-2 m-2">Error: ${escapeHtml(data.message)}</div>`;
        }
    } catch (err) {
        console.error("Failed to load partners:", err);
        listGroup.innerHTML = `<div class="alert alert-danger py-2 m-2">Network Error</div>`;
    }
}

function renderPartnersList() {
    const listGroup = document.getElementById("partnersList");
    if (!listGroup) return;

    listGroup.innerHTML = "";

    if (allPartners.length === 0) {
        listGroup.innerHTML = `<div class="text-center p-3 text-muted">No partners added yet.</div>`;
        return;
    }

    allPartners.forEach(partner => {
        const a = document.createElement("a");
        a.href = "#";
        a.className = "list-group-item list-group-item-action bg-dark text-white border-secondary d-flex align-items-center justify-content-between p-2";
        a.dataset.id = partner.id;
        
        // Show logo thumbnail and name
        const logoUrl = partner.image_path ? `../${partner.image_path}` : 'placeholder.jpg';
        a.innerHTML = `
            <div class="d-flex align-items-center">
                <img src="${logoUrl}" class="rounded bg-light p-1 me-2" style="width: 40px; height: 40px; object-fit: contain;">
                <div>
                    <h6 class="mb-0 text-white">${escapeHtml(partner.name)}</h6>
                    <small class="text-muted">Order: ${partner.display_order}</small>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-gold small"></i>
        `;

        a.addEventListener("click", (e) => {
            e.preventDefault();
            
            // Toggle active selection styling
            listGroup.querySelectorAll("a").forEach(item => item.classList.remove("active", "border-gold"));
            a.classList.add("active", "border-gold");
            
            selectPartner(partner);
        });

        listGroup.appendChild(a);
    });
}

function selectPartner(partner) {
    selectedPartnerId = partner.id;

    // Reset Form fields
    document.getElementById("partnerId").value = partner.id;
    document.getElementById("partnerName").value = partner.name;
    document.getElementById("partnerOrder").value = partner.display_order;

    // Reset logo input field
    document.getElementById("partnerLogo").value = "";

    // Show image preview
    const preview = document.getElementById("partnerLogoPreview");
    const placeholder = document.getElementById("partnerLogoPlaceholder");
    
    if (partner.image_path) {
        preview.src = `../${partner.image_path}?v=${Date.now()}`;
        preview.style.display = "block";
        placeholder.style.display = "none";
    } else {
        preview.style.display = "none";
        placeholder.style.display = "block";
    }

    // Toggle forms visual display
    document.getElementById("partnerFormTitle").innerHTML = `<i class="fa-solid fa-pen-to-square me-2"></i> Edit Partner: ${escapeHtml(partner.name)}`;
    document.getElementById("btnDeletePartner").style.display = "block";
    document.getElementById("partnerEditCard").style.display = "block";
    document.getElementById("partnerPlaceholder").style.display = "none";
}

function resetPartnerForm() {
    selectedPartnerId = null;

    // Deselect active link
    const listGroup = document.getElementById("partnersList");
    if (listGroup) {
        listGroup.querySelectorAll("a").forEach(item => item.classList.remove("active", "border-gold"));
    }

    // Reset inputs
    document.getElementById("partnerId").value = "";
    document.getElementById("partnerName").value = "";
    document.getElementById("partnerOrder").value = "1";
    document.getElementById("partnerLogo").value = "";

    // Hide preview
    document.getElementById("partnerLogoPreview").style.display = "none";
    document.getElementById("partnerLogoPlaceholder").style.display = "block";

    // Toggle Form display
    document.getElementById("partnerFormTitle").innerHTML = `<i class="fa-solid fa-plus me-2"></i> Add New Partner`;
    document.getElementById("btnDeletePartner").style.display = "none";
    document.getElementById("partnerEditCard").style.display = "block";
    document.getElementById("partnerPlaceholder").style.display = "none";
}

function handlePartnerLogoPreview(e) {
    const file = e.target.files[0];
    const preview = document.getElementById("partnerLogoPreview");
    const placeholder = document.getElementById("partnerLogoPlaceholder");

    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            preview.src = event.target.result;
            preview.style.display = "block";
            placeholder.style.display = "none";
        };
        reader.readAsDataURL(file);
    }
}

async function handleSavePartner(e) {
    e.preventDefault();

    const form = document.getElementById("partnerForm");
    const saveBtn = document.getElementById("btnSavePartner");
    const originalHtml = saveBtn.innerHTML;

    saveBtn.disabled = true;
    saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Saving...`;

    try {
        const formData = new FormData(form);

        const response = await fetch("../backend/admin/save-partner.php", {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message || "Partner saved successfully.");
            
            // Reload list and reset UI state
            await loadPartners();
            
            // Hide edit panel
            document.getElementById("partnerEditCard").style.display = "none";
            document.getElementById("partnerPlaceholder").style.display = "block";
            selectedPartnerId = null;
        } else {
            alert(result.message || "Save operation failed.");
        }
    } catch (err) {
        console.error("Save partner error:", err);
        alert("An error occurred: " + err.message);
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalHtml;
    }
}

async function handleDeletePartner() {
    if (!selectedPartnerId) return;
    if (!confirm("Are you sure you want to delete this partner? The logo image file will be deleted permanently.")) return;

    const deleteBtn = document.getElementById("btnDeletePartner");
    const originalHtml = deleteBtn.innerHTML;

    deleteBtn.disabled = true;
    deleteBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Deleting...`;

    try {
        const formData = new FormData();
        formData.append("partnerId", selectedPartnerId);

        const response = await fetch("../backend/admin/delete-partner.php", {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message || "Partner deleted successfully.");
            
            // Reload list and reset UI state
            await loadPartners();
            
            // Hide edit panel
            document.getElementById("partnerEditCard").style.display = "none";
            document.getElementById("partnerPlaceholder").style.display = "block";
            selectedPartnerId = null;
        } else {
            alert(result.message || "Delete operation failed.");
        }
    } catch (err) {
        console.error("Delete partner error:", err);
        alert("An error occurred: " + err.message);
    } finally {
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = originalHtml;
    }
}

// --- NEWS & ANNOUNCEMENTS MANAGER LOGIC ---

function initializeNewsManager() {
    const btnAddNew = document.getElementById("btnAddNewNews");
    const newsForm = document.getElementById("newsForm");
    const btnDelete = document.getElementById("btnDeleteNews");
    const coverInput = document.getElementById("newsImage");

    if (btnAddNew) {
        btnAddNew.addEventListener("click", resetNewsForm);
    }

    if (newsForm) {
        newsForm.addEventListener("submit", handleSaveNews);
    }

    if (btnDelete) {
        btnDelete.addEventListener("click", handleDeleteNews);
    }

    if (coverInput) {
        coverInput.addEventListener("change", handleNewsCoverPreview);
    }

    // Load news articles list
    loadNewsFeed();
}

async function loadNewsFeed() {
    const listGroup = document.getElementById("newsFeedList");
    if (!listGroup) return;

    listGroup.innerHTML = `<div class="text-center p-3 text-muted"><div class="spinner-border text-gold spinner-border-sm me-2"></div> Loading news feed...</div>`;

    try {
        const response = await fetch("../backend/get-news.php");
        const data = await response.json();

        if (data.success) {
            allNews = data.news || [];
            renderNewsFeedList();
        } else {
            listGroup.innerHTML = `<div class="alert alert-danger py-2 m-2">Error: ${escapeHtml(data.message)}</div>`;
        }
    } catch (err) {
        console.error("Failed to load news:", err);
        listGroup.innerHTML = `<div class="alert alert-danger py-2 m-2">Network Error</div>`;
    }
}

function renderNewsFeedList() {
    const listGroup = document.getElementById("newsFeedList");
    if (!listGroup) return;

    listGroup.innerHTML = "";

    if (allNews.length === 0) {
        listGroup.innerHTML = `<div class="text-center p-3 text-muted">No news articles published yet.</div>`;
        return;
    }

    allNews.forEach(article => {
        const a = document.createElement("a");
        a.href = "#";
        a.className = "list-group-item list-group-item-action bg-dark text-white border-secondary d-flex align-items-center justify-content-between p-2";
        a.dataset.id = article.id;
        
        // Featured icon decoration
        const featuredIcon = parseInt(article.is_featured) === 1 ? `<i class="fa-solid fa-star text-gold ms-1 small" title="Featured Spotlight"></i>` : "";
        const logoUrl = article.image_path ? `../${article.image_path}` : 'placeholder.jpg';

        a.innerHTML = `
            <div class="d-flex align-items-center" style="max-width: 90%;">
                <img src="${logoUrl}" class="rounded me-2" style="width: 45px; height: 45px; object-fit: cover;">
                <div style="overflow: hidden;">
                    <h6 class="mb-0 text-white text-truncate">${escapeHtml(article.title)} ${featuredIcon}</h6>
                    <small class="text-muted">${article.published_date} | Category: ${escapeHtml(article.category)}</small>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-gold small"></i>
        `;

        a.addEventListener("click", (e) => {
            e.preventDefault();
            
            // Toggle active styling
            listGroup.querySelectorAll("a").forEach(item => item.classList.remove("active", "border-gold"));
            a.classList.add("active", "border-gold");
            
            loadAndSelectNewsArticle(article.id);
        });

        listGroup.appendChild(a);
    });
}

async function loadAndSelectNewsArticle(id) {
    try {
        const response = await fetch(`../backend/get-news.php?id=${id}`);
        const data = await response.json();

        if (data.success && data.article) {
            selectNewsArticle(data.article);
        } else {
            alert(data.message || "Failed to load article detail.");
        }
    } catch (err) {
        console.error("Load single news article error:", err);
        alert("Failed to fetch article details.");
    }
}

function selectNewsArticle(article) {
    selectedNewsId = article.id;

    // Reset Form and set fields
    document.getElementById("newsId").value = article.id;
    document.getElementById("newsTitleInput").value = article.title;
    document.getElementById("newsCategoryInput").value = article.category;
    document.getElementById("newsDateInput").value = article.published_date;
    document.getElementById("newsOrderInput").value = article.display_order ?? 1;
    document.getElementById("newsSummaryInput").value = article.summary;
    document.getElementById("newsContentInput").value = article.content;
    document.getElementById("isFeaturedInput").checked = parseInt(article.is_featured) === 1;

    // Clear file selection
    document.getElementById("newsImage").value = "";

    // Show cover image preview
    const preview = document.getElementById("newsCoverPreview");
    const placeholder = document.getElementById("newsCoverPlaceholder");
    
    if (article.image_path) {
        preview.src = `../${article.image_path}?v=${Date.now()}`;
        preview.style.display = "block";
        placeholder.style.display = "none";
    } else {
        preview.style.display = "none";
        placeholder.style.display = "block";
    }

    // Toggle dashboard UI blocks
    document.getElementById("newsFormTitle").innerHTML = `<i class="fa-solid fa-pen-to-square me-2"></i> Edit News: ${escapeHtml(article.title)}`;
    document.getElementById("btnDeleteNews").style.display = "block";
    document.getElementById("newsEditCard").style.display = "block";
    document.getElementById("newsPlaceholder").style.display = "none";
}

function resetNewsForm() {
    selectedNewsId = null;

    // Deselect list link active status
    const listGroup = document.getElementById("newsFeedList");
    if (listGroup) {
        listGroup.querySelectorAll("a").forEach(item => item.classList.remove("active", "border-gold"));
    }

    // Reset Form fields
    document.getElementById("newsId").value = "";
    document.getElementById("newsTitleInput").value = "";
    document.getElementById("newsCategoryInput").value = "";
    document.getElementById("newsDateInput").value = new Date().toISOString().substring(0, 10);
    document.getElementById("newsOrderInput").value = "1";
    document.getElementById("newsSummaryInput").value = "";
    document.getElementById("newsContentInput").value = "";
    document.getElementById("isFeaturedInput").checked = false;
    document.getElementById("newsImage").value = "";

    // Hide preview
    document.getElementById("newsCoverPreview").style.display = "none";
    document.getElementById("newsCoverPlaceholder").style.display = "block";

    // Toggle dashboard UI blocks
    document.getElementById("newsFormTitle").innerHTML = `<i class="fa-solid fa-plus me-2"></i> Publish New News Article`;
    document.getElementById("btnDeleteNews").style.display = "none";
    document.getElementById("newsEditCard").style.display = "block";
    document.getElementById("newsPlaceholder").style.display = "none";
}

function handleNewsCoverPreview(e) {
    const file = e.target.files[0];
    const preview = document.getElementById("newsCoverPreview");
    const placeholder = document.getElementById("newsCoverPlaceholder");

    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            preview.src = event.target.result;
            preview.style.display = "block";
            placeholder.style.display = "none";
        };
        reader.readAsDataURL(file);
    }
}

async function handleSaveNews(e) {
    e.preventDefault();

    const form = document.getElementById("newsForm");
    const saveBtn = document.getElementById("btnSaveNews");
    const originalHtml = saveBtn.innerHTML;

    saveBtn.disabled = true;
    saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Publishing...`;

    try {
        const formData = new FormData(form);

        const response = await fetch("../backend/admin/save-news.php", {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message || "News article saved successfully.");
            
            // Reload list and reset UI view state
            await loadNewsFeed();
            
            // Hide edit form panel
            document.getElementById("newsEditCard").style.display = "none";
            document.getElementById("newsPlaceholder").style.display = "block";
            selectedNewsId = null;
        } else {
            alert(result.message || "Save operation failed.");
        }
    } catch (err) {
        console.error("Save news error:", err);
        alert("An error occurred: " + err.message);
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalHtml;
    }
}

async function handleDeleteNews() {
    if (!selectedNewsId) return;
    if (!confirm("Are you sure you want to delete this article? The cover image file will be deleted permanently.")) return;

    const deleteBtn = document.getElementById("btnDeleteNews");
    const originalHtml = deleteBtn.innerHTML;

    deleteBtn.disabled = true;
    deleteBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Deleting...`;

    try {
        const formData = new FormData();
        formData.append("newsId", selectedNewsId);

        const response = await fetch("../backend/admin/delete-news.php", {
            method: "POST",
            body: formData
        });
        const result = await response.json();

        if (result.success) {
            alert(result.message || "News article deleted successfully.");
            
            // Reload list and reset UI view state
            await loadNewsFeed();
            
            // Hide edit form panel
            document.getElementById("newsEditCard").style.display = "none";
            document.getElementById("newsPlaceholder").style.display = "block";
            selectedNewsId = null;
        } else {
            alert(result.message || "Delete operation failed.");
        }
    } catch (err) {
        console.error("Delete news error:", err);
        alert("An error occurred: " + err.message);
    } finally {
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = originalHtml;
    }
}

