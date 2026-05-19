<?php 
require_once 'includes/functions.php';

$db = get_db_connection();
$query = "SELECT d.*, s.name as specialization, s.icon as specialty_icon
          FROM doctors d
          JOIN users u ON d.user_id = u.id
          LEFT JOIN specializations s ON d.specialization_id = s.id
          WHERE u.status = 'active'
          ORDER BY d.experience_years DESC
          LIMIT 4";
$stmt = $db->prepare($query);
$stmt->execute();
$doctors = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - SUDAMA CLINIC</title>
    <meta name="description"
        content="Learn about SUDAMA CLINIC, our mission, values, and the expert team behind your healthcare.">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/landing.css">
    <style>
        .about-hero {
            padding: 120px 0 60px;
            text-align: center;
        }

        .about-hero h1 {
            font-size: var(--font-size-5xl);
            margin-bottom: var(--spacing-lg);
        }

        .about-hero p {
            font-size: var(--font-size-xl);
            color: var(--color-text-secondary);
            max-width: 700px;
            margin: 0 auto;
        }

        .about-section {
            padding: var(--spacing-3xl) 0;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-3xl);
            align-items: center;
        }

        .about-image img {
            width: 100%;
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-xl);
        }

        .about-content h2 {
            font-size: var(--font-size-3xl);
            margin-bottom: var(--spacing-lg);
        }

        .about-content p {
            margin-bottom: var(--spacing-md);
            line-height: 1.8;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: var(--spacing-xl);
            margin-top: var(--spacing-2xl);
        }

        .value-card {
            text-align: center;
            padding: var(--spacing-2xl);
            background: var(--color-bg-secondary);
            border-radius: var(--border-radius-lg);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-md);
            transition: all var(--transition-spring);
        }

        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-light);
        }

        .value-icon {
            font-size: 4rem;
            margin-bottom: var(--spacing-lg);
        }

        .value-card h3 {
            font-size: var(--font-size-xl);
            margin-bottom: var(--spacing-md);
        }

        .team-section {
            background: linear-gradient(180deg, transparent, rgba(6, 182, 212, 0.03), transparent);
            padding: var(--spacing-3xl) 0;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-xl);
            margin-top: var(--spacing-2xl);
        }

        .team-card {
            text-align: center;
            padding: var(--spacing-xl);
            background: var(--color-bg-secondary);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            transition: all var(--transition-spring);
        }

        .team-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .team-avatar {
            font-size: 5rem;
            margin-bottom: var(--spacing-md);
        }

        .team-card h4 {
            font-size: var(--font-size-lg);
            margin-bottom: var(--spacing-xs);
        }

        .team-role {
            color: var(--primary-start);
            font-weight: 600;
            font-size: var(--font-size-sm);
            margin-bottom: var(--spacing-sm);
        }

        .stats-section {
            padding: var(--spacing-3xl) 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: var(--spacing-xl);
            text-align: center;
        }

        .stat-box {
            padding: var(--spacing-xl);
        }

        .stat-box .stat-number {
            font-size: var(--font-size-4xl);
            font-weight: 800;
            margin-bottom: var(--spacing-sm);
        }

        .stat-box .stat-label {
            font-size: var(--font-size-base);
            color: var(--color-text-secondary);
        }

        @media (max-width: 768px) {
            .about-grid {
                grid-template-columns: 1fr;
            }

            .about-image {
                order: -1;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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
                    style="padding: 10px 18px; color: white; text-decoration: none; border-radius: 8px; background: #06b6d4; font-weight: 600; font-size: 0.9rem;">About</a>
                <a href="contact.php"
                    style="padding: 10px 18px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-weight: 500; font-size: 0.9rem;"
                    onmouseover="this.style.color='#06b6d4'; this.style.background='rgba(6,182,212,0.1)'"
                    onmouseout="this.style.color='#94a3b8'; this.style.background='transparent'">Contact</a>
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

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <h1>About <span class="text-gradient">SUDAMA CLINIC</span></h1>
            <p>India's trusted multi-specialty healthcare destination, serving over 5,000 patients annually with 15+
                medical specializations, cutting-edge diagnostics, and a patient-first digital experience since 2020.
            </p>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-image slide-in-left">
                    <img src="images/clinic-building.png" alt="SUDAMA CLINIC Building - Andheri West, Mumbai"
                        style="border-radius: var(--border-radius-xl); box-shadow: var(--shadow-xl);">
                </div>
                <div class="about-content slide-in-right">
                    <h2>Our <span class="text-gradient">Mission</span></h2>
                    <p>At SUDAMA CLINIC, we envision a healthcare ecosystem where every patient receives timely,
                        personalized, and affordable medical care. Our mission is to leverage technology to eliminate
                        barriers between patients and quality healthcare.</p>
                    <p>Founded in Mumbai in 2020, SUDAMA CLINIC has grown from a single-specialty practice to a
                        comprehensive multi-specialty healthcare center. Our integrated digital platform enables
                        patients to book appointments online, access lab reports digitally, receive e-prescriptions, and
                        consult with specialists—all from the convenience of their devices.</p>
                    <p>With a team of 15+ specialist doctors across General Medicine, Cardiology, Neurology,
                        Orthopedics, Pediatrics, Dermatology, Gynecology, ENT, Ophthalmology, Psychiatry, Dentistry,
                        Oncology, Gastroenterology, Nephrology, and Pulmonology—we offer complete healthcare under one
                        roof.</p>
                    <p><strong>NABH Accreditation Applied</strong> | <strong>ISO 9001:2015 Quality Standards</strong> |
                        <strong>HIPAA Compliant Digital Records</strong>
                    </p>
                    <a href="contact.php" class="btn btn-primary btn-lg mt-4">Schedule a Visit</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="about-section">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Our Core <span class="text-gradient">Values</span></h2>
                <p class="section-subtitle">The principles that guide everything we do</p>
            </div>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">❤️</div>
                    <h3>Compassionate Care</h3>
                    <p>We treat every patient with empathy, respect, and understanding, ensuring they feel heard and
                        valued.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🔬</div>
                    <h3>Innovation</h3>
                    <p>We continuously embrace new technologies and methods to improve healthcare delivery and patient
                        outcomes.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3>Trust & Integrity</h3>
                    <p>We maintain the highest ethical standards in all our interactions, protecting patient privacy and
                        data.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">⭐</div>
                    <h3>Excellence</h3>
                    <p>We strive for excellence in every aspect of our service, from medical care to user experience.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🌍</div>
                    <h3>Accessibility</h3>
                    <p>We believe quality healthcare should be available to everyone, removing barriers through
                        technology and inclusive services.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">👥</div>
                    <h3>Teamwork</h3>
                    <p>We collaborate across departments to provide coordinated care, ensuring every patient receives
                        comprehensive treatment.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Facilities -->
    <section class="about-section"
        style="background: linear-gradient(180deg, transparent, rgba(6, 182, 212, 0.03), transparent);">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Our <span class="text-gradient">Facilities</span></h2>
                <p class="section-subtitle">State-of-the-art infrastructure for comprehensive healthcare</p>
            </div>
            <div class="about-grid" style="margin-top: var(--spacing-2xl);">
                <div class="about-content">
                    <h3 style="font-size: var(--font-size-2xl); margin-bottom: var(--spacing-lg);">Modern <span
                            class="text-gradient">Reception & Waiting Area</span></h3>
                    <p>Our welcoming reception area features comfortable seating, digital check-in kiosks, and real-time
                        appointment displays. Enjoy complimentary Wi-Fi while you wait in our air-conditioned, sanitized
                        environment.</p>
                    <ul style="list-style: none; padding: 0; margin: var(--spacing-lg) 0;">
                        <li style="padding: var(--spacing-sm) 0;">✓ Digital queue management system</li>
                        <li style="padding: var(--spacing-sm) 0;">✓ Wheelchair accessible throughout</li>
                        <li style="padding: var(--spacing-sm) 0;">✓ Separate pediatric waiting area</li>
                        <li style="padding: var(--spacing-sm) 0;">✓ In-house pharmacy & lab collection</li>
                        <li style="padding: var(--spacing-sm) 0;">✓ Cafeteria with healthy options</li>
                    </ul>
                </div>
                <div class="about-image">
                    <img src="images/reception-area.png" alt="SUDAMA CLINIC Reception Area"
                        style="border-radius: var(--border-radius-xl); box-shadow: var(--shadow-xl); width: 100%;">
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-number text-gradient">5,200+</div>
                    <div class="stat-label">Patients Served Annually</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number text-gradient">15</div>
                    <div class="stat-label">Specialist Doctors</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number text-gradient">15</div>
                    <div class="stat-label">Medical Specialties</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number text-gradient">4.8★</div>
                    <div class="stat-label">Google Rating (500+ reviews)</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Meet Our <span class="text-gradient">Leadership</span></h2>
                <p class="section-subtitle">The dedicated team behind SUDAMA CLINIC</p>
            </div>
            <!-- Team Photo -->
            <div style="text-align: center; margin: var(--spacing-2xl) 0;">
                <img src="images/doctors-team.png" alt="SUDAMA CLINIC Medical Team"
                    style="max-width: 800px; width: 100%; border-radius: var(--border-radius-xl); box-shadow: var(--shadow-xl);">
                <p style="color: var(--color-text-secondary); margin-top: var(--spacing-md); font-style: italic;">Our
                    expert team of specialists - united by a commitment to your health</p>
            </div>
            <div class="team-grid">
                <?php foreach ($doctors as $doc): ?>
                    <div class="team-card">
                        <div class="team-avatar">
                            👨‍⚕️
                        </div>
                        <?php 
                            // Remove existing "Dr." or "Dr " prefix if present in the database name
                            $cleanName = preg_replace('/^Dr\.?\s+/i', '', trim($doc['name']));
                        ?>
                        <h4>Dr. <?php echo htmlspecialchars($cleanName); ?></h4>
                        <p class="team-role"><?php echo htmlspecialchars($doc['specialization'] ?? 'General Medicine'); ?></p>
                        <p><?php echo htmlspecialchars($doc['qualification']); ?> | <?php echo htmlspecialchars($doc['experience_years']); ?>+ years experience</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="about-section text-center">
        <div class="container">
            <h2>Ready to Experience <span class="text-gradient">Better Healthcare?</span></h2>
            <p class="section-subtitle mb-4">Join thousands of patients who trust SUDAMA CLINIC for their healthcare
                needs</p>
            <div class="hero-buttons" style="justify-content: center;">
                <a href="patient-register.php" class="btn btn-primary btn-lg">Get Started</a>
                <a href="contact.php" class="btn btn-outline btn-lg">Contact Us</a>
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
</body>

</html>

