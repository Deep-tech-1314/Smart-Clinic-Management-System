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

$message = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        // Clear existing slots for this doctor to simplify updates (or we can do upsert, but clear-insert is easier for schedule management)
        // Actually, let's update by day.
        
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        
        // First delete all existing slots for this doctor
        $stmt = $db->prepare("DELETE FROM time_slots WHERE doctor_id = ?");
        $stmt->execute([$doctorId]);
        
        $insert = $db->prepare("INSERT INTO time_slots (doctor_id, day_of_week, start_time, end_time, status) VALUES (?, ?, ?, ?, 'active')");
        
        foreach ($days as $day) {
            if (isset($_POST['day_' . $day])) {
                $start = $_POST['start_' . $day];
                $end = $_POST['end_' . $day];
                
                if ($start && $end) {
                    $insert->execute([$doctorId, $day, $start, $end]);
                }
            }
        }
        
        $db->commit();
        $message = "Schedule updated successfully!";
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Failed to update schedule: " . $e->getMessage();
    }
}

// Fetch existing schedule
$stmt = $db->prepare("SELECT * FROM time_slots WHERE doctor_id = ?");
$stmt->execute([$doctorId]);
$schedule = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE); // Group by day (needs modification in query to be useful key)

// Re-fetch properly
$stmt = $db->prepare("SELECT * FROM time_slots WHERE doctor_id = ?");
$stmt->execute([$doctorId]);
$rows = $stmt->fetchAll();
$schedule = [];
foreach ($rows as $row) {
    $schedule[$row['day_of_week']] = $row;
}


$daysOfWeek = ['Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 'Sun' => 'Sunday'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule - Doctor Dashboard</title>
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
                 <li><a href="doctor-schedule.php" class="nav-link active">Schedule</a></li>
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
                    <h1>Manage <span class="text-gradient">Schedule</span></h1>
                     <p>Set your available hours for appointments</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success" style="background:#d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
             <?php if ($error): ?>
                <div class="alert alert-danger" style="background:#f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="form-section">
                <div class="alert alert-info" style="background: rgba(6, 182, 212, 0.1); border-left: 4px solid var(--primary-start); padding: 15px; margin-bottom: 20px;">
                    <strong>ℹ️ Note:</strong> Patients can only book appointments during these hours. Uncheck a day to mark it as "Day Off".
                </div>
                
                <form method="POST">
                    <div class="schedule-grid">
                        <?php foreach ($daysOfWeek as $short => $full): ?>
                            <?php 
                                $isActive = isset($schedule[$short]);
                                $start = $isActive ? $schedule[$short]['start_time'] : '09:00';
                                $end = $isActive ? $schedule[$short]['end_time'] : '17:00';
                            ?>
                            <div class="schedule-row" style="display: flex; align-items: center; gap: 20px; padding: 15px; background: var(--color-bg-secondary); border-radius: 8px; margin-bottom: 10px; border: 1px solid var(--glass-border);">
                                <div style="width: 150px;">
                                    <label class="custom-checkbox">
                                        <input type="checkbox" name="day_<?php echo $short; ?>" <?php echo $isActive ? 'checked' : ''; ?> onchange="toggleRow(this)">
                                        <span class="checkmark"></span>
                                        <span style="font-weight: 600;"><?php echo $full; ?></span>
                                    </label>
                                </div>
                                <div class="time-inputs <?php echo $isActive ? '' : 'disabled'; ?>" style="display: flex; align-items: center; gap: 10px; opacity: <?php echo $isActive ? '1' : '0.5'; ?>;">
                                    <input type="time" name="start_<?php echo $short; ?>" class="form-control" value="<?php echo $start; ?>" <?php echo $isActive ? '' : 'disabled'; ?>>
                                    <span>to</span>
                                    <input type="time" name="end_<?php echo $short; ?>" class="form-control" value="<?php echo $end; ?>" <?php echo $isActive ? '' : 'disabled'; ?>>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary btn-lg">Save Schedule</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleRow(checkbox) {
            const row = checkbox.closest('.schedule-row');
            const inputs = row.querySelector('.time-inputs');
            const timeFields = inputs.querySelectorAll('input[type="time"]');
            
            if (checkbox.checked) {
                inputs.style.opacity = '1';
                inputs.classList.remove('disabled');
                timeFields.forEach(f => f.disabled = false);
            } else {
                inputs.style.opacity = '0.5';
                inputs.classList.add('disabled');
                timeFields.forEach(f => f.disabled = true);
            }
        }
    </script>
</body>
</html>
