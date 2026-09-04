<?php
/**
 * STAR FAIR - Dynamic Course Detail Page
 * Core PHP 8.x + MySQL
 */

require_once __DIR__ . '/backend/database.php';

$slug = trim($_GET['slug'] ?? '');
$id = intval($_GET['id'] ?? 0);
$preview = !empty($_GET['preview']) && !empty($_SESSION['admin_logged_in']);

$course = null;
$modules = [];
$schedules = [];
$careers = [];
$galleries = [];

if (!empty($slug) || $id > 0) {
    try {
        if (!empty($slug)) {
            $stmt = $pdo->prepare($preview ? "SELECT * FROM `courses` WHERE `slug` = ?" : "SELECT * FROM `courses` WHERE `slug` = ? AND `status` = 'published'");
            $stmt->execute([$slug]);
        } else {
            $stmt = $pdo->prepare($preview ? "SELECT * FROM `courses` WHERE `id` = ?" : "SELECT * FROM `courses` WHERE `id` = ? AND `status` = 'published'");
            $stmt->execute([$id]);
        }
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($course) {
            $cId = $course['id'];

            // Modules
            $stmtMod = $pdo->prepare("SELECT `module_name` FROM `course_modules` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
            $stmtMod->execute([$cId]);
            $modules = $stmtMod->fetchAll(PDO::FETCH_ASSOC);

            // Schedules
            $stmtSch = $pdo->prepare("SELECT `day_name`, `time_text`, `topic_text` FROM `course_schedules` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
            $stmtSch->execute([$cId]);
            $schedules = $stmtSch->fetchAll(PDO::FETCH_ASSOC);

            // Careers
            $stmtCar = $pdo->prepare("SELECT `career_title` FROM `course_careers` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
            $stmtCar->execute([$cId]);
            $careers = $stmtCar->fetchAll(PDO::FETCH_ASSOC);

            // Galleries
            $stmtGal = $pdo->prepare("SELECT `image_path`, `alt_text` FROM `course_galleries` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
            $stmtGal->execute([$cId]);
            $galleries = $stmtGal->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        $course = null;
    }
}

if (!$course) {
    http_response_code(404);
    include __DIR__ . '/404.html';
    exit;
}

$domain = 'https://starfairbd.com';
$canonicalUrl = $domain . '/course-detail.php?slug=' . urlencode($course['slug']);
$metaTitle = htmlspecialchars($course['meta_title'] ?: ($course['title'] . ' | STAR FAIR Bangladesh'));
$metaDesc = htmlspecialchars($course['meta_description'] ?: $course['short_description']);
$ogImage = str_starts_with($course['hero_image'], 'http') ? $course['hero_image'] : ($domain . '/' . ltrim($course['hero_image'], '/'));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Dynamic SEO Meta Tags -->
    <title><?php echo $metaTitle; ?></title>
    <meta name="description" content="<?php echo $metaDesc; ?>">
    <meta name="keywords" content="Star Fair, Star Fair BD, <?php echo htmlspecialchars($course['title']); ?>, modeling course Dhaka, grooming training Bangladesh">
    <link rel="canonical" href="<?php echo $canonicalUrl; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $canonicalUrl; ?>">
    <meta property="og:title" content="<?php echo $metaTitle; ?>">
    <meta property="og:description" content="<?php echo $metaDesc; ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $canonicalUrl; ?>">
    <meta property="twitter:title" content="<?php echo $metaTitle; ?>">
    <meta property="twitter:description" content="<?php echo $metaDesc; ?>">
    <meta property="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/logo/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/logo/favicon.ico">

    <!-- JSON-LD Organization Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "STAR FAIR Fashion and Cultural Training Institute",
      "alternateName": "STAR FAIR BD",
      "url": "<?php echo $domain; ?>",
      "logo": "<?php echo $domain; ?>/assets/images/logo/logo.jpg"
    }
    </script>

    <!-- JSON-LD Course Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Course",
      "name": "<?php echo htmlspecialchars($course['title']); ?>",
      "description": "<?php echo $metaDesc; ?>",
      "provider": {
        "@type": "EducationalOrganization",
        "name": "STAR FAIR Fashion and Cultural Training Institute",
        "sameAs": "<?php echo $domain; ?>"
      }
    }
    </script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style2.css">
</head>

<body style="background: #000000; color: #ffffff;">

    <!-- HEADER START -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.html">
                    <img src="assets/images/logo/logo.jpg" alt="Star Fair BD Logo" class="logo-img" data-key="logo">
                    <span class="logo-text"> </span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="eventsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Events</a>
                            <ul class="dropdown-menu" aria-labelledby="eventsDropdown">
                                <li><a class="dropdown-item" href="kids-modelling.html">Kids Modelling</a></li>
                                <li><a class="dropdown-item" href="fashion-show.html">Fashion Show</a></li>
                                <li><a class="dropdown-item" href="campaign-shoot.html">Campaign Shoot</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="galleryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gallery</a>
                            <ul class="dropdown-menu" aria-labelledby="galleryDropdown">
                                <li><a class="dropdown-item" href="photo-gallery.html">Photo Gallery</a></li>
                                <li><a class="dropdown-item" href="kids-portfolio.html">Kids Portfolio</a></li>
                                <li><a class="dropdown-item" href="teenager-portfolio.html">Teenager Portfolio</a></li>
                                <li><a class="dropdown-item" href="models-portfolio.html">Models Portfolio</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="advisor-trainers.html">Advisor & Trainers</a></li>
                        <li class="nav-item"><a class="nav-link" href="award.html">Award</a></li>
                        <li class="nav-item"><a class="nav-link" href="magazine.html">Magazine</a></li>
                        <li class="nav-item"><a class="nav-link active" href="course.php">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <!-- HEADER END -->

    <!-- HERO SECTION START -->
    <section class="program-detail-hero" data-bg-key="hero_img" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?php echo htmlspecialchars($course['hero_image']); ?>'); background-size: cover; background-position: center;">
        <div class="container text-center" data-aos="fade-up">
            <span class="program-meta-badge"><?php echo htmlspecialchars($course['category_badge']); ?></span>
            <h1 class="program-detail-title"><?php echo htmlspecialchars($course['title']); ?></h1>
            <div style="width: 80px; height: 3px; background: #D4AF37; margin: 20px auto;"></div>
            <p class="lead" style="max-width: 800px; margin: 0 auto; color: #ddd;"><?php echo htmlspecialchars($course['short_description']); ?></p>
        </div>
    </section>
    <!-- HERO SECTION END -->

    <!-- PROGRAM OVERVIEW START -->
    <section class="program-section-dark">
        <div class="container">
            <div class="row g-5">
                <!-- Left Details -->
                <div class="col-lg-8" data-aos="fade-right">
                    <h3 class="form-section-title" style="margin-top:0;">Program Overview</h3>
                    <p style="color:#ccc; line-height:1.9; font-size:1.05rem; text-align: justify;">
                        <?php echo nl2br(htmlspecialchars($course['full_description'])); ?>
                    </p>
                    
                    <?php if (!empty($modules)): ?>
                    <h3 class="form-section-title" style="margin-top:50px;">Training Modules</h3>
                    <ul class="module-list-group">
                        <?php foreach ($modules as $m): ?>
                            <li><?php echo htmlspecialchars($m['module_name']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <!-- Right Box Info -->
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="program-info-box">
                        <h4>Program Details</h4>
                        <ul class="program-info-list">
                            <li><strong>Duration:</strong> <span><?php echo htmlspecialchars($course['duration']); ?></span></li>
                            <?php if (!empty($course['admission_fee'])): ?>
                                <li><strong>Admission Fee:</strong> <span><?php echo htmlspecialchars($course['admission_fee']); ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($course['course_fee']) && $course['course_fee'] !== $course['admission_fee']): ?>
                                <li><strong>Course Fee:</strong> <span><?php echo htmlspecialchars($course['course_fee']); ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($course['eligibility'])): ?>
                                <li><strong>Eligibility:</strong> <span><?php echo htmlspecialchars($course['eligibility']); ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($course['age_requirement'])): ?>
                                <li><strong>Age Requirement:</strong> <span><?php echo htmlspecialchars($course['age_requirement']); ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($course['course_type'])): ?>
                                <li><strong>Course Type:</strong> <span><?php echo htmlspecialchars($course['course_type']); ?></span></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- PROGRAM OVERVIEW END -->

    <!-- SCHEDULE & OPPORTUNITIES START -->
    <?php if (!empty($schedules) || !empty($careers)): ?>
    <section class="program-section-dark" style="background:#080808; border-top: 1px solid rgba(212, 175, 55, 0.15);">
        <div class="container">
            <div class="row g-5">
                <!-- Weekly schedule -->
                <?php if (!empty($schedules)): ?>
                <div class="<?php echo !empty($careers) ? 'col-lg-6' : 'col-lg-12'; ?>" data-aos="fade-right">
                    <h3 class="form-section-title" style="margin-top:0;">Weekly Schedule</h3>
                    <div style="background:#111; border: 1px solid rgba(212, 175, 55, 0.2); padding: 30px; border-radius:12px;">
                        <ul style="list-style:none; padding:0; margin:0;">
                            <?php foreach ($schedules as $s): ?>
                                <li class="py-2 border-bottom border-secondary">
                                    <i class="fa-solid fa-clock text-gold me-2"></i>
                                    <strong><?php echo htmlspecialchars($s['day_name']); ?>:</strong> 
                                    <span style="color: #ddd; margin-left: 8px;">
                                        <?php echo htmlspecialchars($s['time_text']); ?>
                                        <?php if (!empty($s['topic_text'])): ?>
                                            (<?php echo htmlspecialchars($s['topic_text']); ?>)
                                        <?php endif; ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Career Opps -->
                <?php if (!empty($careers)): ?>
                <div class="<?php echo !empty($schedules) ? 'col-lg-6' : 'col-lg-12'; ?>" data-aos="fade-left">
                    <h3 class="form-section-title" style="margin-top:0;">Career Opportunities</h3>
                    <ul class="module-list-group">
                        <?php foreach ($careers as $c): ?>
                            <li><?php echo htmlspecialchars($c['career_title']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- SCHEDULE & OPPORTUNITIES END -->

    <!-- GALLERY START -->
    <?php if (!empty($galleries)): ?>
    <section class="program-section-dark" style="border-top: 1px solid rgba(212, 175, 55, 0.15);">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-subtitle">GALLERY</span>
                <h2 class="text-white">Program Highlights</h2>
                <div style="width: 80px; height: 3px; background: #D4AF37; margin: 0 auto 15px;"></div>
            </div>
            <div class="row g-3" data-aos="fade-up">
                <?php foreach ($galleries as $g): ?>
                <div class="col-md-3 col-6">
                    <img src="<?php echo htmlspecialchars($g['image_path']); ?>" class="gallery-img img-fluid" style="height: 250px; object-fit: cover;" alt="<?php echo htmlspecialchars($g['alt_text'] ?: 'Program Highlight'); ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- GALLERY END -->

    <!-- REGISTRATION CTA START -->
    <section class="registration-cta" style="background: #000; border-top: 1px solid rgba(212, 175, 55, 0.15);">
        <div class="container text-center" data-aos="fade-up">
            <div class="cta-box">
                <h2>Enroll In This Program</h2>
                <p class="lead text-muted mb-4" style="color: #bbb !important;">
                    Take the first step towards building a successful career. Apply today for admission.
                </p>
                <a href="registration.html?course=<?php echo urlencode($course['slug']); ?>" class="btn-register">Apply Online Now</a>
            </div>
        </div>
    </section>
    <!-- REGISTRATION CTA END -->

    <!-- FOOTER START -->
    <footer class="footer-section">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="footer-logo">
                        <img src="assets/images/logo/logo.jpg" alt="Star Fair Logo" loading="lazy" data-key="logo">
                    </div>
                    <p class="footer-description">
                        Star Fair Fashion and Cultural Training Institute has been developing confidence, personality, fashion, grooming and creative talent since 2009. Our mission is to empower future models, performers and young leaders through professional training, events and industry exposure.
                    </p>
                </div>
                <div class="col-lg-4">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="index.html">About Us</a></li>
                        <li><a href="index.html#events">Events</a></li>
                        <li><a href="magazine.html">Magazine</a></li>
                        <li><a href="contact.html">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h4 class="footer-title">Follow Us</h4>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/starfairfashion/" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/alamgir_hossain_alo/" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="http://www.youtube.com/@starfairbd" target="_blank"><i class="fab fa-youtube"></i></a>
                        <a href="http://www.youtube.com/@starfairbd" target="_blank"><i class="fab fa-linkedin"></i></a>
                        <a href="http://www.youtube.com/@starfairbd" target="_blank"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <hr class="footer-line">
            <div class="footer-bottom">
                <p>© 2026 Star Fair Bangladesh. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    <!-- FOOTER END -->

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            disable: window.innerWidth < 768
        });
    </script>

    <script src="assets/js/dynamic-images.js"></script>
</body>

</html>
