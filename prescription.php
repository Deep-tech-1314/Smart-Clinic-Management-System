<?php
require_once 'includes/functions.php';
require_login('doctor');

$db = get_db_connection();
$doctorId = $_SESSION['doctor_id'];
$doctorName = $_SESSION['name'];

// Ensure prescriptions table exists
$db->exec("CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    diagnosis TEXT,
    medicines TEXT,
    notes TEXT,
    remarks TEXT,
    follow_up_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Get Appointment/Patient Details
$appointmentId = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : null;
$patientId = null;
$appointmentDate = date('Y-m-d');
$patientInfo = null;

if ($appointmentId) {
    $stmt = $db->prepare("SELECT a.*, p.first_name, p.last_name, p.gender, p.date_of_birth, p.phone, a.reason, a.symptoms 
                          FROM appointments a 
                          JOIN patients p ON a.patient_id = p.id 
                          WHERE a.id = ? AND a.doctor_id = ?");
    $stmt->execute([$appointmentId, $doctorId]);
    $appointment = $stmt->fetch();

    if ($appointment) {
        $patientId = $appointment['patient_id'];
        $appointmentDate = $appointment['appointment_date'];
        $patientInfo = $appointment;
        
        // Calculate Age
        if ($appointment['date_of_birth']) {
            $dob = new DateTime($appointment['date_of_birth']);
            $now = new DateTime();
            $patientInfo['age'] = $now->diff($dob)->y;
        } else {
             $patientInfo['age'] = 'N/A';
        }
    } else {
        $error = "Appointment not found.";
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p_patientId = $_POST['patient_id']; // Hidden field
    $p_appointmentId = $_POST['appointment_id']; // Hidden field
    $diagnosis = $_POST['diagnosis'];
    $treatmentNotes = $_POST['treatment_notes'];
    $doctorRemarks = $_POST['doctor_remarks'];
    $followUpDate = $_POST['follow_up_date'] ?: null;
    $action = $_POST['action']; // 'save' or 'complete'
    
    // Process Medicines
    $medicines = [];
    if (isset($_POST['med_name'])) {
        for ($i = 0; $i < count($_POST['med_name']); $i++) {
            if (!empty($_POST['med_name'][$i])) {
                $medicines[] = [
                    'name' => $_POST['med_name'][$i],
                    'dosage' => $_POST['med_dosage'][$i],
                    'frequency' => $_POST['med_freq'][$i],
                    'duration' => $_POST['med_duration'][$i],
                    'instruction' => $_POST['med_instruction'][$i]
                ];
            }
        }
    }
    $medicinesJson = json_encode($medicines);

    try {
        // Insert Prescription
        $stmt = $db->prepare("INSERT INTO prescriptions (appointment_id, patient_id, doctor_id, diagnosis, medicines, instructions, remarks, follow_up_date) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $p_appointmentId, 
            $p_patientId, 
            $doctorId, 
            $diagnosis, 
            $medicinesJson, 
            $treatmentNotes, 
            $doctorRemarks, 
            $followUpDate
        ]);

        // Handle 'Complete Appointment'
        if ($action === 'complete' && $p_appointmentId) {
             $stmt = $db->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
             $stmt->execute([$p_appointmentId]);
        }
        
        // Redirect
        header("Location: doctor-appointments.php?msg=prescription_saved");
        exit;
        
    } catch (PDOException $e) {
        $error = "Error saving prescription: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Prescription - SUDAMA CLINIC</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <script>
        function addMedicineRow() {
            const container = document.getElementById('medicine-rows');
            const index = container.children.length;
            const row = document.createElement('div');
            row.className = 'medicine-row';
            row.style.display = 'grid';
            row.style.gridTemplateColumns = '2fr 1fr 1fr 1fr 2fr 0.5fr';
            row.style.gap = '10px';
            row.style.marginBottom = '10px';
            
            row.innerHTML = `
                <input type="text" name="med_name[]" class="form-control" placeholder="Medicine Name" required>
                <input type="text" name="med_dosage[]" class="form-control" placeholder="Dosage">
                <select name="med_freq[]" class="form-control">
                    <option value="1-0-1">1-0-1</option>
                    <option value="1-1-1">1-1-1</option>
                    <option value="1-0-0">1-0-0</option>
                    <option value="0-0-1">0-0-1</option>
                    <option value="SOS">SOS</option>
                </select>
                <input type="text" name="med_duration[]" class="form-control" placeholder="Days">
                <input type="text" name="med_instruction[]" class="form-control" placeholder="Instructions">
                <button type="button" class="btn btn-ghost" onclick="this.parentElement.remove()" style="color:red;">🗑️</button>
            `;
            container.appendChild(row);
        }
    </script>
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
                <li><a href="doctor-dashboard.php" class="nav-link">Dashboard</a></li>
                <li><a href="doctor-appointments.php" class="nav-link active">Appointments</a></li>
                <li><a href="doctor-schedule.php"><span class="nav-icon">🕒</span> Schedule</a></li>
                <li><a href="doctor-messages.php" class="nav-link">Messages</a></li>
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
                    <h1>Write <span class="text-gradient">Prescription</span></h1>
                </div>
                <a href="doctor-appointments.php" class="btn btn-ghost">← Back</a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" style="color: red; background: #fee; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($patientInfo): ?>
                <form method="POST" action="prescription.php">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointmentId; ?>">
                    <input type="hidden" name="patient_id" value="<?php echo $patientId; ?>">

                    <div class="form-section">
                        <h3>👤 Patient Details</h3>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; background: var(--color-bg-secondary); padding: 15px; border-radius: 8px; margin-top: 10px;">
                            <div>
                                <small>Name</small>
                                <div><strong><?php echo htmlspecialchars($patientInfo['first_name'] . ' ' . $patientInfo['last_name']); ?></strong></div>
                            </div>
                            <div>
                                <small>Age/Gender</small>
                                <div><?php echo $patientInfo['age']; ?> yrs / <?php echo ucfirst($patientInfo['gender']); ?></div>
                            </div>
                            <div>
                                <small>Contact</small>
                                <div><?php echo htmlspecialchars($patientInfo['phone']); ?></div>
                            </div>
                            <div>
                                <small>Date</small>
                                <div><?php echo date('d M Y', strtotime($appointmentDate)); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>📋 Patient's Reason for Visit</h3>
                        <?php if (!empty($patientInfo['reason'])): ?>
                            <h4 style="color: var(--primary-start); margin-bottom: 8px;">Reason: <?php echo htmlspecialchars($patientInfo['reason']); ?></h4>
                        <?php endif; ?>
                        
                        <?php if (!empty($patientInfo['symptoms'])): ?>
                            <h4 style="color: var(--text-dark); font-weight: 500;">Symptoms: <?php echo htmlspecialchars($patientInfo['symptoms']); ?></h4>
                        <?php endif; ?>
                        
                        <?php if (empty($patientInfo['reason']) && empty($patientInfo['symptoms'])): ?>
                            <p style="color: #64748b;">No reason or symptoms provided.</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-section">
                        <h3>🔬 Diagnosis</h3>
                        <div class="form-group">
                            <textarea name="diagnosis" class="form-control" rows="3" placeholder="Enter clinical diagnosis and observations..." required></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>💊 Medicines</h3>
                        <div id="medicine-rows">
                            <!-- JS adds rows here -->
                            <div class="medicine-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 2fr 0.5fr; gap: 10px; margin-bottom: 10px;">
                                <input type="text" name="med_name[]" class="form-control" placeholder="Medicine Name" required>
                                <input type="text" name="med_dosage[]" class="form-control" placeholder="Dosage (500mg)">
                                <select name="med_freq[]" class="form-control">
                                    <option value="1-0-1">1-0-1</option>
                                    <option value="1-1-1">1-1-1</option>
                                    <option value="1-0-0">1-0-0</option>
                                    <option value="0-0-1">0-0-1</option>
                                    <option value="SOS">SOS</option>
                                </select>
                                <input type="text" name="med_duration[]" class="form-control" placeholder="Days">
                                <input type="text" name="med_instruction[]" class="form-control" placeholder="Instructions">
                                <button type="button" class="btn btn-ghost" disabled>🗑️</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-ghost" onclick="addMedicineRow()">➕ Add Medicine</button>
                    </div>

                    <div class="form-section">
                        <h3>📝 Notes</h3>
                        <div class="form-group">
                            <label>Advice / Instructions</label>
                            <textarea name="treatment_notes" class="form-control" rows="3" placeholder="Diet, habits, exercise..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Follow-up Date</label>
                            <input type="date" name="follow_up_date" class="form-control" style="max-width: 200px;">
                        </div>
                        <div class="form-group">
                            <label>Private Remarks (Doctor Only)</label>
                            <textarea name="doctor_remarks" class="form-control" rows="2" placeholder="Internal notes..."></textarea>
                        </div>
                    </div>

                    <div class="form-section" style="text-align: right; border-top: 1px solid #eee; padding-top: 20px;">
                        <button type="submit" name="action" value="save" class="btn btn-ghost" style="margin-right: 10px;">💾 Save Only</button>
                        <button type="submit" name="action" value="complete" class="btn btn-primary btn-lg">✅ Submit & Complete Appointment</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">
                    No appointment selected. Please select an appointment from the list.
                </div>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>
