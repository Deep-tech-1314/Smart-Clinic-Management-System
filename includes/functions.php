<?php
/**
 * SUDAMA CLINIC - Helper Functions
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

/**
 * Redirect to a specific URL
 */
function redirect($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit();
    } else {
        echo "<script>window.location.href='$url';</script>";
        exit();
    }
}

/**
 * Check if user is logged in
 * @param array|string|null $allowedRoles Single role or array of allowed roles
 * @return array User session data
 */
function require_login($allowedRoles = null) {
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php'); // Redirect to home so they can choose login
    }

    // Check role if specified
    if ($allowedRoles) {
        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            // Unauthorized access
            redirect('unauthorized.php'); // Need to create this or redirect to dashboard
        }
    }

    return [
        'user_id' => $_SESSION['user_id'],
        'role' => $_SESSION['role'],
        'name' => $_SESSION['name'] ?? 'User'
    ];
}

/**
 * Check if user is already logged in (for login pages)
 */
function redirect_if_logged_in() {
    if (isset($_SESSION['user_id'])) {
        $role = $_SESSION['role'];
        switch ($role) {
            case 'admin':
                redirect('admin-dashboard.php');
                break;
            case 'doctor':
                redirect('doctor-dashboard.php');
                break;
            case 'patient':
                redirect('patient-dashboard.php');
                break;

                break;
            default:
                redirect('index.php');
        }
    }
}

/**
 * Get DB Connection helper
 */
function get_db_connection() {
    $database = new Database();
    return $database->getConnection();
}
