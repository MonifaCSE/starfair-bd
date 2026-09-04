<?php
/**
 * STAR FAIR - Dynamic Courses Listing Page
 * Core PHP 8.x + MySQL
 */

require_once __DIR__ . '/backend/database.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM `courses` WHERE `status` = 'published' ORDER BY `display_order` ASC, `id` ASC");
    $stmt->execute();
    $allCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $allCourses = [];
}

$coreProgrammes = array_filter($allCourses, fn($c) => $c['category'] === 'core_programme');
$profDevModules = array_filter($allCourses, fn($c) => $c['category'] === 'professional_development');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title>STAR FAIR | Fashion & Cultural Training Institute Bangladesh</title>
    <meta name="description" content="STAR FAIR Fashion and Cultural Training Institute is Bangladesh's leading platform for professional modeling, grooming, acting, dance, photography, and cultural training. Nurturing future stars since 2009.">
    <meta name="keywords" content="Star Fair, Star Fair BD, modeling course Dhaka, grooming training Bangladesh, acting classes, dance academy, photography course, beauty pageant preparation, Alamgir Hossain Alo">
    <link rel="canonical" href="https://starfairbd.com/course.php">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://starfairbd.com/course.php">
    <meta property="og:title" content="STAR FAIR | Fashion & Cultural Training Institute Bangladesh">
    <meta property="og:description" content="Nurturing future stars in fashion, beauty, culture, and lifestyle since 2009. Professional courses in runway modeling, acting, photography, and grooming.">
    <meta property="og:image" content="https://starfairbd.com/assets/images/logo/logo.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://starfairbd.com/course.php">
    <meta property="twitter:title" content="STAR FAIR | Fashion & Cultural Training Institute Bangladesh">
    <meta property="twitter:description" content="Nurturing future stars in fashion, beauty, culture, and lifestyle since 2009. Professional courses in runway modeling, acting, photography, and grooming.">
    <meta property="twitter:image" content="https://starfairbd.com/assets/images/logo/logo.jpg">

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
      "url": "https://starfairbd.com",
      "logo": "https://starfairbd.com/assets/images/logo/logo.jpg",
      "sameAs": [
        "https://www.facebook.com/starfairfashion/",
        "https://www.instagram.com/alamgir_hossain_alo/",
        "https://www.youtube.com/@starfairbd"
      ]
    }
    </script>

    <!-- JSON-LD Educational Organization Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "STAR FAIR Fashion and Cultural Training Institute",
      "url": "https://starfairbd.com",
      "logo": "https://starfairbd.com/assets/images/logo/logo.jpg",
      "description": "Bangladesh's leading training institute for runway modeling, personal grooming, acting, dance, photography and beauty pageants.",
      "parentOrganization": {
        "@type": "Organization",
        "name": "STAR FAIR BD"
      }
    }
    </script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style2.css">
</head>

<body>

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
                        <li class="nav-item"><a class="nav-link " href="index.html">Home</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle " href="#" id="eventsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Events</a>
                            <ul class="dropdown-menu" aria-labelledby="eventsDropdown">
                                <li><a class="dropdown-item" href="kids-modelling.html">Kids Modelling</a></li>
                                <li><a class="dropdown-item" href="fashion-show.html">Fashion Show</a></li>
                                <li><a class="dropdown-item" href="campaign-shoot.html">Campaign Shoot</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle " href="#" id="galleryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gallery</a>
                            <ul class="dropdown-menu" aria-labelledby="galleryDropdown">
                                <li><a class="dropdown-item" href="photo-gallery.html">Photo Gallery</a></li>
                                <li><a class="dropdown-item" href="kids-portfolio.html">Kids Portfolio</a></li>
                                <li><a class="dropdown-item" href="teenager-portfolio.html">Teenager Portfolio</a></li>
                                <li><a class="dropdown-item" href="models-portfolio.html">Models Portfolio</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link " href="advisor-trainers.html">Advisor & Trainers</a></li>
                        <li class="nav-item"><a class="nav-link " href="award.html">Award</a></li>
                        <li class="nav-item"><a class="nav-link " href="magazine.html">Magazine</a></li>
                        <li class="nav-item"><a class="nav-link active" href="course.php">Courses</a></li>
                        <li class="nav-item"><a class="nav-link " href="registration.html">Admission</a></li>
                        <li class="nav-item"><a class="nav-link " href="contact.html">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <!-- HEADER END -->

    <!-- HERO SECTION START -->
    <section class="courses-hero" data-bg-key="hero_img">
        <div class="container">
            <div class="courses-hero-content">
                <h2>STAR FAIR COURSES & MODULES</h2>
                <p>Professional training programs designed to develop creativity, technical excellence, leadership, and confidence.</p>
                <a href="#programs" class="hero-btn">Explore Programs</a>
            </div>
        </div>
    </section>
    <!-- HERO SECTION END -->

    <!-- CARD SECTION START-->
    <section id="programs" class="programs-section py-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h5>STAR FAIR CURRICULUM</h5>
                <h2 class="text-white">Training Programmes</h2>
                <div style="width: 80px; height: 3px; background: #D4AF37; margin: 0 auto 20px;"></div>
                <p style="color: #bbb;">
                    Professional training programs designed to develop creativity, technical excellence, leadership, and confidence.
                </p>
            </div>

            <!-- 1. Core Programmes -->
            <?php if (!empty($coreProgrammes)): ?>
            <div class="row mb-5" data-aos="fade-up">
                <div class="col-12 mb-4">
                    <h3 class="form-section-title" style="margin-top:0;">1. Core Programmes</h3>
                </div>
                <?php foreach ($coreProgrammes as $c): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="program-card">
                        <div class="program-img">
                            <img src="<?php echo htmlspecialchars($c['hero_image']); ?>" alt="<?php echo htmlspecialchars($c['title']); ?>">
                        </div>
                        <div class="program-content">
                            <h4><?php echo htmlspecialchars($c['title']); ?></h4>
                            <p class="short-text">Duration: <?php echo htmlspecialchars($c['duration']); ?></p>
                            <a href="course-detail.php?slug=<?php echo urlencode($c['slug']); ?>" class="btn btn-gold w-100">Explore Program</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- 2. Professional Development Modules -->
            <?php if (!empty($profDevModules)): ?>
            <div class="row" data-aos="fade-up">
                <div class="col-12 mb-4">
                    <h3 class="form-section-title">2. Professional Development Modules</h3>
                </div>
                <?php foreach ($profDevModules as $c): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="program-card">
                        <div class="program-img">
                            <img src="<?php echo htmlspecialchars($c['hero_image']); ?>" alt="<?php echo htmlspecialchars($c['title']); ?>">
                        </div>
                        <div class="program-content">
                            <h4><?php echo htmlspecialchars($c['title']); ?></h4>
                            <p class="short-text">Duration: <?php echo htmlspecialchars($c['duration']); ?></p>
                            <a href="course-detail.php?slug=<?php echo urlencode($c['slug']); ?>" class="btn btn-gold w-100">Explore Program</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>
    </section>
    <!--CARD SECTION END-->

    <!--Training Timeline Section-->
    <section class="modeling-course-section">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h5>6 MONTH COURSE</h5>
                <h2>Professional Modeling Training Program</h2>
                <p>
                    A structured 6-month journey designed to transform aspiring individuals into confident and industry-ready models.
                </p>
            </div>

            <div class="timeline">
                <!-- Month 1 -->
                <div class="timeline-item">
                    <div class="month-badge">Month 01</div>
                    <div class="timeline-card">
                        <h3>Basics of Modeling & Runway</h3>
                        <span class="star-title">Exploral Star</span>
                        <ul>
                            <li>Introduction to Modeling</li>
                            <li>Stage Presence</li>
                            <li>Runway Walking</li>
                            <li>Fashion Industry Overview</li>
                            <li>Practical Runway Session</li>
                        </ul>
                    </div>
                </div>
                <!-- Month 2 -->
                <div class="timeline-item">
                    <div class="month-badge">Month 02</div>
                    <div class="timeline-card">
                        <h3>Yoga, Poses & Orientation</h3>
                        <span class="star-title">Aspiring Star</span>
                        <ul>
                            <li>Yoga for Models</li>
                            <li>Professional Posing</li>
                            <li>Expressions Training</li>
                            <li>Modeling Categories</li>
                            <li>Camera Practice Session</li>
                        </ul>
                    </div>
                </div>
                <!-- Month 3 -->
                <div class="timeline-item">
                    <div class="month-badge">Month 03</div>
                    <div class="timeline-card">
                        <h3>Camera Presence & Choreography</h3>
                        <span class="star-title">Rising Star</span>
                        <ul>
                            <li>Camera Angles</li>
                            <li>Expressions</li>
                            <li>Body Language</li>
                            <li>Confidence Building</li>
                            <li>Choreography Practice</li>
                        </ul>
                    </div>
                </div>
                <!-- Month 4 -->
                <div class="timeline-item">
                    <div class="month-badge">Month 04</div>
                    <div class="timeline-card">
                        <h3>Styling & Fashion Show Management</h3>
                        <span class="star-title">Ambassador Star</span>
                        <ul>
                            <li>Fashion Styling</li>
                            <li>Dance Techniques</li>
                            <li>Backstage Management</li>
                            <li>Show Planning</li>
                            <li>Fashion Show Execution</li>
                        </ul>
                    </div>
                </div>
                <!-- Month 5 -->
                <div class="timeline-item">
                    <div class="month-badge">Month 05</div>
                    <div class="timeline-card">
                        <h3>Makeup, Fitness & Nutrition</h3>
                        <span class="star-title">Envoy Star</span>
                        <ul>
                            <li>Professional Makeup</li>
                            <li>Skincare</li>
                            <li>Fitness Planning</li>
                            <li>Healthy Food Habits</li>
                            <li>Photoshoot Preparation</li>
                        </ul>
                    </div>
                </div>
                <!-- Month 6 -->
                <div class="timeline-item">
                    <div class="month-badge">Month 06</div>
                    <div class="timeline-card">
                        <h3>Drama, Stage Presence & Camera Work</h3>
                        <span class="star-title">Envoy Star</span>
                        <ul>
                            <li>Drama & Acting</li>
                            <li>Stage Confidence</li>
                            <li>Screen Presence</li>
                            <li>Career Preparation</li>
                            <li>Final Presentation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--Portfolio Start-->
    <section class="portfolio-section">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h5>PROFESSIONAL PORTFOLIO</h5>
                <h2>Build Your Modeling Portfolio</h2>
                <p>
                    Every student receives the opportunity to create a professional portfolio with expert makeup, styling and photography support.
                </p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="portfolio-card">
                        <img src="assets/images/programs/closeup.jpg" data-key="gallery_closeup" alt="Close Up Portrait">
                        <div class="portfolio-content">
                            <h4>Close Up Portrait</h4>
                            <p>Professional beauty and facial expression portfolio.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="portfolio-card">
                        <img src="assets/images/programs/formal.jpg" data-key="gallery_formal" alt="Formal & Corporate">
                        <div class="portfolio-content">
                            <h4>Formal & Corporate</h4>
                            <p>Executive, red carpet and fashion styling shoots.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="portfolio-card">
                        <img src="assets/images/programs/bridal.jpg" data-key="gallery_bridal" alt="Indian / Bridal">
                        <div class="portfolio-content">
                            <h4>Indian / Bridal</h4>
                            <p>Traditional and bridal themed professional shoots.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--Trust Section-->
    <section class="why-starfair">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h5>WHY STAR FAIR</h5>
                <h2>What Makes Us Different</h2>
                <p>Building confidence, creativity and professionalism since 2009.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-award"></i></div>
                        <h4>15+ Years Experience</h4>
                        <p>Established in 2009 with years of professional training experience.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-user-graduate"></i></div>
                        <h4>1000+ Students</h4>
                        <p>Successfully trained aspiring models and performers.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-camera"></i></div>
                        <h4>Professional Portfolio</h4>
                        <p>Industry standard photoshoot and portfolio development.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-trophy"></i></div>
                        <h4>Fashion Events</h4>
                        <p>Real world experience through shows and creative events.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
