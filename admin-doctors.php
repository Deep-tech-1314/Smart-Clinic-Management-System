<?php
require_once 'includes/functions.php';

// Require Admin Login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin-login.php");
    exit;
}

$db = get_db_connection();
$message = '';

// Handle Photo Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo']) && isset($_POST['doctor_id'])) {
    $doctorId = (int)$_POST['doctor_id'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $file = $_FILES['photo'];
    
    if (in_array($file['type'], $allowedTypes)) {
        $uploadDir = 'uploads/doctors/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'doctor_' . $doctorId . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Update DB using just the filename or relative path
            $dbPath = $uploadDir . $filename; // Store relative path
            $stmt = $db->prepare("UPDATE doctors SET photo = ? WHERE id = ?");
            $stmt->execute([$dbPath, $doctorId]);
            $message = "Photo updated successfully!";
        } else {
            $message = "Failed to move uploaded file.";
        }
    } else {
        $message = "Invalid file type. Only JPG/PNG allowed.";
    }
}

// Fetch Doctors
$stmt = $db->query("SELECT d.*, u.email, s.name as specialization 
                    FROM doctors d 
                    JOIN users u ON d.user_id = u.id 
                    LEFT JOIN specializations s ON d.specialization_id = s.id 
                    ORDER BY d.name");
$doctors = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        .doctor-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 10px; overflow: hidden; box-shadow: var(--shadow-sm); }
        .doctor-table th, .doctor-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .doctor-table th { background: linear-gradient(135deg, var(--primary-start), var(--primary-end)); color: white; }
        .doctor-img-preview { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .no-photo { width: 50px; height: 50px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #ccc; }
        .btn-upload { background: var(--color-bg-secondary); border: 1px solid var(--glass-border); padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 0.9rem; }
        .upload-form { display: flex; align-items: center; gap: 10px; }
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
    <div class="dashboard-layout">
        <!-- Assuming Admin Sidebar exists or using simplified Nav -->
        <main class="dashboard-main" style="width: 100%; max-width: 1200px; margin: 0 auto;">
            <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h1>Manage <span class="text-gradient">Doctors</span></h1>
                <a href="admin-dashboard.php" class="btn btn-ghost">Back to Dashboard</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-info" onclick="this.style.display='none'"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <table class="doctor-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doctors as $doc): ?>
                        <tr>
                            <td>
                                <?php if (!empty($doc['photo']) && file_exists($doc['photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($doc['photo']); ?>" class="doctor-img-preview" alt="Doctor Photo">
                                <?php else: ?>
                                    <div class="no-photo">👨‍⚕️</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($doc['name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($doc['email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($doc['specialization'] ?? 'General'); ?></td>
                            <td><?php echo htmlspecialchars($doc['phone']); ?></td>
                            <td>
                                <form method="POST" enctype="multipart/form-data" class="upload-form">
                                    <input type="hidden" name="doctor_id" value="<?php echo $doc['id']; ?>">
                                    <input type="file" name="photo" id="file-<?php echo $doc['id']; ?>" style="display: none;" onchange="this.form.submit()" accept="image/*">
                                    <label for="file-<?php echo $doc['id']; ?>" class="btn-upload">📷 Upload Photo</label>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
