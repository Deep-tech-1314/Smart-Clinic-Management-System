<?php
require_once 'includes/functions.php';

// Require Admin Login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

$db = get_db_connection();
$message = '';
$error = '';

// Handle Delete Action
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['doctor_id'])) {
    $doctorId = $_POST['doctor_id'];
    try {
        $db->beginTransaction();
        
        // Get user_id to delete user record too
        $stmt = $db->prepare("SELECT user_id FROM doctors WHERE id = ?");
        $stmt->execute([$doctorId]);
        $doctor = $stmt->fetch();
        
        if ($doctor) {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$doctor['user_id']]); // Cascade delete should handle doctor record, but let's be safe
            // If cascade is on, doctor record is gone. If not, we might need to delete from doctors table too manually unless FK constraints handle it.
            // Based on schema `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`, deleting user should delete doctor.
        }
        
        $db->commit();
        $message = "Doctor deleted successfully.";
    } catch (PDOException $e) {
        $db->rollBack();
        $error = "Failed to delete doctor: " . $e->getMessage();
    }
}

// Handle Status Toggle
if (isset($_POST['action']) && $_POST['action'] === 'toggle_status' && isset($_POST['doctor_id'])) {
    $doctorId = $_POST['doctor_id'];
    $currentStatus = $_POST['current_status'];
    $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';
    
    try {
        // Update users table status primarily (login access)
        $stmt = $db->prepare("UPDATE users u JOIN doctors d ON u.id = d.user_id SET u.status = ? WHERE d.id = ?");
        $stmt->execute([$newStatus, $doctorId]);
        $message = "Status updated to $newStatus.";
    } catch (Exception $e) {
        $error = "Failed to update status.";
    }
}

// Handle Photo Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo']) && isset($_POST['doctor_id'])) {
    $doctorId = (int)$_POST['doctor_id'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $file = $_FILES['photo'];
    $mime = mime_content_type($file['tmp_name']);
    
    if (in_array($file['type'], $allowedTypes) && in_array($mime, $allowedTypes)) {
        $uploadDir = 'uploads/doctors/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'doctor_' . $doctorId . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Update DB using partial path
            $dbPath = $uploadDir . $filename; 
            $stmt = $db->prepare("UPDATE doctors SET photo = ? WHERE id = ?");
            $stmt->execute([$dbPath, $doctorId]);
            $message = "Photo updated successfully!";
        } else {
            $error = "Failed to move uploaded file.";
        }
    } else {
        $error = "Invalid file type. Only JPG/PNG allowed.";
    }
}

// Fetch Doctors
$query = "SELECT d.*, s.name as specialization_name, u.email, u.status as user_status 
          FROM doctors d 
          JOIN users u ON d.user_id = u.id 
          LEFT JOIN specializations s ON d.specialization_id = s.id 
          ORDER BY d.id DESC";
$doctors = $db->query($query)->fetchAll();

// Fetch Specializations for Edit Modal
$specializations = $db->query("SELECT id, name FROM specializations ORDER BY name")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - SUDAMA CLINIC Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; animation: slideUp 0.3s ease; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
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
    <nav class="navbar" id="navbar">
        <div class="container navbar-container">
            <button class="navbar-toggle" onclick="toggleSidebar()">☰</button>
            <a href="index.php" class="navbar-brand">
                <img src="images/logo.png" alt="SUDAMA CLINIC Logo">
                <span class="text-gradient">SUDAMA CLINIC</span>
            </a>
            <ul class="navbar-nav">
                <li><a href="admin-dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="manage-doctors.php" class="nav-link active">Doctors</a></li>
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/admin_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="page-header">
                <div>
                    <h1>Manage <span class="text-gradient">Doctors</span></h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>Doctors</span>
                    </div>
                </div>
                <a href="add-doctor.php" class="btn btn-primary">➕ Add New Doctor</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success" style="background:#d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger" style="background:#f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Doctors Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">All Doctors</h3>
                    <div class="table-actions">
                        <div class="search-box">
                            <input type="text" id="searchDoctor" placeholder="Search doctors..." onkeyup="filterDoctors()">
                        </div>
                    </div>
                </div>
                <table class="clinic-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Experience</th>
                            <th>Charges</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="doctorsTableBody">
                        <?php foreach ($doctors as $doctor): ?>
                            <tr class="doctor-row" data-name="<?php echo strtolower($doctor['name']); ?>" data-spec="<?php echo strtolower($doctor['specialization_name'] ?? ''); ?>">
                                <td>
                                    <div class="cell-avatar">
                                        <?php if (!empty($doctor['photo']) && file_exists($doctor['photo'])): ?>
                                            <img src="<?php echo htmlspecialchars($doctor['photo']); ?>" class="avatar" style="object-fit:cover;" alt="Dr">
                                        <?php else: ?>
                                            <div class="avatar"><?php echo strtoupper(substr($doctor['name'], 0, 2)); ?></div>
                                        <?php endif; ?>
                                        <div class="info">
                                            <span class="name"><?php echo htmlspecialchars($doctor['name']); ?></span>
                                            <span class="sub"><?php echo htmlspecialchars($doctor['email']); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($doctor['specialization_name'] ?? ''); ?></td>
                                <td><?php echo $doctor['experience_years']; ?> years</td>
                                <td>
                                    <div style="font-size: var(--font-size-sm);">
                                        <div>New: ₹<?php echo $doctor['new_case_charge']; ?></div>
                                        <div style="color: var(--color-text-muted);">Follow-up: ₹<?php echo $doctor['old_case_charge']; ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $doctor['user_status']; ?>">
                                        <?php echo ucfirst($doctor['user_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <!-- Edit (Not fully implemented in this file for simplicity, but could pass query params) -->
                                        <!-- For now, just delete and toggle status -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                                            <input type="hidden" name="current_status" value="<?php echo $doctor['user_status']; ?>">
                                            <button type="submit" class="action-btn action-btn-toggle" title="Toggle Status">
                                                <?php echo ($doctor['user_status'] === 'active') ? '⏸️' : '▶️'; ?>
                                            </button>
                                        </form>


                                        <button class="action-btn action-btn-delete" title="Delete" onclick="openDeleteModal(<?php echo $doctor['id']; ?>)">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Delete Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content" style="text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 20px;">⚠️</div>
            <h3>Delete Doctor?</h3>
            <p>This action cannot be undone.</p>
            <form method="POST" style="margin-top: 20px;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="doctor_id" id="deleteDoctorId">
                <button type="button" class="btn btn-ghost" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444;">Delete</button>
            </form>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        function filterDoctors() {
            const search = document.getElementById('searchDoctor').value.toLowerCase();
            const rows = document.querySelectorAll('.doctor-row');
            
            rows.forEach(row => {
                const name = row.dataset.name;
                const spec = row.dataset.spec;
                if (name.includes(search) || spec.includes(search)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openDeleteModal(id) {
            document.getElementById('deleteDoctorId').value = id;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }
    </script>
</body>
</html>
