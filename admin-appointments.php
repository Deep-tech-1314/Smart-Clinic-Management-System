<?php
require_once 'includes/functions.php';

// Require Admin Login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

$db = get_db_connection();
$message = '';
$error = '';

// Handle Cancellation
if (isset($_POST['action']) && $_POST['action'] === 'cancel' && isset($_POST['appointment_id'])) {
    $aptId = $_POST['appointment_id'];
    try {
        $stmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$aptId]);
        $message = "Appointment deleted successfully.";
    } catch (PDOException $e) {
        $error = "Failed to cancel appointment.";
    }
}

// Build Query with Filters
$whereClauses = [];
$params = [];

// Filter: Date
$filterDate = $_GET['date'] ?? '';
if ($filterDate) {
    $whereClauses[] = "a.appointment_date = ?";
    $params[] = $filterDate;
}

// Filter: Doctor
$filterDoctor = $_GET['doctor_id'] ?? '';
if ($filterDoctor) {
    $whereClauses[] = "a.doctor_id = ?";
    $params[] = $filterDoctor;
}

// Filter: Status
$filterStatus = $_GET['status'] ?? '';
if ($filterStatus) {
    $whereClauses[] = "a.status = ?";
    $params[] = $filterStatus;
}

// Filter: Search (Simple name search)
$search = $_GET['search'] ?? '';
if ($search) {
    $whereClauses[] = "(p.first_name LIKE ? OR p.last_name LIKE ? OR d.name LIKE ?)";
    $term = "%$search%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

$sql = "SELECT a.*, p.first_name, p.last_name, p.id as patient_id, d.name as doctor_name 
        FROM appointments a 
        JOIN patients p ON a.patient_id = p.id 
        JOIN doctors d ON a.doctor_id = d.id";

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

$sql .= " ORDER BY a.appointment_date DESC, a.appointment_time ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

// Fetch Doctors for Filter Dropdown
$doctors = $db->query("SELECT id, name FROM doctors ORDER BY name")->fetchAll();

// Calculate Stats (Based on current filtered view or total? Usually Dashboard shows Total, here maybe filtered stats are useful)
// Let's show stats for the *filtered* set for context, or total if no filters.
// For simplicity, I'll calculate from the fetched array.
$totalCount = count($appointments);
$completedCount = 0;
$pendingCount = 0;
$totalRevenue = 0;

foreach ($appointments as $apt) {
    if ($apt['status'] === 'completed') {
        $completedCount++;
        // Assuming charge is a column
        $totalRevenue += (float)$apt['charge']; // If charge column exists
    } elseif ($apt['status'] === 'pending' || $apt['status'] === 'confirmed') {
        $pendingCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Appointments - Admin</title>
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
                <li><a href="admin-dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="manage-doctors.php" class="nav-link">Doctors</a></li>
                <li><a href="admin-appointments.php" class="nav-link active">Appointments</a></li>
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
                    <h1>All <span class="text-gradient">Appointments</span></h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>All Appointments</span>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success" style="background:#d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger" style="background:#f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <form method="GET" class="form-section" style="padding: 20px;">
                <div class="form-row" style="align-items: flex-end;">
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filterDate); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Doctor</label>
                        <select name="doctor_id" class="form-control">
                            <option value="">All Doctors</option>
                            <?php foreach ($doctors as $d): ?>
                                <option value="<?php echo $d['id']; ?>" <?php echo ($filterDoctor == $d['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($d['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo ($filterStatus === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo ($filterStatus === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="completed" <?php echo ($filterStatus === 'completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo ($filterStatus === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="admin-appointments.php" class="btn btn-ghost">Clear</a>
                    </div>
                </div>
            </form>

            <!-- Stats -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon primary">📅</div></div>
                    <div class="stat-value"><?php echo $totalCount; ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon success">✓</div></div>
                    <div class="stat-value"><?php echo $completedCount; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon warning">⏳</div></div>
                    <div class="stat-value"><?php echo $pendingCount; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon info">💰</div></div>
                    <div class="stat-value">₹<?php echo number_format($totalRevenue); ?></div>
                    <div class="stat-label">Revenue (Filtered)</div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Appointments</h3>
                </div>
                <table class="clinic-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date & Time</th>
                            <th>Charge</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($appointments) > 0): ?>
                            <?php foreach ($appointments as $a): ?>
                                <tr>
                                    <td><code style="font-size: 0.8rem;">#<?php echo $a['id']; ?></code></td>
                                    <td>
                                        <div class="cell-avatar">
                                            <div class="avatar"><?php echo strtoupper(substr($a['first_name'], 0, 1)); ?></div>
                                            <div class="info">
                                                <span class="name"><?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name']); ?></span>
                                                <span class="sub">ID: <?php echo $a['patient_id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
                                    <td>
                                        <div><?php echo date('M d, Y', strtotime($a['appointment_date'])); ?></div>
                                        <div style="color: var(--color-text-muted); font-size: 0.875rem;"><?php echo date('h:i A', strtotime($a['appointment_time'])); ?></div>
                                    </td>
                                    <td>₹<?php echo $a['charge']; ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($a['status']); ?>"><?php echo ucfirst($a['status']); ?></span></td>
                                    <td>
                                        <div class="action-btns">
                                            <?php if ($a['status'] !== 'cancelled' && $a['status'] !== 'completed'): ?>
                                                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                                    <input type="hidden" name="action" value="cancel">
                                                    <input type="hidden" name="appointment_id" value="<?php echo $a['id']; ?>">
                                                    <button type="submit" class="action-btn action-btn-delete" title="Cancel Appointment">❌</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center;">No appointments found using the current filters.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
