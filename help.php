<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Information - SUDAMA CLINIC</title>
    <meta name="description" content="Help & Information regarding SUDAMA CLINIC services.">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/landing.css">
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
                    style="padding: 10px 18px; color: white; text-decoration: none; border-radius: 8px; background: #06b6d4; font-weight: 600; font-size: 0.9rem;">Home</a>
                <a href="about.php"
                    style="padding: 10px 18px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-weight: 500; font-size: 0.9rem;"
                    onmouseover="this.style.color='#06b6d4'; this.style.background='rgba(6,182,212,0.1)'"
                    onmouseout="this.style.color='#94a3b8'; this.style.background='transparent'">About</a>
                <a href="contact.php"
                    style="padding: 10px 18px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-weight: 500; font-size: 0.9rem;"
                    onmouseover="this.style.color='#06b6d4'; this.style.background='rgba(6,182,212,0.1)'"
                    onmouseout="this.style.color='#94a3b8'; this.style.background='transparent'">Contact</a>
                <a href="help.php"
                    style="padding: 10px 18px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-weight: 500; font-size: 0.9rem;"
                    onmouseover="this.style.color='#06b6d4'; this.style.background='rgba(6,182,212,0.1)'"
                    onmouseout="this.style.color='#94a3b8'; this.style.background='transparent'">Help</a>
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

    <section style="padding: 120px 20px 60px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); text-align: center;">
        <h1 style="font-size: 3rem; font-weight: 800; color: white; margin-bottom: 16px;">Help & <span style="color: #06b6d4;">Information</span></h1>
        <p style="color: #94a3b8; font-size: 1.125rem; max-width: 600px; margin: 0 auto;">Everything you need to know about our healthcare services.</p>
    </section>
        <!-- App Features Carousel -->
        <section
            style="padding: 80px 0; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); overflow: hidden; width: 100%;">
            <div style="max-width: 100%; padding: 0 40px;">
                <div style="text-align: center; margin-bottom: 50px;">
                    <span
                        style="display: inline-block; padding: 8px 20px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 50px; color: #10b981; font-weight: 600; font-size: 0.875rem; margin-bottom: 16px;">✨
                        Why Choose Us</span>
                    <h2 style="font-size: 2.5rem; font-weight: 800; color: white; margin-bottom: 16px;">Experience <span
                            style="background: linear-gradient(135deg, #06b6d4, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">World-Class
                            Healthcare</span></h2>
                </div>

                <!-- Carousel Container -->
                <div style="position: relative; padding: 0 60px;">
                    <button onclick="moveCarousel(-1)"
                        style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; border-radius: 50%; background: rgba(6, 182, 212, 0.2); border: 1px solid rgba(6, 182, 212, 0.4); color: white; font-size: 1.5rem; cursor: pointer; transition: all 0.3s; z-index: 10;"
                        onmouseover="this.style.background='rgba(6, 182, 212, 0.4)'"
                        onmouseout="this.style.background='rgba(6, 182, 212, 0.2)'">‹</button>

                    <div id="carousel" style="display: flex; gap: 24px; overflow: hidden; scroll-behavior: smooth;">
                        <!-- Slide 1 -->
                        <div
                            style="min-width: calc(33.333% - 16px); border-radius: 20px; overflow: hidden; flex-shrink: 0; position: relative;">
                            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=500&auto=format&fit=crop"
                                alt="Online Booking" style="width: 100%; height: 280px; object-fit: cover;">
                            <div
                                style="position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);">
                                <h3 style="color: white; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">📱
                                    Book From Anywhere</h3>
                                <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6;">Schedule appointments
                                    24/7 from any device</p>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div
                            style="min-width: calc(33.333% - 16px); border-radius: 20px; overflow: hidden; flex-shrink: 0; position: relative;">
                            <img src="https://images.unsplash.com/photo-1666214280557-f1b5022eb634?w=500&auto=format&fit=crop"
                                alt="Zero Wait Time" style="width: 100%; height: 280px; object-fit: cover;">
                            <div
                                style="position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);">
                                <h3 style="color: white; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">⏰
                                    Zero Wait Time</h3>
                                <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6;">Real-time availability,
                                    see your doctor instantly</p>
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div
                            style="min-width: calc(33.333% - 16px); border-radius: 20px; overflow: hidden; flex-shrink: 0; position: relative;">
                            <img src="https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=500&auto=format&fit=crop"
                                alt="Digital Prescriptions" style="width: 100%; height: 280px; object-fit: cover;">
                            <div
                                style="position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);">
                                <h3 style="color: white; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">📋
                                    Digital Prescriptions</h3>
                                <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6;">Access prescriptions
                                    anytime, share with pharmacies</p>
                            </div>
                        </div>

                        <!-- Slide 4 -->
                        <div
                            style="min-width: calc(33.333% - 16px); border-radius: 20px; overflow: hidden; flex-shrink: 0; position: relative;">
                            <img src="https://images.unsplash.com/photo-1551076805-e1869033e561?w=500&auto=format&fit=crop"
                                alt="Health Analytics" style="width: 100%; height: 280px; object-fit: cover;">
                            <div
                                style="position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);">
                                <h3 style="color: white; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">📊
                                    Health Analytics</h3>
                                <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6;">Track trends and make
                                    informed health decisions</p>
                            </div>
                        </div>

                        <!-- Slide 5 -->
                        <div
                            style="min-width: calc(33.333% - 16px); border-radius: 20px; overflow: hidden; flex-shrink: 0; position: relative;">
                            <img src="https://images.unsplash.com/photo-1516549655169-df83a0774514?w=500&auto=format&fit=crop"
                                alt="Emergency Support" style="width: 100%; height: 280px; object-fit: cover;">
                            <div
                                style="position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);">
                                <h3 style="color: white; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">🚨
                                    24/7 Emergency</h3>
                                <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6;">Immediate response when
                                    every second counts</p>
                            </div>
                        </div>

                        <!-- Slide 6 -->
                        <div
                            style="min-width: calc(33.333% - 16px); border-radius: 20px; overflow: hidden; flex-shrink: 0; position: relative;">
                            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=500&auto=format&fit=crop"
                                alt="Doctor Messaging" style="width: 100%; height: 280px; object-fit: cover;">
                            <div
                                style="position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);">
                                <h3 style="color: white; font-size: 1.4rem; font-weight: 700; margin-bottom: 8px;">💬
                                    Doctor Messaging</h3>
                                <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6;">Secure follow-up
                                    consultations with your doctor</p>
                            </div>
                        </div>
                    </div>

                    <button onclick="moveCarousel(1)"
                        style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; border-radius: 50%; background: rgba(6, 182, 212, 0.2); border: 1px solid rgba(6, 182, 212, 0.4); color: white; font-size: 1.5rem; cursor: pointer; transition: all 0.3s; z-index: 10;"
                        onmouseover="this.style.background='rgba(6, 182, 212, 0.4)'"
                        onmouseout="this.style.background='rgba(6, 182, 212, 0.2)'">›</button>
                </div>

                <!-- Carousel Indicators -->
                <div style="display: flex; justify-content: center; gap: 12px; margin-top: 40px;">
                    <button onclick="scrollToSlide(0)"
                        style="width: 12px; height: 12px; border-radius: 50%; background: #06b6d4; border: none; cursor: pointer; transition: all 0.3s;"></button>
                    <button onclick="scrollToSlide(1)"
                        style="width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.3); border: none; cursor: pointer; transition: all 0.3s;"></button>
                    <button onclick="scrollToSlide(2)"
                        style="width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.3); border: none; cursor: pointer; transition: all 0.3s;"></button>
                </div>
            </div>

            <script>
                let currentSlide = 0;
                function moveCarousel(direction) {
                    const carousel = document.getElementById('carousel');
                    const slideWidth = carousel.children[0].offsetWidth + 24;
                    currentSlide = Math.max(0, Math.min(currentSlide + direction, 3));
                    carousel.scrollLeft = currentSlide * slideWidth;
                }
                function scrollToSlide(index) {
                    const carousel = document.getElementById('carousel');
                    const slideWidth = carousel.children[0].offsetWidth + 24;
                    currentSlide = index;
                    carousel.scrollLeft = index * slideWidth;
                }
                // Auto-scroll every 4 seconds
                setInterval(() => {
                    currentSlide = (currentSlide + 1) % 4;
                    const carousel = document.getElementById('carousel');
                    const slideWidth = carousel.children[0].offsetWidth + 24;
                    carousel.scrollLeft = currentSlide * slideWidth;
                }, 4000);
            </script>
        </section>

        <!-- Enhanced FAQ Section -->
        <section
            style="padding: 100px 20px; background: linear-gradient(180deg, #f8fafc 0%, #e0f2fe 50%, #f8fafc 100%);">
            <div style="max-width: 1200px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 60px;">
                    <span
                        style="display: inline-block; padding: 8px 20px; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: 50px; color: #0891b2; font-weight: 600; font-size: 0.875rem; margin-bottom: 16px;">💡
                        Get Answers</span>
                    <h2 style="font-size: 3rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">Frequently Asked
                        <span
                            style="background: linear-gradient(135deg, #06b6d4, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Questions</span>
                    </h2>
                    <p style="color: #64748b; font-size: 1.125rem; max-width: 600px; margin: 0 auto;">Everything you
                        need to know about our healthcare services</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <!-- FAQ Column 1 -->
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <!-- FAQ 1 -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s;"
                            onmouseover="this.style.boxShadow='0 15px 50px rgba(6,182,212,0.12)'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.boxShadow='0 10px 40px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)'">
                            <div style="padding: 28px 32px; cursor: pointer; display: flex; gap: 16px; align-items: flex-start;"
                                onclick="const ans=this.nextElementSibling; ans.style.maxHeight = ans.style.maxHeight ? null : '300px'; this.querySelector('.faq-icon').textContent = ans.style.maxHeight ? '−' : '+'">
                                <div
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #06b6d4, #0891b2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; flex-shrink: 0;">
                                    🏥</div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">What
                                        specialties are available at SUDAMA CLINIC?</h4>
                                </div>
                                <span class="faq-icon"
                                    style="font-size: 1.5rem; color: #06b6d4; font-weight: 300; transition: all 0.3s;">+</span>
                            </div>
                            <div
                                style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; background: #f8fafc;">
                                <p style="padding: 0 32px 28px 96px; color: #64748b; line-height: 1.8; margin: 0;">We
                                    offer 15+ specialties including Cardiology, Orthopedics, Dental, Dermatology,
                                    Pediatrics, Gynecology, Neurology, General Surgery, and more. All doctors are
                                    board-certified with years of experience.</p>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s;"
                            onmouseover="this.style.boxShadow='0 15px 50px rgba(6,182,212,0.12)'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.boxShadow='0 10px 40px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)'">
                            <div style="padding: 28px 32px; cursor: pointer; display: flex; gap: 16px; align-items: flex-start;"
                                onclick="const ans=this.nextElementSibling; ans.style.maxHeight = ans.style.maxHeight ? null : '300px'; this.querySelector('.faq-icon').textContent = ans.style.maxHeight ? '−' : '+'">
                                <div
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; flex-shrink: 0;">
                                    📅</div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">How do I
                                        book an appointment online?</h4>
                                </div>
                                <span class="faq-icon"
                                    style="font-size: 1.5rem; color: #06b6d4; font-weight: 300; transition: all 0.3s;">+</span>
                            </div>
                            <div
                                style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; background: #f8fafc;">
                                <p style="padding: 0 32px 28px 96px; color: #64748b; line-height: 1.8; margin: 0;">
                                    Create a free account, select your specialty, choose a doctor, pick an available
                                    time slot, and confirm. You'll receive instant SMS & email confirmation. It takes
                                    less than 2 minutes!</p>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s;"
                            onmouseover="this.style.boxShadow='0 15px 50px rgba(6,182,212,0.12)'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.boxShadow='0 10px 40px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)'">
                            <div style="padding: 28px 32px; cursor: pointer; display: flex; gap: 16px; align-items: flex-start;"
                                onclick="const ans=this.nextElementSibling; ans.style.maxHeight = ans.style.maxHeight ? null : '300px'; this.querySelector('.faq-icon').textContent = ans.style.maxHeight ? '−' : '+'">
                                <div
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; flex-shrink: 0;">
                                    🚨</div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">Are
                                        emergency services available 24/7?</h4>
                                </div>
                                <span class="faq-icon"
                                    style="font-size: 1.5rem; color: #06b6d4; font-weight: 300; transition: all 0.3s;">+</span>
                            </div>
                            <div
                                style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; background: #f8fafc;">
                                <p style="padding: 0 32px 28px 96px; color: #64748b; line-height: 1.8; margin: 0;">Yes!
                                    Our emergency department operates 24/7, 365 days a year. For medical emergencies,
                                    call our hotline or walk in anytime. Ambulance services are also available.</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Column 2 -->
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <!-- FAQ 4 -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s;"
                            onmouseover="this.style.boxShadow='0 15px 50px rgba(6,182,212,0.12)'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.boxShadow='0 10px 40px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)'">
                            <div style="padding: 28px 32px; cursor: pointer; display: flex; gap: 16px; align-items: flex-start;"
                                onclick="const ans=this.nextElementSibling; ans.style.maxHeight = ans.style.maxHeight ? null : '300px'; this.querySelector('.faq-icon').textContent = ans.style.maxHeight ? '−' : '+'">
                                <div
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; flex-shrink: 0;">
                                    📋</div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">How do I
                                        access my medical records?</h4>
                                </div>
                                <span class="faq-icon"
                                    style="font-size: 1.5rem; color: #06b6d4; font-weight: 300; transition: all 0.3s;">+</span>
                            </div>
                            <div
                                style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; background: #f8fafc;">
                                <p style="padding: 0 32px 28px 96px; color: #64748b; line-height: 1.8; margin: 0;">Login
                                    to your patient portal and navigate to "Medical Records". View your complete
                                    history, prescriptions, test results, and visit summaries. Download or share
                                    securely anytime.</p>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s;"
                            onmouseover="this.style.boxShadow='0 15px 50px rgba(6,182,212,0.12)'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.boxShadow='0 10px 40px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)'">
                            <div style="padding: 28px 32px; cursor: pointer; display: flex; gap: 16px; align-items: flex-start;"
                                onclick="const ans=this.nextElementSibling; ans.style.maxHeight = ans.style.maxHeight ? null : '300px'; this.querySelector('.faq-icon').textContent = ans.style.maxHeight ? '−' : '+'">
                                <div
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; flex-shrink: 0;">
                                    💳</div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">What
                                        payment methods are accepted?</h4>
                                </div>
                                <span class="faq-icon"
                                    style="font-size: 1.5rem; color: #06b6d4; font-weight: 300; transition: all 0.3s;">+</span>
                            </div>
                            <div
                                style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; background: #f8fafc;">
                                <p style="padding: 0 32px 28px 96px; color: #64748b; line-height: 1.8; margin: 0;">We
                                    accept Cash, Credit/Debit Cards, UPI (GPay, PhonePe, Paytm), Net Banking, and health
                                    insurance. Pay online or at the clinic. EMI options available for major treatments.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div style="background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s;"
                            onmouseover="this.style.boxShadow='0 15px 50px rgba(6,182,212,0.12)'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.boxShadow='0 10px 40px rgba(0,0,0,0.06)'; this.style.transform='translateY(0)'">
                            <div style="padding: 28px 32px; cursor: pointer; display: flex; gap: 16px; align-items: flex-start;"
                                onclick="const ans=this.nextElementSibling; ans.style.maxHeight = ans.style.maxHeight ? null : '300px'; this.querySelector('.faq-icon').textContent = ans.style.maxHeight ? '−' : '+'">
                                <div
                                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #ec4899, #db2777); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; flex-shrink: 0;">
                                    🔒</div>
                                <div style="flex: 1;">
                                    <h4 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">Is my
                                        health data secure and private?</h4>
                                </div>
                                <span class="faq-icon"
                                    style="font-size: 1.5rem; color: #06b6d4; font-weight: 300; transition: all 0.3s;">+</span>
                            </div>
                            <div
                                style="max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; background: #f8fafc;">
                                <p style="padding: 0 32px 28px 96px; color: #64748b; line-height: 1.8; margin: 0;">
                                    Absolutely! We're HIPAA compliant with 256-bit SSL encryption. Your data is stored
                                    securely and only accessible by you and authorized medical staff. We never share
                                    your information.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 50px;">
                    <p style="color: #64748b; margin-bottom: 20px; font-size: 1.1rem;">Still have questions? We're here
                        to help!</p>
                    <a href="contact.php"
                        style="display: inline-flex; align-items: center; gap: 12px; padding: 18px 40px; background: linear-gradient(135deg, #0f172a, #1e293b); color: white; text-decoration: none; border-radius: 14px; font-weight: 700; font-size: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transition: all 0.3s;"
                        onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 40px rgba(0,0,0,0.25)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.2)'">
                        📧 Contact Our Support Team
                    </a>
                </div>
            </div>
        </section>
        <!-- User Type Selection -->
        <section class="user-types" id="get-started"
            style="padding: 80px 20px; background: linear-gradient(180deg, #f8fafc 0%, #e0f2fe 50%, #f8fafc 100%);">
            <div style="max-width: 1800px; margin: 0 auto;">
                <div class="section-header text-center" style="margin-bottom: 60px;">
                    <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 16px;">
                        Choose Your <span class="text-gradient">Portal</span>
                    </h2>
                    <p style="font-size: 1.125rem; color: #64748b; max-width: 600px; margin: 0 auto;">
                        Select the portal that matches your role to access the SUDAMA CLINIC system
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; padding: 0 20px; max-width: 1000px; margin: 0 auto;">
                    <!-- Patient Portal -->
                    <div style="background: white; border-radius: 24px; padding: 40px 32px; text-align: center; border-top: 5px solid #06b6d4; box-shadow: 0 10px 40px rgba(6, 182, 212, 0.1); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; overflow: hidden;"
                        onmouseover="this.style.transform='translateY(-16px) scale(1.02)'; this.style.boxShadow='0 25px 60px rgba(6, 182, 212, 0.25)';"
                        onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 10px 40px rgba(6, 182, 212, 0.1)';">
                        <div style="font-size: 4.5rem; margin-bottom: 20px; display: block;">
                            👤</div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 20px;">Patient
                            Portal
                        </h3>
                        <ul style="list-style: none; padding: 0; margin: 0 0 28px 0; text-align: left;">
                            <li style="padding: 10px 0; color: #475569; display: flex; align-items: center; gap: 10px;">
                                <span style="color: #06b6d4; font-weight: bold;">✓</span> Book & manage appointments
                            </li>
                            <li style="padding: 10px 0; color: #475569; display: flex; align-items: center; gap: 10px;">
                                <span style="color: #06b6d4; font-weight: bold;">✓</span> Access medical records
                            </li>
                            <li style="padding: 10px 0; color: #475569; display: flex; align-items: center; gap: 10px;">
                                <span style="color: #06b6d4; font-weight: bold;">✓</span> View prescriptions
                            </li>
                            <li style="padding: 10px 0; color: #475569; display: flex; align-items: center; gap: 10px;">
                                <span style="color: #06b6d4; font-weight: bold;">✓</span> Track health history
                            </li>
                        </ul>
                        <a href="patient-register.php"
                            style="display: block; padding: 16px 32px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 1rem; transition: all 0.3s;">Patient
                            Registration</a>
                    </div>

                    <!-- Doctor Portal -->
                    <div style="background: white; border-radius: 24px; padding: 40px 32px; text-align: center; border-top: 5px solid #10b981; box-shadow: 0 10px 40px rgba(16, 185, 129, 0.1); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; overflow: hidden;"
                        onmouseover="this.style.transform='translateY(-16px) scale(1.02)'; this.style.boxShadow='0 25px 60px rgba(16, 185, 129, 0.25)';"
                        onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 10px 40px rgba(16, 185, 129, 0.1)';">
                        <div style="font-size: 4.5rem; margin-bottom: 20px; display: block;">
                            🩺</div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 20px;">Doctor
                            Portal
                        </h3>
                        <ul style="list-style: none; padding: 0; margin: 0 0 28px 0; text-align: left;">
                            <li style="padding: 10px 0; color: #475569; display: flex; align-items: center; gap: 10px;">
                                <span style="color: #10b981; font-weight: bold;">✓</span> View appointments
                            </li>
                            <li style="padding: 10px 0; color: #475569; display: flex; align-items: center; gap: 10px;">
                                <span style="color: #10b981; font-weight: bold;">✓</span> Access patient records
                            </li>
                            <li style="padding: 10px 0; color: #475569; display: flex; align-items: center; gap: 10px;">
                                <span style="color: #10b981; font-weight: bold;">✓</span> Write prescriptions
                            </li>
                            <li style="padding: 10px 0; color: #475569; display: flex; align-items: center; gap: 10px;">
                                <span style="color: #10b981; font-weight: bold;">✓</span> Send patient messages
                            </li>
                        </ul>
                        <a href="doctor-login.php"
                            style="display: block; padding: 16px 32px; background: linear-gradient(135deg, #10b981, #059669); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 1rem; transition: all 0.3s;">Doctor
                            Login</a>
                        <p style="margin-top: 16px; font-size: 0.875rem; color: #94a3b8;">For registered doctors only
                        </p>
                    </div>

                    <!-- Receptionist Portal Removed -->

                    <!-- Admin Portal Removed -->
                </div>

                <style>
                    @media (max-width: 1400px) {
                        #get-started>div>div:last-of-type {
                            grid-template-columns: repeat(2, 1fr) !important;
                            max-width: 900px !important;
                            margin: 0 auto !important;
                        }
                    }

                    @media (max-width: 768px) {
                        #get-started>div>div:last-of-type {
                            grid-template-columns: 1fr !important;
                            max-width: 450px !important;
                        }
                    }
                </style>
            </div>
        </section>

        <!-- Leading Medical Facility Section -->
        <section
            style="padding: 100px 20px; background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); margin-top: 60px;">
            <div style="max-width: 1400px; margin: 0 auto;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
                    <!-- Left Content -->
                    <div>
                        <span
                            style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: rgba(234, 179, 8, 0.15); border-radius: 50px; color: #fbbf24; font-weight: 600; font-size: 0.9rem; margin-bottom: 24px;">
                            ⚡ Your Health Is Our Top Goal
                        </span>
                        <h2
                            style="font-size: 3rem; font-weight: 800; color: white; line-height: 1.2; margin-bottom: 24px;">
                            Leading Medical Facility for<br>
                            <span style="color: #fbbf24;">you and your Family's</span>
                        </h2>
                        <p style="color: #94a3b8; font-size: 1.05rem; line-height: 1.8; margin-bottom: 32px;">
                            SUDAMA CLINICs is a leading medical facility dedicated to providing advanced, compassionate
                            healthcare for individuals and families. With a wide range of specialties, modern medical
                            technology, and personalized care, the clinic ensures a seamless experience for all
                            patients. Offering services like general surgery, cardiology, dermatology, and more, Smart
                            Clinics prioritizes the well-being of your entire family. Their accessible location, 24/7
                            emergency care, and virtual consultation options make them a reliable choice for
                            comprehensive medical needs.
                        </p>
                        <ul style="list-style: none; padding: 0; margin: 0 0 32px 0;">
                            <li
                                style="display: flex; align-items: center; gap: 12px; color: white; font-size: 1rem; padding: 8px 0;">
                                <span style="color: #fbbf24;">✓</span> Wide range of medical specialties under one roof.
                            </li>
                            <li
                                style="display: flex; align-items: center; gap: 12px; color: white; font-size: 1rem; padding: 8px 0;">
                                <span style="color: #fbbf24;">✓</span> State-of-the-art diagnostic and treatment
                                facilities.
                            </li>
                            <li
                                style="display: flex; align-items: center; gap: 12px; color: white; font-size: 1rem; padding: 8px 0;">
                                <span style="color: #fbbf24;">✓</span> Personalized care for individuals and families.
                            </li>
                        </ul>
                        <a href="about.php"
                            style="display: inline-flex; align-items: center; gap: 10px; padding: 16px 32px; background: #1e3a5f; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;"
                            onmouseover="this.style.background='#2d4a6f'" onmouseout="this.style.background='#1e3a5f'">
                            About Us →
                        </a>
                    </div>
                    <!-- Right Image with Mission Card -->
                    <div style="position: relative;">
                        <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=600&auto=format&fit=crop"
                            alt="Medical Team"
                            style="width: 100%; height: 500px; object-fit: cover; border-radius: 20px;">
                        <!-- Mission Card -->
                        <div
                            style="position: absolute; bottom: -30px; left: -30px; background: #fbbf24; padding: 28px 32px; border-radius: 16px; max-width: 380px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                <span style="color: #0f172a; font-size: 1.25rem;">⚡</span>
                                <h4 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">Our Mission
                                </h4>
                            </div>
                            <p style="color: #1e3a5f; font-size: 0.95rem; line-height: 1.7; margin: 0;">
                                At SUDAMA CLINICs, our mission is to deliver exceptional healthcare services tailored to
                                the unique needs of every patient. We strive to combine compassion, innovation, and
                                expertise to promote the well-being of individuals and families.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trusted Medical Professionals Section -->
        <section style="margin-top: 80px;">
            <!-- Top Banner with Background Image -->
            <div style="position: relative; height: 300px; overflow: hidden;">
                <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=1400&auto=format&fit=crop"
                    alt="Medical Professionals"
                    style="width: 100%; height: 100%; object-fit: cover; filter: blur(2px);">
                <div
                    style="position: absolute; inset: 0; background: rgba(30, 58, 95, 0.75); display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px;">
                    <p style="color: #94a3b8; font-size: 1.1rem; margin-bottom: 16px;">Advanced medical services across
                        gynecology, cardiology, dermatology, and more.</p>
                    <h2 style="font-size: 2.75rem; font-weight: 800; color: white; margin: 0;">Trusted Medical
                        Professionals</h2>
                </div>
            </div>
            <!-- Contact & Benefits Section -->
            <div style="background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%); padding: 60px 20px;">
                <div
                    style="max-width: 1400px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
                    <!-- Left: Contact & Benefits -->
                    <div>
                        <div style="display: flex; gap: 60px; margin-bottom: 40px;">
                            <!-- Contact Us -->
                            <div>
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                                    <div
                                        style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        📞</div>
                                    <h4 style="color: white; font-size: 1.1rem; font-weight: 700; margin: 0;">Contact Us
                                    </h4>
                                </div>
                                <p style="color: #94a3b8; font-size: 0.95rem; margin: 0 0 8px 0;">Email:
                                    smartclinic@gmail.com</p>
                                <p style="color: #94a3b8; font-size: 0.95rem; margin: 0;">Call Us 24h: +91 98765 43210
                                </p>
                            </div>
                            <!-- Address -->
                            <div>
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                                    <div
                                        style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        📍</div>
                                    <h4 style="color: white; font-size: 1.1rem; font-weight: 700; margin: 0;">Address
                                        Medical</h4>
                                </div>
                                <p style="color: #94a3b8; font-size: 0.95rem; margin: 0 0 8px 0;">Near City Hospital,
                                    Main Road</p>
                                <p style="color: #94a3b8; font-size: 0.95rem; margin: 0;">Opening Hours: 24/7 Open</p>
                            </div>
                        </div>
                        <!-- Benefits -->
                        <h3 style="color: white; font-size: 1.25rem; font-weight: 700; margin-bottom: 24px;">Benefits if
                            you Schedule An Appointment</h3>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li
                                style="display: flex; align-items: flex-start; gap: 12px; color: #94a3b8; font-size: 0.95rem; padding: 10px 0; line-height: 1.6;">
                                <span style="color: #06b6d4; font-weight: bold; flex-shrink: 0;">✓</span>
                                <span><strong style="color: white;">Time Management:</strong> Minimize waiting by
                                    securing your consultation in advance.</span>
                            </li>
                            <li
                                style="display: flex; align-items: flex-start; gap: 12px; color: #94a3b8; font-size: 0.95rem; padding: 10px 0; line-height: 1.6;">
                                <span style="color: #06b6d4; font-weight: bold; flex-shrink: 0;">✓</span>
                                <span><strong style="color: white;">Personalized Care:</strong> Receive dedicated
                                    attention tailored to your needs.</span>
                            </li>
                            <li
                                style="display: flex; align-items: flex-start; gap: 12px; color: #94a3b8; font-size: 0.95rem; padding: 10px 0; line-height: 1.6;">
                                <span style="color: #06b6d4; font-weight: bold; flex-shrink: 0;">✓</span>
                                <span><strong style="color: white;">Efficient Service:</strong> Enjoy a streamlined
                                    process for check-ups, diagnostics, or treatments.</span>
                            </li>
                            <li
                                style="display: flex; align-items: flex-start; gap: 12px; color: #94a3b8; font-size: 0.95rem; padding: 10px 0; line-height: 1.6;">
                                <span style="color: #06b6d4; font-weight: bold; flex-shrink: 0;">✓</span>
                                <span><strong style="color: white;">Access to Specialists:</strong> Connect with experts
                                    across a range of medical fields.</span>
                            </li>
                        </ul>
                    </div>
                    <!-- Right: Building Image -->
                    <div>
                        <img src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=600&auto=format&fit=crop"
                            alt="SUDAMA CLINICs Building"
                            style="width: 100%; height: 400px; object-fit: cover; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.4);">
                        <div
                            style="background: white; padding: 20px 24px; border-radius: 12px; margin-top: -40px; margin-left: 30px; margin-right: 30px; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 12px;">
                            <div
                                style="width: 40px; height: 40px; background: #06b6d4; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem;">
                                🏥</div>
                            <span style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">SUDAMA CLINICs</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section style="padding: 80px 20px; background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);">
            <div style="max-width: 1200px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 60px;">
                    <h2 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 16px;">What Our <span
                            class="text-gradient">Patients Say</span></h2>
                    <p style="color: #64748b; font-size: 1.125rem;">Trusted by thousands of patients across the region
                    </p>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
                    <div
                        style="background: white; padding: 32px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">
                        <div style="display: flex; gap: 4px; margin-bottom: 16px;">⭐⭐⭐⭐⭐</div>
                        <p style="color: #475569; line-height: 1.7; margin-bottom: 20px;">"The online booking system is
                            incredibly convenient. I can schedule appointments from my phone and receive reminders. The
                            doctors are very professional and caring."</p>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div
                                style="width: 48px; height: 48px; background: linear-gradient(135deg, #06b6d4, #0891b2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                RK</div>
                            <div><strong style="color: #0f172a;">Rahul Kumar</strong>
                                <p style="color: #94a3b8; font-size: 0.875rem; margin: 0;">Patient since 2024</p>
                            </div>
                        </div>
                    </div>
                    <div
                        style="background: white; padding: 32px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">
                        <div style="display: flex; gap: 4px; margin-bottom: 16px;">⭐⭐⭐⭐⭐</div>
                        <p style="color: #475569; line-height: 1.7; margin-bottom: 20px;">"Finally a clinic that
                            understands
                            technology! The digital prescriptions and medical records access have made managing my
                            family's
                            health so much easier."</p>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div
                                style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                PS</div>
                            <div><strong style="color: #0f172a;">Priya Sharma</strong>
                                <p style="color: #94a3b8; font-size: 0.875rem; margin: 0;">Patient since 2023</p>
                            </div>
                        </div>
                    </div>
                    <div
                        style="background: white; padding: 32px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">
                        <div style="display: flex; gap: 4px; margin-bottom: 16px;">⭐⭐⭐⭐⭐</div>
                        <p style="color: #475569; line-height: 1.7; margin-bottom: 20px;">"Excellent service! The staff
                            is
                            friendly, wait times are minimal, and the online portal keeps everything organized. Highly
                            recommend SUDAMA CLINIC!"</p>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div
                                style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                AM</div>
                            <div><strong style="color: #0f172a;">Amit Mehta</strong>
                                <p style="color: #94a3b8; font-size: 0.875rem; margin: 0;">Patient since 2025</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trust Badges Section -->
        <section style="padding: 60px 20px; background: white;">
            <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
                <h3
                    style="color: #94a3b8; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px;">
                    Trusted & Certified By</h3>
                <div
                    style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 50px; opacity: 0.7;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #64748b;">🏥 NABH</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #64748b;">✅ ISO 9001</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #64748b;">🔒 HIPAA</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #64748b;">⭐ JCI Accredited</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #64748b;">🛡️ SSL Secured</div>
                </div>
            </div>
        </section>

        <!-- Dark Professional Footer -->
        <footer
            style="background: linear-gradient(180deg, #0f172a 0%, #020617 100%); color: #e2e8f0; padding: 80px 20px 0;">
            <div style="max-width: 1400px; margin: 0 auto;">
                <!-- Main Footer Content -->
                <div
                    style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1.5fr; gap: 50px; padding-bottom: 60px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <!-- Brand Column -->
                    <div>
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                            <img src="images/logo.png" alt="SUDAMA CLINIC"
                                style="height: 48px; filter: brightness(1.2);">
                            <span
                                style="font-size: 1.75rem; font-weight: 800; background: linear-gradient(135deg, #06b6d4, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SUDAMA
                                CLINIC</span>
                        </div>
                        <p style="color: #94a3b8; line-height: 1.8; margin-bottom: 24px;">Your trusted healthcare
                            partner.
                            Modern medical services with digital convenience. Book appointments, access records, and
                            manage
                            your health journey all in one place.</p>
                        <div style="display: flex; gap: 12px;">
                            <a href="#"
                                style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #e2e8f0; text-decoration: none; transition: all 0.3s;"
                                onmouseover="this.style.background='#06b6d4'"
                                onmouseout="this.style.background='rgba(255,255,255,0.1)'">📘</a>
                            <a href="#"
                                style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #e2e8f0; text-decoration: none; transition: all 0.3s;"
                                onmouseover="this.style.background='#06b6d4'"
                                onmouseout="this.style.background='rgba(255,255,255,0.1)'">🐦</a>
                            <a href="#"
                                style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #e2e8f0; text-decoration: none; transition: all 0.3s;"
                                onmouseover="this.style.background='#06b6d4'"
                                onmouseout="this.style.background='rgba(255,255,255,0.1)'">📷</a>
                            <a href="#"
                                style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #e2e8f0; text-decoration: none; transition: all 0.3s;"
                                onmouseover="this.style.background='#06b6d4'"
                                onmouseout="this.style.background='rgba(255,255,255,0.1)'">💼</a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 24px; color: white;">Quick Links
                        </h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 14px;"><a href="#features"
                                    style="color: #94a3b8; text-decoration: none; transition: color 0.3s;"
                                    onmouseover="this.style.color='#06b6d4'"
                                    onmouseout="this.style.color='#94a3b8'">Features</a></li>
                            <li style="margin-bottom: 14px;"><a href="#how-it-works"
                                    style="color: #94a3b8; text-decoration: none; transition: color 0.3s;"
                                    onmouseover="this.style.color='#06b6d4'" onmouseout="this.style.color='#94a3b8'">How
                                    It
                                    Works</a></li>
                            <li style="margin-bottom: 14px;"><a href="about.php"
                                    style="color: #94a3b8; text-decoration: none; transition: color 0.3s;"
                                    onmouseover="this.style.color='#06b6d4'"
                                    onmouseout="this.style.color='#94a3b8'">About
                                    Us</a></li>
                            <li style="margin-bottom: 14px;"><a href="contact.php"
                                    style="color: #94a3b8; text-decoration: none; transition: color 0.3s;"
                                    onmouseover="this.style.color='#06b6d4'"
                                    onmouseout="this.style.color='#94a3b8'">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Portals -->
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 24px; color: white;">Portals</h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 14px;"><a href="doctor-login.php"
                                    style="color: #94a3b8; text-decoration: none; transition: color 0.3s;"
                                    onmouseover="this.style.color='#06b6d4'"
                                    onmouseout="this.style.color='#94a3b8'">Doctor
                                    Portal</a></li>
                            <!-- Receptionist Portal Removed -->
                            <!-- Admin Portal Removed -->
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 24px; color: white;">Support</h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 14px;"><a href="#"
                                    style="color: #94a3b8; text-decoration: none; transition: color 0.3s;"
                                    onmouseover="this.style.color='#06b6d4'"
                                    onmouseout="this.style.color='#94a3b8'">Help
                                    Center</a></li>
                            <li style="margin-bottom: 14px;"><a href="#"
                                    style="color: #94a3b8; text-decoration: none; transition: color 0.3s;"
                                    onmouseover="this.style.color='#06b6d4'"
                                    onmouseout="this.style.color='#94a3b8'">FAQs</a></li>
                            <li style="margin-bottom: 14px;"><a href="#"
                                    style="color: #94a3b8; text-decoration: none; transition: color 0.3s;"
                                    onmouseover="this.style.color='#06b6d4'"
                                    onmouseout="this.style.color='#94a3b8'">Privacy
                                    Policy</a></li>
                            <li style="margin-bottom: 14px;"><a href="#"
                                    style="color: #94a3b8; text-decoration: none; transition: color 0.3s;"
                                    onmouseover="this.style.color='#06b6d4'"
                                    onmouseout="this.style.color='#94a3b8'">Terms
                                    of Service</a></li>
                        </ul>
                    </div>

                    <!-- Contact & Newsletter -->
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 24px; color: white;">Contact Us
                        </h4>
                        <div style="margin-bottom: 16px; display: flex; align-items: flex-start; gap: 12px;">
                            <span style="font-size: 1.25rem;">📍</span>
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

                <!-- Bottom Bar -->
                <div
                    style="padding: 30px 0; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
                    <p style="color: #64748b; font-size: 0.875rem;">© 2026 SUDAMA CLINIC. All rights reserved. Made with
                        ❤️
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
            // Animated Counter Script
            function animateCounters() {
                const counters = document.querySelectorAll('.counter');
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'));
                    const duration = 2000;
                    const increment = target / (duration / 16);
                    let current = 0;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            counter.textContent = target.toLocaleString() + '+';
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.floor(current).toLocaleString();
                        }
                    }, 16);
                });
            }

            // Trigger counters when hero is visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.5 });

            const heroSection = document.getElementById('hero');
            if (heroSection) observer.observe(heroSection);
        </script>

        <!-- Section Spacing & FAQ Fix -->
        <style>
            .features,
            [id="services"],
            [id="get-started"] {
                margin-top: 40px;
            }

            section+section {
                margin-top: 0;
            }
        </style>
        <script>
            // Proper FAQ toggle fix
            document.addEventListener('DOMContentLoaded', function () {
                // Find all FAQ question divs (they have cursor:pointer and are followed by answer divs)
                document.querySelectorAll('.faq-icon').forEach(icon => {
                    const questionDiv = icon.closest('[style*="cursor: pointer"]');
                    if (questionDiv) {
                        // Remove the inline onclick to prevent double firing
                        questionDiv.removeAttribute('onclick');

                        questionDiv.addEventListener('click', function () {
                            const answer = this.nextElementSibling;
                            const currentIcon = this.querySelector('.faq-icon');

                            if (answer && answer.style) {
                                const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px' && answer.style.maxHeight !== '0';

                                if (isOpen) {
                                    answer.style.maxHeight = '0';
                                    if (currentIcon) currentIcon.textContent = '+';
                                } else {
                                    answer.style.maxHeight = '300px';
                                    if (currentIcon) currentIcon.textContent = '−';
                                }
                            }
                        });
                    }
                });
            });
        </script>
</body>

</html>
