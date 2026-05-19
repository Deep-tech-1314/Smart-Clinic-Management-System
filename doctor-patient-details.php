<?php
require_once 'includes/functions.php';
require_login('doctor');

$db = get_db_connection();
$doctorId = $_SESSION['doctor_id'];
$doctorName = $_SESSION['name'];
$doctorPhoto = $_SESSION['photo'] ?? null;

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

// Get Patient ID
$patientId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$patientId) {
    header("Location: doctor-patients.php");
    exit;
}

// Fetch Patient Details
$stmt = $db->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if (!$patient) {
    die("Patient not found.");
}

// Calculate Age
$age = 'N/A';
if (!empty($patient['date_of_birth'])) {
    $dob = new DateTime($patient['date_of_birth']);
    $now = new DateTime();
    $age = $now->diff($dob)->y;
}

// Fetch Appointments History
$stmt = $db->prepare("SELECT * FROM appointments WHERE patient_id = ? AND doctor_id = ? ORDER BY appointment_date DESC, appointment_time DESC");
$stmt->execute([$patientId, $doctorId]);
$appointments = $stmt->fetchAll();

// Fetch Prescriptions History
$stmt = $db->prepare("SELECT * FROM prescriptions WHERE patient_id = ? AND doctor_id = ? ORDER BY created_at DESC");
$stmt->execute([$patientId, $doctorId]);
$prescriptions = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details - SUDAMA CLINIC</title>
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
                    <h1>Patient <span class="text-gradient">Details</span></h1>
                    <div class="breadcrumb">
                        <a href="doctor-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <a href="doctor-patients.php">My Patients</a>
                        <span>/</span>
                        <span><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></span>
                    </div>
                </div>
                <a href="doctor-patients.php" class="btn btn-ghost">← Back</a>
            </div>

            <!-- Patient Profile -->
            <div class="form-section">
                <div style="display: flex; gap: 30px; align-items: flex-start;">
                    <div class="user-avatar" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <?php echo strtoupper(substr($patient['first_name'], 0, 1)); ?>
                    </div>
                    <div style="flex: 1;">
                        <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h2>
                        <p style="color: var(--color-text-muted); margin-bottom: 20px;">Patient ID: <?php echo $patient['id']; ?></p>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                            <div>
                                <small style="color: var(--color-text-muted);">Age / Gender</small>
                                <div><?php echo $age; ?> yrs / <?php echo ucfirst($patient['gender'] ?? 'Unknown'); ?></div>
                            </div>
                            <div>
                                <small style="color: var(--color-text-muted);">Blood Group</small>
                                <div><?php echo htmlspecialchars($patient['blood_group'] ?? 'N/A'); ?></div>
                            </div>
                            <div>
                                <small style="color: var(--color-text-muted);">Phone</small>
                                <div><?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></div>
                            </div>
                            <div>
                                <small style="color: var(--color-text-muted);">Emergency Contact</small>
                                <div><?php echo htmlspecialchars($patient['emergency_contact'] ?? 'N/A'); ?></div>
                            </div>
                            <div>
                                <small style="color: var(--color-text-muted);">Email</small>
                                <div><?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?></div>
                            </div>
                            <div>
                                <small style="color: var(--color-text-muted);">Address</small>
                                <div><?php echo htmlspecialchars($patient['address'] ?? 'N/A'); ?></div>
                            </div>
                        </div>

                        <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                            <div style="margin-bottom: 15px;">
                                <small style="color: var(--color-text-muted); display: block; margin-bottom: 5px;">Medical History</small>
                                <div style="background: #f8f9fa; padding: 10px; border-radius: 5px;">
                                    <?php echo nl2br(htmlspecialchars($patient['medical_history'] ?? 'No medical history recorded.')); ?>
                                </div>
                            </div>
                            <div>
                                <small style="color: var(--color-text-muted); display: block; margin-bottom: 5px;">Allergies</small>
                                <div style="background: #fef2f2; color: #991b1b; padding: 10px; border-radius: 5px;">
                                    <?php echo nl2br(htmlspecialchars($patient['allergies'] ?? 'No known allergies.')); ?>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 30px; display: flex; gap: 10px;">
                             <!-- We need to find a way to book a new appointment or just message -->
                            <a href="doctor-messages.php" class="btn btn-ghost">💬 Send Message</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; margin-top: 30px;">
                <!-- Appointment History -->
                <div class="form-section">
                    <h3>📅 Appointment History</h3>
                    <div class="appointments-list" style="max-height: 400px; overflow-y: auto;">
                        <?php if (empty($appointments)): ?>
                            <p style="color: var(--color-text-muted);">No appointment history.</p>
                        <?php else: ?>
                            <?php foreach ($appointments as $apt): ?>
                                <div class="appointment-card" style="padding: 15px;">
                                    <div class="appointment-info">
                                        <div style="font-weight: bold;"><?php echo date('d M Y', strtotime($apt['appointment_date'])); ?></div>
                                        <div style="font-size: 0.9em; color: var(--color-text-muted);"><?php echo date('H:i', strtotime($apt['appointment_time'])); ?></div>
                                    </div>
                                    <div class="appointment-details">
                                        <span class="status-badge status-<?php echo $apt['status']; ?>"><?php echo ucfirst($apt['status']); ?></span>
                                        <?php if (!empty($apt['reason'])): ?>
                                            <div style="font-size: 0.85em; margin-top: 5px; color: var(--color-text-muted);">
                                                <?php echo htmlspecialchars(substr($apt['reason'], 0, 50)) . (strlen($apt['reason']) > 50 ? '...' : ''); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Prescription History -->
                <div class="form-section">
                    <h3>💊 Prescriptions</h3>
                    <div class="prescriptions-list" style="max-height: 400px; overflow-y: auto;">
                         <?php if (empty($prescriptions)): ?>
                            <p style="color: var(--color-text-muted);">No prescriptions found.</p>
                        <?php else: ?>
                            <?php foreach ($prescriptions as $rx): ?>
                                <div class="appointment-card" style="padding: 15px; display: block;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                        <strong><?php echo date('d M Y', strtotime($rx['created_at'])); ?></strong>
                                        <a href="view-prescription.php?id=<?php echo $rx['id']; ?>" class="btn btn-sm btn-ghost" target="_blank">View PDF</a>
                                    </div>
                                    <div style="font-size: 0.9em;">
                                        <strong>Diagnosis:</strong> <?php echo htmlspecialchars($rx['diagnosis']); ?>
                                    </div>
                                    <div style="font-size: 0.85em; color: var(--color-text-muted); margin-top: 5px;">
                                        <?php 
                                            // Extract medicine names for preview
                                            $meds = json_decode($rx['medicines'], true);
                                            if ($meds) {
                                                $names = array_column($meds, 'name');
                                                echo implode(', ', array_slice($names, 0, 3)) . (count($names) > 3 ? '...' : '');
                                            }
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>
