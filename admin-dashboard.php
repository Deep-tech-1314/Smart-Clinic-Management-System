<?php
require_once 'includes/functions.php';

// Require Admin Login
// (Note: You might want a specific function or check here, defaulting to generic check)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

$db = get_db_connection();

// --- Fetch Statistics ---

// 1. Total Doctors
$stmt = $db->query("SELECT COUNT(*) FROM doctors");
$totalDoctors = $stmt->fetchColumn();

// 2. Total Patients
$stmt = $db->query("SELECT COUNT(*) FROM patients");
$totalPatients = $stmt->fetchColumn();

// 3. Today's Appointments
$today = date('Y-m-d');
$stmt = $db->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ?");
$stmt->execute([$today]);
$todayAppointmentsCount = $stmt->fetchColumn();

// 4. Today's Revenue (assuming 'charge' column exists, otherwise 0)
// 4. Today's Revenue
$stmt = $db->prepare("SELECT SUM(charge) FROM appointments WHERE appointment_date = ? AND status IN ('completed')");
$stmt->execute([$today]);
$todayRevenue = $stmt->fetchColumn() ?: 0; 

// --- Fetch Recent Doctors ---
$stmt = $db->query("SELECT d.*, s.name as specialization 
                    FROM doctors d 
                    LEFT JOIN specializations s ON d.specialization_id = s.id 
                    ORDER BY d.id DESC LIMIT 5");
$recentDoctors = $stmt->fetchAll();

// --- Fetch Today's Appointments ---
$sqlKey = "SELECT a.*, p.first_name, p.last_name, d.name as doctor_name 
           FROM appointments a 
           JOIN patients p ON a.patient_id = p.id 
           JOIN doctors d ON a.doctor_id = d.id 
           WHERE a.appointment_date = ? 
           ORDER BY a.appointment_time ASC LIMIT 5";
$stmt = $db->prepare($sqlKey);
$stmt->execute([$today]);
$todayAppointments = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SUDAMA CLINIC</title>
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
    <nav class="navbar" id="navbar">
        <div class="container navbar-container">
            <button class="navbar-toggle" onclick="toggleSidebar()">☰</button>
            <a href="index.php" class="navbar-brand">
                <img src="images/logo.png" alt="SUDAMA CLINIC Logo">
                <span class="text-gradient">SUDAMA CLINIC</span>
            </a>
            <ul class="navbar-nav">
                <li><a href="admin-dashboard.php" class="nav-link active">Dashboard</a></li>
                <li><a href="manage-doctors.php" class="nav-link">Doctors</a></li> <!-- Placeholder link -->
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/admin_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="page-header">
                <div>
                    <h1>Admin <span class="text-gradient">Dashboard</span></h1>
                    <p style="color: var(--color-text-muted);">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
                </div>
                <a href="add-doctor.php" class="btn btn-primary">➕ Add New Doctor</a>
            </div>

            <!-- Stats Grid -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon primary">👨‍⚕️</div></div>
                    <div class="stat-value"><?php echo $totalDoctors; ?></div>
                    <div class="stat-label">Total Doctors</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon success">👥</div></div>
                    <div class="stat-value"><?php echo $totalPatients; ?></div>
                    <div class="stat-label">Total Patients</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon warning">📅</div></div>
                    <div class="stat-value"><?php echo $todayAppointmentsCount; ?></div>
                    <div class="stat-label">Today's Appointments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon info">💰</div></div>
                    <div class="stat-value">₹<?php echo number_format($todayRevenue); ?></div>
                    <div class="stat-label">Today's Revenue</div>
                </div>
            </div>

            <!-- Recent Doctors -->
            <div class="form-section">
                <h3 class="form-section-title">👨‍⚕️ Recent Doctors</h3>
                <div class="table-container" style="border: none; box-shadow: none;">
                    <table class="clinic-table">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recentDoctors) > 0): ?>
                                <?php foreach ($recentDoctors as $doc): ?>
                                    <tr>
                                        <td>
                                            <div class="cell-avatar">
                                                <div class="avatar"><?php echo strtoupper(substr($doc['name'], 0, 2)); ?></div>
                                                <div class="info">
                                                    <span class="name"><?php echo htmlspecialchars($doc['name']); ?></span>
                                                    <span class="sub">ID: <?php echo $doc['id']; ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($doc['specialization'] ?? ''); ?></td>
                                        <td><span class="status-badge status-active">Active</span></td>
                                        <td>
                                            <div class="action-btns">
                                                <a href="manage-doctors.php?id=<?php echo $doc['id']; ?>" class="action-btn action-btn-view">👁️</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align:center;">No doctors found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="form-section">
                <h3 class="form-section-title">📅 Today's Appointments</h3>
                <div class="appointments-list">
                    <?php if (count($todayAppointments) > 0): ?>
                        <?php foreach ($todayAppointments as $apt): ?>
                            <div class="appointment-card">
                                <div class="appointment-info">
                                    <div class="appointment-date" style="background: linear-gradient(135deg, #8b5cf6, #6366f1);">
                                        <div class="appointment-day" style="font-size: 1rem;"><?php echo date('H:i', strtotime($apt['appointment_time'])); ?></div>
                                    </div>
                                    <div class="appointment-details">
                                        <h4><?php echo htmlspecialchars($apt['first_name'] . ' ' . $apt['last_name']); ?></h4>
                                        <p>👨‍⚕️ <?php echo htmlspecialchars($apt['doctor_name']); ?></p>
                                    </div>
                                </div>
                                <div class="appointment-actions">
                                    <span class="status-badge status-<?php echo strtolower($apt['status']); ?>"><?php echo htmlspecialchars($apt['status']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📅</div>
                            <h3>No appointments today</h3>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>
</body>
</html>
