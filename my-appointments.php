<?php
require_once 'includes/functions.php';

// Require login
$user = require_login('patient');
$patientId = $_SESSION['patient_id'];
$db = get_db_connection();

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

// Handle Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $appointId = (int)$_POST['appointment_id'];
    $reason = sanitize($_POST['reason']);
    
    if (empty($reason)) {
        $errorMsg = "Cancellation reason is required.";
    } else {
        // Verify ownership and status
        $stmt = $db->prepare("SELECT id, doctor_id FROM appointments WHERE id = ? AND patient_id = ? AND status IN ('pending', 'confirmed')");
        $stmt->execute([$appointId, $patientId]);
        $aptCheck = $stmt->fetch();
        
        if ($aptCheck) {
            $deleteStmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
            if ($deleteStmt->execute([$appointId])) {
                // Get Doctor's User ID to send a system message
                $stmtDocUser = $db->prepare("SELECT user_id FROM doctors WHERE id = ?");
                $stmtDocUser->execute([$aptCheck['doctor_id']]);
                $docUserId = $stmtDocUser->fetchColumn();
                
                if ($docUserId && isset($_SESSION['user_id'])) {
                    $patientUserId = $_SESSION['user_id'];
                    $msgText = "Patient has cancelled the appointment.\nReason for cancellation: " . $reason;
                    $msgStmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, subject, message, created_at) VALUES (?, ?, 'Appointment Cancelled', ?, NOW())");
                    $msgStmt->execute([$patientUserId, $docUserId, $msgText]);
                }
                $successMsg = "Appointment cancelled successfully.";
            } else {
                $errorMsg = "Failed to cancel appointment. Please try again.";
            }
        } else {
            $errorMsg = "Invalid appointment or cannot be cancelled.";
        }
    }
}

// Filters & Pagination
$status = isset($_GET['status']) ? sanitize($_GET['status']) : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build Query
$whereSql = "WHERE a.patient_id = :pid";
$params = [':pid' => $patientId];

if ($status !== 'all') {
    $whereSql .= " AND a.status = :status";
    $params[':status'] = $status;
}

// Get Total Count for Pagination
$countStmt = $db->prepare("SELECT COUNT(*) FROM appointments a $whereSql");
$countStmt->execute($params);
$totalAppointments = $countStmt->fetchColumn();
$totalPages = ceil($totalAppointments / $limit);

// Get Appointments
$sql = "SELECT a.*, d.name as doctor_name, s.name as specialization, s.icon as specialty_icon 
        FROM appointments a 
        JOIN doctors d ON a.doctor_id = d.id 
        LEFT JOIN specializations s ON d.specialization_id = s.id 
        $whereSql 
        ORDER BY a.appointment_date DESC, a.appointment_time ASC 
        LIMIT $limit OFFSET $offset";

// Prepare and execute query with pagination
// Note: SQL defined below uses :limit and :offset

$sql = "SELECT a.*, d.name as doctor_name, s.name as specialization, s.icon as specialty_icon 
        FROM appointments a 
        JOIN doctors d ON a.doctor_id = d.id 
        LEFT JOIN specializations s ON d.specialization_id = s.id 
        $whereSql 
        ORDER BY a.appointment_date DESC, a.appointment_time ASC 
        LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$appointments = $stmt->fetchAll();

// Get Stats Counts
$stats = [];
foreach (['upcoming', 'completed', 'pending', 'cancelled'] as $s) {
    // Logic for 'upcoming' is date based + status?
    // User interface implies 'upcoming' is a status but usually it's time-based.
    // The previous API logic suggested direct status mapping OR date logic.
    // Let's check API logic from earlier `list.php`: NO, wait, earlier file was prescriptions.
    // I will stick to status column if possible, but 'upcoming' usually means pending/confirmed in future.
    // Let's use simple Status count for now to match UI labels if they map 1:1, but 'upcoming' is special.
    // Actually, let's just count purely by status for simplicity, except 'upcoming' which is likely 'confirmed' + future date.
    
    // Simplification for this view: Just count by status column
    if ($s === 'upcoming') {
         $st = $db->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND appointment_date >= CURDATE() AND status != 'cancelled'");
         $st->execute([$patientId]);
         $stats[$s] = $st->fetchColumn();
    } else {
         $st = $db->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status = ?");
         $st->execute([$patientId, $s]);
         $stats[$s] = $st->fetchColumn();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - SUDAMA CLINIC</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        /* Reuse styles */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--spacing-lg); margin-bottom: var(--spacing-xl); }
        .stat-card-mini { background: var(--color-bg-secondary); border: 1px solid var(--glass-border); border-radius: var(--border-radius-lg); padding: var(--spacing-lg); text-align: center; }
        .stat-card-mini .stat-value { font-size: 2rem; font-weight: 700; margin-bottom: 5px; }
        .stat-card-mini .stat-label { font-size: 0.8rem; text-transform: uppercase; color: var(--color-text-muted); }
        
        .filter-tabs { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; }
        .tab-btn { padding: 8px 16px; border: none; background: transparent; cursor: pointer; border-radius: 5px; color: var(--color-text-muted); }
        .tab-btn.active { background: var(--primary-start); color: white; }
        
        .appointment-card-large { background: var(--color-bg-secondary); border: 1px solid var(--glass-border); border-radius: var(--border-radius-lg); padding: 20px; margin-bottom: 20px; }
        .appointment-header { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .appointment-doctor { display: flex; gap: 15px; align-items: center; }
        .doctor-avatar { width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-start), var(--primary-end)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        .appointment-date-box { text-align: center; background: rgba(6, 182, 212, 0.1); padding: 10px; border-radius: 8px; min-width: 60px; }
        .appointment-date-box .day { font-size: 1.5rem; font-weight: bold; color: var(--primary-start); line-height: 1; }
        
        .appointment-details { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; border-top: 1px solid var(--glass-border); padding-top: 15px; margin-top: 15px; }
        .appointment-actions { margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--glass-border); display: flex; justify-content: flex-end; gap: 10px; }
        
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }
        .status-badge.pending { background: rgba(245, 158, 11, 0.1); color: orange; }
        .status-badge.confirmed { background: rgba(16, 185, 129, 0.1); color: green; }
        .status-badge.cancelled { background: rgba(239, 68, 68, 0.1); color: red; }
        .status-badge.completed { background: rgba(59, 130, 246, 0.1); color: blue; }

        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-overlay.active { display: flex; }
        .modal-content { background: var(--color-bg-primary); padding: 30px; border-radius: 10px; width: 90%; max-width: 500px; }
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
    <nav class="navbar">
        <div class="container-fluid navbar-container">
            <button class="navbar-toggle" onclick="toggleSidebar()">☰</button>
            <a href="patient-dashboard.php" class="navbar-brand">
                <img src="images/logo.png" alt="SUDAMA CLINIC">
                <span class="text-gradient">SUDAMA CLINIC</span>
            </a>
            <ul class="navbar-nav">
                <li><span style="color: var(--color-text-secondary);"><?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/patient_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="page-header">
                <h1>My <span class="text-gradient">Appointments</span></h1>
                <a href="book-appointment.php" class="btn btn-primary">📅 Book New Appointment</a>
            </div>

            <?php if (isset($successMsg)): ?>
                <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($errorMsg)): ?>
                <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo $errorMsg; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['booked'])): ?>
                <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    Appointment booked successfully!
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-card-mini">
                    <div class="stat-value"><?php echo $stats['upcoming']; ?></div>
                    <div class="stat-label">Upcoming</div>
                </div>
                <div class="stat-card-mini">
                    <div class="stat-value"><?php echo $stats['completed']; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card-mini">
                    <div class="stat-value"><?php echo $stats['pending']; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card-mini">
                    <div class="stat-value"><?php echo $stats['cancelled']; ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-tabs">
                <a href="?status=all" class="tab-btn <?php echo $status === 'all' ? 'active' : ''; ?>">All</a>
                <a href="?status=upcoming" class="tab-btn <?php echo $status === 'upcoming' ? 'active' : ''; ?>">Upcoming</a>
                <a href="?status=completed" class="tab-btn <?php echo $status === 'completed' ? 'active' : ''; ?>">Completed</a>
                <a href="?status=pending" class="tab-btn <?php echo $status === 'pending' ? 'active' : ''; ?>">Pending</a>
                <a href="?status=cancelled" class="tab-btn <?php echo $status === 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
            </div>

            <!-- List -->
            <div class="appointments-list">
                <?php if (empty($appointments)): ?>
                    <p style="text-align: center; padding: 40px; color: grey;">No appointments found.</p>
                <?php else: ?>
                    <?php foreach ($appointments as $apt): ?>
                        <div class="appointment-card-large">
                            <div class="appointment-header">
                                <div class="appointment-doctor">
                                    <div class="doctor-avatar"><?php echo strtoupper(substr($apt['doctor_name'], 0, 2)); ?></div>
                                    <div class="doctor-info">
                                        <h4><?php echo htmlspecialchars($apt['doctor_name']); ?></h4>
                                        <p><?php echo htmlspecialchars($apt['specialization']); ?></p>
                                    </div>
                                </div>
                                <div class="appointment-date-box">
                                    <div class="day"><?php echo date('d', strtotime($apt['appointment_date'])); ?></div>
                                    <div class="month"><?php echo date('M', strtotime($apt['appointment_date'])); ?></div>
                                </div>
                            </div>
                            
                            <div class="appointment-details">
                                <div><strong>Time:</strong> <?php echo date('H:i', strtotime($apt['appointment_time'])); ?></div>
                                <div><strong>Fee:</strong> ₹<?php echo number_format($apt['charge'], 2); ?></div>
                                <div><strong>Status:</strong> <span class="status-badge <?php echo $apt['status']; ?>"><?php echo ucfirst($apt['status']); ?></span></div>
                                <?php if ($apt['reason']): ?>
                                    <div style="grid-column: 1 / -1;"><strong>Reason:</strong> <?php echo htmlspecialchars($apt['reason']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="appointment-actions">
                                <?php 
                                    $isFuture = strtotime($apt['appointment_date']) >= strtotime(date('Y-m-d'));
                                    if (($apt['status'] === 'pending' || $apt['status'] === 'confirmed') && $isFuture): 
                                ?>
                                    <button class="btn btn-ghost btn-sm" onclick="openCancelModal(<?php echo $apt['id']; ?>)">Cancel Appointment</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div style="text-align: center; margin-top: 20px;">
                    <?php if ($page > 1): ?>
                        <a href="?status=<?php echo $status; ?>&page=<?php echo $page - 1; ?>" class="btn btn-ghost btn-sm">Previous</a>
                    <?php endif; ?>
                    <span style="margin: 0 10px;">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="?status=<?php echo $status; ?>&page=<?php echo $page + 1; ?>" class="btn btn-ghost btn-sm">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- Cancel Modal -->
    <div class="modal-overlay" id="cancelModal">
        <div class="modal-content">
            <h3>Cancel Appointment</h3>
            <p>Are you sure you want to cancel this appointment?</p>
            <form method="POST" action="">
                <input type="hidden" name="action" value="cancel">
                <input type="hidden" name="appointment_id" id="modalApptId">
                <textarea name="reason" class="form-control" placeholder="Reason for cancellation..." style="width: 100%; margin-top: 10px;" required></textarea>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="button" class="btn btn-ghost" onclick="closeCancelModal()">Close</button>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--error); border-color: var(--error);">Confirm Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCancelModal(id) {
            document.getElementById('modalApptId').value = id;
            document.getElementById('cancelModal').classList.add('active');
        }
        function closeCancelModal() {
            document.getElementById('cancelModal').classList.remove('active');
        }
    </script>
</body>
</html>

