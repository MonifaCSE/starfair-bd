<?php
require_once __DIR__ . '/../backend/config.php';

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STAR FAIR | Admin Dashboard</title>

    <!-- Favicon -->
    <link class="favicon" rel="icon" type="image/x-icon" href="../assets/images/logo/favicon.ico?v=2">
    <link class="favicon" rel="shortcut icon" type="image/x-icon" href="../assets/images/logo/favicon.ico?v=2">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>

<body class="admin-dashboard-body">

    <!-- TOP NAVBAR -->
    <nav class="navbar navbar-expand-lg admin-navbar">
        <div class="container-fluid px-4">
            <a class="navbar-brand text-gold" href="#">
                <img src="../assets/images/logo/logo.jpg" alt="Logo" class="d-inline-block align-top me-2" style="height:35px; border-radius:5px;">
                STAR FAIR Admin Panel
            </a>
            <div class="d-flex align-items-center">
                <span id="adminEmail" class="text-muted me-3 small"><?= htmlspecialchars($_SESSION['admin_email']) ?></span>
                <a href="../backend/admin/logout.php" id="logoutBtn" class="btn btn-outline-gold btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>
    </nav>

    <!-- MAIN BODY -->
    <div class="container-fluid p-4">
        
        <!-- DASHBOARD NAVIGATION TABS -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="registrations-tab" data-bs-toggle="tab" data-bs-target="#registrations" type="button" role="tab" aria-controls="registrations" aria-selected="true">
                    <i class="fa-solid fa-users me-2"></i> Student Registrations
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="magazines-tab" data-bs-toggle="tab" data-bs-target="#magazines" type="button" role="tab" aria-controls="magazines" aria-selected="false">
                    <i class="fa-solid fa-book-open me-2"></i> Magazine Manager
                </button>
            </li>
        </ul>

        <!-- TAB CONTENT PANELS -->
        <div class="tab-content" id="adminTabContent">
            
            <!-- PANEL 1: REGISTRATIONS LIST -->
            <div class="tab-pane fade show active" id="registrations" role="tabpanel" aria-labelledby="registrations-tab">
                <div class="card admin-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-gold">Student Applications</h5>
                        
                        <!-- Search and Filter Controls -->
                        <div class="d-flex gap-2">
                            <input type="text" id="regSearchInput" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Search by name, email or phone..." style="width: 250px;">
                            <select id="regProgramFilter" class="form-select form-select-sm bg-dark text-white border-secondary" style="width: 180px;">
                                <option value="">All Programmes</option>
                                <option value="Runway Modelling">Runway Modelling</option>
                                <option value="Acting">Acting</option>
                                <option value="Classical Dance">Classical Dance</option>
                                <option value="Hiphop Dance">Hiphop Dance</option>
                                <option value="Hosting">Hosting</option>
                                <option value="Makeup">Makeup</option>
                                <option value="Photography">Photography</option>
                                <option value="Poetry">Poetry</option>
                                <option value="Grooming">Grooming</option>
                                <option value="Fine Arts">Fine Arts</option>
                                <option value="Communication Skills">Communication Skills</option>
                            </select>
                            <button id="refreshRegsBtn" class="btn btn-gold btn-sm"><i class="fa-solid fa-arrows-rotate"></i> Refresh</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Gender</th>
                                        <th>Date Submitted</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="registrationsTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="spinner-border text-gold" role="status"></div>
                                            <p class="mt-2 text-muted mb-0">Loading student registration entries...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL 2: MAGAZINE MANAGEMENT -->
            <div class="tab-pane fade" id="magazines" role="tabpanel" aria-labelledby="magazines-tab">
                <div class="row g-4">
                    
                    <!-- UPLOAD NEW ISSUE FORM -->
                    <div class="col-lg-4 col-md-5">
                        <div class="card admin-card">
                            <div class="card-header">
                                <h5 class="mb-0 text-gold">Upload New Edition</h5>
                            </div>
                            <div class="card-body">
                                <form id="magazineForm" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="magTitle" class="form-label">Edition Title <span class="text-gold">*</span></label>
                                        <input type="text" class="form-control" id="magTitle" name="magTitle" required placeholder="e.g. Summer Edition 2026">
                                    </div>
                                    <div class="mb-3">
                                        <label for="magDesc" class="form-label">Short Description <span class="text-gold">*</span></label>
                                        <textarea class="form-control" id="magDesc" name="magDesc" rows="3" required placeholder="Brief summary of highlights..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="magDate" class="form-label">Release Date Text <span class="text-gold">*</span></label>
                                        <input type="text" class="form-control" id="magDate" name="magDate" required placeholder="e.g. August 2026">
                                    </div>
                                    <div class="mb-3">
                                        <label for="magOrder" class="form-label">Display Order Index <span class="text-gold">*</span></label>
                                        <input type="number" class="form-control" id="magOrder" name="magOrder" required value="1" min="1" placeholder="Ascending order in dropdown">
                                    </div>
                                    <div class="mb-3">
                                        <label for="magCoverFile" class="form-label">Cover Image File <span class="text-muted small">(Optional)</span></label>
                                        <input type="file" class="form-control" id="magCoverFile" name="magCoverFile" accept="image/*">
                                        <div class="form-text text-muted small">Falls back to dynamic placeholder covers if empty</div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="magPdfFile" class="form-label">Magazine PDF File <span class="text-gold">*</span></label>
                                        <input type="file" class="form-control" id="magPdfFile" name="magPdfFile" required accept="application/pdf">
                                    </div>

                                    <button type="submit" id="magSubmitBtn" class="btn btn-gold w-100"><i class="fa-solid fa-cloud-arrow-up"></i> Publish Magazine Issue</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- EXISTING ISSUES LIST -->
                    <div class="col-lg-8 col-md-7">
                        <div class="card admin-card">
                            <div class="card-header">
                                <h5 class="mb-0 text-gold">Published Issues</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Cover</th>
                                                <th>Title</th>
                                                <th>Release Date</th>
                                                <th>Order</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="magazinesTableBody">
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="spinner-border text-gold" role="status"></div>
                                                    <p class="mt-2 text-muted mb-0">Loading published issues...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <!-- DETAILS MODAL FOR APPLICATION RECORD -->
    <div class="modal fade" id="regDetailModal" tabindex="-1" aria-labelledby="regDetailModalLabel" aria-hidden="true" style="backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background:#0e0e0e; border:2px solid #D4AF37; border-radius:15px; color:#fff;">
                <div class="modal-header" style="border-bottom:1px solid rgba(212,175,55,0.2);">
                    <h5 class="modal-title text-gold" id="regDetailModalLabel"><i class="fa-solid fa-id-card me-2"></i> Application Detail Record</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="regModalBody" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Appended dynamically -->
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(212,175,55,0.2);">
                    <button type="button" class="btn btn-gold btn-sm px-4" data-bs-dismiss="modal" style="border-radius:50px;">Close Record</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin JS Handler -->
    <script src="js/admin.js"></script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
