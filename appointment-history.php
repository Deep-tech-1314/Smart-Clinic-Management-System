<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment History - SUDAMA CLINIC</title>
    <meta name="description" content="View your past appointments.">
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
        <div class="container-fluid navbar-container">
            <a href="index.php" class="navbar-brand">
                <img src="images/logo.png" alt="SUDAMA CLINIC">
                <span class="text-gradient">SUDAMA CLINIC</span>
            </a>
            <ul class="navbar-nav">
                <li><span id="userName" style="color: var(--color-text-secondary);"></span></li>
                <li><button class="btn btn-ghost btn-sm" onclick="logout()">Logout</button></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="user-profile">
                    <div class="user-avatar" id="userAvatar">JD</div>
                    <div class="user-info">
                        <h4 id="sidebarUserName">John Doe</h4>
                        <p>Patient</p>
                    </div>
                </div>
            </div>

            <nav>
                <ul class="sidebar-nav">
                    <li><a href="patient-dashboard.php"><span class="nav-icon">📊</span> Dashboard</a></li>
                    <li><a href="book-appointment.php"><span class="nav-icon">📅</span> Book Appointment</a></li>
                    <li><a href="appointment-history.php" class="active"><span class="nav-icon">📋</span> Appointment
                            History</a></li>
                    <li><a href="view-prescription.php"><span class="nav-icon">💊</span> Prescriptions</a></li>
                    <li><a href="patient-messages.php"><span class="nav-icon">💬</span> Messages</a></li>
                    <li><a href="#"><span class="nav-icon">⚙️</span> Settings</a></li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <button class="btn btn-ghost btn-block" onclick="logout()">
                    <span>🚪</span> Logout
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="page-header">
                <div>
                    <h1>Appointment <span class="text-gradient">History</span></h1>
                    <div class="breadcrumb">
                        <a href="patient-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>Appointment History</span>
                    </div>
                </div>
                <a href="book-appointment.php" class="btn btn-primary">📅 Book New Appointment</a>
            </div>

            <!-- Filters -->
            <div class="form-section">
                <div class="form-row" style="align-items: flex-end;">
                    <div class="form-group">
                        <label class="form-label">Filter by Status</label>
                        <select id="filterStatus" class="form-control" onchange="filterAppointments()">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Filter by Year</label>
                        <select id="filterYear" class="form-control" onchange="filterAppointments()">
                            <option value="">All Years</option>
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Appointments List -->
            <div class="form-section">
                <h3 class="form-section-title">📅 Your Appointments</h3>
                <div id="appointmentsList">
                    <!-- Dynamic content -->
                </div>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>

    <script src="js/clinic.js"></script>
    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', async function () {
            // Check auth
            const auth = Auth.checkAuth();
            if (!auth.isAuthenticated || auth.user.role !== 'patient') {
                window.location.href = 'index.php';
                return;
            }

            await updateUserInfo();
            await loadAppointments();
        });

        // Update user info
        async function updateUserInfo() {
            try {
                const response = await API.patients.getProfile();
                if (response.success) {
                    const patient = response.data;
                    document.getElementById('userName').textContent = patient.name;
                    document.getElementById('sidebarUserName').textContent = patient.name;
                    document.getElementById('userAvatar').textContent = getInitials(patient.name);
                }
            } catch (error) {
                console.error('Error loading profile:', error);
            }
        }

        let allAppointments = [];

        // Load appointments
        async function loadAppointments() {
            try {
                const response = await API.appointments.getList();

                if (response.success) {
                    allAppointments = response.data;
                    filterAppointments();
                }
            } catch (error) {
                console.error('Error loading appointments:', error);
                document.getElementById('appointmentsList').innerHTML = '<p>Error loading appointments.</p>';
            }
        }

        // Filter and render appointments
        function filterAppointments() {
            const status = document.getElementById('filterStatus').value;
            const year = document.getElementById('filterYear').value;

            let filtered = [...allAppointments];

            if (status) {
                filtered = filtered.filter(apt => apt.status === status);
            }
            if (year) {
                filtered = filtered.filter(apt => apt.appointment_date.startsWith(year));
            }

            renderAppointments(filtered);
        }

        // Render appointments
        function renderAppointments(appointments) {
            const container = document.getElementById('appointmentsList');

            if (appointments.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">📅</div>
                        <h3>No appointments found</h3>
                        <p>No appointments match your filters</p>
                        <a href="book-appointment.php" class="btn btn-primary" style="margin-top: var(--spacing-lg);">Book New Appointment</a>
                    </div>
                `;
                return;
            }

            // Sort by date descending
            appointments.sort((a, b) => new Date(b.appointment_date + ' ' + b.appointment_time) - new Date(a.appointment_date + ' ' + a.appointment_time));

            container.innerHTML = `
                <div class="timeline">
                    ${appointments.map(apt => `
                        <div class="timeline-item">
                            <div class="timeline-date">${formatDate(apt.appointment_date)} at ${formatTime(apt.appointment_time)}</div>
                            <div class="timeline-content">
                                <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: var(--spacing-md);">
                                    <div>
                                        <div class="timeline-title">${apt.doctor_name}</div>
                                        <p style="color: var(--color-text-muted); margin: var(--spacing-xs) 0;">
                                            ${apt.appointment_type === 'new' ? 'New Case Consultation' : 'Follow-up Visit'}
                                        </p>
                                        <p style="color: var(--color-text-muted);">
                                            Charge: <strong>₹${apt.consultation_fee || '-'}</strong>
                                        </p>
                                    </div>
                                    <div style="text-align: right;">
                                        <span class="status-badge status-${apt.status}">${apt.status}</span>
                                        ${apt.status === 'completed' ? `
                                            <br><a href="view-prescription.php?apt=${apt.id}" class="btn btn-ghost btn-sm" style="margin-top: var(--spacing-sm);">View Prescription</a>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Logout
        function logout() {
            Auth.logout();
        }
    </script>
</body>

</html>

