<?php
require_once 'includes/functions.php';

// If logged in as patient, redirect to dashboard. If other role, log them out.
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'patient') {
        redirect('patient-dashboard.php');
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
        $stmt = $db->prepare("SELECT id, password, role, status FROM users WHERE email = :email AND role = 'patient' LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $error = "Your account is currently inactive. Please contact support.";
            } else {
                // Login Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $email;
                
                // Get Patient Name
                $stmtPatient = $db->prepare("SELECT first_name, last_name, id FROM patients WHERE user_id = :user_id");
                $stmtPatient->bindParam(':user_id', $user['id']);
                $stmtPatient->execute();
                $patient = $stmtPatient->fetch();
                
                $_SESSION['name'] = $patient ? $patient['first_name'] . ' ' . $patient['last_name'] : 'Patient';
                $_SESSION['patient_id'] = $patient ? $patient['id'] : null;

                redirect('patient-dashboard.php');
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
    <title>Patient Login - SUDAMA CLINIC</title>
    <meta name="description"
        content="Access your SUDAMA CLINIC patient portal. View appointments, medical records, and prescriptions.">
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
                <h2>Patient Login</h2>
                <p>Access your health dashboard</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form id="patientLoginForm" class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="your.email@example.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" id="passwordToggle">
                            👁️
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember" class="form-check-input">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    Sign In
                </button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="patient-register.php">Register here</a></p>
                <a href="index.php" class="back-home">← Back to Home</a>
            </div>
        </div>

        <div class="auth-illustration slide-up">
            <img src="images/patient-illustration.png" alt="Patient Portal">
            <div class="illustration-text">
                <h3>Welcome Back!</h3>
                <p>Access your medical records and manage appointments with ease.</p>
            </div>
        </div>
    </div>

    <!-- Removed js/api.js and js/auth.js as we are using native PHP auth now -->
    <script src="js/main.js"></script> 
    <script>
        // Password toggle
        document.getElementById('passwordToggle').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>

</body>

</html>
