<?php
require_once 'includes/functions.php';

// Check if logged in as doctor
require_login('doctor');

$db = get_db_connection();
$doctorId = $_SESSION['doctor_id'];
$doctorName = $_SESSION['name'];

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

// Handle Status Update Action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $aptId = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'complete') {
        $stmt = $db->prepare("UPDATE appointments SET status = 'completed' WHERE id = ? AND doctor_id = ?");
        $stmt->execute([$aptId, $doctorId]);
        $msg = 'completed';
    } elseif ($action === 'cancel') {
        // Simple cancellation for now, maybe add reason modal later if requested, 
        // but user asked for "cancel appointment button add"
        $stmt = $db->prepare("DELETE FROM appointments WHERE id = ? AND doctor_id = ?");
        $stmt->execute([$aptId, $doctorId]);
        $msg = 'cancelled';
    }
    
    if (isset($msg)) {
        redirect('doctor-appointments.php?msg=' . $msg);
    }
}

// Filters
$filterDate = $_GET['date'] ?? date('Y-m-d'); // Default to today
$filterStatus = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build Query
$query = "SELECT a.*, p.first_name, p.last_name, p.id as patient_id 
          FROM appointments a 
          JOIN patients p ON a.patient_id = p.id 
          WHERE a.doctor_id = ?";
$params = [$doctorId];

if ($filterDate) {
    $query .= " AND a.appointment_date = ?";
    $params[] = $filterDate;
}

if ($filterStatus !== 'all') {
    $query .= " AND a.status = ?";
    $params[] = $filterStatus;
}

if ($search) {
    $query .= " AND (p.first_name LIKE ? OR p.last_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY a.appointment_date DESC, a.appointment_time ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Doctor Dashboard</title>
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
                <li><a href="doctor-dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="doctor-appointments.php" class="nav-link active">Appointments</a></li>
                <li><a href="doctor-messages.php" class="nav-link">Messages
                    <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                        <span style="background: var(--accent-warning, red); color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem; vertical-align: top; margin-left: 2px;"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/doctor_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="page-header">
                <div>
                    <h1>Patient <span class="text-gradient">Appointments</span></h1>
                    <div class="breadcrumb">
                        <a href="doctor-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>Appointments</span>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="form-section">
                <form method="GET" class="form-row" style="align-items: flex-end;">
                    <div class="form-group">
                        <label class="form-label">Filter by Date</label>
                        <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filterDate); ?>" onchange="this.form.submit()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="pending" <?php echo $filterStatus === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $filterStatus === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="completed" <?php echo $filterStatus === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $filterStatus === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                         <label class="form-label">Search</label>
                         <input type="text" name="search" class="form-control" placeholder="Patient Name..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="doctor-appointments.php" class="btn btn-ghost">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Appointments Table -->
            <div class="table-container">
                <table class="clinic-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Reason for Visit</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Charge</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📅</div>
                                        <h3>No appointments found</h3>
                                        <p>Try adjusting your filters.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $apt): ?>
                                <tr>
                                    <td>
                                        <div class="cell-avatar">
                                            <div class="avatar"><?php echo strtoupper(substr($apt['first_name'], 0, 1)); ?></div>
                                            <div class="info">
                                                <span class="name"><?php echo htmlspecialchars($apt['first_name'] . ' ' . $apt['last_name']); ?></span>
                                                <span class="sub">ID: <?php echo $apt['patient_id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem; max-width: 200px;">
                                            <?php if (!empty($apt['reason'])): ?>
                                                <strong>Reason:</strong> <?php echo htmlspecialchars($apt['reason']); ?><br>
                                            <?php endif; ?>
                                            <?php if ($apt['status'] === 'cancelled' && !empty($apt['cancellation_reason'])): ?>
                                                <strong style="color: var(--accent-warning);">Cancellation Reason:</strong> <?php echo htmlspecialchars($apt['cancellation_reason']); ?><br>
                                            <?php endif; ?>
                                            <?php if (!empty($apt['symptoms'])): ?>
                                                <span style="color: var(--color-text-muted); font-size: 0.85rem;"><strong>Symptoms:</strong> <?php echo htmlspecialchars($apt['symptoms']); ?></span>
                                            <?php else: ?>
                                                <span style="color: var(--color-text-muted);">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?php echo date('d M Y', strtotime($apt['appointment_date'])); ?></div>
                                        <div style="color: var(--color-text-muted); font-size: var(--font-size-sm);"><?php echo date('H:i', strtotime($apt['appointment_time'])); ?></div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $apt['status']; ?>"><?php echo ucfirst($apt['status']); ?></span>
                                    </td>
                                    <td>₹<?php echo $apt['charge']; ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <?php if ($apt['status'] !== 'completed' && $apt['status'] !== 'cancelled'): ?>
                                                <!-- Write Description / Prescribe -->
                                                <a href="prescription.php?appointment_id=<?php echo $apt['id']; ?>" class="action-btn action-btn-edit" title="Write Prescription" style="background: var(--primary-start); color: white; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 5px; text-decoration: none; margin-right: 5px;">
                                                    📝
                                                </a>

                                                <!-- Cancel -->
                                                <a href="doctor-appointments.php?action=cancel&id=<?php echo $apt['id']; ?>" class="action-btn" title="Cancel" onclick="return confirm('Are you sure you want to cancel this appointment?')" style="background: var(--accent-warning); color: white; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 5px; text-decoration: none;">
                                                    ❌
                                                </a>
                                            <?php else: ?>
                                                <span style="color: var(--color-text-muted); font-size: 0.9em;">
                                                    <?php if($apt['status'] === 'completed') echo '✅ Done'; ?>
                                                    <?php if($apt['status'] === 'cancelled') echo '❌ Cancelled'; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
