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
        // Let's filter on the name/email/phone from the search bar
        const matchesSearch = 
            (reg.name || "").toLowerCase().includes(searchQuery) ||
            (reg.email || "").toLowerCase().includes(searchQuery) ||
            (reg.mobile || "").toLowerCase().includes(searchQuery);

        return matchesSearch;
    });

    if (filteredRegs.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">No student registrations found.</td>
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
                    <td colspan="5" class="text-center text-muted py-4">No published magazines found.</td>
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
