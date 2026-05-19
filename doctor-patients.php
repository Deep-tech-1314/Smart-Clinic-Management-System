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

// Get Unique Patients for this doctor
$stmt = $db->prepare("SELECT DISTINCT p.* FROM patients p 
                      JOIN appointments a ON p.id = a.patient_id 
                      WHERE a.doctor_id = ?");
$stmt->execute([$doctorId]);
$patients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Patients - Doctor Dashboard</title>
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
                <li><a href="doctor-appointments.php" class="nav-link">Appointments</a></li>
                <li><a href="doctor-schedule.php"><span class="nav-icon">🕒</span> Schedule</a></li>
                <li><a href="doctor-patients.php" class="nav-link active">My Patients</a></li>
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
                    <h1>My <span class="text-gradient">Patients</span></h1>
                    <div class="breadcrumb">
                        <a href="doctor-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>My Patients</span>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="clinic-table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Contact</th>
                            <th>Age/Gender</th>
                             <th>Last Visit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($patients)): ?>
                            <tr><td colspan="5" style="text-align:center; padding: 30px;">No patients found yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($patients as $p): ?>
                                <tr>
                                    <td>
                                        <div class="cell-avatar">
                                            <div class="avatar"><?php echo strtoupper(substr($p['first_name'], 0, 1)); ?></div>
                                            <div class="info">
                                                <span class="name"><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></span>
                                                <span class="sub">ID: <?php echo $p['id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><?php echo htmlspecialchars($p['email'] ?? 'N/A'); ?></div>
                                        <div style="color:var(--color-text-muted); font-size:0.85em;"><?php echo htmlspecialchars($p['phone'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                            // Calculate Age
                                            $age = 'N/A';
                                            if (!empty($p['date_of_birth'])) {
                                                $dob = new DateTime($p['date_of_birth']);
                                                $now = new DateTime();
                                                $age = $now->diff($dob)->y;
                                            } elseif (isset($p['age'])) {
                                                $age = $p['age'];
                                            }
                                            echo ucfirst($p['gender'] ?? 'Unknown'); ?> / <?php echo $age; ?> yrs
                                    </td>
                                    <td>
                                         --
                                    </td>
                                    <td>
                                        <a href="doctor-patient-details.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-ghost" title="View History">👁️ View History</a>
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
