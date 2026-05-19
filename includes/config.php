<?php
/**
 * SUDAMA CLINIC - Application Configuration
 */

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Disabled for production security
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error.log');

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Application constants
define('APP_NAME', 'SUDAMA CLINIC');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/Smart Clinic'); // Adjust if running in a subdir

// Upload directories
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('PROFILE_IMAGES_DIR', UPLOAD_DIR . 'profiles/');
define('MEDICAL_RECORDS_DIR', UPLOAD_DIR . 'records/');

// Pagination defaults
define('DEFAULT_PAGE_SIZE', 10);
define('MAX_PAGE_SIZE', 100);

// Appointment settings
define('SLOT_DURATION', 30); // minutes
define('WORKING_HOURS_START', '09:00');
define('WORKING_HOURS_END', '18:00');
define('LUNCH_START', '13:00');
define('LUNCH_END', '14:00');