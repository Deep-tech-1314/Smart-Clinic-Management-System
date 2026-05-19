<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details - SUDAMA CLINIC</title>
    <meta name="description" content="View patient profile and medical history.">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
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
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container navbar-container">
            <button class="navbar-toggle" onclick="toggleSidebar()">☰</button>
            <a href="index.php" class="navbar-brand">
                <img src="images/logo.png" alt="SUDAMA CLINIC Logo">
                <span class="text-gradient">SUDAMA CLINIC</span>
            </a>
            <ul class="navbar-nav" id="navbarNav">
                <li><a href="doctor-dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="doctor-appointments.php" class="nav-link">Appointments</a></li>
                <li><a href="doctor-messages.php" class="nav-link">Messages</a></li>
                <li><a href="index.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Dashboard Layout -->
    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <!-- Sidebar -->
        <?php include 'includes/patient_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1>Patient <span class="text-gradient">Details</span></h1>
                    <div class="breadcrumb">
                        <a href="doctor-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <a href="doctor-appointments.php">Appointments</a>
                        <span>/</span>
                        <span>Patient Details</span>
                    </div>
                </div>
                <button class="btn btn-ghost" onclick="history.back()">← Back</button>
            </div>

            <!-- Patient Profile Card -->
            <div class="form-section" id="patientProfile">
                <div style="display: flex; gap: var(--spacing-2xl); flex-wrap: wrap;">
                    <div style="text-align: center;">
                        <div class="user-avatar" style="width: 120px; height: 120px; font-size: 3rem; margin: 0 auto;"
                            id="patientAvatar">JD</div>
                        <h2 style="margin-top: var(--spacing-md);" id="patientName">John Doe</h2>
                        <p style="color: var(--color-text-muted);" id="patientId">PID: PID001</p>
                    </div>
                    <div
                        style="flex: 1; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-lg);">
                        <div>
                            <p style="color: var(--color-text-muted); font-size: var(--font-size-sm);">📧 Email</p>
                            <p style="font-weight: 600;" id="patientEmail">patient@email.com</p>
                        </div>
                        <div>
                            <p style="color: var(--color-text-muted); font-size: var(--font-size-sm);">📞 Phone</p>
                            <p style="font-weight: 600;" id="patientPhone">+91 99887 76655</p>
                        </div>
                        <div>
                            <p style="color: var(--color-text-muted); font-size: var(--font-size-sm);">🎂 Date of Birth
                            </p>
                            <p style="font-weight: 600;" id="patientDob">15 May 1990</p>
                        </div>
                        <div>
                            <p style="color: var(--color-text-muted); font-size: var(--font-size-sm);">👤 Gender</p>
                            <p style="font-weight: 600;" id="patientGender">Male</p>
                        </div>
                        <div>
                            <p style="color: var(--color-text-muted); font-size: var(--font-size-sm);">📍 Address</p>
                            <p style="font-weight: 600;" id="patientAddress">123 Main Street, Mumbai</p>
                        </div>
                        <div>
                            <p style="color: var(--color-text-muted); font-size: var(--font-size-sm);">📅 Last Visit</p>
                            <p style="font-weight: 600;" id="patientLastVisit">20 Jan 2026</p>
                        </div>
                    </div>
                </div>
                <div style="margin-top: var(--spacing-xl); display: flex; gap: var(--spacing-md);">
                    <a href="#" class="btn btn-primary" id="prescribeBtn">📝 Write Prescription</a>
                    <a href="#" class="btn btn-ghost" id="messageBtn">💬 Send Message</a>
                </div>
            </div>

            <!-- Medical History Timeline -->
            <div class="form-section">
                <h3 class="form-section-title">📋 Medical History</h3>
                <div class="timeline" id="medicalHistory">
                    <!-- Dynamic content -->
                </div>
            </div>

            <!-- Past Prescriptions -->
            <div class="form-section">
                <h3 class="form-section-title">💊 Past Prescriptions</h3>
                <div id="pastPrescriptions">
                    <!-- Dynamic content -->
                </div>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>

    <script src="js/clinic.js"></script>
    <script>
        // Check authentication
        const currentUser = SmartClinic.Storage.get('currentUser');
        if (!currentUser || currentUser.role !== 'doctor') {
            window.location.href = 'doctor-login.php';
        }

        // Get patient ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const patientId = urlParams.get('id') || 'PID001';

        // Update doctor info
        function updateDoctorInfo() {
            if (currentUser) {
                document.getElementById('doctorName').textContent = currentUser.name;
                document.getElementById('doctorSpec').textContent = currentUser.specialization;
                document.getElementById('doctorAvatar').textContent = getInitials(currentUser.name);
            }
        }



        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            updateDoctorInfo();
            loadPatientData();
        });

        // Load patient data
        async function loadPatientData() {
            try {
                const response = await API.patients.getProfile(patientId);
                if (response.success) {
                    const patient = response.data;
                    document.getElementById('patientAvatar').textContent = getInitials(patient.name);
                    document.getElementById('patientName').textContent = patient.name;
                    document.getElementById('patientId').textContent = 'PID: ' + patient.id;
                    document.getElementById('patientEmail').textContent = patient.email;
                    document.getElementById('patientPhone').textContent = patient.phone;
                    document.getElementById('patientDob').textContent = formatDate(patient.date_of_birth);
                    document.getElementById('patientGender').textContent = patient.gender;
                    document.getElementById('patientAddress').textContent = patient.address + (patient.city ? `, ${patient.city}` : '');

                    // Update action buttons
                    document.getElementById('prescribeBtn').href = `prescription.php?patient=${patientId}`;
                    document.getElementById('messageBtn').href = `doctor-messages.php?to=${patientId}`;

                    // After loading profile, load history and prescriptions
                    loadMedicalHistory();
                    loadPrescriptions();

                } else {
                    SmartClinic.showNotification(response.message || 'Patient not found', 'error');
                }
            } catch (error) {
                console.error('Error loading patient:', error);
                SmartClinic.showNotification('Error loading patient details', 'error');
            }
        }

        // Load medical history (Appointments)
        async function loadMedicalHistory() {
            const container = document.getElementById('medicalHistory');

            try {
                const response = await API.appointments.getList({ patient_id: patientId });

                if (!response.success || response.data.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <h3>No history found</h3>
                            <p>This patient has no previous visits</p>
                        </div>
                    `;
                    document.getElementById('patientLastVisit').textContent = 'Never';
                    return;
                }

                const appointments = response.data;
                // Sort by date desc (API might already sort but safe to ensure)
                appointments.sort((a, b) => new Date(b.appointment_date + ' ' + b.appointment_time) - new Date(a.appointment_date + ' ' + a.appointment_time));

                // Update last visit
                const completed = appointments.filter(a => a.status === 'completed');
                if (completed.length > 0) {
                    document.getElementById('patientLastVisit').textContent = formatDate(completed[0].appointment_date);
                } else {
                    document.getElementById('patientLastVisit').textContent = 'No completed visits';
                }

                container.innerHTML = appointments.map(apt => `
                    <div class="timeline-item">
                        <div class="timeline-date">${formatDate(apt.appointment_date)} - ${formatTime(apt.appointment_time)}</div>
                        <div class="timeline-content">
                            <div class="timeline-title">${apt.doctor_name}</div>
                            <p style="color: var(--color-text-muted);">
                                ${apt.appointment_type === 'new' ? 'New Case' : 'Follow-up'} Consultation
                            </p>
                            <span class="status-badge status-${apt.status}">${apt.status}</span>
                        </div>
                    </div>
                `).join('');

            } catch (error) {
                console.error('Error loading history:', error);
                container.innerHTML = '<p>Error loading medical history.</p>';
            }
        }

        // Load past prescriptions
        async function loadPrescriptions() {
            const container = document.getElementById('pastPrescriptions');

            try {
                const response = await API.prescriptions.getList({ patient_id: patientId });

                if (!response.success || response.data.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon">💊</div>
                            <h3>No prescriptions</h3>
                            <p>No prescriptions on record</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = response.data.map(rx => `
                    <div class="message-card" style="margin-bottom: var(--spacing-md);">
                        <div class="message-header">
                            <div>
                                <strong>${formatDate(rx.created_at)}</strong>
                                <span style="color: var(--color-text-muted);"> by ${rx.doctor_name}</span>
                            </div>
                            <a href="view-prescription.php?id=${rx.id}" class="btn btn-ghost btn-sm">View</a>
                        </div>
                        <div class="message-body">
                            <p><strong>Diagnosis:</strong> ${rx.diagnosis}</p>
                            <p><strong>Medicines:</strong> ${rx.medicines.map(m => m.name).join(', ')}</p>
                            ${rx.follow_up_date ? `<p><strong>Follow-up:</strong> ${formatDate(rx.follow_up_date)}</p>` : ''}
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading prescriptions:', error);
                container.innerHTML = '<p>Error loading prescriptions.</p>';
            }
        }
    </script>
</body>

</html>

