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
if (!isset($doctorPhoto)) {
    if(!isset($db)) $db = get_db_connection();
    if(isset($_SESSION['doctor_id'])){
        $stmt_photo = $db->prepare("SELECT photo FROM doctors WHERE id = ?");
        $stmt_photo->execute([$_SESSION['doctor_id']]);
        $doctorPhoto = $stmt_photo->fetchColumn();
    }
}
?>
<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="user-profile">
            <div class="user-avatar" id="doctorAvatar" style="overflow:hidden; display:flex; align-items:center; justify-content:center;">
                <?php if (!empty($doctorPhoto) && file_exists($doctorPhoto)): ?>
                    <img src="<?php echo htmlspecialchars($doctorPhoto); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                <?php else: ?>
                    <?php echo strtoupper(substr($_SESSION['name'] ?? 'DR', 0, 2)); ?>
                <?php endif; ?>
            </div>
            <div class="user-info">
                <h4><?php echo htmlspecialchars($_SESSION['name'] ?? 'Doctor'); ?></h4>
                <p>Doctor</p>
            </div>
        </div>
    </div>
    <nav>
        <ul class="sidebar-nav">
            <li><a href="doctor-dashboard.php" class="<?php echo ($current_page == 'doctor-dashboard.php') ? 'active' : ''; ?>"><span class="nav-icon">📊</span> Dashboard</a></li>
            <li><a href="doctor-appointments.php" class="<?php echo ($current_page == 'doctor-appointments.php') ? 'active' : ''; ?>"><span class="nav-icon">📅</span> Appointments</a></li>
            <li><a href="doctor-schedule.php" class="<?php echo ($current_page == 'doctor-schedule.php') ? 'active' : ''; ?>"><span class="nav-icon">🕒</span> Schedule</a></li>
            <li><a href="doctor-patients.php" class="<?php echo ($current_page == 'doctor-patients.php' || $current_page == 'doctor-patient-details.php') ? 'active' : ''; ?>"><span class="nav-icon">👥</span> My Patients</a></li>
            <li><a href="doctor-messages.php" class="<?php echo ($current_page == 'doctor-messages.php') ? 'active' : ''; ?>"><span class="nav-icon">💬</span> Messages
                <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                    <span style="background: var(--accent-warning, red); width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-left: 5px;"></span>
                <?php endif; ?>
            </a></li>
            <li><a href="doctor-settings.php" class="<?php echo ($current_page == 'doctor-settings.php') ? 'active' : ''; ?>"><span class="nav-icon">⚙️</span> Settings</a></li>
        </ul>
    </nav>
</aside>
