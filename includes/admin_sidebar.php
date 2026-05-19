<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="user-profile">
            <div class="user-avatar" style="background: linear-gradient(135deg, #8b5cf6, #6366f1);"><?php echo strtoupper(substr($_SESSION['role'] ?? 'A', 0, 2)); ?></div>
            <div class="user-info">
                <h4><?php echo ucfirst($_SESSION['role'] ?? 'Admin'); ?></h4>
                <p><?php echo $_SESSION['email'] ?? 'admin@smartclinic.com'; ?></p>
            </div>
        </div>
    </div>
    <nav>
        <ul class="sidebar-nav">
            <li><a href="admin-dashboard.php" class="<?php echo ($current_page == 'admin-dashboard.php') ? 'active' : ''; ?>"><span class="nav-icon">📊</span> Dashboard</a></li>
            <li><a href="add-doctor.php" class="<?php echo ($current_page == 'add-doctor.php') ? 'active' : ''; ?>"><span class="nav-icon">➕</span> Add Doctor</a></li>
            <li><a href="manage-doctors.php" class="<?php echo ($current_page == 'manage-doctors.php') ? 'active' : ''; ?>"><span class="nav-icon">👨‍⚕️</span> Manage Doctors</a></li>
            <li><a href="admin-patients.php" class="<?php echo ($current_page == 'admin-patients.php') ? 'active' : ''; ?>"><span class="nav-icon">👥</span> Patients</a></li>
            <li><a href="admin-appointments.php" class="<?php echo ($current_page == 'admin-appointments.php') ? 'active' : ''; ?>"><span class="nav-icon">📅</span> Appointments</a></li>
            <li><a href="admin-settings.php" class="<?php echo ($current_page == 'admin-settings.php') ? 'active' : ''; ?>"><span class="nav-icon">⚙️</span> Settings</a></li>
        </ul>
    </nav>
</aside>
