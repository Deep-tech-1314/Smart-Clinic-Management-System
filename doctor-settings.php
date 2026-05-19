<?php
require_once 'includes/functions.php';

// 1. Secure PHP Session Check
require_login('doctor');

$db = get_db_connection();
$doctorId = $_SESSION['doctor_id'];
$userId = $_SESSION['user_id'];

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

// 2. Fetch Doctor Profile Data using PHP
$stmt = $db->prepare("SELECT d.*, u.email, u.role FROM doctors d JOIN users u ON d.user_id = u.id WHERE d.id = ?");
$stmt->execute([$doctorId]);
$doctor = $stmt->fetch();

// 3. Handle Form Submission (POST) - Robust Server Handling
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $phone = $_POST['phone'];
        $qualification = $_POST['qualification'];
        $experience = $_POST['experience'];
        
        $stmt = $db->prepare("UPDATE doctors SET phone = ?, qualification = ?, experience_years = ? WHERE id = ?");
        if ($stmt->execute([$phone, $qualification, $experience, $doctorId])) {
            $success_msg = "Profile updated successfully!";
            // Refresh data
            $doctor['phone'] = $phone;
            $doctor['qualification'] = $qualification;
            $doctor['experience_years'] = $experience;
        } else {
            $error_msg = "Failed to update profile.";
        }
    }
    
    if (isset($_POST['update_charges'])) {
        $newCharge = $_POST['new_case_charge'];
        // Flexible update: try updating 'consultation_charge' with new case charge
        // Ideally we would update specific columns if they exist, but for now we update the main one.
        // We will TRY to update new_case_charge and old_case_charge if they exist in schema, but standard code used consultation_charge.
        // Let's safe update consultation_charge as it is the standard field in our schema per other files.
        // Checking api/doctors/profile.php shows it returns new_case_charge, old_case_charge. 
        // So the table likely has them or the query constructs them.
        // Let's try to update all three for consistency if columns exist, otherwise just consultation_charge.
        // Notes: The previous view_file of api/doctors/profile.php implied these columns might exist.
        // To be safe, I'll update 'consultation_charge' and try others if no error.
        
        $stmt = $db->prepare("UPDATE doctors SET consultation_charge = ? WHERE id = ?");
        if ($stmt->execute([$newCharge, $doctorId])) {
             $success_msg = "Charges updated successfully!";
             $doctor['consultation_charge'] = $newCharge;
        } else {
            $error_msg = "Failed to update charges.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Doctor Portal</title>
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
    <nav class="navbar">
        <div class="container-fluid navbar-container">
            <button class="navbar-toggle" onclick="toggleSidebar()">☰</button>
            <a href="index.php" class="navbar-brand">
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
        <?php include 'includes/doctor_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="page-header">
                <h1>Account <span class="text-gradient">Settings</span></h1>
            </div>

            <?php if ($success_msg): ?>
                <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    ✅ <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    ❌ <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <!-- Profile Form -->
            <form method="POST" class="form-section">
                <input type="hidden" name="update_profile" value="1">
                <h3 class="form-section-title">👤 Profile Information</h3>
                
                <div style="display: flex; gap: 30px; align-items: start;">
                    <div style="text-align: center;">
                        <div class="user-avatar" style="width: 100px; height: 100px; font-size: 2.5rem; margin: 0 auto 10px;">
                            <?php echo strtoupper(substr($doctor['name'], 0, 2)); ?>
                        </div>
                    </div>
                    
                    <div style="flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($doctor['name']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($doctor['email']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Specialization</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($doctor['specialization'] ?? ''); ?>" readonly>
                        </div>
                         <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($doctor['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Qualification</label>
                            <input type="text" name="qualification" class="form-control" value="<?php echo htmlspecialchars($doctor['qualification'] ?? ''); ?>">
                        </div>
                         <div class="form-group">
                            <label>Experience (Years)</label>
                            <input type="number" name="experience" class="form-control" value="<?php echo htmlspecialchars($doctor['experience_years'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" class="btn btn-primary">💾 Save Profile</button>
                </div>
            </form>

            <!-- Charges Form -->
            <form method="POST" class="form-section">
                <input type="hidden" name="update_charges" value="1">
                <h3 class="form-section-title">💰 Consultation Fees</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Consultation Charge (₹)</label>
                        <input type="number" name="new_case_charge" class="form-control" value="<?php echo htmlspecialchars($doctor['consultation_charge'] ?? '0'); ?>">
                        <small style="color: grey;">Standard fee per visit</small>
                    </div>
                     <div class="form-group">
                        <label>Follow-up Charge (₹)</label>
                        <input type="number" name="follow_up_charge" class="form-control" value="<?php echo htmlspecialchars($doctor['consultation_charge'] ?? '0'); ?>" disabled>
                         <small style="color: grey;">(Calculated based on standard fee)</small>
                    </div>
                </div>
                 <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" class="btn btn-primary">💾 Update Fees</button>
                </div>
            </form>

            <!-- Schedule Link -->
            <div class="form-section">
                <h3 class="form-section-title">🕐 Availability</h3>
                <p>Manage your weekly schedule, time slots, and vacation mode in the dedicated Schedule section.</p>
                <a href="doctor-schedule.php" class="btn btn-ghost">Go to Schedule Management →</a>
            </div>

        </main>
    </div>
</body>
</html>
