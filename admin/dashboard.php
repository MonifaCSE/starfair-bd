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
    <link rel="stylesheet" href="css/admin.css?v=1.3">
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
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab" aria-controls="images" aria-selected="false">
                    <i class="fa-solid fa-image me-2"></i> Image Manager
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab" aria-controls="team" aria-selected="false">
                    <i class="fa-solid fa-graduation-cap me-2"></i> Mentor & Advisor Manager
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="partners-tab" data-bs-toggle="tab" data-bs-target="#partners" type="button" role="tab" aria-controls="partners" aria-selected="false">
                    <i class="fa-solid fa-handshake me-2"></i> Collaboration Partners
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="news-tab" data-bs-toggle="tab" data-bs-target="#newsTab" type="button" role="tab" aria-controls="newsTab" aria-selected="false">
                    <i class="fa-solid fa-newspaper me-2"></i> News & Announcements
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#coursesTab" type="button" role="tab" aria-controls="coursesTab" aria-selected="false">
                    <i class="fa-solid fa-graduation-cap me-2"></i> Course Manager
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
                                <option value="Fashion Modeling">Fashion Modeling</option>
                                <option value="Acting">Acting</option>
                                <option value="Classical Dance">Classical Dance</option>
                                <option value="Hip Hop Dance">Hip Hop Dance</option>
                                <option value="Fine Arts">Fine Arts</option>
                                <option value="Beauty Pageant">Beauty Pageant Grooming</option>
                                <option value="Photography">Photography</option>
                                <option value="Makeup">Fashion & Makeup</option>
                                <option value="Grooming">Personal Grooming</option>
                                <option value="Communication">Communication Skills</option>
                                <option value="Digital Marketing">Digital Marketing & Branding</option>
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

            <!-- IMAGE MANAGER TAB -->
            <div class="tab-pane fade" id="images" role="tabpanel" aria-labelledby="images-tab">
                <div class="row">
                    <!-- LEFT COLUMN: CATEGORIES -->
                    <div class="col-md-4">
                        <div class="card admin-card">
                            <div class="card-header">
                                <h5 class="mb-0 text-gold"><i class="fa-solid fa-folder me-2"></i> Categories</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="imageSectionSelect" class="form-label text-gold">Category Section</label>
                                    <select class="form-select bg-dark text-white border-secondary" id="imageSectionSelect">
                                        <option value="global">Global Settings</option>
                                        <option value="main">Homepage & Main Pages</option>
                                        <option value="courses">Course Pages</option>
                                        <option value="events">Events Dropdown</option>
                                        <option value="gallery">Gallery Dropdown</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="imagePageSelect" class="form-label text-gold">Select Page</label>
                                    <select class="form-select bg-dark text-white border-secondary" id="imagePageSelect">
                                        <!-- Populated dynamically -->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: IMAGE SLOTS -->
                    <div class="col-md-8">
                        <div class="card admin-card">
                            <div class="card-header">
                                <h5 class="mb-0 text-gold" id="selectedPageTitle"><i class="fa-solid fa-images me-2"></i> Manage Page Images</h5>
                            </div>
                            <div class="card-body">
                                <div id="pageSlotsContainer">
                                    <p class="text-muted text-center py-4">Select a category and page to manage images.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MENTOR & ADVISOR MANAGER TAB -->
            <div class="tab-pane fade" id="team" role="tabpanel" aria-labelledby="team-tab">
                <div class="row">
                    <!-- LEFT COLUMN: MEMBER SELECT -->
                    <div class="col-md-4">
                        <div class="card admin-card">
                            <div class="card-header">
                                <h5 class="mb-0 text-gold"><i class="fa-solid fa-users me-2"></i> Profile Directory</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="teamTypeSelect" class="form-label text-gold">Filter Directory By Type</label>
                                    <select class="form-select bg-dark text-white border-secondary" id="teamTypeSelect">
                                        <option value="mentor">Mentors</option>
                                        <option value="advisor">Advisors / Board of Directors</option>
                                        <option value="trainer">Professional Trainers</option>
                                    </select>
                                </div>
                                <div class="list-group bg-dark border-secondary" id="teamMembersList" style="max-height: 400px; overflow-y: auto;">
                                    <!-- Populated dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: MEMBER EDIT FORM -->
                    <div class="col-md-8">
                        <div class="card admin-card" id="memberEditCard" style="display: none;">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-gold" id="editingMemberTitle"><i class="fa-solid fa-user-edit me-2"></i> Edit Profile</h5>
                            </div>
                            <div class="card-body">
                                <form id="memberEditForm" enctype="multipart/form-data">
                                    <input type="hidden" id="editMemberId" name="id">
                                    
                                    <div class="mb-3">
                                        <label for="editMemberName" class="form-label text-gold">Name</label>
                                        <input type="text" class="form-control bg-dark text-white border-secondary" id="editMemberName" name="name" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="editMemberDesignation" class="form-label text-gold">Designation</label>
                                        <input type="text" class="form-control bg-dark text-white border-secondary" id="editMemberDesignation" name="designation" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="editMemberBio" class="form-label text-gold">Bio / Description</label>
                                        <textarea class="form-control bg-dark text-white border-secondary" id="editMemberBio" name="bio" rows="6" required></textarea>
                                    </div>

                                    <div class="row align-items-center mb-3">
                                        <div class="col-sm-4 text-center">
                                            <div class="border border-secondary rounded p-2 mb-2 bg-dark" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                                                <img id="editMemberPhotoPreview" src="" class="img-fluid rounded" style="max-height: 100px; display: none;">
                                                <div id="editMemberPhotoPlaceholder" class="text-muted"><i class="fa-solid fa-user fa-3x"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <label for="editMemberPhotoFile" class="form-label text-gold">Replace Profile Photo</label>
                                            <input class="form-control bg-dark text-white border-secondary mb-2" type="file" id="editMemberPhotoFile" name="imageFile" accept="image/*">
                                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnResetMemberPhoto">
                                                <i class="fa-solid fa-undo me-1"></i> Revert to Default Photo
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 border-top border-secondary pt-3">
                                        <button type="submit" class="btn btn-gold btn-sm px-4" id="btnSaveMember">
                                            <i class="fa-solid fa-save me-1"></i> Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card admin-card text-center py-5 text-muted" id="memberEditPlaceholder">
                            <i class="fa-solid fa-id-card fa-4x mb-3 text-gold" style="opacity: 0.3;"></i>
                            <p>Select a profile from the directory to start editing.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLLABORATION PARTNERS TAB -->
            <div class="tab-pane fade" id="partners" role="tabpanel" aria-labelledby="partners-tab">
                <div class="row">
                    <!-- LEFT COLUMN: PARTNER LIST -->
                    <div class="col-md-4">
                        <div class="card admin-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-gold"><i class="fa-solid fa-handshake me-2"></i> Partner Grid</h5>
                                <button type="button" class="btn btn-gold btn-sm" id="btnAddNewPartner">
                                    <i class="fa-solid fa-plus me-1"></i> Add New
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="list-group bg-dark border-secondary" id="partnersList" style="max-height: 450px; overflow-y: auto;">
                                    <!-- Populated dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: PARTNER EDIT / ADD FORM -->
                    <div class="col-md-8">
                        <div class="card admin-card" id="partnerEditCard" style="display: none;">
                            <div class="card-header">
                                <h5 class="mb-0 text-gold" id="partnerFormTitle"><i class="fa-solid fa-pen-to-square me-2"></i> Add / Edit Partner</h5>
                            </div>
                            <div class="card-body">
                                <form id="partnerForm" enctype="multipart/form-data">
                                    <input type="hidden" id="partnerId" name="partnerId">
                                    
                                    <div class="mb-3">
                                        <label for="partnerName" class="form-label text-gold">Partner / Sponsor Name</label>
                                        <input type="text" class="form-control bg-dark text-white border-secondary" id="partnerName" name="partnerName" required placeholder="e.g. Green Leaf">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="partnerOrder" class="form-label text-gold">Display Order Index</label>
                                        <input type="number" class="form-control bg-dark text-white border-secondary" id="partnerOrder" name="partnerOrder" required value="1" min="0" placeholder="e.g. 1">
                                    </div>

                                    <div class="row align-items-center mb-3">
                                        <div class="col-sm-4 text-center">
                                            <div class="border border-secondary rounded p-2 mb-2 bg-dark d-flex align-items-center justify-content-center" style="height: 120px;">
                                                <img id="partnerLogoPreview" src="" class="img-fluid rounded" style="max-height: 100px; display: none;">
                                                <div id="partnerLogoPlaceholder" class="text-muted"><i class="fa-solid fa-handshake fa-3x"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <label for="partnerLogo" class="form-label text-gold">Upload Logo Image</label>
                                            <input class="form-control bg-dark text-white border-secondary mb-2" type="file" id="partnerLogo" name="partnerLogo" accept="image/*">
                                            <div class="form-text text-muted small">JPG, PNG, WEBP, or SVG vectors are allowed.</div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between border-top border-secondary pt-3 mt-4">
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnDeletePartner" style="display: none;">
                                            <i class="fa-solid fa-trash me-1"></i> Delete Partner
                                        </button>
                                        <span class="flex-grow-1"></span>
                                        <button type="submit" class="btn btn-gold btn-sm px-4" id="btnSavePartner">
                                            <i class="fa-solid fa-save me-1"></i> Save Partner
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card admin-card text-center py-5 text-muted" id="partnerPlaceholder">
                            <i class="fa-solid fa-handshake fa-4x mb-3 text-gold" style="opacity: 0.3;"></i>
                            <p>Select a partner logo from the list to edit, or click "Add New" to add a new sponsor.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NEWS & ANNOUNCEMENTS TAB -->
            <div class="tab-pane fade" id="newsTab" role="tabpanel" aria-labelledby="news-tab">
                <div class="row">
                    <!-- LEFT COLUMN: ARTICLE DIRECTORY -->
                    <div class="col-md-4">
                        <div class="card admin-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-gold"><i class="fa-solid fa-newspaper me-2"></i> News Feed</h5>
                                <button type="button" class="btn btn-gold btn-sm" id="btnAddNewNews">
                                    <i class="fa-solid fa-plus me-1"></i> Add Article
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="list-group bg-dark border-secondary" id="newsFeedList" style="max-height: 480px; overflow-y: auto;">
                                    <!-- Populated dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: ARTICLE ADD / EDIT FORM -->
                    <div class="col-md-8">
                        <div class="card admin-card" id="newsEditCard" style="display: none;">
                            <div class="card-header">
                                <h5 class="mb-0 text-gold" id="newsFormTitle"><i class="fa-solid fa-pen-to-square me-2"></i> Add / Edit News Article</h5>
                            </div>
                            <div class="card-body">
                                <form id="newsForm" enctype="multipart/form-data">
                                    <input type="hidden" id="newsId" name="newsId">
                                    
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="newsTitleInput" class="form-label text-gold">Article Title</label>
                                            <input type="text" class="form-control bg-dark text-white border-secondary" id="newsTitleInput" name="newsTitle" required placeholder="e.g. Agreement Ceremony with Bank">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="newsCategoryInput" class="form-label text-gold">Category</label>
                                            <input type="text" class="form-control bg-dark text-white border-secondary" id="newsCategoryInput" name="newsCategory" required placeholder="e.g. Business, Fashion">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="newsDateInput" class="form-label text-gold">Published Date</label>
                                            <input type="date" class="form-control bg-dark text-white border-secondary" id="newsDateInput" name="newsDate" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="newsOrderInput" class="form-label text-gold">Display Order</label>
                                            <input type="number" class="form-control bg-dark text-white border-secondary" id="newsOrderInput" name="newsOrder" required value="1" min="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="newsSummaryInput" class="form-label text-gold">Short Summary / Excerpt</label>
                                        <textarea class="form-control bg-dark text-white border-secondary" id="newsSummaryInput" name="newsSummary" rows="3" required placeholder="Brief description to show in homepage news card feed list..."></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="newsContentInput" class="form-label text-gold">Full Article Content</label>
                                        <textarea class="form-control bg-dark text-white border-secondary" id="newsContentInput" name="newsContent" rows="8" required placeholder="Detailed body content for the full details page. Double line break represents a new paragraph..."></textarea>
                                    </div>

                                    <div class="row align-items-center mb-3">
                                        <div class="col-sm-4 text-center">
                                            <div class="border border-secondary rounded p-2 mb-2 bg-dark d-flex align-items-center justify-content-center" style="height: 120px;">
                                                <img id="newsCoverPreview" src="" class="img-fluid rounded" style="max-height: 100px; display: none;">
                                                <div id="newsCoverPlaceholder" class="text-muted"><i class="fa-solid fa-image fa-3x"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <label for="newsImage" class="form-label text-gold">Upload Cover Image</label>
                                            <input class="form-control bg-dark text-white border-secondary mb-2" type="file" id="newsImage" name="newsImage" accept="image/*">
                                            <div class="form-text text-muted small">Recommended size: 800x500px. JPG, PNG, WEBP allowed.</div>
                                        </div>
                                    </div>

                                    <div class="mb-3 form-check form-switch mt-3">
                                        <input class="form-check-input" type="checkbox" id="isFeaturedInput" name="isFeatured" value="1">
                                        <label class="form-check-label text-gold" for="isFeaturedInput">
                                            <i class="fa-solid fa-star text-gold me-1"></i> Highlight as Featured Spotlight Article (Left Spotlight Box)
                                        </label>
                                    </div>

                                    <div class="d-flex justify-content-between border-top border-secondary pt-3 mt-4">
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnDeleteNews">
                                            <i class="fa-solid fa-trash me-1"></i> Delete Article
                                        </button>
                                        <span class="flex-grow-1"></span>
                                        <button type="submit" class="btn btn-gold btn-sm px-4" id="btnSaveNews">
                                            <i class="fa-solid fa-save me-1"></i> Publish Article
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card admin-card text-center py-5 text-muted" id="newsPlaceholder">
                            <i class="fa-solid fa-newspaper fa-4x mb-3 text-gold" style="opacity: 0.3;"></i>
                            <p>Select an article from the directory to edit, or click "Add Article" to publish a new one.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COURSE MANAGER TAB -->
            <div class="tab-pane fade" id="coursesTab" role="tabpanel" aria-labelledby="courses-tab">
                <div class="card admin-card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 text-gold"><i class="fa-solid fa-graduation-cap me-2"></i> Course Directory</h5>
                        <div class="d-flex gap-2 align-items-center">
                            <select id="courseCategoryFilter" class="form-select form-select-sm bg-dark text-white border-secondary" style="width: 220px;">
                                <option value="">All Categories</option>
                                <option value="core_programme">Core Programmes</option>
                                <option value="professional_development">Professional Development</option>
                            </select>
                            <input type="text" id="courseSearchInput" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="Search courses..." style="width: 200px;">
                            <button type="button" class="btn btn-gold btn-sm" id="btnAddNewCourse">
                                <i class="fa-solid fa-plus me-1"></i> Add Course
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Duration</th>
                                        <th>Fee</th>
                                        <th>Status</th>
                                        <th class="text-center">Order</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="coursesTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="spinner-border text-gold" role="status"></div>
                                            <p class="mt-2 text-muted mb-0">Loading course directory...</p>
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

    <!-- COURSE EDIT / CREATE MODAL -->
    <div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true" style="backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="background:#0e0e0e; border:2px solid #D4AF37; border-radius:15px; color:#fff;">
                <div class="modal-header" style="border-bottom:1px solid rgba(212,175,55,0.2);">
                    <h5 class="modal-title text-gold" id="courseModalLabel"><i class="fa-solid fa-pen-to-square me-2"></i> Create / Edit Course</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="courseForm" enctype="multipart/form-data">
                        <input type="hidden" id="courseFormId" name="id" value="0">
                        
                        <!-- Row 1: Basic Info -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="courseFormTitle" class="form-label text-gold">Course Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormTitle" name="title" required placeholder="e.g. Fashion Modeling">
                            </div>
                            <div class="col-md-6">
                                <label for="courseFormSlug" class="form-label text-gold">URL Slug (SEO)</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormSlug" name="slug" placeholder="e.g. modeling (auto-generated if empty)">
                            </div>
                            <div class="col-md-4">
                                <label for="courseFormCategory" class="form-label text-gold">Category Group <span class="text-danger">*</span></label>
                                <select class="form-select bg-dark text-white border-secondary" id="courseFormCategory" name="category" required>
                                    <option value="core_programme">Core Programme</option>
                                    <option value="professional_development">Professional Development Module</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="courseFormCategoryBadge" class="form-label text-gold">Hero Badge Text</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormCategoryBadge" name="category_badge" placeholder="e.g. CORE PROGRAMME">
                            </div>
                            <div class="col-md-4">
                                <label for="courseFormStatus" class="form-label text-gold">Publish Status</label>
                                <select class="form-select bg-dark text-white border-secondary" id="courseFormStatus" name="status">
                                    <option value="published">Published</option>
                                    <option value="draft">Draft / Hidden</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Details & Pricing -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="courseFormDuration" class="form-label text-gold">Duration <span class="text-danger">*</span></label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormDuration" name="duration" required placeholder="e.g. 6 Months">
                            </div>
                            <div class="col-md-4">
                                <label for="courseFormAdmissionFee" class="form-label text-gold">Admission Fee Text</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormAdmissionFee" name="admission_fee" placeholder="e.g. BDT 20,000 (Admission Included)">
                            </div>
                            <div class="col-md-4">
                                <label for="courseFormCourseFee" class="form-label text-gold">Course Fee Text</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormCourseFee" name="course_fee" placeholder="e.g. BDT 20,000">
                            </div>
                            <div class="col-md-4">
                                <label for="courseFormEligibility" class="form-label text-gold">Eligibility</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormEligibility" name="eligibility" placeholder="e.g. Passionate individuals seeking modeling careers.">
                            </div>
                            <div class="col-md-4">
                                <label for="courseFormAgeReq" class="form-label text-gold">Age Requirement</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormAgeReq" name="age_requirement" placeholder="e.g. Open to all age groups">
                            </div>
                            <div class="col-md-4">
                                <label for="courseFormOrder" class="form-label text-gold">Display Order Index</label>
                                <input type="number" class="form-control bg-dark text-white border-secondary" id="courseFormOrder" name="display_order" value="1" min="1">
                            </div>
                        </div>

                        <!-- Descriptions -->
                        <div class="mb-3">
                            <label for="courseFormShortDesc" class="form-label text-gold">Short Summary (Hero Tagline & Cards) <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-dark text-white border-secondary" id="courseFormShortDesc" name="short_description" rows="2" required placeholder="Brief description..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="courseFormFullDesc" class="form-label text-gold">Full Program Overview <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-dark text-white border-secondary" id="courseFormFullDesc" name="full_description" rows="4" required placeholder="Detailed overview paragraph..."></textarea>
                        </div>

                        <!-- Hero Image Upload -->
                        <div class="card bg-dark border-secondary p-3 mb-4">
                            <label class="form-label text-gold mb-2"><i class="fa-solid fa-image me-2"></i> Course Hero Image</label>
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center mb-2 mb-md-0">
                                    <img id="heroImagePreview" src="../assets/images/programs/fasion.jpg" class="img-fluid rounded border border-gold" style="max-height: 110px; object-fit: cover;">
                                </div>
                                <div class="col-md-9">
                                    <input type="file" class="form-control bg-dark text-white border-secondary mb-2" id="heroImageFile" name="hero_image_file" accept="image/*">
                                    <small class="text-muted">Recommended aspect ratio 16:9. Formats: JPG, PNG, WEBP. Max size: 5MB.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Repeater 1: Training Modules -->
                        <div class="card bg-dark border-secondary p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-gold"><i class="fa-solid fa-list-check me-2"></i> Training Modules</h6>
                                <button type="button" class="btn btn-outline-gold btn-sm" id="btnAddModuleRow"><i class="fa-solid fa-plus me-1"></i> Add Module</button>
                            </div>
                            <div id="modulesContainer" class="d-flex flex-column gap-2">
                                <!-- Dynamic Module Rows -->
                            </div>
                        </div>

                        <!-- Dynamic Repeater 2: Weekly Schedule -->
                        <div class="card bg-dark border-secondary p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-gold"><i class="fa-solid fa-calendar-days me-2"></i> Weekly Schedule</h6>
                                <button type="button" class="btn btn-outline-gold btn-sm" id="btnAddScheduleRow"><i class="fa-solid fa-plus me-1"></i> Add Class</button>
                            </div>
                            <div id="schedulesContainer" class="d-flex flex-column gap-2">
                                <!-- Dynamic Schedule Rows -->
                            </div>
                        </div>

                        <!-- Dynamic Repeater 3: Career Opportunities -->
                        <div class="card bg-dark border-secondary p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-gold"><i class="fa-solid fa-briefcase me-2"></i> Career Opportunities</h6>
                                <button type="button" class="btn btn-outline-gold btn-sm" id="btnAddCareerRow"><i class="fa-solid fa-plus me-1"></i> Add Career Path</button>
                            </div>
                            <div id="careersContainer" class="d-flex flex-column gap-2">
                                <!-- Dynamic Career Rows -->
                            </div>
                        </div>

                        <!-- Dynamic Repeater 4: Gallery Highlights -->
                        <div class="card bg-dark border-secondary p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-gold"><i class="fa-solid fa-images me-2"></i> Program Highlight Photos</h6>
                            </div>
                            <div id="existingGalleriesContainer" class="row g-2 mb-3">
                                <!-- Existing Gallery Previews -->
                            </div>
                            <div>
                                <label for="galleryFiles" class="form-label text-gold small">Upload Additional Highlight Photos</label>
                                <input type="file" class="form-control bg-dark text-white border-secondary" id="galleryFiles" name="gallery_files[]" multiple accept="image/*">
                            </div>
                        </div>

                        <!-- SEO Metadata -->
                        <div class="card bg-dark border-secondary p-3 mb-3">
                            <h6 class="mb-3 text-gold"><i class="fa-solid fa-magnifying-glass me-2"></i> SEO Meta Tags</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="courseFormMetaTitle" class="form-label text-white small">Meta Title</label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormMetaTitle" name="meta_title" placeholder="SEO title tag">
                                </div>
                                <div class="col-md-6">
                                    <label for="courseFormMetaDesc" class="form-label text-white small">Meta Description</label>
                                    <input type="text" class="form-control bg-dark text-white border-secondary" id="courseFormMetaDesc" name="meta_description" placeholder="SEO meta description">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top border-secondary">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-gold px-4" id="btnSaveCourseSubmit"><i class="fa-solid fa-save me-1"></i> Save Course Record</button>
                        </div>
                    </form>
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
