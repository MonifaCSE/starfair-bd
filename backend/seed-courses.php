<?php
/**
 * STAR FAIR - Idempotent Course Seeder Script
 * Migrates all 13 existing course details from static HTML into MySQL database.
 */

require_once __DIR__ . '/database.php';

$coursesData = [
    [
        'title' => 'Fashion Modeling',
        'slug' => 'modeling',
        'category' => 'core_programme',
        'category_badge' => 'CORE PROGRAMME',
        'short_description' => 'Develop the essential skills required for a successful modelling career.',
        'full_description' => 'Develop the essential skills required for a successful modelling career through professional runway training, fashion presentation, posing, styling, portfolio development, camera performance, commercial modelling, fashion etiquette, confidence building, and personal branding.',
        'hero_image' => 'assets/images/programs/fasion.jpg',
        'duration' => '6 Months',
        'admission_fee' => 'BDT 20,000 (Admission Included)',
        'course_fee' => 'BDT 20,000',
        'eligibility' => 'Passionate individuals seeking modeling careers.',
        'age_requirement' => 'All age groups',
        'course_type' => 'Core',
        'status' => 'published',
        'display_order' => 1,
        'meta_title' => 'STAR FAIR | Fashion Modeling Training Program Bangladesh',
        'meta_description' => 'Professional runway modeling, grooming, and portfolio development course in Bangladesh since 2009.',
        'modules' => [
            'Runway Walking & Posing',
            'Styling & Personal Branding',
            'Commercial Modeling',
            'Fashion Etiquette',
            'Portfolio Development'
        ],
        'schedules' => [
            ['day_name' => 'Friday', 'time_text' => '3:00 PM - 4:00 PM', 'topic_text' => 'Cat Walk'],
            ['day_name' => 'Friday', 'time_text' => '4:00 PM - 5:00 PM', 'topic_text' => 'Acting'],
            ['day_name' => 'Friday', 'time_text' => '5:00 PM - 6:00 PM', 'topic_text' => 'Dance']
        ],
        'careers' => [
            'Professional Runway Model',
            'Brand Ambassador',
            'Commercial Model',
            'Fashion Influencer',
            'Pageant Contestant'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/modeling1.png', 'alt_text' => 'Runway Walk Highlight'],
            ['image_path' => 'assets/images/course-gallery/modeling2.png', 'alt_text' => 'Modeling Class Practice'],
            ['image_path' => 'assets/images/course-gallery/modeling3.png', 'alt_text' => 'Fashion Photoshoot BTS'],
            ['image_path' => 'assets/images/course-gallery/modeling4.jpg', 'alt_text' => 'Fashion Show Finale']
        ]
    ],
    [
        'title' => 'Acting & Performance',
        'slug' => 'acting',
        'category' => 'core_programme',
        'category_badge' => 'CORE PROGRAMME',
        'short_description' => 'Build a strong foundation in acting and performance for stage and screen.',
        'full_description' => 'Build a strong foundation in acting through character development, voice and speech, script interpretation, facial expression, improvisation, stagecraft, audition preparation, camera acting, and professional performance techniques.',
        'hero_image' => 'assets/images/programs/acting.jpg',
        'duration' => '2–4 Years',
        'admission_fee' => 'Admission BDT 3,000 + Monthly BDT 500',
        'course_fee' => 'Monthly BDT 500',
        'eligibility' => 'Open to all age groups and experience levels.',
        'age_requirement' => 'Open to all age groups',
        'course_type' => 'Core',
        'status' => 'published',
        'display_order' => 2,
        'meta_title' => 'STAR FAIR | Acting & Screen Performance Course Bangladesh',
        'meta_description' => 'Comprehensive acting, drama, voice modulation and screen performance course.',
        'modules' => [
            'Character Development & Method Acting',
            'Voice and Speech Modulation',
            'Script Interpretation & Diction',
            'Improvisation & Stagecraft',
            'Audition Prep & Camera Acting'
        ],
        'schedules' => [
            ['day_name' => 'Saturday', 'time_text' => '4:00 PM - 5:30 PM', 'topic_text' => 'Acting Basics'],
            ['day_name' => 'Tuesday', 'time_text' => '4:00 PM - 5:30 PM', 'topic_text' => 'Screen Practice']
        ],
        'careers' => [
            'Theatre Actor',
            'Film & TV Actor',
            'Voiceover Artist',
            'Director / Assistant Director',
            'Screenwriter'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/acting1.png', 'alt_text' => 'Acting Performance Highlight'],
            ['image_path' => 'assets/images/course-gallery/acting2.png', 'alt_text' => 'Acting Workshop Session'],
            ['image_path' => 'assets/images/course-gallery/acting3.png', 'alt_text' => 'Screen Test Practice'],
            ['image_path' => 'assets/images/course-gallery/acting4.png', 'alt_text' => 'Theatre Performance Finale']
        ]
    ],
    [
        'title' => 'Hip Hop Dance',
        'slug' => 'hiphop-dance',
        'category' => 'core_programme',
        'category_badge' => 'CORE PROGRAMME',
        'short_description' => 'Master contemporary Hip Hop dance, choreography and freestyle.',
        'full_description' => 'Master contemporary Hip Hop dance through foundation techniques, choreography, freestyle, musicality, stage performance, teamwork, fitness, and competition-level training.',
        'hero_image' => 'assets/images/programs/hiphop_dance.png',
        'duration' => 'Ongoing Programme',
        'admission_fee' => 'Admission BDT 3,000 + Monthly BDT 1,000',
        'course_fee' => 'Monthly BDT 1,000',
        'eligibility' => 'Dance enthusiasts of all skill levels.',
        'age_requirement' => 'Open to all age groups',
        'course_type' => 'Core',
        'status' => 'published',
        'display_order' => 3,
        'meta_title' => 'STAR FAIR | Hip Hop Dance Academy Bangladesh',
        'meta_description' => 'Learn contemporary hip hop, choreography, freestyle, and stage dance skills.',
        'modules' => [
            'Hip Hop Foundation Techniques',
            'Choreography & Musicality',
            'Freestyle & Improvisation',
            'Stage Performance & Presence',
            'Competition-Level Fitness & Routine'
        ],
        'schedules' => [
            ['day_name' => 'Friday', 'time_text' => '5:00 PM - 6:30 PM', 'topic_text' => 'Hip Hop Class'],
            ['day_name' => 'Tuesday', 'time_text' => '4:00 PM - 5:30 PM', 'topic_text' => 'Freestyle Class']
        ],
        'careers' => [
            'Professional Dancer',
            'Choreographer',
            'Dance Instructor',
            'Backup Dancer',
            'Cultural Performer'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/hiphop1.png', 'alt_text' => 'Hip Hop Dance Performance'],
            ['image_path' => 'assets/images/course-gallery/hiphop2.png', 'alt_text' => 'Dance Choreography Session'],
            ['image_path' => 'assets/images/course-gallery/hiphop3.png', 'alt_text' => 'Dance Show Finale'],
            ['image_path' => 'assets/images/course-gallery/hiphop4.png', 'alt_text' => 'Dance Stage Highlight']
        ]
    ],
    [
        'title' => 'Classical & Cultural Dance',
        'slug' => 'classical-dance',
        'category' => 'core_programme',
        'category_badge' => 'CORE PROGRAMME',
        'short_description' => 'Develop artistic excellence through traditional and cultural dance styles.',
        'full_description' => 'Develop artistic excellence through classical and cultural dance, focusing on rhythm, expression, grace, posture, storytelling, discipline, and stage performance.',
        'hero_image' => 'assets/images/programs/culture.jpg',
        'duration' => 'Ongoing Programme',
        'admission_fee' => 'Admission BDT 3,000 + Monthly BDT 600',
        'course_fee' => 'Monthly BDT 600',
        'eligibility' => 'Open to all age groups and experience levels.',
        'age_requirement' => 'Open to all age groups',
        'course_type' => 'Core',
        'status' => 'published',
        'display_order' => 4,
        'meta_title' => 'STAR FAIR | Classical & Cultural Dance Course Bangladesh',
        'meta_description' => 'Traditional classical dance training, mudras, rhythm, and stage grace.',
        'modules' => [
            'Classical Postures & Mudras',
            'Cultural Storytelling & Expression',
            'Rhythm (Tala) & Footwork',
            'Stage Grace & Posture',
            'Folk & Traditional Routines'
        ],
        'schedules' => [
            ['day_name' => 'Friday', 'time_text' => '4:00 PM - 5:30 PM', 'topic_text' => 'Classical Class'],
            ['day_name' => 'Saturday', 'time_text' => '5:00 PM - 6:30 PM', 'topic_text' => 'Cultural Class']
        ],
        'careers' => [
            'Classical Dancer',
            'Choreographer',
            'Dance Academic',
            'Cultural Ambassador',
            'Performing Artist'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/classical1.jpg', 'alt_text' => 'Classical Dance Performance'],
            ['image_path' => 'assets/images/course-gallery/classical2.png', 'alt_text' => 'Cultural Dance Showcase'],
            ['image_path' => 'assets/images/course-gallery/classical3.png', 'alt_text' => 'Dance Training Session'],
            ['image_path' => 'assets/images/course-gallery/classical4.png', 'alt_text' => 'Dance Stage Highlight']
        ]
    ],
    [
        'title' => 'Fine Arts & Creative Expression',
        'slug' => 'fine-arts',
        'category' => 'core_programme',
        'category_badge' => 'CORE & DEVELOPMENT PROGRAMME',
        'short_description' => 'Explore drawing, painting, color theory, composition, and visual storytelling.',
        'full_description' => 'Develop artistic skills through a comprehensive programme covering drawing, sketching, painting, colour theory, composition, perspective, creative design, crafts, and visual storytelling. Students explore a variety of artistic techniques while enhancing creativity, observation, imagination, and self-expression.',
        'hero_image' => 'assets/images/programs/art.jpg',
        'duration' => '6 Months / Ongoing',
        'admission_fee' => 'Course Fee BDT 15,000 / Monthly BDT 1,000',
        'course_fee' => 'BDT 15,000',
        'eligibility' => 'Open to children and adults interested in visual arts.',
        'age_requirement' => 'All ages',
        'course_type' => 'Core',
        'status' => 'published',
        'display_order' => 5,
        'meta_title' => 'STAR FAIR | Fine Arts & Creative Expression Course',
        'meta_description' => 'Drawing, painting, sketch, color theory and visual storytelling training.',
        'modules' => [
            'Drawing & Sketching Techniques',
            'Painting & Color Theory',
            'Composition & Perspective',
            'Creative Crafts & Design',
            'Visual Storytelling & Portfolios'
        ],
        'schedules' => [
            ['day_name' => 'Saturday', 'time_text' => '3:00 PM - 4:30 PM', 'topic_text' => 'Fine Arts Class'],
            ['day_name' => 'Tuesday', 'time_text' => '5:00 PM - 6:30 PM', 'topic_text' => 'Creative Expression']
        ],
        'careers' => [
            'Fine Artist',
            'Illustrator',
            'Art Teacher',
            'Craft Designer',
            'Creative Consultant'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/fineart1.jpg', 'alt_text' => 'Fine Arts Creative Work'],
            ['image_path' => 'assets/images/course-gallery/fineart2.png', 'alt_text' => 'Art Workshop Session'],
            ['image_path' => 'assets/images/course-gallery/fineart3.png', 'alt_text' => 'Creative Expression Class'],
            ['image_path' => 'assets/images/course-gallery/fineart4.png', 'alt_text' => 'Art Exhibition Display']
        ]
    ],
    [
        'title' => 'Poetry Recitation & Performing Literature',
        'slug' => 'poetry',
        'category' => 'core_programme',
        'category_badge' => 'CORE PROGRAMME',
        'short_description' => 'Develop excellence in poetry recitation, voice modulation and diction.',
        'full_description' => 'Develop excellence in poetry recitation through voice modulation, pronunciation, diction, emotional expression, literary interpretation, and stage confidence.',
        'hero_image' => 'assets/images/programs/poetry_recitation.png',
        'duration' => 'Ongoing Programme',
        'admission_fee' => 'Admission BDT 3,000 + Monthly BDT 500',
        'course_fee' => 'Monthly BDT 500',
        'eligibility' => 'Literature and recitation enthusiasts.',
        'age_requirement' => 'All ages',
        'course_type' => 'Core',
        'status' => 'published',
        'display_order' => 6,
        'meta_title' => 'STAR FAIR | Poetry Recitation & Literature Course',
        'meta_description' => 'Master voice modulation, diction, pronunciation, and poetic stage performance.',
        'modules' => [
            'Voice Modulation & Breath Control',
            'Diction, Pronunciation & Phonetics',
            'Poetic Structure & Rhythm',
            'Emotional Expression & Poise',
            'Performing Literature on Stage'
        ],
        'schedules' => [
            ['day_name' => 'Friday', 'time_text' => '3:00 PM - 4:30 PM', 'topic_text' => 'Diction Class'],
            ['day_name' => 'Saturday', 'time_text' => '4:00 PM - 5:30 PM', 'topic_text' => 'Performance Class']
        ],
        'careers' => [
            'Recitation Artist',
            'Radio Jockey (RJ)',
            'Voiceover Artist',
            'Diction Trainer',
            'Performing Artist'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/poetry1.png', 'alt_text' => 'Poetry Stage Performance'],
            ['image_path' => 'assets/images/course-gallery/poetry2.png', 'alt_text' => 'Recitation Practice Session'],
            ['image_path' => 'assets/images/course-gallery/poetry3.png', 'alt_text' => 'Poetry Show Finale'],
            ['image_path' => 'assets/images/course-gallery/poetry4.png', 'alt_text' => 'Literature Performance']
        ]
    ],
    [
        'title' => 'Presentation & Professional Hosting',
        'slug' => 'hosting',
        'category' => 'core_programme',
        'category_badge' => 'CORE PROGRAMME',
        'short_description' => 'Prepare for professional hosting, public speaking and media communication.',
        'full_description' => 'Prepare for professional hosting, public speaking, television presentation, corporate events, cultural programmes, and media communication through practical, performance-based training.',
        'hero_image' => 'assets/images/programs/hosting.png',
        'duration' => 'Ongoing Programme',
        'admission_fee' => 'Admission BDT 3,000 + Monthly BDT 1,000',
        'course_fee' => 'Monthly BDT 1,000',
        'eligibility' => 'Aspirants looking to enter media, emceeing, or corporate communications.',
        'age_requirement' => 'Open',
        'course_type' => 'Core',
        'status' => 'published',
        'display_order' => 7,
        'meta_title' => 'STAR FAIR | Professional Hosting & Public Speaking Course',
        'meta_description' => 'Emceeing, news anchoring, event hosting, and public speaking training.',
        'modules' => [
            'Public Speaking & Emceeing',
            'Television & Media Presentation',
            'Corporate Event Hosting',
            'Voice Grooming & Body Language',
            'Media Interaction & Q&A'
        ],
        'schedules' => [
            ['day_name' => 'Saturday', 'time_text' => '5:00 PM - 6:30 PM', 'topic_text' => 'Emcee Class'],
            ['day_name' => 'Tuesday', 'time_text' => '5:00 PM - 6:30 PM', 'topic_text' => 'Media Presentation']
        ],
        'careers' => [
            'Professional Emcee / Show Host',
            'TV Presenter',
            'News Anchor',
            'Public Relations Specialist',
            'Corporate Presenter'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/hosting1.png', 'alt_text' => 'Professional Hosting Event'],
            ['image_path' => 'assets/images/course-gallery/hosting2.png', 'alt_text' => 'Presentation Skills Training'],
            ['image_path' => 'assets/images/course-gallery/hosting3.png', 'alt_text' => 'Public Speaking Session'],
            ['image_path' => 'assets/images/course-gallery/hosting4.png', 'alt_text' => 'Event Hosting Highlight']
        ]
    ],
    [
        'title' => 'Beauty Pageant Grooming',
        'slug' => 'pageant',
        'category' => 'professional_development',
        'category_badge' => 'PROFESSIONAL DEVELOPMENT MODULE',
        'short_description' => 'Prepare for success in national and international beauty pageants.',
        'full_description' => 'Prepare for success in national and international beauty pageants through comprehensive professional training. The programme covers runway techniques, pageant walking, stage presence, interview preparation, public speaking, personal branding, grooming, etiquette, fitness presentation, confidence building, beauty styling, makeup fundamentals, media communication, and personality development.',
        'hero_image' => 'assets/images/mentors/torsa.webp?v=1.1',
        'duration' => '3 Months',
        'admission_fee' => 'BDT 30,000',
        'course_fee' => 'BDT 30,000',
        'eligibility' => 'Aspiring beauty pageant contestants.',
        'age_requirement' => 'Open',
        'course_type' => 'Professional Development',
        'status' => 'published',
        'display_order' => 8,
        'meta_title' => 'STAR FAIR | Beauty Pageant Grooming Course Bangladesh',
        'meta_description' => 'Pageant walk, interview prep, stage poise, etiquette, and media communication.',
        'modules' => [
            'Pageant Walk & Runway Poise',
            'Interview Preparation & Speech',
            'Etiquette, Grooming & Social Grace',
            'Fitness & Nutrition Planning',
            'Personal Styling & Media Communication'
        ],
        'schedules' => [
            ['day_name' => 'Friday', 'time_text' => '3:00 PM - 5:00 PM', 'topic_text' => 'Poise & Stage'],
            ['day_name' => 'Saturday', 'time_text' => '4:00 PM - 6:00 PM', 'topic_text' => 'Speech & Styling']
        ],
        'careers' => [
            'Pageant Contestant',
            'Model',
            'Brand Ambassador',
            'Image Consultant',
            'Spokesperson'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/pageant1.png', 'alt_text' => 'Beauty Pageant Stage Walk'],
            ['image_path' => 'assets/images/course-gallery/pageant2.png', 'alt_text' => 'Pageant Runway Highlight'],
            ['image_path' => 'assets/images/course-gallery/pageant3.png', 'alt_text' => 'Pageant Photoshoot BTS'],
            ['image_path' => 'assets/images/course-gallery/pageant4.png', 'alt_text' => 'Pageant Training Session']
        ]
    ],
    [
        'title' => 'Photography & Visual Storytelling',
        'slug' => 'photography',
        'category' => 'professional_development',
        'category_badge' => 'PROFESSIONAL DEVELOPMENT MODULE',
        'short_description' => 'Gain practical skills in fashion photography, studio lighting and editing.',
        'full_description' => 'Gain practical skills in fashion photography, portrait photography, studio lighting, composition, digital editing, visual storytelling, branding, and creative content production.',
        'hero_image' => 'assets/images/programs/photo.jpg',
        'duration' => '3 Months',
        'admission_fee' => 'BDT 20,000',
        'course_fee' => 'BDT 20,000',
        'eligibility' => 'Photography enthusiasts and media aspirants.',
        'age_requirement' => 'Open',
        'course_type' => 'Professional Development',
        'status' => 'published',
        'display_order' => 9,
        'meta_title' => 'STAR FAIR | Photography & Visual Storytelling Course',
        'meta_description' => 'Fashion photography, portrait studio lighting, Lightroom, and digital editing.',
        'modules' => [
            'Camera Controls & Exposure Triads',
            'Studio Lighting Setup & Moods',
            'Fashion & Portrait Shoot Work',
            'Digital Editing & Retouching',
            'Visual Storytelling & Personal Branding'
        ],
        'schedules' => [
            ['day_name' => 'Saturday', 'time_text' => '3:00 PM - 5:00 PM', 'topic_text' => 'Studio Photography'],
            ['day_name' => 'Tuesday', 'time_text' => '4:00 PM - 6:00 PM', 'topic_text' => 'Digital Retouching']
        ],
        'careers' => [
            'Fashion Photographer',
            'Portrait Photographer',
            'Studio Owner',
            'Content Creator',
            'Photojournalist'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/photo1.jpg', 'alt_text' => 'Photography Studio Session'],
            ['image_path' => 'assets/images/course-gallery/photo2.png', 'alt_text' => 'Professional Photoshoot BTS'],
            ['image_path' => 'assets/images/course-gallery/photo3.png', 'alt_text' => 'Camera Work Training'],
            ['image_path' => 'assets/images/course-gallery/photo4.png', 'alt_text' => 'Studio Lighting Setup']
        ]
    ],
    [
        'title' => 'Fashion, Beauty & Prosthetic Makeup',
        'slug' => 'makeup',
        'category' => 'professional_development',
        'category_badge' => 'PROFESSIONAL DEVELOPMENT MODULE',
        'short_description' => 'Professional training covering beauty, editorial, bridal and prosthetic makeup.',
        'full_description' => 'Professional training covering beauty makeup, fashion and editorial makeup, bridal artistry, prosthetic makeup, special effects (SFX), character transformation, skincare, hygiene, and portfolio development.',
        'hero_image' => 'assets/images/programs/prosthetic_makeup.png',
        'duration' => '3 Months',
        'admission_fee' => 'BDT 40,000',
        'course_fee' => 'BDT 40,000',
        'eligibility' => 'Creative makeup enthusiasts and future beauty professionals.',
        'age_requirement' => 'Open',
        'course_type' => 'Professional Development',
        'status' => 'published',
        'display_order' => 10,
        'meta_title' => 'STAR FAIR | Fashion & Prosthetic Makeup Artistry Course',
        'meta_description' => 'Bridal makeup, fashion shoot makeup, SFX, and prosthetic transformation.',
        'modules' => [
            'Skincare & Makeup Fundamentals',
            'Bridal & Glamour Makeup',
            'Fashion & Editorial Shoot Makeup',
            'Prosthetic Makeup & Special Effects (SFX)',
            'Character Transformation & Hygiene'
        ],
        'schedules' => [
            ['day_name' => 'Friday', 'time_text' => '4:00 PM - 6:00 PM', 'topic_text' => 'Bridal & Glamour'],
            ['day_name' => 'Saturday', 'time_text' => '5:00 PM - 7:00 PM', 'topic_text' => 'Prosthetics & SFX']
        ],
        'careers' => [
            'Makeup Artist (MUA)',
            'Prosthetic Artist',
            'Beauty Consultant',
            'Bridal MUA Specialist',
            'Editorial Stylist'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/makeup1.png', 'alt_text' => 'Professional Makeup Session'],
            ['image_path' => 'assets/images/course-gallery/makeup2.png', 'alt_text' => 'Makeup Photoshoot BTS'],
            ['image_path' => 'assets/images/course-gallery/makeup3.png', 'alt_text' => 'Beauty & Styling Class'],
            ['image_path' => 'assets/images/course-gallery/makeup4.png', 'alt_text' => 'Makeup Portfolio Shoot']
        ]
    ],
    [
        'title' => 'Life Skills, Leadership & Communication',
        'slug' => 'communication',
        'category' => 'professional_development',
        'category_badge' => 'PROFESSIONAL DEVELOPMENT MODULE',
        'short_description' => 'Strengthen leadership, communication, EQ and career readiness.',
        'full_description' => 'Strengthen leadership, communication, emotional intelligence, teamwork, critical thinking, professional ethics, problem-solving, career readiness, and workplace confidence.',
        'hero_image' => 'assets/images/programs/communication.jpg',
        'duration' => '3 Months',
        'admission_fee' => 'BDT 20,000',
        'course_fee' => 'BDT 20,000',
        'eligibility' => 'Open to students, professionals, and job seekers.',
        'age_requirement' => 'Open',
        'course_type' => 'Professional Development',
        'status' => 'published',
        'display_order' => 11,
        'meta_title' => 'STAR FAIR | Life Skills & Leadership Communication Course',
        'meta_description' => 'Interpersonal communication, emotional intelligence, teamwork, and leadership.',
        'modules' => [
            'Verbal & Interpersonal Communication',
            'Emotional Intelligence & Self-Awareness',
            'Teamwork & Collaborative Leadership',
            'Critical Thinking & Problem Solving',
            'Workplace Ethics & Career Readiness'
        ],
        'schedules' => [
            ['day_name' => 'Saturday', 'time_text' => '3:00 PM - 5:00 PM', 'topic_text' => 'Communication Skills'],
            ['day_name' => 'Tuesday', 'time_text' => '4:00 PM - 6:00 PM', 'topic_text' => 'Leadership Workshop']
        ],
        'careers' => [
            'Communications Manager',
            'Human Resources Specialist',
            'Public Relations Executive',
            'Corporate Professional',
            'Team Leader'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/comm1.jpg', 'alt_text' => 'Communication Skills Session'],
            ['image_path' => 'assets/images/course-gallery/comm2.png', 'alt_text' => 'Public Speaking Practice'],
            ['image_path' => 'assets/images/course-gallery/comm3.png', 'alt_text' => 'Leadership Training Workshop'],
            ['image_path' => 'assets/images/course-gallery/comm4.png', 'alt_text' => 'Communication Class Highlight']
        ]
    ],
    [
        'title' => 'Personal Grooming & Image Management',
        'slug' => 'grooming',
        'category' => 'professional_development',
        'category_badge' => 'PROFESSIONAL DEVELOPMENT MODULE',
        'short_description' => 'Develop professional etiquette, body language and self-presentation.',
        'full_description' => 'Develop professional etiquette, image management, body language, personal branding, confidence, self-presentation, and social and corporate professionalism.',
        'hero_image' => 'assets/images/programs/grooming.jpg',
        'duration' => '3 Months',
        'admission_fee' => 'BDT 20,000',
        'course_fee' => 'BDT 20,000',
        'eligibility' => 'Individuals seeking self-improvement and corporate grooming.',
        'age_requirement' => 'Open',
        'course_type' => 'Professional Development',
        'status' => 'published',
        'display_order' => 12,
        'meta_title' => 'STAR FAIR | Personal Grooming & Image Management Course',
        'meta_description' => 'Corporate etiquette, posture, body language, personal styling, and image.',
        'modules' => [
            'Social & Corporate Etiquette',
            'Body Language & Posture',
            'Personal Styling & Color Coding',
            'Confidence & Self-Presentation',
            'Digital Branding & Image Management'
        ],
        'schedules' => [
            ['day_name' => 'Friday', 'time_text' => '5:00 PM - 7:00 PM', 'topic_text' => 'Etiquette Class'],
            ['day_name' => 'Saturday', 'time_text' => '3:00 PM - 5:00 PM', 'topic_text' => 'Image Workshop']
        ],
        'careers' => [
            'Image Consultant',
            'Grooming Instructor',
            'Brand Representative',
            'Executive Coach',
            'Etiquette Consultant'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/grooming1.jpg', 'alt_text' => 'Personal Grooming Session'],
            ['image_path' => 'assets/images/course-gallery/grooming2.png', 'alt_text' => 'Posture & Style Training'],
            ['image_path' => 'assets/images/course-gallery/grooming3.png', 'alt_text' => 'Personality Development Class'],
            ['image_path' => 'assets/images/course-gallery/grooming4.png', 'alt_text' => 'Grooming Workshop Highlight']
        ]
    ],
    [
        'title' => 'Digital Marketing & Personal Branding',
        'slug' => 'digital-marketing',
        'category' => 'professional_development',
        'category_badge' => 'PROFESSIONAL DEVELOPMENT MODULE',
        'short_description' => 'Master digital marketing, content strategy, Meta ads, freelancing and AI tools.',
        'full_description' => 'Start From Zero - Grow to Pro. Master content marketing with AI, Meta Ads with AI, YouTube growth, local & global freelancing platforms, active & passive income with career guidelines, and professional productivity boosting with AI tools.',
        'hero_image' => 'assets/images/programs/digital_marketing.png?v=1.2',
        'duration' => '3 Months (36 Classes)',
        'admission_fee' => 'BDT 6,000',
        'course_fee' => 'BDT 6,000',
        'eligibility' => 'Creators, models, students, job holders, and homemakers.',
        'age_requirement' => 'Open',
        'course_type' => 'Professional Development',
        'status' => 'published',
        'display_order' => 13,
        'meta_title' => 'STAR FAIR | Digital Marketing & Personal Branding Course',
        'meta_description' => 'Master Meta Ads, Canva, YouTube growth, Fiverr freelancing and AI tools.',
        'modules' => [
            'Module 1: Pre-requisites & Foundations of Marketing',
            'Module 2: Fundamentals of Digital Marketing & Customer Journeys',
            'Module 3: Content Marketing Principles & Copywriting',
            'Module 4: Graphic Design & Image Marketing with Canva + AI',
            'Module 5: Professional Video Production & Reels in Canva',
            'Module 6: Meta Ads & Social Media Marketing Strategy',
            'Module 7: YouTube Growth & Video Optimization',
            'Module 8: Freelancing on Fiverr & Upwork + Client Management'
        ],
        'schedules' => [
            ['day_name' => 'Weekly Live', 'time_text' => 'Interactive live sessions + Q&A support', 'topic_text' => 'Live Classes'],
            ['day_name' => 'Recorded', 'time_text' => 'Lifetime access to recorded lessons', 'topic_text' => 'Self-Paced'],
            ['day_name' => 'Support', 'time_text' => '24/7 WhatsApp & Facebook groups', 'topic_text' => 'Mentorship']
        ],
        'careers' => [
            'Digital Marketing Specialist',
            'Social Media Manager',
            'Freelancer on Fiverr & Upwork',
            'Content Creator & Strategist',
            'Brand Growth Consultant'
        ],
        'galleries' => [
            ['image_path' => 'assets/images/course-gallery/comm1.jpg', 'alt_text' => 'Digital Marketing Session'],
            ['image_path' => 'assets/images/course-gallery/comm2.png', 'alt_text' => 'Content Creation Workshop'],
            ['image_path' => 'assets/images/course-gallery/comm3.png', 'alt_text' => 'Freelancing Strategy Class'],
            ['image_path' => 'assets/images/course-gallery/comm4.png', 'alt_text' => 'Social Media Marketing']
        ]
    ]
];

$seededCount = 0;
$updatedCount = 0;

foreach ($coursesData as $c) {
    // Check if course exists
    $stmt = $pdo->prepare("SELECT id FROM `courses` WHERE `slug` = ?");
    $stmt->execute([$c['slug']]);
    $existing = $stmt->fetch();

    if ($existing) {
        $courseId = $existing['id'];
        // Update main course record
        $updateSql = "UPDATE `courses` SET 
            `title` = ?, `category` = ?, `category_badge` = ?, `short_description` = ?, `full_description` = ?, 
            `hero_image` = ?, `duration` = ?, `admission_fee` = ?, `course_fee` = ?, `eligibility` = ?, 
            `age_requirement` = ?, `course_type` = ?, `status` = ?, `display_order` = ?, `meta_title` = ?, `meta_description` = ?
            WHERE `id` = ?";
        $stmtUpdate = $pdo->prepare($updateSql);
        $stmtUpdate->execute([
            $c['title'], $c['category'], $c['category_badge'], $c['short_description'], $c['full_description'],
            $c['hero_image'], $c['duration'], $c['admission_fee'], $c['course_fee'], $c['eligibility'],
            $c['age_requirement'], $c['course_type'], $c['status'], $c['display_order'], $c['meta_title'], $c['meta_description'],
            $courseId
        ]);
        $updatedCount++;
    } else {
        // Insert main course record
        $insertSql = "INSERT INTO `courses` 
            (`title`, `slug`, `category`, `category_badge`, `short_description`, `full_description`, `hero_image`, `duration`, `admission_fee`, `course_fee`, `eligibility`, `age_requirement`, `course_type`, `status`, `display_order`, `meta_title`, `meta_description`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $pdo->prepare($insertSql);
        $stmtInsert->execute([
            $c['title'], $c['slug'], $c['category'], $c['category_badge'], $c['short_description'], $c['full_description'],
            $c['hero_image'], $c['duration'], $c['admission_fee'], $c['course_fee'], $c['eligibility'],
            $c['age_requirement'], $c['course_type'], $c['status'], $c['display_order'], $c['meta_title'], $c['meta_description']
        ]);
        $courseId = $pdo->lastInsertId();
        $seededCount++;
    }

    // Re-seed child relations (Modules, Schedules, Careers, Galleries)
    $pdo->prepare("DELETE FROM `course_modules` WHERE `course_id` = ?")->execute([$courseId]);
    $stmtMod = $pdo->prepare("INSERT INTO `course_modules` (`course_id`, `module_name`, `display_order`) VALUES (?, ?, ?)");
    foreach ($c['modules'] as $idx => $modName) {
        $stmtMod->execute([$courseId, $modName, $idx + 1]);
    }

    $pdo->prepare("DELETE FROM `course_schedules` WHERE `course_id` = ?")->execute([$courseId]);
    $stmtSch = $pdo->prepare("INSERT INTO `course_schedules` (`course_id`, `day_name`, `time_text`, `topic_text`, `display_order`) VALUES (?, ?, ?, ?, ?)");
    foreach ($c['schedules'] as $idx => $sch) {
        $stmtSch->execute([$courseId, $sch['day_name'], $sch['time_text'], $sch['topic_text'] ?? null, $idx + 1]);
    }

    $pdo->prepare("DELETE FROM `course_careers` WHERE `course_id` = ?")->execute([$courseId]);
    $stmtCar = $pdo->prepare("INSERT INTO `course_careers` (`course_id`, `career_title`, `display_order`) VALUES (?, ?, ?)");
    foreach ($c['careers'] as $idx => $carTitle) {
        $stmtCar->execute([$courseId, $carTitle, $idx + 1]);
    }

    $pdo->prepare("DELETE FROM `course_galleries` WHERE `course_id` = ?")->execute([$courseId]);
    $stmtGal = $pdo->prepare("INSERT INTO `course_galleries` (`course_id`, `image_path`, `alt_text`, `display_order`) VALUES (?, ?, ?, ?)");
    foreach ($c['galleries'] as $idx => $gal) {
        $stmtGal->execute([$courseId, $gal['image_path'], $gal['alt_text'] ?? null, $idx + 1]);
    }
}

$isJson = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
if ($isJson || (isset($_GET['format']) && $_GET['format'] === 'json')) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => "Course seeding complete.",
        'inserted' => $seededCount,
        'updated' => $updatedCount,
        'total' => count($coursesData)
    ]);
} else {
    echo "<h1>STAR FAIR Course Seeder</h1>";
    echo "<p>Course seeding successfully completed!</p>";
    echo "<ul><li>Inserted: {$seededCount}</li><li>Updated: {$updatedCount}</li><li>Total Courses: " . count($coursesData) . "</li></ul>";
}
