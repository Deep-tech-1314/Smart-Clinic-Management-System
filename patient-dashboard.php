<?php
require_once 'includes/functions.php';

// Require patient login
$user = require_login('patient');
$patientId = $_SESSION['patient_id'];

$db = get_db_connection();

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

// 1. Get Stats
// Upcoming appointments
$stmt = $db->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = :pid AND appointment_date >= CURDATE() AND status != 'cancelled'");
$stmt->execute([':pid' => $patientId]);
$upcomingCount = $stmt->fetchColumn();

// Total visits (completed or past appointments)
$stmt = $db->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = :pid AND (status = 'completed' OR (appointment_date < CURDATE() AND status != 'cancelled'))");
$stmt->execute([':pid' => $patientId]);
$visitsCount = $stmt->fetchColumn();

// Prescriptions count
$stmt = $db->prepare("SELECT COUNT(*) FROM prescriptions WHERE patient_id = :pid");
$stmt->execute([':pid' => $patientId]);
$prescriptionsCount = $stmt->fetchColumn();

// Unread messages
$stmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = :uid AND is_read = 0");
$stmt->execute([':uid' => $user['user_id']]);
$messagesCount = $stmt->fetchColumn();

// 2. Get Upcoming Appointments (Limit 2 for dashboard)
$stmt = $db->prepare("
    SELECT a.*, d.name as doctor_name, s.name as area_of_specialization 
    FROM appointments a 
    JOIN doctors d ON a.doctor_id = d.id 
    LEFT JOIN specializations s ON d.specialization_id = s.id
    WHERE a.patient_id = :pid AND a.appointment_date >= CURDATE() AND a.status != 'cancelled'
    ORDER BY a.appointment_date ASC, a.appointment_time ASC 
    LIMIT 2
");
$stmt->execute([':pid' => $patientId]);
$upcomingAppointments = $stmt->fetchAll();

// 3. Get Recent Prescriptions (Limit 2)
$stmt = $db->prepare("
    SELECT p.*, d.name as doctor_name 
    FROM prescriptions p 
    JOIN doctors d ON p.doctor_id = d.id 
    WHERE p.patient_id = :pid 
    ORDER BY p.created_at DESC 
    LIMIT 2
");
$stmt->execute([':pid' => $patientId]);
$recentPrescriptions = $stmt->fetchAll();

// 4. Get Patient Profile (for Last Visit)
$stmt = $db->prepare("SELECT last_login FROM users WHERE id = :uid");
$stmt->execute([':uid' => $user['user_id']]);
$userProfile = $stmt->fetch();
$lastVisit = $userProfile['last_login'] ? date('d M Y', strtotime($userProfile['last_login'])) : 'First login';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - SUDAMA CLINIC</title>
    <meta name="description" content="Your personal health dashboard.">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        /* Enhanced Dashboard Styles */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: var(--spacing-2xl);
        }

        .welcome-section h1 {
            font-size: var(--font-size-3xl);
            margin-bottom: var(--spacing-xs);
        }

        .last-visit {
            color: var(--color-text-muted);
            font-size: var(--font-size-sm);
        }

        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }

        .quick-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-xl);
            background: var(--color-bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-lg);
            transition: all var(--transition-fast);
            cursor: pointer;
            text-decoration: none;
            color: var(--color-text-primary);
        }

        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-start);
        }

        .quick-action-icon-large {
            font-size: 2.5rem;
            margin-bottom: var(--spacing-md);
        }

        .quick-action-label {
            font-weight: 600;
            font-size: var(--font-size-sm);
        }

        .appointment-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg);
            background: var(--color-bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-lg);
            margin-bottom: var(--spacing-md);
            transition: all var(--transition-fast);
            cursor: pointer;
        }

        .appointment-card:hover {
            transform: translateX(5px);
            border-color: var(--primary-start);
            box-shadow: var(--shadow-md);
        }

        .appointment-info h4 {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-lg);
        }

        .appointment-meta {
            display: flex;
            gap: var(--spacing-md);
            color: var(--color-text-muted);
            font-size: var(--font-size-sm);
        }

        .status-badge {
            padding: var(--spacing-xs) var(--spacing-md);
            border-radius: 20px;
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .status-badge.confirmed {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .status-badge.cancelled {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error);
        }

        .health-tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
        }

        .health-tip-card {
            padding: var(--spacing-lg);
            background: rgba(6, 182, 212, 0.05);
            border-radius: var(--border-radius-md);
            border-left: 3px solid var(--primary-start);
        }

        .health-tip-card strong {
            display: block;
            margin-bottom: var(--spacing-xs);
            color: var(--color-text-primary);
        }

        .health-tip-card p {
            color: var(--color-text-muted);
            font-size: var(--font-size-sm);
            margin: 0;
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-3xl);
            color: var(--color-text-muted);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
            opacity: 0.5;
        }

        .prescription-card {
            padding: var(--spacing-lg);
            background: var(--color-bg-secondary);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-lg);
            margin-bottom: var(--spacing-md);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .prescription-card:hover {
            border-color: var(--primary-start);
            box-shadow: var(--shadow-md);
        }

        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: var(--spacing-sm);
        }

        .prescription-header h4 {
            margin: 0;
            font-size: var(--font-size-md);
        }

        .prescription-date {
            font-size: var(--font-size-xs);
            color: var(--color-text-muted);
        }

        .diagnosis-text {
            color: var(--color-text-secondary);
            font-size: var(--font-size-sm);
            margin: 0;
        }
        
        /* Notifications Bell */
        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: var(--spacing-sm);
            font-size: 1.25rem;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--error);
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 10px;
            min-width: 16px;
            text-align: center;
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
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container-fluid navbar-container">
            <button class="navbar-toggle" onclick="toggleSidebar()">☰</button>
            <a href="patient-dashboard.php" class="navbar-brand">
                <img src="images/logo.png" alt="SUDAMA CLINIC">
                <span class="text-gradient">SUDAMA CLINIC</span>
            </a>
            <ul class="navbar-nav">
                <li>
                    <div class="notification-bell">
                        🔔
                        <?php if ($messagesCount > 0): ?>
                        <span class="notification-badge"><?php echo $messagesCount; ?></span>
                        <?php endif; ?>
                    </div>
                </li>
                <li><span id="userName" style="color: var(--color-text-secondary);"><?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <!-- Sidebar -->
        <?php include 'includes/patient_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Welcome Header -->
            <div class="dashboard-header">
                <div class="welcome-section">
                    <h1>Welcome back, <span class="text-gradient"><?php echo htmlspecialchars($_SESSION['name']); ?></span>! 👋</h1>
                    <p class="last-visit">Last login: <?php echo $lastVisit; ?></p>
                </div>
                <a href="book-appointment.php" class="btn btn-primary btn-lg">📅 Book Appointment</a>
            </div>

            <!-- Stats Cards -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon primary">📅</div>
                    </div>
                    <div class="stat-value"><?php echo $upcomingCount; ?></div>
                    <div class="stat-label">Upcoming Appointments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon success">✓</div>
                    </div>
                    <div class="stat-value"><?php echo $visitsCount; ?></div>
                    <div class="stat-label">Total Visits</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon warning">💊</div>
                    </div>
                    <div class="stat-value"><?php echo $prescriptionsCount; ?></div>
                    <div class="stat-label">Prescriptions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon info">💬</div>
                    </div>
                    <div class="stat-value"><?php echo $messagesCount; ?></div>
                    <div class="stat-label">Unread Messages</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="form-section">
                <h3 class="form-section-title">⚡ Quick Actions</h3>
                <div class="quick-actions-grid">
                    <a href="book-appointment.php" class="quick-action-btn">
                        <div class="quick-action-icon-large">📅</div>
                        <div class="quick-action-label">Book Appointment</div>
                    </a>
                    <a href="my-appointments.php" class="quick-action-btn">
                        <div class="quick-action-icon-large">📋</div>
                        <div class="quick-action-label">View Appointments</div>
                    </a>
                    <a href="view-prescription.php" class="quick-action-btn">
                        <div class="quick-action-icon-large">💊</div>
                        <div class="quick-action-label">My Prescriptions</div>
                    </a>
                    <a href="patient-messages.php" class="quick-action-btn">
                        <div class="quick-action-icon-large">💬</div>
                        <div class="quick-action-label">Messages</div>
                    </a>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-xl);">
                <!-- Upcoming Appointments -->
                <div class="form-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                        <h3 class="form-section-title" style="margin: 0;">📅 Upcoming Appointments</h3>
                        <a href="my-appointments.php" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                    <div id="upcomingAppointments">
                        <?php if (empty($upcomingAppointments)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">📅</div>
                                <h4>No upcoming appointments</h4>
                                <p>Book your first appointment today</p>
                                <a href="book-appointment.php" class="btn btn-primary" style="margin-top: var(--spacing-md);">Book Now</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($upcomingAppointments as $apt): ?>
                                <div class="appointment-card" onclick="window.location.href='my-appointments.php'">
                                    <div class="appointment-info">
                                        <h4><?php echo htmlspecialchars($apt['doctor_name']); ?></h4>
                                        <div class="appointment-meta">
                                            <span>🏥 <?php echo htmlspecialchars($apt['area_of_specialization'] ?? 'General'); ?></span>
                                            <span>📅 <?php echo date('d M Y', strtotime($apt['appointment_date'])); ?></span>
                                            <span>🕒 <?php echo date('H:i', strtotime($apt['appointment_time'])); ?></span>
                                        </div>
                                    </div>
                                    <span class="status-badge <?php echo $apt['status']; ?>"><?php echo ucfirst($apt['status']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Prescriptions -->
                <div class="form-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                        <h3 class="form-section-title" style="margin: 0;">💊 Recent Prescriptions</h3>
                        <a href="view-prescription.php" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                    <div id="recentPrescriptions">
                        <?php if (empty($recentPrescriptions)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">💊</div>
                                <h4>No prescriptions yet</h4>
                                <p>Your prescriptions will appear here after your appointments</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentPrescriptions as $pres): ?>
                                <div class="prescription-card" onclick="window.location.href='view-prescription.php?id=<?php echo $pres['id']; ?>'">
                                    <div class="prescription-header">
                                        <h4><?php echo htmlspecialchars($pres['doctor_name']); ?></h4>
                                        <span class="prescription-date"><?php echo date('d M Y', strtotime($pres['created_at'])); ?></span>
                                    </div>
                                    <p class="diagnosis-text"><?php echo htmlspecialchars($pres['diagnosis'] ?? 'General Checkup'); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Health Tips -->
            <div class="form-section">
                <h3 class="form-section-title">💡 Daily Health Tips</h3>
                <div class="health-tips-grid">
                    <div class="health-tip-card">
                        <strong>💧 Stay Hydrated</strong>
                        <p>Drink at least 8 glasses of water daily to maintain proper body function and energy levels.</p>
                    </div>
                    <div class="health-tip-card">
                        <strong>🏃 Regular Exercise</strong>
                        <p>30 minutes of physical activity daily helps improve cardiovascular health and mental well-being.</p>
                    </div>
                    <div class="health-tip-card">
                        <strong>🥗 Healthy Diet</strong>
                        <p>Include fruits, vegetables, and whole grains in your diet for essential nutrients and vitamins.</p>
                    </div>
                    <div class="health-tip-card">
                        <strong>😴 Sleep Well</strong>
                        <p>Aim for 7-8 hours of quality sleep to support immune function and cognitive performance.</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Animations or minor interactivity can stay here
        // But data is now server-rendered
    </script>
</body>
</html>

