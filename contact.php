<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - SUDAMA CLINIC</title>
    <meta name="description"
        content="Get in touch with SUDAMA CLINIC. Find our location, contact details, and send us a message.">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/landing.css">
    <style>
        .contact-hero {
            padding: 120px 0 60px;
            text-align: center;
        }

        .contact-hero h1 {
            font-size: var(--font-size-5xl);
            margin-bottom: var(--spacing-lg);
        }

        .contact-hero p {
            font-size: var(--font-size-xl);
            color: var(--color-text-secondary);
            max-width: 700px;
            margin: 0 auto;
        }

        .contact-section {
            padding: var(--spacing-3xl) 0;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-3xl);
        }

        .contact-form-card {
            background: var(--color-bg-secondary);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--glass-border);
        }

        .contact-form-card h2 {
            font-size: var(--font-size-2xl);
            margin-bottom: var(--spacing-xl);
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-xl);
        }

        .info-card {
            background: var(--color-bg-secondary);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: flex-start;
            gap: var(--spacing-lg);
            transition: all var(--transition-spring);
        }

        .info-card:hover {
            transform: translateX(10px);
            border-color: var(--primary-light);
            box-shadow: var(--shadow-lg);
        }

        .info-icon {
            font-size: 2.5rem;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(139, 92, 246, 0.1));
            border-radius: var(--border-radius-md);
        }

        .info-content h3 {
            font-size: var(--font-size-lg);
            margin-bottom: var(--spacing-xs);
        }

        .info-content p {
            color: var(--color-text-secondary);
            margin: 0;
            line-height: 1.6;
        }

        .info-content a {
            color: var(--primary-start);
            font-weight: 600;
        }

        .map-section {
            padding: var(--spacing-3xl) 0;
            background: linear-gradient(180deg, transparent, rgba(6, 182, 212, 0.03), transparent);
        }

        .map-container {
            border-radius: var(--border-radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--glass-border);
        }

        .map-container iframe {
            width: 100%;
            height: 450px;
            border: none;
        }

        .hours-section {
            padding: var(--spacing-3xl) 0;
        }

        .hours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: var(--spacing-xl);
            margin-top: var(--spacing-2xl);
        }

        .hours-card {
            background: var(--color-bg-secondary);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--glass-border);
            text-align: center;
            transition: all var(--transition-spring);
        }

        .hours-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .hours-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
        }

        .hours-card h3 {
            font-size: var(--font-size-xl);
            margin-bottom: var(--spacing-md);
        }

        .hours-list {
            list-style: none;
            padding: 0;
        }

        .hours-list li {
            display: flex;
            justify-content: space-between;
            padding: var(--spacing-sm) 0;
            border-bottom: 1px solid var(--glass-border);
            color: var(--color-text-secondary);
        }

        .hours-list li:last-child {
            border-bottom: none;
        }

        .hours-list .day {
            font-weight: 600;
            color: var(--color-text-primary);
        }

        .emergency-card {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            border-color: #ef4444;
        }

        .emergency-card h3 {
            color: #ef4444;
        }

        .emergency-number {
            font-size: var(--font-size-3xl);
            font-weight: 800;
            color: #ef4444;
            margin-top: var(--spacing-md);
        }

        .success-message {
            display: none;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--accent-success);
            border-radius: var(--border-radius-md);
            padding: var(--spacing-lg);
            margin-top: var(--spacing-lg);
            color: var(--accent-success);
            text-align: center;
        }

        .success-message.show {
            display: block;
            animation: slideUp 0.5s ease-out;
        }

        /* Hero section with image beside text */
        .contact-hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-2xl);
            align-items: center;
            padding: 120px 0 40px;
        }

        .contact-hero-content h1 {
            font-size: var(--font-size-5xl);
            margin-bottom: var(--spacing-lg);
        }

        .contact-hero-content p {
            font-size: var(--font-size-xl);
            color: var(--color-text-secondary);
            line-height: 1.7;
        }

        .contact-hero-image img {
            width: 100%;
            max-width: 500px;
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-xl);
        }

        /* Contact Cards Section */
        .contact-cards-section {
            padding: 80px 0;
            background: white;
        }

        .contact-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 50px;
        }

        /* Card Container - Fixed height for consistency */
        .contact-card {
            border-radius: 20px;
            overflow: hidden;
            text-align: center;
            position: relative;
            height: 480px;
            /* Taller to accommodate full image look */
            background: #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Image fills the entire card */
        .contact-card-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .contact-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(100%);
            /* Opacity handling is done via overlay */
        }

        /* Gradient Overlay Pseudo-element */
        .contact-card-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        /* Specific Gradients - Tint top, Solid bottom for text readability */
        /* Yellow */
        .contact-card.yellow .contact-card-image::after {
            background: linear-gradient(to bottom,
                    rgba(250, 204, 21, 0.4) 0%,
                    rgba(250, 204, 21, 0.9) 45%,
                    #facc15 100%);
        }

        /* Teal/Blue */
        .contact-card.teal .contact-card-image::after {
            background: linear-gradient(to bottom,
                    rgba(6, 182, 212, 0.3) 0%,
                    rgba(165, 243, 252, 0.9) 45%,
                    #ecfeff 100%);
        }

        /* Grey */
        .contact-card.grey .contact-card-image::after {
            background: linear-gradient(to bottom,
                    rgba(148, 163, 184, 0.3) 0%,
                    rgba(226, 232, 240, 0.9) 45%,
                    #f8fafc 100%);
        }

        /* Content - Positioned at bottom over the image/overlay */
        .contact-card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 55%;
            /* Occupies bottom half */
            padding: 40px 24px 32px;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            /* No background - relies on the gradient overlay of the image */
        }

        .contact-card-content h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #0f172a;
        }

        .contact-card-content p {
            color: #334155;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 24px;
            max-width: 300px;
        }

        .contact-card-btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            background: #0f172a;
            color: white;
            min-width: 200px;
        }

        .contact-card-btn:hover {
            background: #1e293b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 1024px) {
            .contact-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .contact-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .contact-cards-grid {
                grid-template-columns: 1fr;
                gap: 40px;
                padding: 0 20px;
            }
        }

        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .map-container iframe {
                height: 300px;
            }

            .contact-hero-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .contact-hero-image {
                order: -1;
            }

            .contact-hero-image img {
                max-width: 300px;
            }
        }
    </style>
<script>
    (function() {
        try {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        } catch (e) {}
    })();
</script>\n</head>

<body>
    <!-- Enhanced Navigation -->
    <nav
        style="position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: #1e293b; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border-bottom: 3px solid #06b6d4;">
        <div
            style="max-width: 1400px; margin: 0 auto; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; height: 75px;">
            <a href="index.php" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                <img src="images/logo.png" alt="SUDAMA CLINIC" style="height: 45px;">
                <span style="font-size: 1.6rem; font-weight: 800; color: white; letter-spacing: -0.5px;">SUDAMA <span
                        style="color: #06b6d4;">CLINIC</span></span>
            </a>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="index.php"
                    style="padding: 10px 18px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-weight: 500; font-size: 0.9rem;"
                    onmouseover="this.style.color='#06b6d4'; this.style.background='rgba(6,182,212,0.1)'"
                    onmouseout="this.style.color='#94a3b8'; this.style.background='transparent'">Home</a>
                <a href="about.php"
                    style="padding: 10px 18px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-weight: 500; font-size: 0.9rem;"
                    onmouseover="this.style.color='#06b6d4'; this.style.background='rgba(6,182,212,0.1)'"
                    onmouseout="this.style.color='#94a3b8'; this.style.background='transparent'">About</a>
                <a href="contact.php"
                    style="padding: 10px 18px; color: white; text-decoration: none; border-radius: 8px; background: #06b6d4; font-weight: 600; font-size: 0.9rem;">Contact</a>
                <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2); margin: 0 16px;"></div>
                <a href="patient-login.php"
                    style="padding: 10px 20px; color: #e2e8f0; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s;"
                    onmouseover="this.style.borderColor='#06b6d4'; this.style.color='#06b6d4'"
                    onmouseout="this.style.borderColor='rgba(255,255,255,0.3)'; this.style.color='#e2e8f0'">Patient
                    Login</a>
                <a href="doctor-login.php"
                    style="padding: 10px 20px; color: #e2e8f0; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s;"
                    onmouseover="this.style.borderColor='#8b5cf6'; this.style.color='#8b5cf6'"
                    onmouseout="this.style.borderColor='rgba(255,255,255,0.3)'; this.style.color='#e2e8f0'">Doctor
                    Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Image Beside Text -->
    <section>
        <div class="container">
            <div class="contact-hero-grid">
                <div class="contact-hero-content">
                    <h1>Get in <span class="text-gradient">Touch</span></h1>
                    <p>Have questions about our services, need to book an appointment, or want to provide feedback? Our
                        dedicated team is here to assist you Monday through Sunday.</p>
                    <div style="margin-top: var(--spacing-xl); display: flex; gap: var(--spacing-lg); flex-wrap: wrap;">
                        <a href="tel:+918488002969" class="btn btn-primary"
                            style="display: inline-flex; align-items: center; gap: 8px;">
                            📞 Call Now
                        </a>
                        <a href="https://wa.me/8488002969" class="btn btn-secondary"
                            style="display: inline-flex; align-items: center; gap: 8px; background: #25D366; border-color: #25D366;">
                            💬 WhatsApp
                        </a>
                    </div>
                </div>
                <div class="contact-hero-image">
                    <img src="images/hero-medical.png" alt="Friendly medical team ready to help you">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Cards Section - 3 Cards with Images -->
    <section class="contact-cards-section">
        <div class="container">
            <div class="contact-cards-grid">
                <!-- Telephone Support Card -->
                <div class="contact-card yellow">
                    <div class="contact-card-image">
                        <img src="images/phone-support.png" alt="Telephone Support">
                    </div>
                    <div class="contact-card-content">
                        <h3>Telephone Support</h3>
                        <p>Call us 24/7 available, our representatives will help you make an appointment that's
                            convenient for you.</p>
                        <a href="tel:+918488002969" class="contact-card-btn">Call Support: +91 84880 02969</a>
                    </div>
                </div>

                <!-- Virtual Visits Card -->
                <div class="contact-card teal">
                    <div class="contact-card-image">
                        <img src="images/virtual-visit.png" alt="Virtual Visits">
                    </div>
                    <div class="contact-card-content">
                        <h3>Virtual Visits</h3>
                        <p>You can have your appointment without leaving your home. It's the easiest way to get the care
                            you need to stay healthy.</p>
                        <a href="patient-register.php" class="contact-card-btn">Read More Virtual Visits →</a>
                    </div>
                </div>

                <!-- Book Appointment Card -->
                <div class="contact-card grey">
                    <div class="contact-card-image">
                        <img src="images/book-appointment.png" alt="Book Appointment">
                    </div>
                    <div class="contact-card-content">
                        <h3>Book An Appointment</h3>
                        <p>Make an appointment with us at the nearest facility to directly examine your health.</p>
                        <a href="patient-register.php" class="contact-card-btn">Book An Appointment →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form & Info -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-card slide-in-left">
                    <h2>Send Us a <span class="text-gradient">Message</span></h2>
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" class="form-control" placeholder="Enter your name" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" class="form-control" placeholder="+91 84880 02969">
                        </div>
                        <div class="form-group">
                            <label for="subject" class="form-label">Subject</label>
                            <select id="subject" class="form-control" required>
                                <option value="">Select a subject</option>
                                <option value="appointment">Book Appointment</option>
                                <option value="inquiry">General Inquiry</option>
                                <option value="feedback">Feedback</option>
                                <option value="complaint">Complaint</option>
                                <option value="partnership">Partnership</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message" class="form-label">Message</label>
                            <textarea id="message" class="form-control" rows="5" placeholder="How can we help you?"
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Send Message</button>
                    </form>
                    <div class="success-message" id="successMessage">
                        ✅ Thank you! Your message has been sent successfully. We'll get back to you within 24 hours.
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="contact-info slide-in-right">
                    <div class="info-card">
                        <div class="info-icon">🏥</div>
                        <div class="info-content">
                            <h3>Main Clinic Address</h3>
                            <p>SUDAMA CLINIC Healthcare Center<br>203-205, Paramount Tower<br>Off S.V. Road, Andheri
                                West<br>Mumbai, Maharashtra 400053</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">📞</div>
                        <div class="info-content">
                            <h3>Phone & WhatsApp</h3>
                            <p><strong>Reception:</strong> <a href="tel:+918488002969">+91 84880 02969</a><br>
                                <strong>Appointments:</strong> <a href="tel:+912226730002">+91 22 2673 0002</a><br>
                                <strong>WhatsApp:</strong> <a href="https://wa.me/918488002969">+91 84880 02969</a>
                            </p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">📧</div>
                        <div class="info-content">
                            <h3>Email Us</h3>
                            <p><strong>General:</strong> <a
                                    href="mailto:info@sudamaclinic.in">info@sudamaclinic.in</a><br>
                                <strong>Appointments:</strong> <a
                                    href="mailto:book@sudamaclinic.in">book@sudamaclinic.in</a><br>
                                <strong>Careers:</strong> <a
                                    href="mailto:careers@sudamaclinic.in">careers@sudamaclinic.in</a>
                            </p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-icon">🌐</div>
                        <div class="info-content">
                            <h3>Connect With Us</h3>
                            <p><strong>Website:</strong> www.sudamaclinic.in<br>
                                <strong>Facebook:</strong> fb.com/SudamaClinicIndia<br>
                                <strong>Instagram:</strong> @sudamaclinic.in<br>
                                <strong>LinkedIn:</strong> SUDAMA CLINIC Healthcare
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Find Us on <span class="text-gradient">Map</span></h2>
                <p class="section-subtitle">Visit our clinic for personalized healthcare services</p>
            </div>
            <div class="map-container mt-4" style="position: relative;">
                <a href="https://share.google/M70BPckMNnPMBDNcz" target="_blank" style="position: absolute; top:0; left:0; width:100%; height:100%; z-index: 10;"></a>
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3769.5893848476184!2d72.8296068!3d19.1362215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7b63aceef0c69%3A0x2aa80cf2287dfa3b!2sAndheri%20West%2C%20Mumbai%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1706097600000!5m2!1sen!2sin"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="opacity: 0.6; filter: grayscale(100%);">
                </iframe>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 20; text-align: center; pointer-events: none;">
                    <div style="background: white; padding: 20px 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                        <div style="font-size: 2.5rem; margin-bottom: 10px;">📍</div>
                        <h3 style="color: #0f172a; margin-bottom: 8px;">View Our Location</h3>
                        <div style="display: inline-block; padding: 10px 24px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; border-radius: 8px; font-weight: 600; box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);">
                            Open in Google Maps
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Hours Section -->
    <section class="hours-section">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Clinic <span class="text-gradient">Hours</span></h2>
                <p class="section-subtitle">We're here when you need us</p>
            </div>
            <div class="hours-grid">
                <div class="hours-card">
                    <div class="hours-icon">🏥</div>
                    <h3>OPD Timings</h3>
                    <ul class="hours-list">
                        <li><span class="day">Monday - Friday</span><span>9:00 AM - 8:00 PM</span></li>
                        <li><span class="day">Saturday</span><span>9:00 AM - 5:00 PM</span></li>
                        <li><span class="day">Sunday</span><span>10:00 AM - 2:00 PM</span></li>
                    </ul>
                </div>
                <div class="hours-card">
                    <div class="hours-icon">🧪</div>
                    <h3>Lab & Diagnostics</h3>
                    <ul class="hours-list">
                        <li><span class="day">Monday - Saturday</span><span>7:00 AM - 9:00 PM</span></li>
                        <li><span class="day">Sunday</span><span>8:00 AM - 1:00 PM</span></li>
                        <li><span class="day">Reports Collection</span><span>24/7 Online</span></li>
                    </ul>
                </div>
                <div class="hours-card emergency-card">
                    <div class="hours-icon">🚑</div>
                    <h3>Emergency Services</h3>
                    <p>For medical emergencies, our on-call physicians are available around the clock. We coordinate
                        with nearby hospitals for critical care.</p>
                    <div class="emergency-number">24/7</div>
                    <p style="margin-top: var(--spacing-md);">Emergency Hotline: <strong style="color: #ef4444;">+91 22
                            2673 0000</strong></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dark Professional Footer -->
    <footer
        style="background: linear-gradient(180deg, #0f172a 0%, #020617 100%); color: #e2e8f0; padding: 80px 20px 0;">
        <div style="max-width: 1400px; margin: 0 auto;">
            <div
                style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1.5fr; gap: 50px; padding-bottom: 60px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                        <img src="images/logo.png" alt="SUDAMA CLINIC" style="height: 48px; filter: brightness(1.2);">
                        <span
                            style="font-size: 1.75rem; font-weight: 800; background: linear-gradient(135deg, #06b6d4, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SUDAMA
                            CLINIC</span>
                    </div>
                    <p style="color: #94a3b8; line-height: 1.8; margin-bottom: 24px;">Your trusted healthcare partner.
                        Modern medical services with digital convenience.</p>
                    <div style="display: flex; gap: 12px;">
                        <a href="#"
                            style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #e2e8f0; text-decoration: none;">📘</a>
                        <a href="#"
                            style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #e2e8f0; text-decoration: none;">🐦</a>
                        <a href="#"
                            style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #e2e8f0; text-decoration: none;">📷</a>
                        <a href="#"
                            style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #e2e8f0; text-decoration: none;">💼</a>
                    </div>
                </div>
                <div>
                    <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 24px; color: white;">Quick Links</h4>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 14px;"><a href="index.php"
                                style="color: #94a3b8; text-decoration: none;">Home</a></li>
                        <li style="margin-bottom: 14px;"><a href="about.php"
                                style="color: #94a3b8; text-decoration: none;">About Us</a></li>
                        <li style="margin-bottom: 14px;"><a href="contact.php"
                                style="color: #94a3b8; text-decoration: none;">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 24px; color: white;">Portals</h4>
                        <li style="margin-bottom: 14px;"><a href="doctor-login.php"
                                style="color: #94a3b8; text-decoration: none;">Doctor Portal</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 24px; color: white;">Support</h4>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 14px;"><a href="#" style="color: #94a3b8; text-decoration: none;">Help
                                Center</a></li>
                        <li style="margin-bottom: 14px;"><a href="#"
                                style="color: #94a3b8; text-decoration: none;">FAQs</a></li>
                        <li style="margin-bottom: 14px;"><a href="#"
                                style="color: #94a3b8; text-decoration: none;">Privacy Policy</a></li>
                        <li style="margin-bottom: 14px;"><a href="#"
                                style="color: #94a3b8; text-decoration: none;">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 24px; color: white;">Contact Us</h4>
                    <div style="margin-bottom: 16px; display: flex; align-items: flex-start; gap: 12px;">
                        <span style="font-size: 1.25rem;">🏥</span>
                        <span style="color: #94a3b8;">203-205 Paramount Tower,<br>Andheri West, Mumbai 400053</span>
                    </div>
                    <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 1.25rem;">📞</span>
                        <span style="color: #94a3b8;">+91 84880 02969</span>
                    </div>
                    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 1.25rem;">📧</span>
                        <span style="color: #94a3b8;">info@sudamaclinic.in</span>
                    </div>
                    <h5 style="font-size: 0.875rem; margin-bottom: 12px; color: white;">Subscribe to Newsletter</h5>
                    <div style="display: flex; gap: 8px;">
                        <input type="email" placeholder="Your email"
                            style="flex: 1; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; background: rgba(255,255,255,0.05); color: white; outline: none;">
                        <button
                            style="padding: 12px 20px; background: linear-gradient(135deg, #06b6d4, #0891b2); border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer;">→</button>
                    </div>
                </div>
            </div>
            <div
                style="padding: 30px 0; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
                <p style="color: #64748b; font-size: 0.875rem;">© 2026 SUDAMA CLINIC. All rights reserved. Made with ❤️
                    for better healthcare.</p>
                <div style="display: flex; gap: 24px;">
                    <a href="#" style="color: #64748b; font-size: 0.875rem; text-decoration: none;">Privacy</a>
                    <a href="#" style="color: #64748b; font-size: 0.875rem; text-decoration: none;">Terms</a>
                    <a href="#" style="color: #64748b; font-size: 0.875rem; text-decoration: none;">Cookies</a>
                </div>
            </div>
        </div>
        <style>
            @media (max-width: 1200px) {
                footer>div>div:first-child {
                    grid-template-columns: 1fr 1fr 1fr !important;
                }

                footer>div>div:first-child>div:first-child {
                    grid-column: 1 / -1;
                }

                footer>div>div:first-child>div:last-child {
                    grid-column: 1 / -1;
                }
            }

            @media (max-width: 768px) {
                footer>div>div:first-child {
                    grid-template-columns: 1fr 1fr !important;
                }
            }

            @media (max-width: 480px) {
                footer>div>div:first-child {
                    grid-template-columns: 1fr !important;
                }
            }
        </style>
    </footer>

    <script src="js/main.js"></script>
    <script>
        // Contact Form Submission
        document.getElementById('contactForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Show success message
            document.getElementById('successMessage').classList.add('show');

            // Reset form
            this.reset();

            // Hide success message after 5 seconds
            setTimeout(() => {
                document.getElementById('successMessage').classList.remove('show');
            }, 5000);
        });
    </script>
</body>

</html>

