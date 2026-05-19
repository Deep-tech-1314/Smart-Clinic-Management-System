<?php
require_once 'includes/functions.php';

// Require login
$user = require_login('patient');
$patientId = $_SESSION['patient_id'];
$db = get_db_connection();

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

// Create uploads directory if not exists
$uploadDir = 'uploads/records/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle File Upload
$uploadError = null;
$uploadSuccess = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    $title = sanitize($_POST['title']);
    $type = sanitize($_POST['type']);
    $date = sanitize($_POST['date']);
    
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'pdf', 'jpeg');
        $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif', 'application/pdf');
        $mime = mime_content_type($fileTmpPath);

        if (in_array($fileExtension, $allowedfileExtensions) && in_array($mime, $allowedMimeTypes)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $db->prepare("INSERT INTO medical_records (patient_id, record_type, title, file_path, file_type, record_date) VALUES (?, ?, ?, ?, ?, ?)");
                if($stmt->execute([$patientId, $type, $title, $dest_path, $fileExtension, $date])){
                    $uploadSuccess = "File uploaded successfully.";
                } else {
                    $uploadError = "Database error.";
                }
            } else {
                $uploadError = "Error moving file.";
            }
        } else {
            $uploadError = "Invalid file type.";
        }
    } else {
        $uploadError = "No file uploaded or error occurred.";
    }
}

// Fetch Patient Statistics
// Total Visits (Completed Appointments)
$stmt = $db->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status = 'completed'");
$stmt->execute([$patientId]);
$totalVisits = $stmt->fetchColumn();

// Total Prescriptions
$stmt = $db->prepare("SELECT COUNT(*) FROM prescriptions WHERE patient_id = ?");
$stmt->execute([$patientId]);
$totalPrescriptions = $stmt->fetchColumn();

// Doctors Seen
$stmt = $db->prepare("SELECT COUNT(DISTINCT doctor_id) FROM appointments WHERE patient_id = ? AND status = 'completed'");
$stmt->execute([$patientId]);
$uniqueDoctors = $stmt->fetchColumn();

// Member Since
$stmt = $db->prepare("SELECT created_at FROM patients WHERE id = ?");
$stmt->execute([$patientId]);
$memberSince = $stmt->fetchColumn();

// Fetch Timeline (Appointments & Prescriptions)
// We'll fetch completed appointments and join with prescriptions if any
$sql = "SELECT a.*, d.name as doctor_name, s.name as specialization, 
        p.id as prescription_id, p.diagnosis 
        FROM appointments a 
        JOIN doctors d ON a.doctor_id = d.id 
        LEFT JOIN specializations s ON d.specialization_id = s.id 
        LEFT JOIN prescriptions p ON a.id = p.appointment_id 
        WHERE a.patient_id = ? AND a.status = 'completed' 
        ORDER BY a.appointment_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$patientId]);
$timeline = $stmt->fetchAll();

// Fetch Uploaded Records
$stmt = $db->prepare("SELECT * FROM medical_records WHERE patient_id = ? ORDER BY record_date DESC");
$stmt->execute([$patientId]);
$uploadedRecords = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Records - SUDAMA CLINIC</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        /* Reuse & Additions */
        .health-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .health-stat-card { background: var(--color-bg-secondary); border: 1px solid var(--glass-border); padding: 20px; border-radius: 10px; text-align: center; }
        .health-stat-value { font-size: 2rem; font-weight: bold; color: var(--primary-start); }
        
        /* Timeline Styles */
        .timeline { position: relative; padding-left: 30px; border-left: 2px solid #eee; margin-left: 20px; }
        .timeline-item { position: relative; margin-bottom: 30px; }
        .timeline-item::before { content: ''; position: absolute; left: -37px; top: 0; width: 12px; height: 12px; background: var(--primary-start); border-radius: 50%; border: 3px solid white; }
        .timeline-date { color: grey; font-size: 0.9rem; margin-bottom: 5px; }
        .timeline-content { background: var(--color-bg-secondary); border: 1px solid var(--glass-border); padding: 20px; border-radius: 10px; }
        
        .records-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .record-card { background: white; border: 1px solid #ddd; padding: 15px; border-radius: 8px; position: relative; }
        .record-card:hover { border-color: var(--primary-start); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .record-icon { font-size: 2rem; margin-bottom: 10px; color: var(--primary-start); }
        
        .upload-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .upload-modal.active { display: flex; }
        .upload-content { background: white; padding: 30px; border-radius: 10px; width: 400px; }
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
    <nav class="navbar">
        <div class="container-fluid navbar-container">
            <button class="navbar-toggle" onclick="toggleSidebar()">☰</button>
            <a href="patient-dashboard.php" class="navbar-brand">
                <img src="images/logo.png" alt="SUDAMA CLINIC">
                <span class="text-gradient">SUDAMA CLINIC</span>
            </a>
            <ul class="navbar-nav">
                <li><span><?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/patient_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="page-header">
                <div>
                   <h1>Medical <span class="text-gradient">Records</span></h1>
                </div>
                <button class="btn btn-primary" onclick="openUploadModal()">📤 Upload Record</button>
            </div>

            <?php if ($uploadSuccess): ?>
                <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin-bottom:20px;"><?php echo $uploadSuccess; ?></div>
            <?php endif; ?>
            <?php if ($uploadError): ?>
                <div class="alert alert-danger" style="background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin-bottom:20px;"><?php echo $uploadError; ?></div>
            <?php endif; ?>

            <!-- Health Stats -->
            <div class="health-stats-grid">
                <div class="health-stat-card">
                    <div class="health-stat-icon">📅</div>
                    <div class="health-stat-value"><?php echo $totalVisits; ?></div>
                    <div class="health-stat-label">Total Visits</div>
                </div>
                <div class="health-stat-card">
                    <div class="health-stat-icon">💊</div>
                    <div class="health-stat-value"><?php echo $totalPrescriptions; ?></div>
                    <div class="health-stat-label">Prescriptions</div>
                </div>
                <div class="health-stat-card">
                    <div class="health-stat-icon">👨‍⚕️</div>
                    <div class="health-stat-value"><?php echo $uniqueDoctors; ?></div>
                    <div class="health-stat-label">Doctors</div>
                </div>
                <div class="health-stat-card">
                    <div class="health-stat-icon">📆</div>
                    <div class="health-stat-value"><?php echo $memberSince ? date('Y', strtotime($memberSince)) : '-'; ?></div>
                    <div class="health-stat-label">Member Since</div>
                </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                <button class="btn btn-primary btn-sm" onclick="showTab('timeline')">Visit History</button>
                <button class="btn btn-ghost btn-sm" onclick="showTab('documents')">Uploaded Documents</button>
            </div>

            <!-- Timeline -->
            <div id="timelineSection">
                <h3>📋 Complete Medical History</h3>
                <?php if (empty($timeline)): ?>
                    <p>No completed appointments found.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($timeline as $visit): ?>
                            <div class="timeline-item">
                                <div class="timeline-date">
                                    <?php echo date('d M Y, h:i A', strtotime($visit['appointment_date'] . ' ' . $visit['appointment_time'])); ?>
                                </div>
                                <div class="timeline-content">
                                    <h4><?php echo htmlspecialchars($visit['doctor_name']); ?></h4>
                                    <p style="color: grey;"><?php echo htmlspecialchars($visit['specialization']); ?> • <?php echo ucfirst($visit['appointment_type']); ?></p>
                                    
                                    <?php if ($visit['reason']): ?>
                                        <p><strong>Reason:</strong> <?php echo htmlspecialchars($visit['reason']); ?></p>
                                    <?php endif; ?>

                                    <?php if ($visit['prescription_id']): ?>
                                        <div style="margin-top: 10px; background: #e0f7fa; padding: 10px; border-radius: 5px;">
                                            <strong>Diagnosis:</strong> <?php echo htmlspecialchars($visit['diagnosis']); ?><br>
                                            <a href="view-prescription.php?id=<?php echo $visit['prescription_id']; ?>" style="color: var(--primary-start); font-weight: bold; font-size: 0.9rem;">View Prescription</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Documents -->
            <div id="documentsSection" style="display: none;">
                <h3>📂 Uploaded Reports & Records</h3>
                <?php if (empty($uploadedRecords)): ?>
                    <p>No documents uploaded yet.</p>
                <?php else: ?>
                    <div class="records-grid">
                        <?php foreach ($uploadedRecords as $doc): ?>
                            <div class="record-card">
                                <div class="record-icon">
                                    <?php echo match($doc['file_type']) { 'pdf' => '📄', 'jpg' => '🖼️', 'png' => '🖼️', default => '📎' }; ?>
                                </div>
                                <h4><?php echo htmlspecialchars($doc['title']); ?></h4>
                                <p style="font-size: 0.8rem; color: grey;">
                                    <?php echo htmlspecialchars(ucfirst($doc['record_type'])); ?><br>
                                    <?php echo date('d M Y', strtotime($doc['record_date'])); ?>
                                </p>
                                <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank" class="btn btn-ghost btn-sm" style="width: 100%; margin-top: 10px; display: inline-block; text-align: center;">View File</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <!-- Upload Modal -->
    <div class="upload-modal" id="uploadModal">
        <div class="upload-content">
            <h3>Upload Medical Record</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload">
                
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Blood Test Report">
                </div>
                
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="lab_report">Lab Report</option>
                        <option value="imaging">X-Ray/MRI</option>
                        <option value="vaccination">Vaccination</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label>File (PDF, JPG, PNG)</label>
                    <input type="file" name="file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="button" class="btn btn-ghost" onclick="closeUploadModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUploadModal() { document.getElementById('uploadModal').classList.add('active'); }
        function closeUploadModal() { document.getElementById('uploadModal').classList.remove('active'); }

        function showTab(tab) {
            if (tab === 'timeline') {
                document.getElementById('timelineSection').style.display = 'block';
                document.getElementById('documentsSection').style.display = 'none';
            } else {
                document.getElementById('timelineSection').style.display = 'none';
                document.getElementById('documentsSection').style.display = 'block';
            }
        }
    </script>
</body>
</html>
