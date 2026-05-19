<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin-dashboard.php");
        exit;
    } else {
        // If logged in as someone else (like patient), log them out so they can log in as admin
        session_unset();
        session_destroy();
        session_start();
    }
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $db = get_db_connection();
    $stmt = $db->prepare("SELECT id, password, role, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] === 'admin') {
            if ($user['status'] !== 'active') {
                $error = "Account is inactive.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $email;
                $_SESSION['name'] = 'Administrator';

                if ($user['role'] === 'admin') {
                    header("Location: admin-dashboard.php");
                } else {

                }
                exit;
            }
        } else {
            $error = "Access denied. Admin portal only.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SUDAMA CLINIC</title>
    <meta name="description" content="Access SUDAMA CLINIC admin portal.">
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
        <div class="auth-card fade-in admin-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="images/logo.png" alt="SUDAMA CLINIC">
                    <h1 class="text-gradient">SUDAMA CLINIC</h1>
                </div>
                <h2>Admin Portal</h2>
                <p>Clinic staff and administrators only</p>
                <div class="badge badge-warning" style="font-size: 0.75rem;">🔒 Secure Access</div>
            </div>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="adminEmail" class="form-label">Admin Email</label>
                    <input type="email" name="email" id="adminEmail" class="form-control" placeholder="admin@smartclinic.com" required>
                </div>

                <div class="form-group">
                    <label for="adminPassword" class="form-label">Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="password" id="adminPassword" class="form-control" placeholder="Enter password" required>
                        <button type="button" class="password-toggle" id="adminPasswordToggle">
                            👁️
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-secondary btn-block btn-lg">
                    Access Admin Panel
                </button>
            </form>

            <div class="auth-footer">
                <a href="index.php" class="back-home">← Back to Home</a>
            </div>
        </div>

        <div class="auth-illustration slide-up">
            <img src="images/admin-illustration.png" alt="Admin Dashboard">
            <div class="illustration-text">
                <h3>Admin Dashboard</h3>
                <p>Manage patient records, appointments, and clinic operations efficiently.</p>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        // Password toggle
        document.getElementById('adminPasswordToggle').addEventListener('click', function () {
            const passwordField = document.getElementById('adminPassword');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>
</body>
</html>
