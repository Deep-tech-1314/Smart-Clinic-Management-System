<?php
require_once 'includes/functions.php';

// If logged in as doctor, redirect to dashboard. If other role, log them out.
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'doctor') {
        redirect('doctor-dashboard.php');
    } else {
        session_unset();
        session_destroy();
        session_start();
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $db = get_db_connection();
        // Check user existence and role
        $stmt = $db->prepare("SELECT id, password, role, status FROM users WHERE email = :email AND role = 'doctor' LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $error = "Your account is currently inactive. Please contact admin.";
            } else {
                // Login Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $email;
                
                // Get Doctor Details
                $stmtDoctor = $db->prepare("SELECT id, name, specialization_id FROM doctors WHERE user_id = :user_id");
                $stmtDoctor->execute([':user_id' => $user['id']]);
                $doctor = $stmtDoctor->fetch();
                
                if ($doctor) {
                    $_SESSION['doctor_id'] = $doctor['id'];
                    $_SESSION['name'] = $doctor['name'];
                    
                    // Get Specialization Name if needed, or just store ID
                    // For dashboard display, name is better.
                    // Doing a quick fetch for spec name if needed, but for now ID is likely enough or we can join.
                    // Let's keep it simple for now.
                } else {
                    // Fallback if doctor profile missing (shouldn't happen if created properly via admin)
                    $_SESSION['name'] = 'Doctor';
                }

                redirect('doctor-dashboard.php');
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Login - SUDAMA CLINIC</title>
    <meta name="description" content="Doctor portal login for SUDAMA CLINIC.">
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
        <div class="auth-card fade-in" style="border-color: var(--accent-success);">
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="images/logo.png" alt="SUDAMA CLINIC">
                    <h1 class="text-gradient">SUDAMA CLINIC</h1>
                </div>
                <h2>Doctor Portal</h2>
                <p>Access your medical dashboard</p>
                <div class="badge badge-success" style="font-size: 0.75rem;">🩺 For Registered Doctors</div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form id="doctorLoginForm" class="auth-form" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="doctor@smartclinic.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" id="passwordToggle">👁️</button>
                    </div>
                </div>

                <div class="form-options">
                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember" class="form-check-input">
                        <label for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="background: linear-gradient(135deg, #10b981, #059669);">
                    🩺 Sign In to Dashboard
                </button>
            </form>

            <div class="auth-footer">
                <p class="text-center" style="color: var(--color-text-muted); font-size: 0.875rem;">
                    Contact admin if you need access credentials
                </p>
                <a href="index.php" class="back-home">← Back to Home</a>
            </div>
        </div>

        <div class="auth-illustration slide-up">
            <img src="images/doctor-illustration.png" alt="Doctor Portal">
            <div class="illustration-text">
                <h3>Welcome, Doctor!</h3>
                <p>Manage your appointments, write prescriptions, and communicate with patients.</p>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.getElementById('passwordToggle').addEventListener('click', function () {
            const field = document.getElementById('password');
            const type = field.type === 'password' ? 'text' : 'password';
            field.type = type;
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>
</body>
</html>
