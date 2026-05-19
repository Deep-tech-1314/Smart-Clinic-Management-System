<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Redirect if already logged in
redirect_if_logged_in();

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = get_db_connection();
    
    $firstName = sanitize($_POST['first_name']);
    $lastName = sanitize($_POST['last_name']);
    $dob = sanitize($_POST['date_of_birth']);
    $gender = sanitize($_POST['gender']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $bloodGroup = sanitize($_POST['blood_group'] ?? '');
    $emergencyContact = sanitize($_POST['emergency_contact'] ?? '');
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            try {
                $db->beginTransaction();

                // 1. Create User
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, 'patient', 'active')");
                $stmt->execute([$email, $hashedPassword]);
                $userId = $db->lastInsertId();

                // 2. Create Patient Profile
                $stmt = $db->prepare("INSERT INTO patients (user_id, first_name, last_name, date_of_birth, gender, phone, address, city, blood_group, emergency_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $firstName, $lastName, $dob, $gender, $phone, $address, $city, $bloodGroup, $emergencyContact]);

                $db->commit();
                
                // Redirect to home with success message
                header("Location: index.php?registered=1");
                exit;

            } catch (Exception $e) {
                $db->rollBack();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Registration - SUDAMA CLINIC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/auth.css">
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
    <div class="auth-container">
        <div class="auth-card fade-in">
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="images/logo.png" alt="SUDAMA CLINIC">
                    <h1 class="text-gradient">SUDAMA CLINIC</h1>
                </div>
                <h2>Create Account</h2>
                <p>Join thousands of patients using SUDAMA CLINIC</p>
            </div>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" onsubmit="return validateForm()">
                <!-- Form Steps would normally be JS driven, keeping it simple single page for PHP version robustness, or using JS to toggle visibility -->
                
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" max="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" placeholder="+91 84880 02969" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-control">
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Emergency Contact</label>
                    <input type="tel" name="emergency_contact" class="form-control" placeholder="Emergency Contact Number">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required minlength="8">
                    <small>Min 8 chars</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                    Create Account
                </button>
            </form>

            <div class="auth-footer">
                <a href="index.php" class="back-home">← Back to Home</a>
            </div>
        </div>

        <div class="auth-illustration slide-up">
            <img src="images/patient-illustration.png" alt="Patient Registration">
            <div class="illustration-text">
                <h3>Start Your Health Journey</h3>
                <p>Create your account and get access to all SUDAMA CLINIC features including appointment booking, medical records, and more.</p>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            var p1 = document.getElementById("password").value;
            var p2 = document.getElementById("confirm_password").value;
            if(p1 != p2) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
