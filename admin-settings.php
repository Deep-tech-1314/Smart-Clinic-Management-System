<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
    <meta name="description" content="Admin settings and configuration.">
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
                    <h1>Admin <span class="text-gradient">Settings</span></h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>Settings</span>
                    </div>
                </div>
            </div>

            <!-- Clinic Info -->
            <div class="form-section">
                <h3 class="form-section-title">🏥 Clinic Information</h3>
                <form id="clinicForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Clinic Name</label>
                            <input type="text" class="form-control" value="SUDAMA CLINIC">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="info@smartclinic.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" value="+1 (555) 123-4567">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" value="https://smartclinic.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" rows="2">123 Healthcare St, Medical City, MC 12345</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                </form>
            </div>

            <!-- Working Hours -->
            <div class="form-section">
                <h3 class="form-section-title">🕐 Working Hours</h3>
                <div style="display: grid; gap: var(--spacing-md);">
                    <div
                        style="display: flex; align-items: center; gap: var(--spacing-lg); padding: var(--spacing-sm) 0;">
                        <span style="min-width: 100px; font-weight: 600;">Monday - Friday</span>
                        <input type="time" class="form-control" value="09:00" style="max-width: 120px;">
                        <span>to</span>
                        <input type="time" class="form-control" value="18:00" style="max-width: 120px;">
                    </div>
                    <div
                        style="display: flex; align-items: center; gap: var(--spacing-lg); padding: var(--spacing-sm) 0;">
                        <span style="min-width: 100px; font-weight: 600;">Saturday</span>
                        <input type="time" class="form-control" value="09:00" style="max-width: 120px;">
                        <span>to</span>
                        <input type="time" class="form-control" value="14:00" style="max-width: 120px;">
                    </div>
                    <div
                        style="display: flex; align-items: center; gap: var(--spacing-lg); padding: var(--spacing-sm) 0;">
                        <span style="min-width: 100px; font-weight: 600;">Sunday</span>
                        <span style="color: var(--color-text-muted);">Closed</span>
                    </div>
                </div>
            </div>

            <!-- Appointment Settings -->
            <div class="form-section">
                <h3 class="form-section-title">📅 Appointment Settings</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Slot Duration (minutes)</label>
                        <select class="form-control">
                            <option value="15">15 minutes</option>
                            <option value="30" selected>30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Advance Booking (days)</label>
                        <input type="number" class="form-control" value="30">
                    </div>
                </div>
                <div style="margin-top: var(--spacing-lg);">
                    <label style="display: flex; align-items: center; gap: var(--spacing-md); cursor: pointer;">
                        <input type="checkbox" checked style="width: 20px; height: 20px;">
                        <div>
                            <strong>Allow Online Booking</strong>
                            <p style="color: var(--color-text-muted); font-size: var(--font-size-sm); margin: 0;">
                                Patients can book appointments online</p>
                        </div>
                    </label>
                    <label
                        style="display: flex; align-items: center; gap: var(--spacing-md); cursor: pointer; margin-top: var(--spacing-md);">
                        <input type="checkbox" checked style="width: 20px; height: 20px;">
                        <div>
                            <strong>Send Reminders</strong>
                            <p style="color: var(--color-text-muted); font-size: var(--font-size-sm); margin: 0;">Send
                                appointment reminders via email/SMS</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Admin Password -->
            <div class="form-section">
                <h3 class="form-section-title">🔒 Change Admin Password</h3>
                <form id="passwordForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" placeholder="Enter current password">
                        </div>
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" placeholder="Enter new password">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">🔐 Update Password</button>
                </form>
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

        document.getElementById('clinicForm').addEventListener('submit', function (e) {
            e.preventDefault();
            SmartClinic.showNotification('Settings saved!', 'success');
        });

        document.getElementById('passwordForm').addEventListener('submit', function (e) {
            e.preventDefault();
            SmartClinic.showNotification('Password updated!', 'success');
            this.reset();
        });
    </script>
</body>

</html>

