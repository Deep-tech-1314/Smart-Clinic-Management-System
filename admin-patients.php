<?php
require_once 'includes/functions.php';

// Require Admin Login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

$db = get_db_connection();

// Fetch Patients with Last Visit and Total Visits
// Using a subquery or JOIN to get appointment stats
$query = "SELECT p.*, u.email, u.status as user_status, 
          (SELECT MAX(appointment_date) FROM appointments WHERE patient_id = p.id) as last_visit,
          (SELECT COUNT(*) FROM appointments WHERE patient_id = p.id) as total_appointments
          FROM patients p 
          JOIN users u ON p.user_id = u.id 
          ORDER BY p.id DESC";
$patients = $db->query($query)->fetchAll();

// Calculate Stats
$totalPatients = count($patients);
$activeMonth = 0;
$newWeek = 0;
$currentMonth = date('Y-m');
$oneWeekAgo = date('Y-m-d', strtotime('-1 week'));

foreach ($patients as $p) {
    if (isset($p['last_visit']) && strpos($p['last_visit'], $currentMonth) === 0) {
        $activeMonth++;
    }
    // registered_on is in patients table as created_at usually
    if (isset($p['created_at']) && substr($p['created_at'], 0, 10) >= $oneWeekAgo) {
        $newWeek++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Patients - Admin</title>
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
                <li><a href="admin-patients.php" class="nav-link active">Patients</a></li>
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
                    <h1>All <span class="text-gradient">Patients</span></h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>All Patients</span>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon primary">👥</div></div>
                    <div class="stat-value"><?php echo $totalPatients; ?></div>
                    <div class="stat-label">Total Patients</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon success">📅</div></div>
                    <div class="stat-value"><?php echo $activeMonth; ?></div>
                    <div class="stat-label">Active This Month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><div class="stat-icon warning">🆕</div></div>
                    <div class="stat-value"><?php echo $newWeek; ?></div>
                    <div class="stat-label">New This Week</div>
                </div>
            </div>

            <!-- Patients Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Patient List</h3>
                    <div class="table-actions">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Search patients..." onkeyup="filterPatients()">
                        </div>
                    </div>
                </div>
                <table class="clinic-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Contact</th>
                            <th>Last Visit</th>
                            <th>Total Visits</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patientsTable">
                        <?php foreach ($patients as $p): ?>
                            <tr class="patient-row" data-name="<?php echo strtolower($p['first_name'] . ' ' . $p['last_name']); ?>" data-email="<?php echo strtolower($p['email']); ?>" data-phone="<?php echo $p['phone']; ?>">
                                <td>
                                    <div class="cell-avatar">
                                        <div class="avatar"><?php echo strtoupper(substr($p['first_name'], 0, 1) . substr($p['last_name'], 0, 1)); ?></div>
                                        <div class="info">
                                            <span class="name">
                                                <?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?>
                                                <?php if ($p['user_status'] === 'inactive'): ?>
                                                    <span class="status-badge status-cancelled">Inactive</span>
                                                <?php endif; ?>
                                            </span>
                                            <span class="sub">ID: <?php echo $p['id']; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($p['email']); ?></div>
                                    <div style="color: var(--color-text-muted); font-size: 0.875rem;"><?php echo htmlspecialchars($p['phone']); ?></div>
                                </td>
                                <td><?php echo $p['last_visit'] ? date('M d, Y', strtotime($p['last_visit'])) : 'Never'; ?></td>
                                <td><?php echo $p['total_appointments']; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <!-- View (Placeholder) -->
                                        <button class="action-btn action-btn-view" title="View Details">👁️</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
    <script>
        function filterPatients() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.patient-row');
            
            rows.forEach(row => {
                const name = row.dataset.name;
                const email = row.dataset.email;
                const phone = row.dataset.phone;
                
                if (name.includes(search) || email.includes(search) || phone.includes(search)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
