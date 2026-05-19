<?php
require_once 'includes/functions.php';

// Require login
$user = require_login('patient');
$userId = $_SESSION['user_id'];
$patientId = $_SESSION['patient_id'];
$db = get_db_connection();

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

$successMsg = '';
$errorMsg = '';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            // Update Profile
            $firstName = sanitize($_POST['first_name']);
            $lastName = sanitize($_POST['last_name']);
            $phone = sanitize($_POST['phone']);
            $dob = sanitize($_POST['date_of_birth']);
            $gender = sanitize($_POST['gender']);
            $bloodGroup = sanitize($_POST['blood_group']);
            $emergency = sanitize($_POST['emergency_contact']);
            $address = sanitize($_POST['address']);
            $city = sanitize($_POST['city']);
            $state = sanitize($_POST['state']);
            $history = sanitize($_POST['medical_history']);
            $allergies = sanitize($_POST['allergies']);

            $sql = "UPDATE patients SET 
                    first_name = ?, last_name = ?, phone = ?, date_of_birth = ?, 
                    gender = ?, blood_group = ?, emergency_contact = ?, 
                    address = ?, city = ?, state = ?, 
                    medical_history = ?, allergies = ? 
                    WHERE id = ?";
            $stmt = $db->prepare($sql);
            if ($stmt->execute([$firstName, $lastName, $phone, $dob, $gender, $bloodGroup, $emergency, $address, $city, $state, $history, $allergies, $patientId])) {
                $successMsg = "Profile updated successfully.";
                // Update session name if changed
                $_SESSION['name'] = $firstName . ' ' . $lastName;
            } else {
                $errorMsg = "Failed to update profile.";
            }

        } elseif ($_POST['action'] === 'change_password') {
            // Change Password
            $currentPass = $_POST['current_password'];
            $newPass = $_POST['new_password'];
            $confirmPass = $_POST['confirm_password'];

            if ($newPass !== $confirmPass) {
                $errorMsg = "New passwords do not match.";
            } else {
                // Verify current password
                $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $userRow = $stmt->fetch();

                if ($userRow && password_verify($currentPass, $userRow['password'])) {
                    // Update password
                    $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                    if ($stmt->execute([$hashedPass, $userId])) {
                        $successMsg = "Password changed successfully.";
                    } else {
                        $errorMsg = "Failed to update password.";
                    }
                } else {
                    $errorMsg = "Incorrect current password.";
                }
            }
        } elseif ($_POST['action'] === 'delete_account') {
            // Delete Account
            $confirmText = $_POST['confirm_text'];
            if ($confirmText === 'DELETE MY ACCOUNT') {
                $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                if ($stmt->execute([$userId])) {
                    // Logout and redirect
                    session_destroy();
                    header("Location: index.php");
                    exit;
                } else {
                    $errorMsg = "Failed to delete account.";
                }
            } else {
                $errorMsg = "Confirmation text did not match.";
            }
        }
    }
}

// Fetch Current Profile Data
$stmt = $db->prepare("SELECT p.*, u.email FROM patients p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$patientId]);
$profile = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - SUDAMA CLINIC</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        .settings-nav { display: flex; gap: 20px; border-bottom: 1px solid #ddd; margin-bottom: 20px; padding-bottom: 10px; }
        .settings-tab { cursor: pointer; padding: 10px 20px; font-weight: bold; color: grey; }
        .settings-tab.active { color: var(--primary-start); border-bottom: 2px solid var(--primary-start); }
        .section-content { display: none; }
        .section-content.active { display: block; }
        .form-group { margin-bottom: 15px; }
        .danger-zone { border: 2px solid #ef4444; background: #fef2f2; padding: 20px; border-radius: 10px; margin-top: 30px; }
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
                <li><span><?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/patient_sidebar.php'; ?>
        
        <main class="dashboard-main">
            <div class="page-header">
                <h1>Account <span class="text-gradient">Settings</span></h1>
            </div>

            <?php if ($successMsg): ?>
                <div class="alert alert-success" style="background:#d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;"><?php echo $successMsg; ?></div>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
                <div class="alert alert-danger" style="background:#f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 5px;"><?php echo $errorMsg; ?></div>
            <?php endif; ?>

            <div class="settings-nav">
                <div class="settings-tab active" onclick="showSection('profile', this)">👤 Profile</div>
                <div class="settings-tab" onclick="showSection('password', this)">🔒 Password</div>
                <div class="settings-tab" onclick="showSection('delete', this)">⚠️ Danger Zone</div>
            </div>

            <!-- Profile Form -->
            <div id="profile" class="section-content active">
                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($profile['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($profile['last_name']); ?>" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Email (Cannot change)</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($profile['email']); ?>" readonly disabled>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($profile['phone']); ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($profile['date_of_birth']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="male" <?php if($profile['gender'] == 'male') echo 'selected'; ?>>Male</option>
                                <option value="female" <?php if($profile['gender'] == 'female') echo 'selected'; ?>>Female</option>
                                <option value="other" <?php if($profile['gender'] == 'other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Blood Group</label>
                            <input type="text" name="blood_group" class="form-control" value="<?php echo htmlspecialchars($profile['blood_group'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Emergency Contact</label>
                            <input type="text" name="emergency_contact" class="form-control" value="<?php echo htmlspecialchars($profile['emergency_contact'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($profile['state'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Medical History</label>
                        <textarea name="medical_history" class="form-control"><?php echo htmlspecialchars($profile['medical_history'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Allergies</label>
                        <textarea name="allergies" class="form-control"><?php echo htmlspecialchars($profile['allergies'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>

            <!-- Password Form -->
            <div id="password" class="section-content">
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="8">
                    </div>
                    
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="8">
                    </div>

                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>

            <!-- Delete Form -->
            <div id="delete" class="section-content">
                <div class="danger-zone">
                    <h3 style="color: #dc3545;">Delete Account</h3>
                    <p>Unless you are sure, do not perform this action. It will permanently delete your account and all associated data.</p>
                    
                    <form method="POST" onsubmit="return confirm('Are you absolutely sure? This cannot be undone.');">
                        <input type="hidden" name="action" value="delete_account">
                        
                        <div class="form-group">
                            <label>Type <strong>DELETE MY ACCOUNT</strong> to confirm</label>
                            <input type="text" name="confirm_text" class="form-control" required pattern="DELETE MY ACCOUNT">
                        </div>

                        <button type="submit" class="btn btn-primary" style="background: #dc3545; border-color: #dc3545;">Delete My Account</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        function showSection(sectionId, tabElement) {
            // Hide all sections
            document.querySelectorAll('.section-content').forEach(el => el.classList.remove('active'));
            // Remove active class from all tabs
            document.querySelectorAll('.settings-tab').forEach(el => el.classList.remove('active'));
            
            // Show target section
            document.getElementById(sectionId).classList.add('active');
            // Activate target tab
            tabElement.classList.add('active');
        }
    </script>
</body>
</html>
