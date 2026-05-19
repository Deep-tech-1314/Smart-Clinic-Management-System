<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - SUDAMA CLINIC</title>
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
        <div class="auth-card fade-in" style="border-color: #ef4444; border-top: 5px solid #ef4444;">
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="images/logo.png" alt="SUDAMA CLINIC">
                    <h1 class="text-gradient">SUDAMA CLINIC</h1>
                </div>
                <h2 style="color: #ef4444;">Access Denied</h2>
                <p>You do not have permission to view this page.</p>
            </div>

            <div style="text-align: center; margin-bottom: 30px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🚫</div>
                <p style="color: #64748b;">The page you are trying to access requires specific privileges or you are not logged in.</p>
            </div>

            <div class="auth-footer">
                <a href="index.php" class="btn btn-primary btn-block">Return to Home</a>
                <div class="divider"><span>OR</span></div>
                <a href="logout.php" class="btn btn-ghost btn-block">Logout & Login with different account</a>
            </div>
        </div>
    </div>
</body>
</html>
