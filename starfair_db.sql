-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 04, 2026 at 06:11 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `starfair_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password_hash`, `created_at`) VALUES
(1, 'admin@starfairbd.com', '$2y$10$Ga5ifFiyYZjBBCnWkimrzehyRYNPnYIImgKDqDHM5QQpvhST38hui', '2026-08-04 11:28:58');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'core_programme',
  `category_badge` varchar(100) NOT NULL DEFAULT 'CORE PROGRAMME',
  `short_description` text NOT NULL,
  `full_description` longtext NOT NULL,
  `hero_image` varchar(255) NOT NULL,
  `duration` varchar(100) NOT NULL,
  `admission_fee` varchar(255) DEFAULT NULL,
  `course_fee` varchar(255) DEFAULT NULL,
  `eligibility` text DEFAULT NULL,
  `age_requirement` varchar(100) DEFAULT NULL,
  `course_type` varchar(100) DEFAULT NULL,
  `status` enum('published','draft') NOT NULL DEFAULT 'published',
  `display_order` int(11) NOT NULL DEFAULT 1,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `slug`, `category`, `category_badge`, `short_description`, `full_description`, `hero_image`, `duration`, `admission_fee`, `course_fee`, `eligibility`, `age_requirement`, `course_type`, `status`, `display_order`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 'Fashion Modeling', 'modeling', 'core_programme', 'CORE PROGRAMME', 'Develop the essential skills required for a successful modelling career.', 'Develop the essential skills required for a successful modelling career through professional runway training, fashion presentation, posing, styling, portfolio development, camera performance, commercial modelling, fashion etiquette, confidence building, and personal branding.', 'assets/images/programs/fasion.jpg', '6 Months', 'BDT 20,000 (Admission Included)', 'BDT 20,000', 'Passionate individuals seeking modeling careers.', 'All age groups', 'Core', 'published', 1, 'STAR FAIR | Fashion Modeling Training Program Bangladesh', 'Professional runway modeling, grooming, and portfolio development course in Bangladesh since 2009.', '2026-09-04 15:20:21', '2026-09-04 15:46:04'),
(2, 'Acting & Performance', 'acting', 'core_programme', 'CORE PROGRAMME', 'Build a strong foundation in acting and performance for stage and screen.', 'Build a strong foundation in acting through character development, voice and speech, script interpretation, facial expression, improvisation, stagecraft, audition preparation, camera acting, and professional performance techniques.', 'assets/images/programs/acting.jpg', '2–4 Years', 'Admission BDT 3,000 + Monthly BDT 500', 'Monthly BDT 500', 'Open to all age groups and experience levels.', 'Open to all age groups', 'Core', 'published', 2, 'STAR FAIR | Acting & Screen Performance Course Bangladesh', 'Comprehensive acting, drama, voice modulation and screen performance course.', '2026-09-04 15:20:21', '2026-09-04 15:20:21'),
(3, 'Hip Hop Dance', 'hiphop-dance', 'core_programme', 'CORE PROGRAMME', 'Master contemporary Hip Hop dance, choreography and freestyle.', 'Master contemporary Hip Hop dance through foundation techniques, choreography, freestyle, musicality, stage performance, teamwork, fitness, and competition-level training.', 'assets/images/programs/hiphop_dance.png', 'Ongoing Programme', 'Admission BDT 3,000 + Monthly BDT 1,000', 'Monthly BDT 1,000', 'Dance enthusiasts of all skill levels.', 'Open to all age groups', 'Core', 'published', 3, 'STAR FAIR | Hip Hop Dance Academy Bangladesh', 'Learn contemporary hip hop, choreography, freestyle, and stage dance skills.', '2026-09-04 15:20:21', '2026-09-04 15:20:21'),
(4, 'Classical & Cultural Dance', 'classical-dance', 'core_programme', 'CORE PROGRAMME', 'Develop artistic excellence through traditional and cultural dance styles.', 'Develop artistic excellence through classical and cultural dance, focusing on rhythm, expression, grace, posture, storytelling, discipline, and stage performance.', 'assets/images/programs/culture.jpg', 'Ongoing Programme', 'Admission BDT 3,000 + Monthly BDT 600', 'Monthly BDT 600', 'Open to all age groups and experience levels.', 'Open to all age groups', 'Core', 'published', 4, 'STAR FAIR | Classical & Cultural Dance Course Bangladesh', 'Traditional classical dance training, mudras, rhythm, and stage grace.', '2026-09-04 15:20:21', '2026-09-04 15:20:21'),
(5, 'Fine Arts & Creative Expression', 'fine-arts', 'core_programme', 'CORE & DEVELOPMENT PROGRAMME', 'Explore drawing, painting, color theory, composition, and visual storytelling.', 'Develop artistic skills through a comprehensive programme covering drawing, sketching, painting, colour theory, composition, perspective, creative design, crafts, and visual storytelling. Students explore a variety of artistic techniques while enhancing creativity, observation, imagination, and self-expression.', 'assets/images/programs/art.jpg', '6 Months / Ongoing', 'Course Fee BDT 15,000 / Monthly BDT 1,000', 'BDT 15,000', 'Open to children and adults interested in visual arts.', 'All ages', 'Core', 'published', 5, 'STAR FAIR | Fine Arts & Creative Expression Course', 'Drawing, painting, sketch, color theory and visual storytelling training.', '2026-09-04 15:20:21', '2026-09-04 15:20:21'),
(6, 'Poetry Recitation & Performing Literature', 'poetry', 'core_programme', 'CORE PROGRAMME', 'Develop excellence in poetry recitation, voice modulation and diction.', 'Develop excellence in poetry recitation through voice modulation, pronunciation, diction, emotional expression, literary interpretation, and stage confidence.', 'assets/images/programs/poetry_recitation.png', 'Ongoing Programme', 'Admission BDT 3,000 + Monthly BDT 500', 'Monthly BDT 500', 'Literature and recitation enthusiasts.', 'All ages', 'Core', 'published', 6, 'STAR FAIR | Poetry Recitation & Literature Course', 'Master voice modulation, diction, pronunciation, and poetic stage performance.', '2026-09-04 15:20:21', '2026-09-04 15:20:21'),
(7, 'Presentation & Professional Hosting', 'hosting', 'core_programme', 'CORE PROGRAMME', 'Prepare for professional hosting, public speaking and media communication.', 'Prepare for professional hosting, public speaking, television presentation, corporate events, cultural programmes, and media communication through practical, performance-based training.', 'assets/images/programs/hosting.png', 'Ongoing Programme', 'Admission BDT 3,000 + Monthly BDT 1,000', 'Monthly BDT 1,000', 'Aspirants looking to enter media, emceeing, or corporate communications.', 'Open', 'Core', 'published', 7, 'STAR FAIR | Professional Hosting & Public Speaking Course', 'Emceeing, news anchoring, event hosting, and public speaking training.', '2026-09-04 15:20:21', '2026-09-04 15:20:21'),
(8, 'Beauty Pageant Grooming', 'pageant', 'professional_development', 'PROFESSIONAL DEVELOPMENT MODULE', 'Prepare for success in national and international beauty pageants.', 'Prepare for success in national and international beauty pageants through comprehensive professional training. The programme covers runway techniques, pageant walking, stage presence, interview preparation, public speaking, personal branding, grooming, etiquette, fitness presentation, confidence building, beauty styling, makeup fundamentals, media communication, and personality development.', 'assets/images/mentors/torsa.webp?v=1.1', '3 Months', 'BDT 30,000', 'BDT 30,000', 'Aspiring beauty pageant contestants.', 'Open', '', 'published', 8, 'STAR FAIR | Beauty Pageant Grooming Course Bangladesh', 'Pageant walk, interview prep, stage poise, etiquette, and media communication.', '2026-09-04 15:20:21', '2026-09-04 15:53:44'),
(9, 'Photography & Visual Storytelling', 'photography', 'professional_development', 'PROFESSIONAL DEVELOPMENT MODULE', 'Gain practical skills in fashion photography, studio lighting and editing.', 'Gain practical skills in fashion photography, portrait photography, studio lighting, composition, digital editing, visual storytelling, branding, and creative content production.', 'assets/images/programs/photo.jpg', '3 Months', 'BDT 20,000', 'BDT 20,000', 'Photography enthusiasts and media aspirants.', 'Open', 'Professional Development', 'published', 9, 'STAR FAIR | Photography & Visual Storytelling Course', 'Fashion photography, portrait studio lighting, Lightroom, and digital editing.', '2026-09-04 15:20:21', '2026-09-04 15:20:21'),
(10, 'Fashion, Beauty & Prosthetic Makeup', 'makeup', 'professional_development', 'PROFESSIONAL DEVELOPMENT MODULE', 'Professional training covering beauty, editorial, bridal and prosthetic makeup.', 'Professional training covering beauty makeup, fashion and editorial makeup, bridal artistry, prosthetic makeup, special effects (SFX), character transformation, skincare, hygiene, and portfolio development.', 'assets/images/programs/prosthetic_makeup.png', '3 Months', 'BDT 40,000', 'BDT 40,000', 'Creative makeup enthusiasts and future beauty professionals.', 'Open', 'Professional Development', 'published', 10, 'STAR FAIR | Fashion & Prosthetic Makeup Artistry Course', 'Bridal makeup, fashion shoot makeup, SFX, and prosthetic transformation.', '2026-09-04 15:20:21', '2026-09-04 15:47:27'),
(11, 'Life Skills, Leadership & Communication', 'communication', 'professional_development', 'PROFESSIONAL DEVELOPMENT MODULE', 'Strengthen leadership, communication, EQ and career readiness.', 'Strengthen leadership, communication, emotional intelligence, teamwork, critical thinking, professional ethics, problem-solving, career readiness, and workplace confidence.', 'assets/images/programs/communication.jpg', '3 Months', 'BDT 20,000', 'BDT 20,000', 'Open to students, professionals, and job seekers.', 'Open', 'Professional Development', 'published', 11, 'STAR FAIR | Life Skills & Leadership Communication Course', 'Interpersonal communication, emotional intelligence, teamwork, and leadership.', '2026-09-04 15:20:21', '2026-09-04 15:20:21'),
(12, 'Personal Grooming & Image Management', 'grooming', 'professional_development', 'PROFESSIONAL DEVELOPMENT MODULE', 'Develop professional etiquette, body language and self-presentation.', 'Develop professional etiquette, image management, body language, personal branding, confidence, self-presentation, and social and corporate professionalism.', 'assets/images/programs/grooming.jpg', '3 Months', 'BDT 20,000', 'BDT 20,000', 'Individuals seeking self-improvement and corporate grooming.', 'Open', 'Professional Development', 'published', 12, 'STAR FAIR | Personal Grooming & Image Management Course', 'Corporate etiquette, posture, body language, personal styling, and image.', '2026-09-04 15:20:21', '2026-09-04 15:20:21'),
(13, 'Digital Marketing & Personal Branding', 'digital-marketing', 'professional_development', 'PROFESSIONAL DEVELOPMENT MODULE', 'Master digital marketing, content strategy, Meta ads, freelancing and AI tools.', 'Start From Zero - Grow to Pro. Master content marketing with AI, Meta Ads with AI, YouTube growth, local & global freelancing platforms, active & passive income with career guidelines, and professional productivity boosting with AI tools.', 'assets/images/programs/digital_marketing.png?v=1.2', '3 Months (36 Classes)', 'BDT 6,000', 'BDT 6,000', 'Creators, models, students, job holders, and homemakers.', 'Open', 'Professional Development', 'published', 13, 'STAR FAIR | Digital Marketing & Personal Branding Course', 'Master Meta Ads, Canva, YouTube growth, Fiverr freelancing and AI tools.', '2026-09-04 15:20:21', '2026-09-04 15:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `course_careers`
--

CREATE TABLE `course_careers` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `career_title` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_careers`
--

INSERT INTO `course_careers` (`id`, `course_id`, `career_title`, `display_order`) VALUES
(218, 1, 'Professional Runway Model', 1),
(219, 1, 'Brand Ambassador', 2),
(220, 1, 'Commercial Model', 3),
(221, 1, 'Fashion Influencer', 4),
(222, 1, 'Pageant Contestant', 5),
(223, 2, 'Theatre Actor', 1),
(224, 2, 'Film & TV Actor', 2),
(225, 2, 'Voiceover Artist', 3),
(226, 2, 'Director / Assistant Director', 4),
(227, 2, 'Screenwriter', 5),
(228, 3, 'Professional Dancer', 1),
(229, 3, 'Choreographer', 2),
(230, 3, 'Dance Instructor', 3),
(231, 3, 'Backup Dancer', 4),
(232, 3, 'Cultural Performer', 5),
(233, 4, 'Classical Dancer', 1),
(234, 4, 'Choreographer', 2),
(235, 4, 'Dance Academic', 3),
(236, 4, 'Cultural Ambassador', 4),
(237, 4, 'Performing Artist', 5),
(238, 5, 'Fine Artist', 1),
(239, 5, 'Illustrator', 2),
(240, 5, 'Art Teacher', 3),
(241, 5, 'Craft Designer', 4),
(242, 5, 'Creative Consultant', 5),
(243, 6, 'Recitation Artist', 1),
(244, 6, 'Radio Jockey (RJ)', 2),
(245, 6, 'Voiceover Artist', 3),
(246, 6, 'Diction Trainer', 4),
(247, 6, 'Performing Artist', 5),
(248, 7, 'Professional Emcee / Show Host', 1),
(249, 7, 'TV Presenter', 2),
(250, 7, 'News Anchor', 3),
(251, 7, 'Public Relations Specialist', 4),
(252, 7, 'Corporate Presenter', 5),
(258, 9, 'Fashion Photographer', 1),
(259, 9, 'Portrait Photographer', 2),
(260, 9, 'Studio Owner', 3),
(261, 9, 'Content Creator', 4),
(262, 9, 'Photojournalist', 5),
(263, 10, 'Makeup Artist (MUA)', 1),
(264, 10, 'Prosthetic Artist', 2),
(265, 10, 'Beauty Consultant', 3),
(266, 10, 'Bridal MUA Specialist', 4),
(267, 10, 'Editorial Stylist', 5),
(268, 11, 'Communications Manager', 1),
(269, 11, 'Human Resources Specialist', 2),
(270, 11, 'Public Relations Executive', 3),
(271, 11, 'Corporate Professional', 4),
(272, 11, 'Team Leader', 5),
(273, 12, 'Image Consultant', 1),
(274, 12, 'Grooming Instructor', 2),
(275, 12, 'Brand Representative', 3),
(276, 12, 'Executive Coach', 4),
(277, 12, 'Etiquette Consultant', 5),
(278, 13, 'Digital Marketing Specialist', 1),
(279, 13, 'Social Media Manager', 2),
(280, 13, 'Freelancer on Fiverr & Upwork', 3),
(281, 13, 'Content Creator & Strategist', 4),
(282, 13, 'Brand Growth Consultant', 5),
(289, 8, 'Pageant Contestant', 1),
(290, 8, 'Model', 2),
(291, 8, 'Brand Ambassador', 3),
(292, 8, 'Image Consultant', 4),
(293, 8, 'Spokesperson', 5);

-- --------------------------------------------------------

--
-- Table structure for table `course_galleries`
--

CREATE TABLE `course_galleries` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_galleries`
--

INSERT INTO `course_galleries` (`id`, `course_id`, `image_path`, `alt_text`, `display_order`) VALUES
(173, 1, 'assets/images/course-gallery/modeling1.png', 'Runway Walk Highlight', 1),
(174, 1, 'assets/images/course-gallery/modeling2.png', 'Modeling Class Practice', 2),
(175, 1, 'assets/images/course-gallery/modeling3.png', 'Fashion Photoshoot BTS', 3),
(176, 1, 'assets/images/course-gallery/modeling4.jpg', 'Fashion Show Finale', 4),
(177, 2, 'assets/images/course-gallery/acting1.png', 'Acting Performance Highlight', 1),
(178, 2, 'assets/images/course-gallery/acting2.png', 'Acting Workshop Session', 2),
(179, 2, 'assets/images/course-gallery/acting3.png', 'Screen Test Practice', 3),
(180, 2, 'assets/images/course-gallery/acting4.png', 'Theatre Performance Finale', 4),
(181, 3, 'assets/images/course-gallery/hiphop1.png', 'Hip Hop Dance Performance', 1),
(182, 3, 'assets/images/course-gallery/hiphop2.png', 'Dance Choreography Session', 2),
(183, 3, 'assets/images/course-gallery/hiphop3.png', 'Dance Show Finale', 3),
(184, 3, 'assets/images/course-gallery/hiphop4.png', 'Dance Stage Highlight', 4),
(185, 4, 'assets/images/course-gallery/classical1.jpg', 'Classical Dance Performance', 1),
(186, 4, 'assets/images/course-gallery/classical2.png', 'Cultural Dance Showcase', 2),
(187, 4, 'assets/images/course-gallery/classical3.png', 'Dance Training Session', 3),
(188, 4, 'assets/images/course-gallery/classical4.png', 'Dance Stage Highlight', 4),
(189, 5, 'assets/images/course-gallery/fineart1.jpg', 'Fine Arts Creative Work', 1),
(190, 5, 'assets/images/course-gallery/fineart2.png', 'Art Workshop Session', 2),
(191, 5, 'assets/images/course-gallery/fineart3.png', 'Creative Expression Class', 3),
(192, 5, 'assets/images/course-gallery/fineart4.png', 'Art Exhibition Display', 4),
(193, 6, 'assets/images/course-gallery/poetry1.png', 'Poetry Stage Performance', 1),
(194, 6, 'assets/images/course-gallery/poetry2.png', 'Recitation Practice Session', 2),
(195, 6, 'assets/images/course-gallery/poetry3.png', 'Poetry Show Finale', 3),
(196, 6, 'assets/images/course-gallery/poetry4.png', 'Literature Performance', 4),
(197, 7, 'assets/images/course-gallery/hosting1.png', 'Professional Hosting Event', 1),
(198, 7, 'assets/images/course-gallery/hosting2.png', 'Presentation Skills Training', 2),
(199, 7, 'assets/images/course-gallery/hosting3.png', 'Public Speaking Session', 3),
(200, 7, 'assets/images/course-gallery/hosting4.png', 'Event Hosting Highlight', 4),
(205, 9, 'assets/images/course-gallery/photo1.jpg', 'Photography Studio Session', 1),
(206, 9, 'assets/images/course-gallery/photo2.png', 'Professional Photoshoot BTS', 2),
(207, 9, 'assets/images/course-gallery/photo3.png', 'Camera Work Training', 3),
(208, 9, 'assets/images/course-gallery/photo4.png', 'Studio Lighting Setup', 4),
(209, 10, 'assets/images/course-gallery/makeup1.png', 'Professional Makeup Session', 1),
(210, 10, 'assets/images/course-gallery/makeup2.png', 'Makeup Photoshoot BTS', 2),
(211, 10, 'assets/images/course-gallery/makeup3.png', 'Beauty & Styling Class', 3),
(212, 10, 'assets/images/course-gallery/makeup4.png', 'Makeup Portfolio Shoot', 4),
(213, 11, 'assets/images/course-gallery/comm1.jpg', 'Communication Skills Session', 1),
(214, 11, 'assets/images/course-gallery/comm2.png', 'Public Speaking Practice', 2),
(215, 11, 'assets/images/course-gallery/comm3.png', 'Leadership Training Workshop', 3),
(216, 11, 'assets/images/course-gallery/comm4.png', 'Communication Class Highlight', 4),
(217, 12, 'assets/images/course-gallery/grooming1.jpg', 'Personal Grooming Session', 1),
(218, 12, 'assets/images/course-gallery/grooming2.png', 'Posture & Style Training', 2),
(219, 12, 'assets/images/course-gallery/grooming3.png', 'Personality Development Class', 3),
(220, 12, 'assets/images/course-gallery/grooming4.png', 'Grooming Workshop Highlight', 4),
(221, 13, 'assets/images/course-gallery/comm1.jpg', 'Digital Marketing Session', 1),
(222, 13, 'assets/images/course-gallery/comm2.png', 'Content Creation Workshop', 2),
(223, 13, 'assets/images/course-gallery/comm3.png', 'Freelancing Strategy Class', 3),
(224, 13, 'assets/images/course-gallery/comm4.png', 'Social Media Marketing', 4),
(225, 8, 'assets/images/course-gallery/pageant1.png', 'Beauty Pageant Stage Walk', 1),
(226, 8, 'assets/images/course-gallery/pageant2.png', 'Pageant Runway Highlight', 2),
(227, 8, 'assets/images/course-gallery/pageant3.png', 'Pageant Photoshoot BTS', 3),
(228, 8, 'assets/images/course-gallery/pageant4.png', 'Pageant Training Session', 4);

-- --------------------------------------------------------

--
-- Table structure for table `course_modules`
--

CREATE TABLE `course_modules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_modules`
--

INSERT INTO `course_modules` (`id`, `course_id`, `module_name`, `display_order`) VALUES
(227, 1, 'Runway Walking & Posing', 1),
(228, 1, 'Styling & Personal Branding', 2),
(229, 1, 'Commercial Modeling', 3),
(230, 1, 'Fashion Etiquette', 4),
(231, 1, 'Portfolio Development', 5),
(232, 2, 'Character Development & Method Acting', 1),
(233, 2, 'Voice and Speech Modulation', 2),
(234, 2, 'Script Interpretation & Diction', 3),
(235, 2, 'Improvisation & Stagecraft', 4),
(236, 2, 'Audition Prep & Camera Acting', 5),
(237, 3, 'Hip Hop Foundation Techniques', 1),
(238, 3, 'Choreography & Musicality', 2),
(239, 3, 'Freestyle & Improvisation', 3),
(240, 3, 'Stage Performance & Presence', 4),
(241, 3, 'Competition-Level Fitness & Routine', 5),
(242, 4, 'Classical Postures & Mudras', 1),
(243, 4, 'Cultural Storytelling & Expression', 2),
(244, 4, 'Rhythm (Tala) & Footwork', 3),
(245, 4, 'Stage Grace & Posture', 4),
(246, 4, 'Folk & Traditional Routines', 5),
(247, 5, 'Drawing & Sketching Techniques', 1),
(248, 5, 'Painting & Color Theory', 2),
(249, 5, 'Composition & Perspective', 3),
(250, 5, 'Creative Crafts & Design', 4),
(251, 5, 'Visual Storytelling & Portfolios', 5),
(252, 6, 'Voice Modulation & Breath Control', 1),
(253, 6, 'Diction, Pronunciation & Phonetics', 2),
(254, 6, 'Poetic Structure & Rhythm', 3),
(255, 6, 'Emotional Expression & Poise', 4),
(256, 6, 'Performing Literature on Stage', 5),
(257, 7, 'Public Speaking & Emceeing', 1),
(258, 7, 'Television & Media Presentation', 2),
(259, 7, 'Corporate Event Hosting', 3),
(260, 7, 'Voice Grooming & Body Language', 4),
(261, 7, 'Media Interaction & Q&A', 5),
(267, 9, 'Camera Controls & Exposure Triads', 1),
(268, 9, 'Studio Lighting Setup & Moods', 2),
(269, 9, 'Fashion & Portrait Shoot Work', 3),
(270, 9, 'Digital Editing & Retouching', 4),
(271, 9, 'Visual Storytelling & Personal Branding', 5),
(272, 10, 'Skincare & Makeup Fundamentals', 1),
(273, 10, 'Bridal & Glamour Makeup', 2),
(274, 10, 'Fashion & Editorial Shoot Makeup', 3),
(275, 10, 'Prosthetic Makeup & Special Effects (SFX)', 4),
(276, 10, 'Character Transformation & Hygiene', 5),
(277, 11, 'Verbal & Interpersonal Communication', 1),
(278, 11, 'Emotional Intelligence & Self-Awareness', 2),
(279, 11, 'Teamwork & Collaborative Leadership', 3),
(280, 11, 'Critical Thinking & Problem Solving', 4),
(281, 11, 'Workplace Ethics & Career Readiness', 5),
(282, 12, 'Social & Corporate Etiquette', 1),
(283, 12, 'Body Language & Posture', 2),
(284, 12, 'Personal Styling & Color Coding', 3),
(285, 12, 'Confidence & Self-Presentation', 4),
(286, 12, 'Digital Branding & Image Management', 5),
(287, 13, 'Module 1: Pre-requisites & Foundations of Marketing', 1),
(288, 13, 'Module 2: Fundamentals of Digital Marketing & Customer Journeys', 2),
(289, 13, 'Module 3: Content Marketing Principles & Copywriting', 3),
(290, 13, 'Module 4: Graphic Design & Image Marketing with Canva + AI', 4),
(291, 13, 'Module 5: Professional Video Production & Reels in Canva', 5),
(292, 13, 'Module 6: Meta Ads & Social Media Marketing Strategy', 6),
(293, 13, 'Module 7: YouTube Growth & Video Optimization', 7),
(294, 13, 'Module 8: Freelancing on Fiverr & Upwork + Client Management', 8),
(305, 8, 'Pageant Walk & Runway Poise', 1),
(306, 8, 'Interview Preparation & Speech', 2),
(307, 8, 'Etiquette, Grooming & Social Grace', 3),
(308, 8, 'Fitness & Nutrition Planning', 4),
(309, 8, 'Personal Styling & Media Communication', 5);

-- --------------------------------------------------------

--
-- Table structure for table `course_schedules`
--

CREATE TABLE `course_schedules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `day_name` varchar(100) NOT NULL,
  `time_text` varchar(255) NOT NULL,
  `topic_text` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_schedules`
--

INSERT INTO `course_schedules` (`id`, `course_id`, `day_name`, `time_text`, `topic_text`, `display_order`) VALUES
(87, 1, 'Friday', '3:00 PM - 4:00 PM', 'Cat Walk', 1),
(88, 1, 'Friday', '4:00 PM - 5:00 PM', 'Acting', 2),
(89, 1, 'Friday', '5:00 PM - 6:00 PM', 'Dance', 3),
(90, 2, 'Saturday', '4:00 PM - 5:30 PM', 'Acting Basics', 1),
(91, 2, 'Tuesday', '4:00 PM - 5:30 PM', 'Screen Practice', 2),
(92, 3, 'Friday', '5:00 PM - 6:30 PM', 'Hip Hop Class', 1),
(93, 3, 'Tuesday', '4:00 PM - 5:30 PM', 'Freestyle Class', 2),
(94, 4, 'Friday', '4:00 PM - 5:30 PM', 'Classical Class', 1),
(95, 4, 'Saturday', '5:00 PM - 6:30 PM', 'Cultural Class', 2),
(96, 5, 'Saturday', '3:00 PM - 4:30 PM', 'Fine Arts Class', 1),
(97, 5, 'Tuesday', '5:00 PM - 6:30 PM', 'Creative Expression', 2),
(98, 6, 'Friday', '3:00 PM - 4:30 PM', 'Diction Class', 1),
(99, 6, 'Saturday', '4:00 PM - 5:30 PM', 'Performance Class', 2),
(100, 7, 'Saturday', '5:00 PM - 6:30 PM', 'Emcee Class', 1),
(101, 7, 'Tuesday', '5:00 PM - 6:30 PM', 'Media Presentation', 2),
(104, 9, 'Saturday', '3:00 PM - 5:00 PM', 'Studio Photography', 1),
(105, 9, 'Tuesday', '4:00 PM - 6:00 PM', 'Digital Retouching', 2),
(106, 10, 'Friday', '4:00 PM - 6:00 PM', 'Bridal & Glamour', 1),
(107, 10, 'Saturday', '5:00 PM - 7:00 PM', 'Prosthetics & SFX', 2),
(108, 11, 'Saturday', '3:00 PM - 5:00 PM', 'Communication Skills', 1),
(109, 11, 'Tuesday', '4:00 PM - 6:00 PM', 'Leadership Workshop', 2),
(110, 12, 'Friday', '5:00 PM - 7:00 PM', 'Etiquette Class', 1),
(111, 12, 'Saturday', '3:00 PM - 5:00 PM', 'Image Workshop', 2),
(112, 13, 'Weekly Live', 'Interactive live sessions + Q&A support', 'Live Classes', 1),
(113, 13, 'Recorded', 'Lifetime access to recorded lessons', 'Self-Paced', 2),
(114, 13, 'Support', '24/7 WhatsApp & Facebook groups', 'Mentorship', 3),
(121, 8, 'Friday', '3:00 PM - 6:00 PM', 'Poise & Stage', 1),
(122, 8, 'Saturday', '4:00 PM - 6:00 PM', 'Speech & Styling', 2);

-- --------------------------------------------------------

--
-- Table structure for table `magazines`
--

CREATE TABLE `magazines` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_text` varchar(100) NOT NULL,
  `pdf_path` varchar(255) NOT NULL,
  `cover_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `magazines`
--

INSERT INTO `magazines` (`id`, `title`, `description`, `date_text`, `pdf_path`, `cover_path`, `display_order`, `created_at`, `updated_at`) VALUES
(4, 'Model Edition', 'Modern lifestyle, fashion inspiration and exclusive features.', '5 august 2026', 'issue_0f90819a9be7af1bde4a.pdf', 'cover_81327ee430501e1128ab.jpg', 2, '2026-08-06 07:14:46', '2026-08-06 07:14:46'),
(5, 'Fashion Edition', 'Fashion trends, beauty insights, exclusive interviews and event coverage.', 'January 2026', 'issue_464089fc8abe3602f271.pdf', 'cover_338ebec2a60a5d47a5ef.jpg', 1, '2026-08-06 07:15:51', '2026-08-06 07:15:51'),
(8, 'Star Fair', 'Fashion Frequency', 'September', 'issue_90ef7ee25a9ec61eb266.pdf', NULL, 1, '2026-08-26 08:45:58', '2026-08-26 08:45:58');

-- --------------------------------------------------------

--
-- Table structure for table `mentors`
--

CREATE TABLE `mentors` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `bio` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentors`
--

INSERT INTO `mentors` (`id`, `type`, `name`, `designation`, `bio`, `image_path`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'advisor', 'Alamgir Hossain Alo', 'Founder, Executive Director', 'Alamgir Hossain Alo is the Founder and Executive Director of STAR FAIR Fashion & Cultural Training Institute. With years of experience in fashion choreography, runway production, talent development, and cultural event management, he has established STAR FAIR as one of Bangladesh\'s emerging platforms for fashion education.', 'assets/images/mentors/founder.jpg', 0, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(2, 'advisor', 'Shariful Islam', 'Director', 'Shariful Islam oversees organizational planning, institutional development, and operational management. His leadership ensures that STAR FAIR maintains professional standards, strengthens partnerships, and continues its sustainable growth in fashion and cultural education.', NULL, 1, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(3, 'advisor', 'Moshyudulla Omi', 'Director', 'Moshyudulla Omi contributes to program development, student engagement, and event management. He works closely with the executive team to ensure quality education, creative innovation, and professional excellence across all STAR FAIR initiatives.', NULL, 2, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(4, 'advisor', 'Md Ahmed Mia', 'Creative Director', 'Md Ahmed Mia leads the institute\'s creative vision by supervising branding, visual storytelling, campaign concepts, fashion presentations, and digital media. His innovative approach enhances STAR FAIR\'s creative identity and public image.', NULL, 3, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(5, 'advisor', 'Hasanuz Zaman', 'Senior Advisor', 'Provides strategic guidance on institutional development, leadership, and long-term planning while supporting STAR FAIR\'s mission to nurture future talents.', NULL, 4, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(6, 'advisor', 'Sajjad Bin Khaled Sumon', 'Advisor', 'Supports strategic planning, program development, and collaborative initiatives that enhance educational quality and industry connections.', NULL, 5, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(7, 'advisor', 'Taslim Hasan Hridoy', 'Advisor / Green Leaf Publisher', 'Taslim Hasan Hridoy is the Editor & Publisher of Green Leaf Magazine. With extensive experience in media, publishing, and creative communication, he provides valuable guidance in editorial development, branding, cultural promotion, and strategic media engagement.', NULL, 6, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(8, 'advisor', 'Sarmi Sen Gupta', 'Advisor – Kids STAR FAIR', 'Provides guidance in child personality development, creativity, communication, and cultural education, helping children build confidence from an early age.', NULL, 7, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(9, 'advisor', 'Shakhawat Hossain', 'Advisor – Kids STAR FAIR', 'Supports the planning and implementation of children\'s educational and cultural programs while promoting leadership, discipline, and creativity.', NULL, 8, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(10, 'trainer', 'Shagor Dev Nath', 'Acting Trainer', 'Shagor Dev Nath is an acting professional dedicated to developing students\' performance skills through character building, stage acting, camera acting, dialogue delivery, emotional expression, and performance confidence.', NULL, 0, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(11, 'trainer', 'Marjuk', 'Dance & Performance Trainer', 'Marjuk is a professional dance instructor specializing in stage performance, choreography, rhythm, movement, and performance techniques. He helps students develop creativity, confidence, and stage presence through dance.', NULL, 1, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(12, 'trainer', 'Adiyan Das', 'Art & Creative Design Trainer', 'Adiyan Das mentors students in creative arts, design concepts, visual composition, artistic expression, and fashion-related creative projects, encouraging innovation and imagination.', NULL, 2, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(13, 'trainer', 'Sabrina', 'Modeling & Grooming Trainer', 'Sabrina provides professional training in runway walking, posing, etiquette, body language, fashion presentation, and personality grooming, preparing models for professional events.', NULL, 3, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(14, 'trainer', 'Md. Masum', 'Professional Modeling Trainer', 'Md. Masum specializes in runway techniques, editorial posing, portfolio preparation, fashion presentation, and model professionalism to equip students for national and international runways.', NULL, 4, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(15, 'trainer', 'Jahidul Islam', 'Dance Trainer', 'Jahidul Islam focuses on movement, rhythm, choreography, stage performance, and performance confidence, combining technical excellence with creativity.', NULL, 5, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(16, 'trainer', 'Ferdous Ahsan Orko', 'Board Member & Head of Makeup Education', 'A professional makeup artist and educator with expertise in fashion, editorial, bridal, and runway makeup, leading dynamic makeup education programs.', NULL, 6, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(17, 'trainer', 'Fatema Binte Yasmeen', 'Board Member & Makeup Education Director', 'An accomplished makeup artist and beauty educator specializing in bridal and editorial makeup, driving curriculum design and practical training programs.', NULL, 7, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(18, 'mentor', 'Alamgir Hossain Alo', 'Lead Fashion Choreographer & Mentor', 'Enlisted Shilpokola Academy member. Over 15 years mentoring models, organizing premium pageants, runway productions, and directing national cultural events.', 'assets/images/mentors/founder.jpg', 0, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(19, 'mentor', 'Reyad Uddin Bhuiyan Cheater', 'Senior Digital Marketing Instructor & Mentor', 'Founder & CEO of Techy Octopus and Senior Digital Marketing Instructor at BRAC ISD. An alumnus of the University of Chittagong (BBA & MBA in Marketing) with over 8 years of industry experience, training 1,000+ students.', 'assets/images/mentors/reyad.png', 1, '2026-08-26 08:21:56', '2026-09-04 15:52:18'),
(20, 'mentor', 'Rafa Nanjiba Torsa', 'Beauty Pageant Mentor', 'Miss World Bangladesh 2019, Guinness World Record Holder, artist, and socio-political activist. She mentors pageant preparation, confidence, runway poise, and public speaking.', 'assets/images/mentors/torsa.webp', 2, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(21, 'mentor', 'Arafat Islam Rupak', 'Speech & Communication Mentor', 'Enlisted Shilpokola Academy member. Specializes in public speaking, communication, voice modulation, interview preparation, and leadership development.', NULL, 3, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(22, 'mentor', 'Saleh Robi Jhon', 'Fashion Styling & Image Consulting Mentor', 'Experienced fashion stylist and creative mentor specializing in wardrobe styling, personal branding, fashion trends, and image consulting for runways.', NULL, 4, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(23, 'mentor', 'Ramisa Maliat', 'Marks All Rounder 2010 & Cultural Mentor', 'Dedicated to empowering young talents through creative education, confidence building, and cultural development, inspiring leadership and discipline.', NULL, 5, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(24, 'mentor', 'Mehedi Hasan Fahim', 'Men\'s Grooming & Pageant Mentor', 'Mr. World Bangladesh 2019. Mentors grooming, fitness, runway presence, and styling required to compete at premium pageants.', NULL, 6, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(25, 'mentor', 'Ambika Talukder', 'Presentation & Grooming Mentor', 'Mentors presentation skills, etiquette, personality grooming, and stage confidence, blending Bangladeshi culture with international agency requirements.', NULL, 7, '2026-08-26 08:21:56', '2026-08-26 08:21:56'),
(26, 'mentor', 'Sajidur Rahman', 'Fashion Photography & Portfolio Mentor', 'Professional photographer who mentors portfolio shoots, lighting, camera confidence, and creative visual storytelling for fashion aspirants.', NULL, 8, '2026-08-26 08:21:56', '2026-08-26 08:21:56');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'General',
  `summary` text NOT NULL,
  `content` longtext NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_featured` tinyint(4) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `published_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `category`, `summary`, `content`, `image_path`, `is_featured`, `display_order`, `published_date`, `created_at`, `updated_at`) VALUES
(1, 'স্টার ফেয়ার ফ্যাশন অ্যান্ড কালচারাল ইনস্টিটিউটের নতুন গ্রুমিং সেশন শুরু', 'Academy', 'বাংলাদেশর শীর্ষস্থানীয় ফ্যাশন ও মডেলিং ইনস্টিটিউট স্টার ফেয়ার-এর নতুন গ্রুমিং ও মডেলিং ব্যাচে ভর্তি চলছে।', 'স্টার ফেয়ার ফ্যাশন অ্যান্ড কালচারাল ইনস্টিটিউট বাংলাদেশর নতুন গ্রুমিং ও মডেলিং ব্যাচে ভর্তি কার্যক্রম শুরু হয়েছে। প্রতিষ্ঠাতা ও নির্বাহী পরিচালক আলমগীর হোসেন আলো জানান, নতুন ব্যাচে মডেলিং, র‍্যাম্প ওয়াক, বডি ল্যাঙ্গুয়েজ, ফটোশুটের কৌশল এবং পারসোনালিটি ডেভেলপমেন্টের ওপর আন্তর্জাতিক মানের প্রশিক্ষণ দেওয়া হবে।\n\nপ্রশিক্ষণ শেষে সফল শিক্ষার্থীদের বিভিন্ন বিজ্ঞাপন, জাতীয় ও আন্তর্জাতিক ফ্যাশন শো ও প্রিন্ট মিডিয়ার সাথে সরাসরি যুক্ত করা হবে। আগ্রহী শিক্ষার্থীরা অফিসিয়াল রেজিস্ট্রেশন পেজ থেকে সরাসরি আবেদন করতে পারবেন।', 'assets/images/programs/grooming.jpg', 1, 1, '2026-08-26', '2026-08-26 16:13:38', '2026-08-26 16:13:38'),
(2, 'Star Fair BD Model Audition for Dhaka Fashion Runway 2026', 'Audition', 'Get ready for the biggest national model hunt! Star Fair BD is hosting the official auditions for the upcoming Dhaka Fashion Runway.', 'Star Fair BD is organizing the official model search auditions for the prestigious Dhaka Fashion Runway 2026.\n\nAspiring male and female models aged between 16 to 28 are invited to showcase their walk and confidence. Selected models will receive intensive runway training and represent top designer brands in the country. Registration is open until the end of this month.', 'assets/images/programs/fasion.jpg', 0, 2, '2026-08-25', '2026-08-26 16:13:38', '2026-08-26 16:13:38'),
(3, 'Grand Finale of Star Fair Pageant Awards and Cultural Evening', 'Awards', 'A spectacular night celebrating Bangladesh\'s young cultural talents and fashion achievers at Shilpakala Academy.', 'The grand finale of the Star Fair Pageant Awards was held at the National Theater Hall of Shilpakala Academy.\n\nThe cultural evening featured traditional dances, fashion shows by Star Fair models, and award presentations to the country\'s upcoming talents. Eminent fashion designers and industry icons graced the occasion as honorable guests, praising Star Fair\'s contribution to cultural development.', 'assets/images/fashion-show/fashion-hero.jpg', 0, 3, '2026-08-24', '2026-08-26 16:13:38', '2026-08-26 16:13:38'),
(4, 'Launch of Star Fair acting & media presentation workshop', 'Workshop', 'An intensive television acting, modeling portfolio guidance, and media presentation course starting next month.', 'Enrollment is now officially open for Star Fair\'s premium acting and media presentation workshop.\n\nLed by professional television directors and actors, this course covers screen acting, camera confidence, voice modulation, and commercial photo-modeling guidelines. Interested students can register online.', 'assets/images/programs/acting.jpg', 0, 4, '2026-08-23', '2026-08-26 16:13:38', '2026-08-26 16:13:38');

-- --------------------------------------------------------

--
-- Table structure for table `page_images`
--

CREATE TABLE `page_images` (
  `id` int(11) NOT NULL,
  `section` varchar(50) NOT NULL,
  `page` varchar(100) NOT NULL,
  `image_key` varchar(100) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `name`, `image_path`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Green Leaf', 'assets/images/partners/green-leaf.png', 1, '2026-08-26 15:26:51', '2026-08-26 15:26:51'),
(2, 'Shilpakala Academy', 'assets/images/partners/shilpakala.png', 2, '2026-08-26 15:26:51', '2026-08-26 15:26:51'),
(3, 'Lubna House', 'assets/images/partners/lubna-house.png', 3, '2026-08-26 15:26:51', '2026-08-26 15:26:51'),
(4, 'Habib Tazkiraz', 'assets/images/partners/habib-tazkiraz.png', 4, '2026-08-26 15:26:51', '2026-08-26 15:26:51'),
(5, 'Adlib', 'assets/images/partners/adlib.svg', 5, '2026-08-26 15:26:51', '2026-08-26 15:26:51'),
(6, 'Anzara', 'assets/images/partners/anzara.svg', 6, '2026-08-26 15:26:51', '2026-08-26 15:26:51'),
(7, 'Monir\'s Beauty Lounge', 'assets/images/partners/monirs-beauty-lounge.svg', 7, '2026-08-26 15:26:51', '2026-08-26 15:26:51');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `blood_group` varchar(10) NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `occupation` varchar(255) NOT NULL,
  `education` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `alt_mobile` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `present_address` text NOT NULL,
  `permanent_address` text NOT NULL,
  `guardian_name` varchar(255) NOT NULL,
  `guardian_mobile` varchar(20) NOT NULL,
  `emergency_name` varchar(255) NOT NULL,
  `emergency_relation` varchar(100) NOT NULL,
  `programmes` text NOT NULL,
  `events` text NOT NULL,
  `previous_experience` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `special_skills` text DEFAULT NULL,
  `why_join` text NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `nid_bc_path` varchar(255) NOT NULL,
  `portfolio_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `name`, `dob`, `father_name`, `mother_name`, `gender`, `blood_group`, `nationality`, `occupation`, `education`, `mobile`, `alt_mobile`, `email`, `present_address`, `permanent_address`, `guardian_name`, `guardian_mobile`, `emergency_name`, `emergency_relation`, `programmes`, `events`, `previous_experience`, `medical_conditions`, `special_skills`, `why_join`, `photo_path`, `nid_bc_path`, `portfolio_path`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Monifa Sultana', '1998-12-10', 'Nurul Alam', 'Mariam Sultana', 'female', 'O+', 'Bangladeshi', 'educator', 'B.sc in CSE', '01791662433', '01791662418', 'monifasultana5637@gmail.com', 'Bakolia, Chittagong', 'chittagong', 'Mariam Sultana', '01791662418', 'Mariam Sultana', 'Mother', '[\"Communication\"]', '[]', 'nnn', 'nn', 'nn', 'nnn', 'e402c068ffc717a3c32c6a6f/photo_a77ef45a74bb50e7.png', 'e402c068ffc717a3c32c6a6f/nid_bc_3bc1e05b3f8bacd5.pdf', 'e402c068ffc717a3c32c6a6f/portfolio_d86cd64020e102c2.pdf', 'pending', '2026-08-04 11:44:54', '2026-08-04 11:44:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `course_careers`
--
ALTER TABLE `course_careers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_galleries`
--
ALTER TABLE `course_galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_modules`
--
ALTER TABLE `course_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_schedules`
--
ALTER TABLE `course_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `magazines`
--
ALTER TABLE `magazines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mentors`
--
ALTER TABLE `mentors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_images`
--
ALTER TABLE `page_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_image_key` (`section`,`page`,`image_key`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `course_careers`
--
ALTER TABLE `course_careers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;

--
-- AUTO_INCREMENT for table `course_galleries`
--
ALTER TABLE `course_galleries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `course_modules`
--
ALTER TABLE `course_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=315;

--
-- AUTO_INCREMENT for table `course_schedules`
--
ALTER TABLE `course_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `magazines`
--
ALTER TABLE `magazines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mentors`
--
ALTER TABLE `mentors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `page_images`
--
ALTER TABLE `page_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course_careers`
--
ALTER TABLE `course_careers`
  ADD CONSTRAINT `course_careers_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_galleries`
--
ALTER TABLE `course_galleries`
  ADD CONSTRAINT `course_galleries_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_modules`
--
ALTER TABLE `course_modules`
  ADD CONSTRAINT `course_modules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_schedules`
--
ALTER TABLE `course_schedules`
  ADD CONSTRAINT `course_schedules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
