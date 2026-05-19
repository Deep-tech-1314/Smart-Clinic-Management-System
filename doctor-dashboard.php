<?php
require_once 'includes/functions.php';

// Check if logged in as doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: doctor-login.php");
    exit;
}

$db = get_db_connection();
$doctorId = $_SESSION['doctor_id'];
$doctorName = $_SESSION['name'];

// Fetch doctor photo
$stmt = $db->prepare("SELECT photo FROM doctors WHERE id = ?");
$stmt->execute([$doctorId]);
$doctorPhoto = $stmt->fetchColumn();

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

// 1. Get Today's Appointments
$today = date('Y-m-d');
$stmt = $db->prepare("
    SELECT a.*, p.first_name, p.last_name, p.id as patient_id 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.id 
    WHERE a.doctor_id = ? AND a.appointment_date = ? 
    ORDER BY a.appointment_time ASC
");
$stmt->execute([$doctorId, $today]);
$todayAppointments = $stmt->fetchAll();

// 2. Get Stats
// Total Unique Patients
$stmt = $db->prepare("SELECT COUNT(DISTINCT patient_id) FROM appointments WHERE doctor_id = ?");
$stmt->execute([$doctorId]);
$totalPatients = $stmt->fetchColumn();

// Pending Consultations (Pending or Confirmed today)
$completedToday = 0;
foreach ($todayAppointments as $apt) {
    if ($apt['status'] === 'completed') {
        $completedToday++;
    }
}

// 3. Get Total Active Appointments (Pending & Confirmed across all dates)
$stmt = $db->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status IN ('pending', 'confirmed')");
$stmt->execute([$doctorId]);
$totalActiveAppointments = $stmt->fetchColumn();

// 4. Get Total Completed Appointments (All Dates)
$stmt = $db->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'completed'");
$stmt->execute([$doctorId]);
$totalCompletedAppointments = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - SUDAMA CLINIC</title>
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
    <nav style="position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 70px;">
            <button class="navbar-toggle" onclick="toggleSidebar()">☰</button>
            <a href="index.php" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                <img src="images/logo.png" alt="SUDAMA CLINIC" style="height: 40px;">
                <span style="font-size: 1.5rem; font-weight: 800; background: linear-gradient(135deg, #06b6d4, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SUDAMA CLINIC</span>
            </a>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="doctor-dashboard.php" style="padding: 10px 16px; color: white; text-decoration: none; border-radius: 8px; background: rgba(6, 182, 212, 0.2); font-weight: 500;">Dashboard</a>
                <a href="doctor-appointments.php" class="nav-link-custom">Appointments</a>
                <a href="doctor-patients.php" class="nav-link-custom">My Patients</a>
                <a href="doctor-messages.php" class="nav-link-custom">Messages
                    <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                        <span style="background: var(--accent-warning, red); color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem; vertical-align: top; margin-left: 2px;"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a>
                <div style="width: 1px; height: 24px; background: rgba(255,255,255,0.2); margin: 0 8px;"></div>
                <a href="logout.php" style="padding: 10px 20px; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">Logout</a>
            </div>
        </div>
    </nav>
    <style>
        .nav-link-custom { padding: 10px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-weight: 500; }
        .nav-link-custom:hover { color: white; background: rgba(255,255,255,0.1); }
    </style>

    <div class="dashboard-layout" style="padding-top: 70px;">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/doctor_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Welcome, <span class="text-gradient"><?php echo htmlspecialchars($doctorName); ?></span>!</h1>
                <p class="dashboard-subtitle">Here's your overview for today</p>
            </div>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon primary">📅</div></div>
                    <div class="stat-value"><?php echo count($todayAppointments); ?></div>
                    <div class="stat-label">Today's Appointments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon success">👥</div></div>
                    <div class="stat-value"><?php echo $totalPatients; ?></div>
                    <div class="stat-label">Total Patients</div>
                </div>
                <!-- Pending & Confirmed (all dates) -->
                <a href="doctor-appointments.php" style="text-decoration: none; color: inherit; display: block;" class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon warning">⏳</div></div>
                    <div class="stat-value"><?php echo $totalActiveAppointments; ?></div>
                    <div class="stat-label">Pending & Confirmed</div>
                </a>
                <a href="doctor-appointments.php?status=completed&date=" style="text-decoration: none; color: inherit; display: block;" class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon info">✅</div></div>
                    <div class="stat-value">
                        <?php echo $completedToday; ?> <span style="font-size: 1.25rem; color: #94a3b8;">/ <?php echo $totalCompletedAppointments; ?></span>
                    </div>
                    <div class="stat-label">Completed (Today / All)</div>
                </a>
            </div>

            <!-- Today's Appointments -->
            <div class="form-section">
                <h3 class="form-section-title">📅 Today's Appointments</h3>
                <div class="appointments-list">
                    <?php if (empty($todayAppointments)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📅</div>
                            <h3>No appointments today</h3>
                            <p>Enjoy your free day!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($todayAppointments as $apt): ?>
                            <?php 
                                $bgStyle = 'linear-gradient(135deg, var(--primary-start), var(--primary-end))';
                                if ($apt['status'] === 'completed') $bgStyle = 'var(--accent-success)';
                                elseif ($apt['status'] === 'pending') $bgStyle = 'var(--accent-warning)';
                            ?>
                            <div class="appointment-card">
                                <div class="appointment-info">
                                    <div class="appointment-date" style="background: <?php echo $bgStyle; ?>;">
                                        <div style="font-size: 1.25rem; font-weight: bold;"><?php echo date('H:i', strtotime($apt['appointment_time'])); ?></div>
                                    </div>
                                    <div class="appointment-details">
                                        <h4><?php echo htmlspecialchars($apt['first_name'] . ' ' . $apt['last_name']); ?></h4>
                                        <p>Patient ID: <?php echo $apt['patient_id']; ?></p>
                                        <p>Status: <span class="status-badge status-<?php echo $apt['status']; ?>"><?php echo ucfirst($apt['status']); ?></span></p>
                                    </div>
                                </div>
                                <div class="appointment-actions">
                                    <a href="doctor-patient-details.php?id=<?php echo $apt['patient_id']; ?>" class="btn btn-ghost btn-sm">View Patient</a>
                                    <?php if ($apt['status'] !== 'completed' && $apt['status'] !== 'cancelled'): ?>
                                        <a href="prescription.php?appointment_id=<?php echo $apt['id']; ?>" class="btn btn-primary btn-sm">Start Consultation</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">⚡ Quick Actions</h3>
                <div class="quick-actions">
                    <a href="doctor-appointments.php" class="quick-action-card">
                        <div class="quick-action-icon">📋</div>
                        <div class="quick-action-label">View All Appointments</div>
                    </a>
                    <a href="doctor-messages.php" class="quick-action-card">
                        <div class="quick-action-icon">📨</div>
                        <div class="quick-action-label">Send Message</div>
                    </a>
                </div>
            </div>

        </main>
    </div>
    <script src="js/main.js"></script>
</body>
</html>
