<?php
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($unreadCount)) {
    if(!isset($db)) $db = get_db_connection();
    if(isset($_SESSION['user_id'])){
        $unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
        $unreadStmt->execute([$_SESSION['user_id']]);
        $unreadCount = $unreadStmt->fetchColumn();
    }
}
?>
<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="user-profile">
            <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['name'] ?? 'P', 0, 2)); ?></div>
            <div class="user-info">
                <h4><?php echo htmlspecialchars($_SESSION['name'] ?? 'Patient'); ?></h4>
                <p>Patient</p>
            </div>
        </div>
    </div>
    <nav>
        <ul class="sidebar-nav">
            <li><a href="patient-dashboard.php" class="<?php echo ($current_page == 'patient-dashboard.php') ? 'active' : ''; ?>"><span class="nav-icon">📊</span> Dashboard</a></li>
            <li><a href="patient-messages.php" class="<?php echo ($current_page == 'patient-messages.php') ? 'active' : ''; ?>"><span class="nav-icon">💬</span> Messages
                <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                    <span style="background: var(--accent-warning, red); width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-left: 5px;"></span>
                <?php endif; ?>
            </a></li>
            <li><a href="patient-doctors.php" class="<?php echo ($current_page == 'patient-doctors.php') ? 'active' : ''; ?>"><span class="nav-icon">👨‍⚕️</span> Our Doctors</a></li>
            <li><a href="compare-doctors.php" class="<?php echo ($current_page == 'compare-doctors.php') ? 'active' : ''; ?>"><span class="nav-icon">⚖️</span> Compare Doctors</a></li>
            <li><a href="book-appointment.php" class="<?php echo ($current_page == 'book-appointment.php') ? 'active' : ''; ?>"><span class="nav-icon">📅</span> Book Appointment</a></li>
            <li><a href="my-appointments.php" class="<?php echo ($current_page == 'my-appointments.php') ? 'active' : ''; ?>"><span class="nav-icon">📋</span> My Appointments</a></li>
            <li><a href="medical-records.php" class="<?php echo ($current_page == 'medical-records.php') ? 'active' : ''; ?>"><span class="nav-icon">📄</span> Medical Records</a></li>
            <li><a href="view-prescription.php" class="<?php echo ($current_page == 'view-prescription.php') ? 'active' : ''; ?>"><span class="nav-icon">💊</span> Prescriptions</a></li>
            <li><a href="patient-settings.php" class="<?php echo ($current_page == 'patient-settings.php') ? 'active' : ''; ?>"><span class="nav-icon">⚙️</span> Settings</a></li>
        </ul>
    </nav>
</aside>
