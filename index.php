<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUDAMA CLINIC - Modern Healthcare Appointment System</title>
    <meta name="description"
        content="Book appointments online with SUDAMA CLINIC. Manage your healthcare digitally with our modern patient portal and digital record system.">
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
</script>
</head>

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

    <!-- Hero Section with Video Background -->
    <section class="hero" id="hero" style="position: relative; overflow: hidden; min-height: 100vh; padding-top: 80px;">
        <!-- Video Background -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0;">
            <video autoplay muted loop playsinline style="width: 100%; height: 100%; object-fit: cover; opacity: 0.1;">
                <source src="https://assets.mixkit.co/videos/preview/mixkit-set-of-platelets-in-blood-702-large.mp4"
                    type="video/mp4">
            </video>
            <div
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(2, 6, 23, 0.97) 0%, rgba(15, 23, 42, 0.95) 40%, rgba(6, 182, 212, 0.15) 100%);">
            </div>
        </div>

        <!-- Animated Floating Elements -->
        <div
            style="position: absolute; top: 20%; left: 10%; width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(139, 92, 246, 0.2)); animation: float 6s ease-in-out infinite; filter: blur(40px);">
        </div>
        <div
            style="position: absolute; top: 60%; right: 15%; width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(6, 182, 212, 0.2)); animation: float 8s ease-in-out infinite reverse; filter: blur(50px);">
        </div>
        <div
            style="position: absolute; bottom: 20%; left: 30%; width: 80px; height: 80px; border-radius: 50%; background: rgba(16, 185, 129, 0.2); animation: float 5s ease-in-out infinite; filter: blur(30px);">
        </div>

        <style>
            @keyframes float {

                0%,
                100% {
                    transform: translateY(0) rotate(0deg);
                }

                50% {
                    transform: translateY(-30px) rotate(10deg);
                }
            }

            @keyframes pulse-ring {
                0% {
                    transform: scale(1);
                    opacity: 0.8;
                }

                100% {
                    transform: scale(1.5);
                    opacity: 0;
                }
            }

            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-100px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(50px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes countUp {
                from {
                    opacity: 0;
                    transform: scale(0.5);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }

            @keyframes typewriter {
                from {
                    width: 0;
                }

                to {
                    width: 100%;
                }
            }

            @keyframes heartbeat {

                0%,
                100% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.1);
                }
            }
        </style>

        <div class="container" style="position: relative; z-index: 1;">
            <div
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; min-height: calc(100vh - 80px); padding: 40px 0;">
                <div style="animation: slideInLeft 1s ease-out;">
                    <!-- Badge -->
                    <div
                        style="display: inline-flex; align-items: center; gap: 10px; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: 50px; padding: 8px 20px; margin-bottom: 24px;">
                        <span
                            style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; animation: heartbeat 1.5s ease infinite;"></span>
                        <span style="color: #06b6d4; font-weight: 600; font-size: 0.875rem;">24/7 Emergency Services
                            Available</span>
                    </div>

                    <h1 style="font-size: 4rem; font-weight: 800; line-height: 1.1; color: white; margin-bottom: 24px;">
                        Not Just Better Healthcare,<br>
                        <span
                            style="background: linear-gradient(135deg, #06b6d4, #8b5cf6, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-size: 200% 200%; animation: gradientShift 3s ease infinite;">A
                            Better Experience</span>
                    </h1>

                    <style>
                        @keyframes gradientShift {

                            0%,
                            100% {
                                background-position: 0% 50%;
                            }

                            50% {
                                background-position: 100% 50%;
                            }
                        }
                    </style>

                    <p
                        style="font-size: 1.25rem; color: #94a3b8; line-height: 1.8; margin-bottom: 32px; max-width: 540px;">
                        Experience world-class healthcare with our team of expert doctors. Book appointments online,
                        access medical records instantly, and receive personalized care in a comfortable environment.
                    </p>

                    <div style="display: flex; gap: 16px; margin-bottom: 48px;">
                        <a href="patient-register.php"
                            style="display: inline-flex; align-items: center; gap: 10px; padding: 18px 36px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 1rem; box-shadow: 0 8px 30px rgba(6, 182, 212, 0.4); transition: all 0.3s;"
                            onmouseover="this.style.transform='translateY(-4px) scale(1.02)'; this.style.boxShadow='0 12px 40px rgba(6, 182, 212, 0.5)'"
                            onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 8px 30px rgba(6, 182, 212, 0.4)'">
                            🏥 Book Appointment
                        </a>
                        <a href="#services"
                            style="display: inline-flex; align-items: center; gap: 10px; padding: 18px 36px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 1rem; transition: all 0.3s; backdrop-filter: blur(10px);"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.borderColor='rgba(6,182,212,0.5)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='rgba(255,255,255,0.2)'">
                            ▶ View Services
                        </a>
                    </div>

                    <!-- Animated Counter Stats -->
                    <div style="display: flex; gap: 40px;">
                        <div style="text-align: center; animation: fadeInUp 1s ease-out 0.3s both;">
                            <div style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #06b6d4, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"
                                class="counter" data-target="5000">0</div>
                            <div style="color: #64748b; font-size: 0.875rem; font-weight: 500;">Happy Patients</div>
                        </div>
                        <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                        <div style="text-align: center; animation: fadeInUp 1s ease-out 0.5s both;">
                            <div style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"
                                class="counter" data-target="50">0</div>
                            <div style="color: #64748b; font-size: 0.875rem; font-weight: 500;">Expert Doctors</div>
                        </div>
                        <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                        <div style="text-align: center; animation: fadeInUp 1s ease-out 0.7s both;">
                            <div style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #f59e0b, #d97706); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"
                                class="counter" data-target="15">0</div>
                            <div style="color: #64748b; font-size: 0.875rem; font-weight: 500;">Specialties</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Image with Floating Cards -->
                <div style="position: relative; animation: slideInRight 1s ease-out;">
                    <img src="https://images.unsplash.com/photo-1631815588090-d4bfec5b1ccb?w=600&auto=format&fit=crop"
                        alt="Medical Team"
                        style="width: 100%; border-radius: 24px; box-shadow: 0 30px 60px rgba(0,0,0,0.3);">

                    <!-- Floating Info Card 1 -->
                    <div
                        style="position: absolute; top: 20px; left: -40px; background: white; padding: 16px 20px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); animation: float 4s ease-in-out infinite; display: flex; align-items: center; gap: 12px;">
                        <div
                            style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                            ✓</div>
                        <div>
                            <div style="font-weight: 700; color: #0f172a;">Verified Doctors</div>
                            <div style="color: #64748b; font-size: 0.875rem;">100% Qualified</div>
                        </div>
                    </div>



                    <!-- Pulse Ring Effect -->
                    <div
                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 120px; height: 120px; border-radius: 50%; border: 2px solid rgba(6, 182, 212, 0.5); animation: pulse-ring 2s ease-out infinite;">
                    </div>
                </div>
            </div>
        </div>
        <!-- Scroll Indicator -->
        <div
            style="position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); text-align: center; animation: fadeInUp 1s ease-out 1s both;">
            <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 8px;">Scroll to explore</div>
            <div
                style="width: 24px; height: 40px; border: 2px solid rgba(255,255,255,0.3); border-radius: 12px; margin: 0 auto; position: relative;">
                <div
                    style="width: 4px; height: 8px; background: #06b6d4; border-radius: 2px; position: absolute; left: 50%; top: 8px; transform: translateX(-50%); animation: scrollBounce 2s ease-in-out infinite;">
                </div>
            </div>
        </div>
        <style>
            @keyframes scrollBounce {

                0%,
                100% {
                    top: 8px;
                    opacity: 1;
                }

                50% {
                    top: 20px;
                    opacity: 0.5;
                }
            }
        </style>
    </section>

    <!-- Patient Login Section -->
    <section style="padding: 80px 20px; background: #f8fafc;" id="patient-login">
        <div style="max-width: 500px; margin: 0 auto; background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); border-top: 5px solid #06b6d4;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Patient <span style="color: #06b6d4;">Login</span></h2>
                <p style="color: #64748b;">Access your medical dashboard instantly.</p>
            </div>
            <!-- Submits POST payload exactly to patient-login.php -->
            <form method="POST" action="patient-login.php">
                <div style="margin-bottom: 20px; text-align: left;">
                    <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 8px;">Email Address</label>
                    <input type="email" name="email" placeholder="your.email@example.com" required style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; box-sizing: border-box; font-size: 1rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#06b6d4'" onblur="this.style.borderColor='#cbd5e1'">
                </div>
                <div style="margin-bottom: 24px; text-align: left;">
                    <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 8px;">Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; box-sizing: border-box; font-size: 1rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#06b6d4'" onblur="this.style.borderColor='#cbd5e1'">
                </div>
                <button type="submit" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 20px rgba(6,182,212,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">Sign In</button>
            </form>
            <div style="text-align: center; margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                <p style="color: #64748b; font-size: 0.95rem; margin: 0;">New patient? <a href="patient-register.php" style="color: #06b6d4; font-weight: 600; text-decoration: none;">Register here</a></p>
                <div style="margin-top: 10px;">
                    <a href="doctor-login.php" style="color: #8b5cf6; font-size: 0.9rem; font-weight: 600; text-decoration: none;">Are you a doctor? Login here.</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">
                    Why Choose <span class="text-gradient">SUDAMA CLINIC</span>
                </h2>
                <p class="section-subtitle">
                    Modern healthcare management designed for convenience and efficiency
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card card scale-in">
                    <div class="feature-icon">📅</div>
                    <h3 class="feature-title">Easy Appointment Booking</h3>
                    <p class="feature-description">
                        Book, reschedule, or cancel appointments in seconds. View real-time availability and choose
                        slots that work for you.
                    </p>
                </div>
                <div class="feature-card card scale-in" style="animation-delay: 0.1s;">
                    <div class="feature-icon">📋</div>
                    <h3 class="feature-title">Digital Medical Records</h3>
                    <p class="feature-description">
                        Access your complete medical history, prescriptions, and test results anytime, anywhere. No more
                        lost paperwork.
                    </p>
                </div>
                <div class="feature-card card scale-in" style="animation-delay: 0.2s;">
                    <div class="feature-icon">🔔</div>
                    <h3 class="feature-title">Smart Reminders</h3>
                    <p class="feature-description">
                        Never miss an appointment with automated email and SMS reminders sent before your scheduled
                        visit.
                    </p>
                </div>
                <div class="feature-card card scale-in" style="animation-delay: 0.3s;">
                    <div class="feature-icon">🏥</div>
                    <h3 class="feature-title">Multi-Specialty Clinics</h3>
                    <p class="feature-description">
                        Connect with specialists across various medical fields. Find the right doctor for your needs.
                    </p>
                </div>
                <div class="feature-card card scale-in" style="animation-delay: 0.4s;">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">Secure & Private</h3>
                    <p class="feature-description">
                        Your health data is encrypted and protected with industry-standard security measures. HIPAA
                        compliant.
                    </p>
                </div>
                <div class="feature-card card scale-in" style="animation-delay: 0.5s;">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Health Analytics</h3>
                    <p class="feature-description">
                        Track your health journey with visual analytics and insights from your medical history and
                        visits.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Medical Specialties Section -->
    <section id="services" style="padding: 100px 20px; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
        <div style="max-width: 1400px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 60px;">
                <span
                    style="display: inline-block; padding: 8px 20px; background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: 50px; color: #06b6d4; font-weight: 600; font-size: 0.875rem; margin-bottom: 16px;">Our
                    Specialties</span>
                <h2 style="font-size: 3rem; font-weight: 800; color: white; margin-bottom: 16px;">Scope of <span
                        style="background: linear-gradient(135deg, #06b6d4, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Services</span>
                </h2>
                <p style="color: #94a3b8; font-size: 1.125rem; max-width: 600px; margin: 0 auto;">Comprehensive
                    healthcare services with expertise across multiple specialties</p>
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
                <!-- Cardiology -->
                <div style="position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: all 0.5s;"
                    onmouseover="this.querySelector('.overlay').style.opacity='1'; this.style.transform='scale(1.05)'"
                    onmouseout="this.querySelector('.overlay').style.opacity='0.7'; this.style.transform='scale(1)'">
                    <img src="https://images.unsplash.com/photo-1559757175-0eb30cd8c063?w=400&auto=format&fit=crop"
                        alt="Cardiology" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="overlay"
                        style="position: absolute; bottom: 0; left: 0; right: 0; padding: 24px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); opacity: 0.7; transition: all 0.3s;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">❤</div>
                        <h3 style="color: white; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;">Cardiology
                        </h3>
                        <p style="color: #94a3b8; font-size: 0.875rem;">Heart & cardiovascular care</p>
                    </div>
                </div>
                <!-- Orthopedics -->
                <div style="position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: all 0.5s;"
                    onmouseover="this.querySelector('.overlay').style.opacity='1'; this.style.transform='scale(1.05)'"
                    onmouseout="this.querySelector('.overlay').style.opacity='0.7'; this.style.transform='scale(1)'">
                    <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&auto=format&fit=crop"
                        alt="Orthopedics" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="overlay"
                        style="position: absolute; bottom: 0; left: 0; right: 0; padding: 24px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); opacity: 0.7; transition: all 0.3s;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">🦴</div>
                        <h3 style="color: white; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;">Orthopedics
                        </h3>
                        <p style="color: #94a3b8; font-size: 0.875rem;">Bone & joint specialists</p>
                    </div>
                </div>
                <!-- Dental -->
                <div style="position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: all 0.5s;"
                    onmouseover="this.querySelector('.overlay').style.opacity='1'; this.style.transform='scale(1.05)'"
                    onmouseout="this.querySelector('.overlay').style.opacity='0.7'; this.style.transform='scale(1)'">
                    <img src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=400&auto=format&fit=crop"
                        alt="Dental" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="overlay"
                        style="position: absolute; bottom: 0; left: 0; right: 0; padding: 24px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); opacity: 0.7; transition: all 0.3s;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">🦷</div>
                        <h3 style="color: white; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;">Dental Care
                        </h3>
                        <p style="color: #94a3b8; font-size: 0.875rem;">Complete oral health</p>
                    </div>
                </div>
                <!-- Dermatology -->
                <div style="position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: all 0.5s;"
                    onmouseover="this.querySelector('.overlay').style.opacity='1'; this.style.transform='scale(1.05)'"
                    onmouseout="this.querySelector('.overlay').style.opacity='0.7'; this.style.transform='scale(1)'">
                    <img src="https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?w=400&auto=format&fit=crop"
                        alt="Dermatology" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="overlay"
                        style="position: absolute; bottom: 0; left: 0; right: 0; padding: 24px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); opacity: 0.7; transition: all 0.3s;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">✨</div>
                        <h3 style="color: white; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;">Dermatology
                        </h3>
                        <p style="color: #94a3b8; font-size: 0.875rem;">Skin & cosmetology</p>
                    </div>
                </div>
                <!-- Pediatrics -->
                <div style="position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: all 0.5s;"
                    onmouseover="this.querySelector('.overlay').style.opacity='1'; this.style.transform='scale(1.05)'"
                    onmouseout="this.querySelector('.overlay').style.opacity='0.7'; this.style.transform='scale(1)'">
                    <img src="https://images.unsplash.com/photo-1581594693702-fbdc51b2763b?w=400&auto=format&fit=crop"
                        alt="Pediatrics" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="overlay"
                        style="position: absolute; bottom: 0; left: 0; right: 0; padding: 24px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); opacity: 0.7; transition: all 0.3s;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">👶</div>
                        <h3 style="color: white; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;">Pediatrics
                        </h3>
                        <p style="color: #94a3b8; font-size: 0.875rem;">Child healthcare</p>
                    </div>
                </div>
                <!-- Gynecology -->
                <div style="position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: all 0.5s;"
                    onmouseover="this.querySelector('.overlay').style.opacity='1'; this.style.transform='scale(1.05)'"
                    onmouseout="this.querySelector('.overlay').style.opacity='0.7'; this.style.transform='scale(1)'">
                    <img src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?w=400&auto=format&fit=crop"
                        alt="Gynecology" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="overlay"
                        style="position: absolute; bottom: 0; left: 0; right: 0; padding: 24px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); opacity: 0.7; transition: all 0.3s;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">👩‍⚕️</div>
                        <h3 style="color: white; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;">Gynecology
                        </h3>
                        <p style="color: #94a3b8; font-size: 0.875rem;">Women's health</p>
                    </div>
                </div>
                <!-- General Surgery -->
                <div style="position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: all 0.5s;"
                    onmouseover="this.querySelector('.overlay').style.opacity='1'; this.style.transform='scale(1.05)'"
                    onmouseout="this.querySelector('.overlay').style.opacity='0.7'; this.style.transform='scale(1)'">
                    <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=400&auto=format&fit=crop"
                        alt="Surgery" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="overlay"
                        style="position: absolute; bottom: 0; left: 0; right: 0; padding: 24px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); opacity: 0.7; transition: all 0.3s;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">🏥</div>
                        <h3 style="color: white; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;">General
                            Surgery</h3>
                        <p style="color: #94a3b8; font-size: 0.875rem;">Surgical procedures</p>
                    </div>
                </div>
                <!-- Neurology -->
                <div style="position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 1; cursor: pointer; transition: all 0.5s;"
                    onmouseover="this.querySelector('.overlay').style.opacity='1'; this.style.transform='scale(1.05)'"
                    onmouseout="this.querySelector('.overlay').style.opacity='0.7'; this.style.transform='scale(1)'">
                    <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&auto=format&fit=crop"
                        alt="Neurology" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="overlay"
                        style="position: absolute; bottom: 0; left: 0; right: 0; padding: 24px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); opacity: 0.7; transition: all 0.3s;">
                        <div style="font-size: 2rem; margin-bottom: 8px;">🧠</div>
                        <h3 style="color: white; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;">Neurology
                        </h3>
                        <p style="color: #94a3b8; font-size: 0.875rem;">Brain & nerve care</p>
                    </div>
                </div>
            </div>
            <div style="text-align: center; margin-top: 50px;">
                <a href="patient-register.php"
                    style="display: inline-flex; align-items: center; gap: 12px; padding: 18px 40px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 1rem; box-shadow: 0 8px 30px rgba(6, 182, 212, 0.4); transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-4px)'"
                    onmouseout="this.style.transform='translateY(0)'">
                    View All Services & Book Now →
                </a>
            </div>
        </div>
    </section>
        <!-- Getting Started Section -->
        <section id="how-it-works" style="padding: 80px 20px; background: #f8fafc;">
            <div style="max-width: 1200px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 50px;">
                    <h2 style="font-size: 2.5rem; font-weight: 800; color: #0f172a; margin-bottom: 16px;">
                        Getting Started is <span
                            style="background: linear-gradient(135deg, #06b6d4, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Simple</span>
                    </h2>
                    <p style="font-size: 1.125rem; color: #64748b;">Three easy steps to better healthcare management</p>
                </div>
                <div style="display: flex; justify-content: center; align-items: center; gap: 30px;">
                    <div
                        style="flex: 1; max-width: 280px; background: white; border-radius: 20px; padding: 40px 30px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.06);">
                        <div
                            style="width: 70px; height: 70px; background: linear-gradient(135deg, #06b6d4, #0891b2); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.75rem; font-weight: 800;">
                            1</div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 12px;">Create
                            Your Account</h3>
                        <p style="color: #64748b; line-height: 1.6;">Sign up in minutes with your basic information.
                            It's free and secure.</p>
                    </div>
                    <div style="color: #cbd5e1; font-size: 2.5rem;">→</div>
                    <div
                        style="flex: 1; max-width: 280px; background: white; border-radius: 20px; padding: 40px 30px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.06);">
                        <div
                            style="width: 70px; height: 70px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.75rem; font-weight: 800;">
                            2</div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 12px;">Book
                            Appointment</h3>
                        <p style="color: #64748b; line-height: 1.6;">Choose your doctor, select a convenient time slot,
                            and confirm your booking.</p>
                    </div>
                    <div style="color: #cbd5e1; font-size: 2.5rem;">→</div>
                    <div
                        style="flex: 1; max-width: 280px; background: white; border-radius: 20px; padding: 40px 30px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.06);">
                        <div
                            style="width: 70px; height: 70px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.75rem; font-weight: 800;">
                            3</div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 12px;">Visit &
                            Track</h3>
                        <p style="color: #64748b; line-height: 1.6;">Attend your appointment and access all records
                            digitally in your patient portal.</p>
                    </div>
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
                if (entries[0].isIntersecting) {
                    animateCounters();
                    observer.disconnect();
                }
            });

            const heroSection = document.getElementById('hero');
            if (heroSection) {
                observer.observe(heroSection);
            }
        </script>
</body>

</html>
