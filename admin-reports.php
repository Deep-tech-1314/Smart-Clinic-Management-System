<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin</title>
    <meta name="description" content="Clinic reports and analytics.">
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
            <ul class="navbar-nav">
                <li><a href="admin-dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="index.php" class="btn btn-ghost btn-sm" onclick="SmartClinic.Storage.clear()">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <!-- Sidebar -->
        <?php include 'includes/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div class="page-header">
                <div>
                    <h1>Clinic <span class="text-gradient">Reports</span></h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>Reports</span>
                    </div>
                </div>
            </div>

            <!-- Overview Stats -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon primary">💰</div>
                    </div>
                    <div class="stat-value" id="totalRevenue">₹0</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon success">📅</div>
                    </div>
                    <div class="stat-value" id="totalAppointments">0</div>
                    <div class="stat-label">Total Appointments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon warning">👥</div>
                    </div>
                    <div class="stat-value" id="totalPatients">0</div>
                    <div class="stat-label">Total Patients</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon info">👨‍⚕️</div>
                    </div>
                    <div class="stat-value" id="totalDoctors">0</div>
                    <div class="stat-label">Active Doctors</div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="form-section">
                <h3 class="form-section-title">💰 Revenue Overview</h3>
                <div id="revenueChart"
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: var(--spacing-md);">
                </div>
            </div>

            <!-- Top Doctors -->
            <div class="form-section">
                <h3 class="form-section-title">⭐ Top Doctors by Appointments</h3>
                <div id="topDoctors"></div>
            </div>

            <!-- Recent Activity -->
            <div class="form-section">
                <h3 class="form-section-title">📋 Appointment Status Distribution</h3>
                <div id="statusDistribution"
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: var(--spacing-lg);">
                </div>
            </div>

            <!-- Export -->
            <div class="form-section">
                <h3 class="form-section-title">📤 Export Data</h3>
                <div style="display: flex; gap: var(--spacing-md); flex-wrap: wrap;">
                    <button class="btn btn-primary" onclick="exportData('appointments')">📅 Export Appointments</button>
                    <button class="btn btn-outline" onclick="exportData('patients')">👥 Export Patients</button>
                    <button class="btn btn-outline" onclick="exportData('doctors')">👨‍⚕️ Export Doctors</button>
                </div>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>

    <script src="js/clinic.js"></script>
    <script>
        // Check authentication
        const currentUser = SmartClinic.Storage.get('currentUser');
        if (!currentUser || currentUser.role !== 'admin') {
            window.location.href = 'admin-login.php';
        }

        let allAppointments = [];
        let allDoctors = [];
        let allPatients = [];

        async function loadReportsData() {
            try {
                // Load parallel data
                const [aptRes, docRes, patRes] = await Promise.all([
                    API.appointments.getList({ limit: 1000 }), // Get enough data for stats
                    API.admin.getDoctors({ per_page: 100 }),
                    API.admin.getPatients({ per_page: 100 })
                ]);

                if (aptRes.success) allAppointments = aptRes.data;
                if (docRes.success) allDoctors = docRes.data;
                if (patRes.success) allPatients = patRes.data; // Note: this is just first page, mostly for total count we might rely on array length or separate stats api

                updateStats();
                renderRevenueChart();
                renderTopDoctors();
                renderStatusDistribution();

            } catch (error) {
                console.error('Error loading reports:', error);
            }
        }

        function updateStats() {
            const completedApts = allAppointments.filter(a => a.status === 'completed');
            const totalRevenue = completedApts.reduce((sum, a) => sum + (parseFloat(a.charge) || 0), 0);
            const activeDoctors = allDoctors.filter(d => d.status === 'active');

            document.getElementById('totalRevenue').textContent = formatCurrency(totalRevenue);
            document.getElementById('totalAppointments').textContent = allAppointments.length;
            // For total patients, either use the fetched array length or a static number if > 100
            // Ideally we'd use a dedicated 'stats' endpoint, but this works for now
            document.getElementById('totalPatients').textContent = allPatients.length;
            document.getElementById('totalDoctors').textContent = activeDoctors.length;
        }

        function renderRevenueChart() {
            const container = document.getElementById('revenueChart');
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']; // Static for now, or dynamic based on data

            // Group revenue by month (simple version based on static months array assuming current year)
            // Real implementation would be more robust
            const currentYear = new Date().getFullYear();
            const revenues = months.map((m, i) => {
                return allAppointments
                    .filter(a => {
                        const d = new Date(a.appointment_date);
                        return d.getMonth() === i && d.getFullYear() === currentYear && a.status === 'completed';
                    })
                    .reduce((sum, a) => sum + (parseFloat(a.charge) || 0), 0);
            });

            const maxRevenue = Math.max(...revenues, 1000); // Avoid divide by zero

            container.innerHTML = months.map((month, i) => `
                <div style="text-align: center;">
                    <div style="height: 150px; display: flex; flex-direction: column; justify-content: flex-end; align-items: center;">
                        <div style="width: 40px; height: ${Math.max((revenues[i] / maxRevenue) * 100, 2)}%; background: linear-gradient(180deg, var(--primary-start), var(--secondary-start)); border-radius: var(--border-radius-sm);"></div>
                    </div>
                    <div style="margin-top: var(--spacing-sm); font-weight: 600;">${month}</div>
                    <div style="font-size: var(--font-size-xs); color: var(--color-text-muted);">₹${(revenues[i] / 1000).toFixed(0)}K</div>
                </div>
            `).join('');
        }

        function renderTopDoctors() {
            const container = document.getElementById('topDoctors');

            // Calculate appointments per doctor
            const doctorStats = allDoctors.map(d => ({
                ...d,
                appointmentCount: allAppointments.filter(a => a.doctor_id == d.id).length
            })).sort((a, b) => b.appointmentCount - a.appointmentCount).slice(0, 5);

            if (doctorStats.length === 0 || doctorStats.every(d => d.appointmentCount === 0)) {
                container.innerHTML = '<p style="color: var(--color-text-muted);">No appointment data available</p>';
                return;
            }

            const maxApts = Math.max(...doctorStats.map(d => d.appointmentCount)) || 1;

            container.innerHTML = doctorStats.map((d, i) => `
                <div style="display: flex; align-items: center; gap: var(--spacing-md); padding: var(--spacing-md) 0; border-bottom: 1px solid var(--glass-border);">
                    <span style="width: 24px; font-weight: 700; color: ${i === 0 ? 'gold' : i === 1 ? 'silver' : i === 2 ? '#cd7f32' : 'var(--color-text-muted)'};">#${i + 1}</span>
                    <div class="user-avatar" style="width: 36px; height: 36px; font-size: var(--font-size-sm);">${getInitials(d.name)}</div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600;">${d.name}</div>
                        <div style="font-size: var(--font-size-sm); color: var(--color-text-muted);">${d.specialization || 'Doctor'}</div>
                    </div>
                    <div style="flex: 2;">
                        <div style="height: 8px; background: var(--glass-border); border-radius: var(--border-radius-xl); overflow: hidden;">
                            <div style="height: 100%; width: ${(d.appointmentCount / maxApts) * 100}%; background: linear-gradient(90deg, var(--primary-start), var(--secondary-start));"></div>
                        </div>
                    </div>
                    <span style="font-weight: 600; min-width: 60px; text-align: right;">${d.appointmentCount} apts</span>
                </div>
            `).join('');
        }

        function renderStatusDistribution() {
            const container = document.getElementById('statusDistribution');
            const statuses = ['completed', 'confirmed', 'pending', 'cancelled'];
            const colors = { completed: '#10b981', confirmed: '#3b82f6', pending: '#f59e0b', cancelled: '#ef4444' };

            container.innerHTML = statuses.map(status => {
                const count = allAppointments.filter(a => a.status === status).length;
                const color = colors[status] || '#6b7280';
                return `
                    <div style="padding: var(--spacing-lg); background: ${color}15; border-left: 4px solid ${color}; border-radius: var(--border-radius-md);">
                        <div style="font-size: var(--font-size-2xl); font-weight: 800; color: ${color};">${count}</div>
                        <div style="text-transform: capitalize; color: var(--color-text-secondary);">${status}</div>
                    </div>
                `;
            }).join('');
        }

        function exportData(type) {
            let data;
            const timestamp = new Date().toISOString().split('T')[0];
            switch (type) {
                case 'appointments': data = allAppointments; break;
                case 'patients': data = allPatients; break;
                case 'doctors': data = allDoctors; break;
            }
            if (!data) return;

            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${type}_export_${timestamp}.json`;
            a.click();
            SmartClinic.showNotification(`${type} exported!`, 'success');
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadReportsData();
        });
    </script>
</body>

</html>

