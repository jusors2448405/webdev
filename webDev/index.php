<?php
require_once 'includes/db.php';
$works = getWorks();
$categories = [];

foreach ($works as $work) {
    if (!empty($work['category']) && !in_array($work['category'], $categories)) {
        $categories[] = $work['category'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/header.php'; ?>
    <title>Photography Portfolio</title>
    <style>
        /* Additional Photography-Specific Styles */
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #333;
            --accent-color: #f5a623;
            --light-color: #f8f8f8;
            --text-color: #222;
            --white: #ffffff;
            --dark-overlay: rgba(0, 0, 0, 0.7);
            --light-overlay: rgba(255, 255, 255, 0.9);
        }
        
        body {
            font-family: 'Playfair Display', 'Roboto', serif;
            line-height: 1.8;
            color: var(--text-color);
            background-color: var(--white);
        }
        
        .logo {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-weight: 700;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        
        .main-header {
            padding: 15px 0;
            background-color: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .main-nav a {
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        /* Hero Section Enhancement */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                url('https://source.unsplash.com/random/1600x900/?photography') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
            position: center;
        }
        
        .hero-content {
            max-width: 1400px;
            padding: 20px;
            position: center;
            z-index: 1;
        }
        
        .hero-content h2 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            font-weight: 300;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: var(--accent-color);
            color: var(--white);
            border: none;
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 2px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .btn:hover {
            background-color: var(--white);
            color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        /* About Section Enhancement */
        .about {
            padding: 100px 0;
            background-color: var(--white);
        }
        
        .about-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 60px;
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            color: var(--primary-color);
            font-family: 'Playfair Display', serif;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
            transform: translateX(-50%);
        }
        
        .about-text {
            max-width: 800px;
            text-align: center;
            font-size: 1.1rem;
            line-height: 1.8;
        }
        
        .about-text p {
            margin-bottom: 25px;
        }
        
        .about-image {
            margin-top: 40px;
            position: relative;
            display: inline-block;
        }
        
        .about-image img {
            max-width: 350px;
            border-radius: 50%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .about-signature {
            margin-top: 30px;
            font-family: 'Pacifico', cursive;
            font-size: 2rem;
            color: var(--primary-color);
        }
        
        /* Portfolio Section Enhancement */
        .portfolio {
            padding: 100px 0;
            background-color: var(--light-color);
        }
        
        .filter-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }
        
        .filter-btn {
            background: transparent;
            border: none;
            padding: 10px 20px;
            margin: 0 5px 10px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .filter-btn:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--accent-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .filter-btn:hover:after,
        .filter-btn.active:after {
            width: 70%;
        }
        
        .filter-btn.active {
            color: var(--accent-color);
        }
        
        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .portfolio-item {
            position: relative;
            overflow: hidden;
            border-radius: 5px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: all 0.5s ease;
            height: 300px;
        }
        
        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }
        
        .portfolio-item:hover img {
            transform: scale(1.1);
        }
        
        .portfolio-item .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--dark-overlay);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            padding: 30px;
            text-align: center;
            transition: all 0.5s ease;
        }
        
        .portfolio-item:hover .overlay {
            opacity: 1;
        }
        
        .portfolio-item .overlay h3 {
            color: var(--white);
            font-size: 1.5rem;
            margin-bottom: 15px;
            transform: translateY(-20px);
            transition: all 0.5s ease;
            opacity: 0;
        }
        
        .portfolio-item:hover .overlay h3 {
            transform: translateY(0);
            opacity: 1;
            transition-delay: 0.1s;
        }
        
        .portfolio-item .overlay p {
            color: var(--white);
            margin-bottom: 20px;
            transform: translateY(20px);
            transition: all 0.5s ease;
            opacity: 0;
        }
        
        .portfolio-item:hover .overlay p {
            transform: translateY(0);
            opacity: 1;
            transition-delay: 0.2s;
        }
        
        .btn-view {
            display: inline-block;
            padding: 10px 25px;
            background: var(--accent-color);
            color: var(--white);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 3px;
            transform: translateY(20px);
            transition: all 0.5s ease;
            opacity: 0;
        }
        
        .portfolio-item:hover .btn-view {
            transform: translateY(0);
            opacity: 1;
            transition-delay: 0.3s;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            font-size: 1.2rem;
            color: #777;
            font-style: italic;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
        }
        
        /* Contact Section Enhancement */
        .contact {
            padding: 100px 0;
            background-color: var(--white);
            position: relative;
        }
        
        .contact:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(to bottom, var(--light-color), var(--white));
            z-index: 1;
        }
        
        .contact .container {
            position: relative;
            z-index: 2;
        }
        
        .contact-content {
            display: flex;
            justify-content: center;
        }
        
        .contact-info {
            text-align: center;
            max-width: 600px;
        }
        
        .contact-info p {
            margin-bottom: 30px;
            font-size: 1.2rem;
        }
        
        .contact-form {
            margin-top: 40px;
            padding: 40px;
            background: var(--white);
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .contact-details {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 40px;
        }
        
        .contact-details p {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
        }
        
        .contact-details i {
            margin-right: 15px;
            width: 40px;
            height: 40px;
            background: var(--light-color);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .social-links {
            margin-top: 30px;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            margin: 0 10px;
            border-radius: 50%;
            background: var(--light-color);
            color: var(--primary-color);
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--accent-color);
            color: var(--white);
            transform: translateY(-5px);
        }
        
        /* Footer Enhancement */
        .main-footer {
            background-color: var(--primary-color);
            color: rgba(255, 255, 255, 0.8);
            padding: 60px 0 30px;
            text-align: center;
        }
        
        .footer-content p {
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .footer-nav {
            margin: 30px 0;
        }
        
        .footer-nav ul {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .footer-nav li {
            margin: 0 20px;
        }
        
        .footer-nav a {
            color: rgba(255, 255, 255, 0.7);
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .footer-nav a:hover {
            color: var(--accent-color);
        }
        
        .footer-bottom {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Responsive Enhancements */
        @media (max-width: 992px) {
            .hero-content h2 {
                font-size: 3rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .portfolio-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .hero-content h2 {
                font-size: 2.5rem;
            }
            
            .hero-content p {
                font-size: 1.2rem;
            }
            
            .about, .portfolio, .contact {
                padding: 70px 0;
            }
            
            .section-title {
                font-size: 2rem;
                margin-bottom: 40px;
            }
        }
        
        @media (max-width: 576px) {
            .hero-content h2 {
                font-size: 2rem;
            }
            
            .portfolio-grid {
                grid-template-columns: 1fr;
            }
            
            .contact-form {
                padding: 20px;
            }
        }
        
        /* Animation classes */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .fade-in.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Photo viewer modal styles */
        .photo-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            overflow: auto;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            padding: 20px;
        }
        
        .photo-container {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            margin: 0 auto;
            background-color: transparent;
            box-shadow: none;
            border: none;
        }
        
        .photo-container img {
            display: block;
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border: 5px solid white;
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            color: white;
            font-size: 35px;
            cursor: pointer;
            z-index: 1001;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
        }
        
        .close-modal:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
        
        /* View Image button */
        .btn-view-image {
            display: inline-block;
            padding: 8px 16px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 3px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        
        .btn-view-image:hover {
            background-color: #d98c10;
        }
        
        /* Make portfolio items clickable */
        .portfolio-item {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1 class="logo">Mark Fernandez Photography</h1>
            <nav>
                <ul class="main-nav">
                    <li><a href="#hero" class="active">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#portfolio">Gallery</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="admin/login.php" class="admin-link">Admin</a></li>
                </ul>
            </nav>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <section id="hero" class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Capturing Life's Beautiful Moments</h2>
                <p>Professional photography by Mark Fernandez</p>
                <a href="#portfolio" class="btn">View Gallery</a>
            </div>
        </div>
    </section>

    <section id="about" class="about">
        <div class="container">
            <h2 class="section-title fade-in">About Mark Fernandez</h2>
            <div class="about-content">
                <div class="about-text fade-in">
                    <p>I'm Mark Fernandez, a passionate photographer dedicated to capturing the beauty in everyday moments. With an eye for detail and a love for composition, I specialize in portrait, landscape, and event photography that tells compelling stories through imagery.</p>
                    <p>My approach combines technical expertise with artistic vision, resulting in photographs that evoke emotion and preserve memories. This portfolio showcases some of my favorite works from various projects and personal explorations.</p>
                </div>
                <div class="about-image fade-in">
                    <img src="mfphotography.png" alt="Mark Fernandez - Photographer">
                </div>
                <div class="about-signature fade-in">
                    Mark Fernandez
                </div>
            </div>
        </div>
    </section>

    <section id="portfolio" class="portfolio">
        <div class="container">
            <h2 class="section-title fade-in">Photography Gallery</h2>
            
            <?php if (count($categories) > 0): ?>
            <div class="filter-container fade-in">
                <button class="filter-btn active" data-filter="all">All Work</button>
                <?php foreach ($categories as $category): ?>
                <button class="filter-btn" data-filter="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="portfolio-grid">
                <?php if (empty($works)): ?>
                <p class="empty-state fade-in">No gallery items found. Check back later for new photography work!</p>
                <?php else: ?>
                    <?php foreach ($works as $work): ?>
                    <div class="portfolio-item fade-in" data-category="<?= htmlspecialchars($work['category'] ?? 'uncategorized') ?>" onclick="openPhotoModal('<?= htmlspecialchars($work['image']) ?>', '<?= htmlspecialchars(addslashes($work['title'])) ?>', '<?= htmlspecialchars(addslashes($work['description'])) ?>')">
                        <img src="<?= htmlspecialchars($work['image']) ?>" alt="<?= htmlspecialchars($work['title']) ?>">
                        <div class="overlay">
                            <h3><?= htmlspecialchars($work['title']) ?></h3>
                            <p><?= htmlspecialchars($work['description']) ?></p>
                            <?php if (!empty($work['link'])): ?>
                            <a href="<?= htmlspecialchars($work['link']) ?>" target="_blank" class="btn-view">View Full Gallery</a>
                            <?php else: ?>
                            <button class="btn-view-image" onclick="event.stopPropagation(); openPhotoModal('<?= htmlspecialchars($work['image']) ?>', '<?= htmlspecialchars(addslashes($work['title'])) ?>', '<?= htmlspecialchars(addslashes($work['description'])) ?>')">View Image</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <!-- Photo Modal -->
    <div id="photoModal" class="photo-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closePhotoModal()">&times;</span>
            <div class="photo-container">
                <img id="modalImage" src="" alt="">
            </div>
        </div>
    </div>

    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title fade-in">Get In Touch</h2>
            <div class="contact-content">
                <div class="contact-info fade-in">
                    <p>Interested in booking a photography session with Mark Fernandez or have questions about my services? I'd love to hear from you!</p>
                    <div class="contact-details">
                        <p><i class="fas fa-envelope"></i> mfpeventphoto@gmail.com</p>
                        <p><i class="fas fa-phone"></i> (63)9936806795</p>
                        <p><i class="fas fa-map-marker-alt"></i> Quezon City, Philippines</p>
                        <div class="social-links">
                            <a href="https://www.facebook.com/profile.php?id=100090976873690" aria-label="Mark Fernandez Photography on Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.instagram.com/its_mfphotography/" aria-label="Mark Fernandez Photography on Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://www.tiktok.com/@mfphotography08" aria-label="Mark Fernandez Photography on TikTok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
    <script>
        // Animation for fade-in elements
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            
            function checkFade() {
                fadeElements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const windowHeight = window.innerHeight;
                    
                    if (elementTop < windowHeight - 100) {
                        element.classList.add('active');
                    }
                });
            }
            
            // Check elements on load
            checkFade();
            
            // Check elements on scroll
            window.addEventListener('scroll', checkFade);
        });
        
        // Photo modal functionality
        function openPhotoModal(imageUrl, title, description) {
            const modal = document.getElementById('photoModal');
            const modalImg = document.getElementById('modalImage');
            
            modalImg.src = imageUrl;
            modal.style.display = 'block';
            
            // Prevent scrolling on the background
            document.body.style.overflow = 'hidden';
        }
        
        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            modal.style.display = 'none';
            
            // Restore scrolling
            document.body.style.overflow = '';
        }
        
        // Close modal when clicking outside the image
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('photoModal');
            const modalContent = document.querySelector('.modal-content');
            
            if (modal && modal.style.display === 'block') {
                // Check if the click is directly on the modal (not its children)
                if (event.target === modal) {
                    closePhotoModal();
                }
            }
        });
        
        // Close modal with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
</body>
</html>
