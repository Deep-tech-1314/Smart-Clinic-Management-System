<?php
require_once 'includes/functions.php';

// Allow both patient and doctor
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'patient' && $_SESSION['role'] !== 'doctor')) {
    header("Location: index.php");
    exit;
}

$db = get_db_connection();

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();
$viewId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$currentPrescription = null;

$patientId = null;
if ($_SESSION['role'] === 'patient') {
    $patientId = $_SESSION['patient_id'];
}

if ($viewId) {
    // If Patient: Ensure it's their prescription
    if ($_SESSION['role'] === 'patient') {
        $stmt = $db->prepare("SELECT p.*, d.name as doctor_name, d.qualification, s.name as specialization, 
                              u.email as doctor_email
                              FROM prescriptions p 
                              JOIN doctors d ON p.doctor_id = d.id 
                              JOIN users u ON d.user_id = u.id
                              LEFT JOIN specializations s ON d.specialization_id = s.id 
                              WHERE p.id = ? AND p.patient_id = ?");
        $stmt->execute([$viewId, $patientId]);
    } 
    // If Doctor: Ensure they are logged in (can view any for now or restrict to their own)
    else {
        $stmt = $db->prepare("SELECT p.*, d.name as doctor_name, d.qualification, s.name as specialization, 
                              u.email as doctor_email
                              FROM prescriptions p 
                              JOIN doctors d ON p.doctor_id = d.id 
                              JOIN users u ON d.user_id = u.id
                              LEFT JOIN specializations s ON d.specialization_id = s.id 
                              WHERE p.id = ?");
        $stmt->execute([$viewId]);
    }
    $currentPrescription = $stmt->fetch();
}

// Get Patient Info (for detail view)
if ($currentPrescription) {
    $stmt = $db->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$currentPrescription['patient_id']]);
    $patientInfo = $stmt->fetch();
}

// Filter Logic
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sql = "SELECT p.*, d.name as doctor_name, s.name as specialization 
        FROM prescriptions p 
        JOIN doctors d ON p.doctor_id = d.id 
        LEFT JOIN specializations s ON d.specialization_id = s.id 
        WHERE p.patient_id = :pid";

$params = [':pid' => $patientId];

if ($filter === 'recent') {
    $sql .= " AND p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
} elseif ($filter === 'followup') {
    $sql .= " AND p.follow_up_date > CURDATE()";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$prescriptions = $stmt->fetchAll();

// Helper to calc age
function getAge($dob) {
    $dob = new DateTime($dob);
    $now = new DateTime();
    return $now->diff($dob)->y;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescriptions - SUDAMA CLINIC</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        /* Reuse styles */
        .prescriptions-filter { display: flex; gap: 10px; margin-bottom: 20px; }
        .prescription-card { background: var(--color-bg-secondary); border: 1px solid var(--glass-border); border-radius: 10px; padding: 20px; margin-bottom: 20px; transition: all 0.2s; }
        .prescription-card:hover { transform: translateX(5px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-color: var(--primary-start); }
        .prescription-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .medicines-preview { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 5px; }
        .medicine-tag { background: var(--color-bg-primary); padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; border: 1px solid var(--glass-border); }
        .prescription-summary { background: rgba(6, 182, 212, 0.05); padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .empty-prescriptions { text-align: center; padding: 40px; color: grey; }
        
        .prescription-modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .prescription-modal-overlay.active { display: flex; }
        .prescription-modal-content { background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; position: relative; }
        .medicines-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .medicines-table th { text-align: left; background: rgba(6, 182, 212, 0.1); padding: 10px; color: var(--primary-start); border-bottom: 2px solid var(--primary-start); }
        .medicines-table th, .medicines-table td { padding: 10px; border-bottom: 1px solid var(--glass-border); }
        
        /* PRINT STYLES - PROFESSIONAL LETTERHEAD */
        @media print {
            body * { visibility: hidden; }
            .prescription-modal-content, .prescription-modal-content * { visibility: visible; }
            
            .navbar, .dashboard-sidebar, .modal-actions, .prescriptions-filter, .dashboard-main > .page-header { display: none !important; }
            
            .prescription-modal-overlay { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%; 
                height: 100%; 
                background: white; 
                display: block !important;
                z-index: 9999;
            }
            
            .prescription-modal-content { 
                box-shadow: none; 
                padding: 0; 
                width: 100%; 
                max-width: 100%; 
                position: absolute; 
                left: 0; 
                top: 0;
                overflow: visible;
                background: white;
            }

            /* Professional Header */
            .print-header {
                border-bottom: 2px solid #333;
                padding-bottom: 20px;
                margin-bottom: 30px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .print-logo-section {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .print-logo-section img {
                height: 60px;
                width: auto;
            }
            .print-clinic-info h1 {
                margin: 0;
                color: #2c3e50;
                font-size: 24px;
            }
            .print-clinic-info p {
                margin: 5px 0 0;
                color: #555;
                font-size: 14px;
            }
            .print-meta {
                text-align: right;
                font-size: 14px;
            }

            /* Doctor & Patient Info Block */
            .print-info-grid {
                display:flex;
                justify-content: space-between;
                margin-bottom: 30px;
                border: 1px solid #ddd;
                padding: 15px;
                border-radius: 5px;
            }
            .print-info-col h3 {
                margin: 0 0 10px 0;
                font-size: 16px;
                color: #333;
                text-transform: uppercase;
                border-bottom: 1px solid #eee;
                padding-bottom: 5px;
            }
            .print-info-row {
                margin-bottom: 5px;
                font-size: 14px;
            }
            .print-info-label {
                font-weight: bold;
                color: #555;
                width: 80px;
                display: inline-block;
            }

            /* Table Styling for Print */
            .medicines-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            .medicines-table th {
                background-color: #f8f9fa !important;
                color: #333 !important;
                border: 1px solid #ddd;
                padding: 10px;
                -webkit-print-color-adjust: exact;
            }
            .medicines-table td {
                border: 1px solid #ddd;
                padding: 10px;
            }

            /* Diagnosis & Sections */
            .print-section {
                margin-bottom: 20px;
            }
            .print-section-title {
                font-weight: bold;
                font-size: 16px;
                color: #333;
                margin-bottom: 10px;
                display: block;
            }
            .print-box {
                border: 1px solid #eee;
                padding: 10px;
                background: #fcfcfc;
            }

            /* Footer */
            .print-footer {
                margin-top: 50px;
                border-top: 1px solid #ddd;
                padding-top: 10px;
                display: flex;
                justify-content: space-between;
                font-size: 12px;
                color: #777;
            }
            .signature-box {
                text-align: right;
                margin-top: 40px;
            }
            .signature-line {
                border-top: 1px solid #333;
                width: 200px;
                display: inline-block;
                margin-bottom: 5px;
            }
        }
        
        /* Hide print header in screen view */
        .print-only { display: none; }
        @media print { .print-only { display: block; } }
        /* Hide screen-only elements in print */
        @media print { .screen-only { display: none; } }
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
                <li><span style="color: var(--color-text-secondary);"><?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/patient_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="page-header">
                <h1>My <span class="text-gradient">Prescriptions</span></h1>
            </div>

            <!-- Filters -->
            <div class="prescriptions-filter">
                <a href="?filter=all" class="btn btn-sm <?php echo $filter === 'all' ? 'btn-primary' : 'btn-ghost'; ?>">All Prescriptions</a>
                <a href="?filter=recent" class="btn btn-sm <?php echo $filter === 'recent' ? 'btn-primary' : 'btn-ghost'; ?>">Last 30 Days</a>
                <a href="?filter=followup" class="btn btn-sm <?php echo $filter === 'followup' ? 'btn-primary' : 'btn-ghost'; ?>">With Follow-up</a>
            </div>

            <!-- List -->
            <div id="prescriptionsList">
                <?php if (empty($prescriptions)): ?>
                    <div class="empty-prescriptions">
                        <div style="font-size: 3rem; opacity: 0.5;">💊</div>
                        <h3>No prescriptions found</h3>
                        <p>Your prescriptions will appear here after your appointments.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($prescriptions as $rx): ?>
                        <?php 
                            $meds = json_decode($rx['medicines'], true) ?? [];
                            $date = date('d M Y', strtotime($rx['created_at']));
                        ?>
                        <div class="prescription-card">
                            <div class="prescription-header">
                                <div>
                                    <h3 style="margin: 0; color: var(--primary-start);"><?php echo htmlspecialchars($rx['diagnosis'] ?? 'General Checkup'); ?></h3>
                                    <div style="color: grey; font-size: 0.9rem;">
                                        👨‍⚕️ <?php echo htmlspecialchars($rx['doctor_name']); ?> • <?php echo htmlspecialchars($rx['specialization']); ?> <br>
                                        📅 <?php echo $date; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="prescription-summary">
                                <strong>💊 Medicines:</strong>
                                <div class="medicines-preview">
                                    <?php foreach (array_slice($meds, 0, 3) as $m): ?>
                                        <span class="medicine-tag"><?php echo htmlspecialchars($m['name']); ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($meds) > 3): ?>
                                        <span class="medicine-tag">+<?php echo count($meds) - 3; ?> more</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div style="text-align: right;">
                                <a href="?id=<?php echo $rx['id']; ?>&filter=<?php echo $filter; ?>" class="btn btn-primary btn-sm">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <?php if ($viewId && $currentPrescription): ?>
    <div class="prescription-modal-overlay active" id="detailModal">
        <div class="prescription-modal-content">
            
            <!-- PRINT HEADER (Visible only in print) -->
            <div class="print-only">
                <div class="print-header">
                    <div class="print-logo-section">
                        <img src="images/logo.png" alt="Logo">
                        <div class="print-clinic-info">
                            <h1>SUDAMA CLINIC</h1>
                            <p>Excellence in Healthcare</p>
                        </div>
                    </div>
                    <div class="print-meta">
                        <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($currentPrescription['created_at'])); ?></p>
                        <p><strong>Prescription ID:</strong> #<?php echo $currentPrescription['id']; ?></p>
                    </div>
                </div>

                <div class="print-info-grid">
                    <div class="print-info-col" style="width: 48%;">
                        <h3>Doctor Details</h3>
                        <div class="print-info-row"><span class="print-info-label">Name:</span> <?php echo htmlspecialchars($currentPrescription['doctor_name']); ?></div>
                         <div class="print-info-row"><span class="print-info-label">Qual:</span> <?php echo htmlspecialchars($currentPrescription['qualification']); ?></div>
                        <div class="print-info-row"><span class="print-info-label">Specialty:</span> <?php echo htmlspecialchars($currentPrescription['specialization']); ?></div>
                        <div class="print-info-row"><span class="print-info-label">Email:</span> <?php echo htmlspecialchars($currentPrescription['doctor_email']); ?></div>
                    </div>
                    <div class="print-info-col" style="width: 48%;">
                        <h3>Patient Details</h3>
                        <div class="print-info-row"><span class="print-info-label">Name:</span> <?php echo htmlspecialchars($patientInfo['first_name'] . ' ' . $patientInfo['last_name']); ?></div>
                        <div class="print-info-row"><span class="print-info-label">Age/Sex:</span> <?php echo getAge($patientInfo['date_of_birth']); ?> Yrs / <?php echo ucfirst($patientInfo['gender']); ?></div>
                        <div class="print-info-row"><span class="print-info-label">Phone:</span> <?php echo htmlspecialchars($patientInfo['phone']); ?></div>
                         <div class="print-info-row"><span class="print-info-label">Address:</span> <?php echo htmlspecialchars($patientInfo['address']); ?></div>
                    </div>
                </div>
            </div>

            <!-- SCREEN HEADER (Hidden in print) -->
            <div class="screen-only" style="text-align: center; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
                <h2 style="margin:0; color: var(--primary-start);">🏥 SUDAMA CLINIC</h2>
                <p style="margin:0; color: grey;">Medical Prescription</p>
            </div>

            <!-- Screen Info Grid (Hidden in print as we used custom one above) -->
            <div class="screen-only" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <div>
                    <strong>Patient:</strong> <?php echo htmlspecialchars($patientInfo['first_name'] . ' ' . $patientInfo['last_name']); ?><br>
                    <small>Age: <?php echo getAge($patientInfo['date_of_birth']); ?> | Gender: <?php echo ucfirst($patientInfo['gender']); ?></small>
                </div>
                <div style="text-align: right;">
                    <strong><?php echo htmlspecialchars($currentPrescription['doctor_name']); ?></strong><br>
                    <small><?php echo htmlspecialchars($currentPrescription['qualification']) . ' - ' . htmlspecialchars($currentPrescription['specialization']); ?></small>
                </div>
            </div>

            <!-- Diagnosis -->
            <div class="print-section">
                <span class="print-section-title">Diagnosis:</span>
                <div class="print-box">
                    <?php echo htmlspecialchars($currentPrescription['diagnosis']); ?>
                </div>
            </div>

            <!-- Medicines Table -->
            <div class="print-section">
                <span class="screen-only"><h4>💊 Prescribed Medicines</h4></span>
                <span class="print-only print-section-title">Rx (Medicines):</span>
                <table class="medicines-table">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Dosage</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $meds = json_decode($currentPrescription['medicines'], true) ?? [];
                            foreach ($meds as $m): 
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($m['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($m['dosage']); ?></td>
                                <td><?php echo htmlspecialchars($m['frequency']); ?></td>
                                <td><?php echo htmlspecialchars($m['duration']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Instructions -->
            <?php if ($currentPrescription['instructions']): ?>
                <div class="print-section">
                     <span class="print-section-title">Instructions / Advice:</span>
                    <div style="padding: 15px; background: #fffbe6; border-left: 4px solid orange; border-radius: 4px;" class="print-box">
                        <?php echo nl2br(htmlspecialchars($currentPrescription['instructions'])); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Remarks -->
            <?php if (!empty($currentPrescription['remarks'])): ?>
                 <div class="print-section">
                     <span class="print-section-title">Additional Notes:</span>
                     <div style="padding: 15px; background: #f0f0f0; border-left: 4px solid #999; border-radius: 4px;" class="print-box">
                        <?php echo nl2br(htmlspecialchars($currentPrescription['remarks'])); ?>
                    </div>
                </div>
            <?php endif; ?>

             <!-- Follow up -->
            <?php if ($currentPrescription['follow_up_date']): ?>
                <div class="print-section" style="margin-top: 20px;">
                    <div style="padding: 15px; background: #e6fffa; border-left: 4px solid #00b894; border-radius: 4px;" class="print-box">
                        <strong>📅 Follow-up Required:</strong> <?php echo date('d M Y', strtotime($currentPrescription['follow_up_date'])); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- PRINT FOOTER -->
            <div class="print-only print-footer">
                <div>
                    Generated by SUDAMA CLINIC Management System<br>
                    <?php echo date('d M Y H:i:s'); ?>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div><strong><?php echo htmlspecialchars($currentPrescription['doctor_name']); ?></strong></div>
                    <div>Signature</div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="modal-actions" style="margin-top: 30px; text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                <button class="btn btn-ghost" onclick="window.print()">🖨️ Print</button>
                <a href="view-prescription.php?filter=<?php echo $filter; ?>" class="btn btn-primary">Close</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>

