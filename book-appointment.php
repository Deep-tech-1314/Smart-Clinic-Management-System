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

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$doctorId = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : (isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : null);
$date = isset($_GET['date']) ? sanitize($_GET['date']) : (isset($_POST['date']) ? sanitize($_POST['date']) : null);
$time = isset($_GET['time']) ? sanitize($_GET['time']) : (isset($_POST['time']) ? sanitize($_POST['time']) : null);

// Initialize variables for Step 4
$reason = isset($_POST['reason']) ? sanitize($_POST['reason']) : '';
$symptoms = isset($_POST['symptoms']) ? sanitize($_POST['symptoms']) : '';

// Handle Step Transitions via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'proceed_payment') {
        $step = 4;
    } elseif (isset($_POST['action']) && $_POST['action'] === 'book') {
        // Final Booking Submission
        $p_doctorId = (int)$_POST['doctor_id'];
        $p_date = sanitize($_POST['date']);
        $p_time = sanitize($_POST['time']);
        $p_reason = sanitize($_POST['reason']);
        $p_symptoms = sanitize($_POST['symptoms']);
        $p_payment_method = sanitize($_POST['payment_method']);

        // Verify slot availability (Race Condition Check)
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
        $checkStmt->execute([$p_doctorId, $p_date, $p_time]);
        if ($checkStmt->fetchColumn() > 0) {
            $error = "Sorry, this slot was just booked by another patient. Please select a different time.";
            $step = 2; // Go back to date selection
        } elseif (strtotime("$p_date $p_time") < time()) {
            $error = "Cannot book a time slot in the past.";
            $step = 2;
        } else {
            // Get consultation charge
            $stmt = $db->prepare("SELECT new_case_charge FROM doctors WHERE id = ?");
            $stmt->execute([$p_doctorId]);
            $charge = $stmt->fetchColumn();

            // Insert Appointment with Payment Status 'paid'
            $stmt = $db->prepare("INSERT INTO appointments 
                (patient_id, doctor_id, appointment_date, appointment_time, reason, symptoms, charge, status, payment_status, booked_by) 
                VALUES (:pid, :did, :date, :time, :reason, :symptoms, :charge, 'confirmed', 'paid', 'patient')");
            
            $result = $stmt->execute([
                ':pid' => $patientId,
                ':did' => $p_doctorId,
                ':date' => $p_date,
                ':time' => $p_time,
                ':reason' => $p_reason,
                ':symptoms' => $p_symptoms,
                ':charge' => $charge
            ]);

            if ($result) {
                // Determine Payment Success Message logic (could be improved)
                redirect('my-appointments.php?booked=1&payment=success');
            } else {
                $error = "Failed to book appointment.";
            }
        }
    }
}

// Data Fetching based on Step

// Step 1: Get Doctors & Specializations
$doctors = [];
$specializations = [];
if ($step === 1) {
    $specStmt = $db->query("SELECT * FROM specializations WHERE status = 'active'");
    $specializations = $specStmt->fetchAll();

    $docQuery = "SELECT d.*, s.name as specialization_name FROM doctors d 
                 LEFT JOIN specializations s ON d.specialization_id = s.id 
                 WHERE d.user_id IN (SELECT id FROM users WHERE status = 'active')";
    
    // Filter by specialization if selected
    $filterSpec = isset($_GET['spec']) ? (int)$_GET['spec'] : null;
    $params = [];
    if ($filterSpec) {
        $docQuery .= " AND d.specialization_id = ?";
        $params[] = $filterSpec;
    }
    
    $stmt = $db->prepare($docQuery);
    $stmt->execute($params);
    $doctors = $stmt->fetchAll();
}

// Step 2: Get Doctor Details & Slots
$selectedDoctor = null;
$availableSlots = [];
if (($step === 2 || $step === 3 || $step === 4) && $doctorId) {
    $stmt = $db->prepare("SELECT d.*, s.name as specialization_name FROM doctors d LEFT JOIN specializations s ON d.specialization_id = s.id WHERE d.id = ?");
    $stmt->execute([$doctorId]);
    $selectedDoctor = $stmt->fetch();
}

if ($step === 2 && $selectedDoctor && $date) {
    // [Existing Logic for Slots]
    $dayOfWeek = date('D', strtotime($date));
    $stmt = $db->prepare("SELECT * FROM time_slots WHERE doctor_id = ? AND day_of_week = ? AND status = 'active'");
    $stmt->execute([$doctorId, $dayOfWeek]);
    $workSlots = $stmt->fetchAll();

    $stmt = $db->prepare("SELECT appointment_time FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND status != 'cancelled'");
    $stmt->execute([$doctorId, $date]);
    $bookedTimes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $duration = $selectedDoctor['slot_duration'] ?: 30;

    foreach ($workSlots as $ws) {
        $start = strtotime($date . ' ' . $ws['start_time']);
        $end = strtotime($date . ' ' . $ws['end_time']);

        while ($start < $end) {
            $slotTime = date('H:i:00', $start);
            $displayTime = date('H:i', $start);
            $isBooked = in_array($slotTime, $bookedTimes);
            $isPast = ($date === date('Y-m-d') && $start < time());

            if (!$isBooked && !$isPast) {
                $availableSlots[] = $displayTime;
            }
            $start = strtotime("+$duration minutes", $start);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - SUDAMA CLINIC</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        /* Reusing styles from html */
        .booking-steps { display: flex; justify-content: center; gap: var(--spacing-2xl); margin-bottom: var(--spacing-2xl); position: relative; }
        .booking-step { display: flex; flex-direction: column; align-items: center; position: relative; opacity: 0.5; }
        .booking-step.active, .booking-step.completed { opacity: 1; }
        .step-circle { width: 40px; height: 40px; border-radius: 50%; background: var(--color-bg-secondary); border: 2px solid var(--glass-border); display: flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: var(--spacing-xs); }
        .booking-step.active .step-circle { background: var(--primary-start); border-color: var(--primary-start); color: white; }
        .booking-step.completed .step-circle { background: var(--success); border-color: var(--success); color: white; }
        .step-line-progress { position: absolute; top: 20px; left: 20%; width: 60%; height: 2px; background: #e2e8f0; z-index: -1; }
        /* Dynamic width for progress bar based on step would be inline style */
        
        .doctor-card-booking { background: var(--color-bg-secondary); border: 2px solid var(--glass-border); border-radius: var(--border-radius-lg); padding: var(--spacing-lg); cursor: pointer; transition: all 0.2s; }
        .doctor-card-booking:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
        .doctor-header { display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-md); }
        .doctor-avatar-large { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-start), var(--primary-end)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem; }
        
        .time-slots-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: var(--spacing-sm); margin-top: var(--spacing-md); }
        .time-slot-btn { padding: var(--spacing-sm) var(--spacing-md); border: 1px solid var(--glass-border); border-radius: var(--border-radius-md); background: var(--color-bg-secondary); cursor: pointer; text-decoration: none; color: var(--color-text-primary); text-align: center; display: block; }
        .time-slot-btn:hover { border-color: var(--primary-start); background: rgba(6, 182, 212, 0.1); }
        
        .doctors-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--spacing-lg); }

        .payment-option {
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 10px;
        }
        .payment-option:hover { background: rgba(6, 182, 212, 0.05); border-color: var(--primary-start); }
        .payment-option input[type="radio"] { width: 18px; height: 18px; }
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
    <!-- Navbar -->
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
                <h1>Book <span class="text-gradient">Appointment</span></h1>
            </div>

            <?php if (isset($error)): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Steps -->
            <div class="booking-steps">
                <div class="step-line-progress" style="width: 60%; background: linear-gradient(to right, var(--primary-start) <?php echo ($step - 1) * 33; ?>%, #e2e8f0 <?php echo ($step - 1) * 33; ?>%);"></div>
                <div class="booking-step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">
                    <div class="step-circle">1</div>
                    <div class="step-label">Select Doctor</div>
                </div>
                <div class="booking-step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">
                    <div class="step-circle">2</div>
                    <div class="step-label">Date & Time</div>
                </div>
                <div class="booking-step <?php echo $step >= 3 ? 'active' : ''; ?> <?php echo $step > 3 ? 'completed' : ''; ?>">
                    <div class="step-circle">3</div>
                    <div class="step-label">Details</div>
                </div>
                <div class="booking-step <?php echo $step >= 4 ? 'active' : ''; ?>">
                    <div class="step-circle">4</div>
                    <div class="step-label">Payment</div>
                </div>
            </div>

            <!-- STEP 1: Select Doctor -->
            <?php if ($step === 1): ?>
            <div class="form-section">
                <h3 class="form-section-title">👨‍⚕️ Select Your Doctor</h3>
                <div class="form-group" style="max-width: 350px;">
                    <label class="form-label">Filter by Specialization</label>
                    <select class="form-control" onchange="window.location.href='?step=1&spec='+this.value">
                        <option value="">All Specializations</option>
                        <?php foreach ($specializations as $spec): ?>
                            <option value="<?php echo $spec['id']; ?>" <?php echo (isset($_GET['spec']) && $_GET['spec'] == $spec['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($spec['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="doctors-grid">
                    <?php if (empty($doctors)): ?>
                        <p>No doctors found.</p>
                    <?php else: ?>
                        <?php foreach ($doctors as $doc): ?>
                            <div class="doctor-card-booking" onclick="window.location.href='?step=2&doctor_id=<?php echo $doc['id']; ?>'">
                                <div class="doctor-header">
                                    <?php if (!empty($doc['photo']) && file_exists($doc['photo'])): ?>
                                        <img src="<?php echo htmlspecialchars($doc['photo']); ?>" class="doctor-avatar-large" style="object-fit:cover;" alt="Dr">
                                    <?php else: ?>
                                        <div class="doctor-avatar-large"><?php echo strtoupper(substr($doc['name'], 0, 2)); ?></div>
                                    <?php endif; ?>
                                    <div class="doctor-info">
                                        <h4><?php echo htmlspecialchars($doc['name']); ?></h4>
                                        <div class="doctor-meta">
                                            <span><?php echo htmlspecialchars($doc['specialization_name']); ?></span><br>
                                            <span><?php echo htmlspecialchars($doc['qualification']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top: 10px; font-weight: bold; color: var(--primary-start);">
                                    Consultation: ₹<?php echo number_format($doc['new_case_charge'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- STEP 2: Date & Time -->
            <?php if ($step === 2 && $selectedDoctor): ?>
            <div class="form-section">
                <h3>📅 Select Slot for <?php echo htmlspecialchars($selectedDoctor['name']); ?></h3>
                <a href="?step=1" class="btn btn-ghost btn-sm">← Change Doctor</a>
                
                <div style="margin-top: 20px; max-width: 300px;">
                    <label class="form-label">Select Date</label>
                    <input type="date" class="form-control" 
                           value="<?php echo $date; ?>" 
                           min="<?php echo date('Y-m-d'); ?>"
                           onchange="window.location.href='?step=2&doctor_id=<?php echo $doctorId; ?>&date='+this.value">
                </div>

                <?php if ($date): ?>
                    <div style="margin-top: 20px;">
                        <label class="form-label">Available Slots</label>
                        <div class="time-slots-container">
                            <?php if (empty($availableSlots)): ?>
                                <p style="color: var(--error);">No slots available on this date.</p>
                            <?php else: ?>
                                <?php foreach ($availableSlots as $slot): ?>
                                    <a href="?step=3&doctor_id=<?php echo $doctorId; ?>&date=<?php echo $date; ?>&time=<?php echo $slot; ?>" class="time-slot-btn">
                                        <?php echo $slot; ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- STEP 3: Details (Reason & Symptoms) -->
            <?php if ($step === 3 && $selectedDoctor): ?>
            <div class="form-section">
                <h3>📝 Appointment Details</h3>
                
                <div class="appointment-summary" style="background: var(--color-bg-secondary); padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                    <p><strong>Doctor:</strong> <?php echo htmlspecialchars($selectedDoctor['name']); ?></p>
                    <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($date)); ?> at <?php echo $time; ?></p>
                </div>

                <form method="POST" action="book-appointment.php">
                    <input type="hidden" name="action" value="proceed_payment">
                    <input type="hidden" name="doctor_id" value="<?php echo $doctorId; ?>">
                    <input type="hidden" name="date" value="<?php echo $date; ?>">
                    <input type="hidden" name="time" value="<?php echo $time; ?>">

                    <div class="form-group">
                        <label class="form-label">Reason for Visit <span style="color: red;">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Briefly describe why you are visiting..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Symptoms (Optional)</label>
                        <textarea name="symptoms" class="form-control" rows="2" placeholder="List symptoms..."></textarea>
                    </div>

                    <div class="btn-group" style="display: flex; gap: 10px;">
                        <a href="?step=2&doctor_id=<?php echo $doctorId; ?>&date=<?php echo $date; ?>" class="btn btn-ghost">← Back</a>
                        <button type="submit" class="btn btn-primary btn-lg">Proceed to Payment →</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- STEP 4: Payment -->
            <?php if ($step === 4 && $selectedDoctor): ?>
            <div class="form-section">
                <h3>💳 Payment Details</h3>
                
                <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 300px;">
                        <div class="appointment-summary" style="background: var(--color-bg-secondary); padding: 20px; border-radius: 10px;">
                            <h4 style="margin-top: 0; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px;">Order Summary</h4>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Consultation Fee</span>
                                <span>₹<?php echo number_format($selectedDoctor['new_case_charge'], 2); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: green;">
                                <span>Booking Fee</span>
                                <span>₹0.00</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem; border-top: 1px solid var(--glass-border); padding-top: 10px; margin-top: 10px;">
                                <span>Total Payable</span>
                                <span>₹<?php echo number_format($selectedDoctor['new_case_charge'], 2); ?></span>
                            </div>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <p><strong>Appointment:</strong> <?php echo date('d M Y', strtotime($date)); ?> at <?php echo $time; ?></p>
                            <p><strong>Doctor:</strong> <?php echo htmlspecialchars($selectedDoctor['name']); ?></p>
                        </div>
                    </div>

                    <div style="flex: 1; min-width: 300px;">
                        <form method="POST" action="book-appointment.php">
                            <input type="hidden" name="action" value="book">
                            <input type="hidden" name="doctor_id" value="<?php echo $doctorId; ?>">
                            <input type="hidden" name="date" value="<?php echo $date; ?>">
                            <input type="hidden" name="time" value="<?php echo $time; ?>">
                            <input type="hidden" name="reason" value="<?php echo htmlspecialchars($reason); ?>">
                            <input type="hidden" name="symptoms" value="<?php echo htmlspecialchars($symptoms); ?>">

                            <h4 style="margin-top: 0;">Select Payment Method</h4>
                            
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="upi" checked>
                                <span style="font-size: 1.5rem;">📱</span>
                                <div>
                                    <div style="font-weight: bold;">UPI</div>
                                    <div style="font-size: 0.9rem; color: #666;">Google Pay, PhonePe, Paytm</div>
                                </div>
                            </label>
                            
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card">
                                <span style="font-size: 1.5rem;">💳</span>
                                <div>
                                    <div style="font-weight: bold;">Credit / Debit Card</div>
                                    <div style="font-size: 0.9rem; color: #666;">Visa, Mastercard, RuPay</div>
                                </div>
                            </label>

                             <label class="payment-option">
                                <input type="radio" name="payment_method" value="netbanking">
                                <span style="font-size: 1.5rem;">🏦</span>
                                <div>
                                    <div style="font-weight: bold;">Net Banking</div>
                                    <div style="font-size: 0.9rem; color: #666;">All Indian Banks</div>
                                </div>
                            </label>

                            <div style="margin-top: 30px;">
                                <button type="submit" class="btn btn-primary btn-lg btn-block" style="width: 100%;">
                                    Pay ₹<?php echo number_format($selectedDoctor['new_case_charge'], 2); ?> & Book
                                </button>
                                <p style="text-align: center; margin-top: 10px; font-size: 0.9rem; color: #666;">
                                    🔒 Secure Payment Gateway
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>

