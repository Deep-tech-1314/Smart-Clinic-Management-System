<?php
require_once 'includes/functions.php';

// Require patient login
$user = require_login('patient');

$db = get_db_connection();

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

// Fetch All Doctors with Specializations
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

// Fetch real availability from time_slots
$slotsStmt = $db->query("SELECT doctor_id, day_of_week FROM time_slots WHERE status = 'active' ORDER BY FIELD(day_of_week, 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun')");
$slots = $slotsStmt->fetchAll(PDO::FETCH_ASSOC);

$doctorSlots = [];
foreach($slots as $slot) {
    $doctorSlots[$slot['doctor_id']][] = $slot['day_of_week'];
}

foreach ($doctors as &$doc) {
    if (isset($doctorSlots[$doc['id']]) && count($doctorSlots[$doc['id']]) > 0) {
        $doc['real_availability'] = implode(', ', $doctorSlots[$doc['id']]);
    } else {
        $doc['real_availability'] = 'Schedule Not Set';
    }
}

// Get unique specializations for filter
$specs = $db->query("SELECT * FROM specializations ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compare Doctors - SUDAMA CLINIC</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        .compare-section {
            background: var(--color-bg-secondary);
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--glass-border);
            margin-bottom: 30px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .doctor-select-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: var(--color-bg-secondary);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .comparison-table th, .comparison-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .comparison-table th {
            background: rgba(6, 182, 212, 0.1);
            color: var(--primary-start);
            font-weight: 600;
            width: 20%;
        }

        .comparison-table td {
            width: 40%;
            color: var(--color-text-primary);
        }

        .comparison-table tr:last-child td, .comparison-table tr:last-child th {
            border-bottom: none;
        }

        .doctor-profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid var(--primary-start);
        }
        
        .empty-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(139, 92, 246, 0.2));
            color: var(--primary-start);
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 15px;
            border: 3px solid var(--primary-start);
        }

        .book-btn-container {
            margin-top: 20px;
        }
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

    // Pass PHP data to JS
    const doctorsData = <?php echo json_encode($doctors); ?>;
</script>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="container navbar-container">
            <button class="navbar-toggle" onclick="toggleSidebar()">☰</button>
            <a href="patient-dashboard.php" class="navbar-brand">
                <img src="images/logo.png" alt="SUDAMA CLINIC Logo">
                <span class="text-gradient">SUDAMA CLINIC</span>
            </a>
            <ul class="navbar-nav">
                <li><a href="patient-dashboard.php" class="nav-link">Dashboard</a></li>
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
                    <h1>Compare <span class="text-gradient">Doctors</span></h1>
                    <div class="breadcrumb">
                        <a href="patient-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>Compare Doctors</span>
                    </div>
                </div>
            </div>

            <div class="compare-section">
                <h3 style="margin-bottom: 20px;">Select Doctors to Compare</h3>
                
                <div class="filter-grid">
                    <div class="doctor-select-container">
                        <label for="specialtyFilter" style="font-weight: 600;">Step 1: Select Specialization</label>
                        <select id="specialtyFilter" class="form-control" onchange="filterSpecialty()">
                            <option value="">-- Choose Specialization --</option>
                            <?php foreach ($specs as $s): ?>
                                <option value="<?php echo htmlspecialchars($s['name']); ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="doctor-select-container">
                        <label for="doctor1Select" style="font-weight: 600;">Step 2: Select First Doctor</label>
                        <select id="doctor1Select" class="form-control" onchange="updateComparison()" disabled>
                            <option value="">-- Select Doctor 1 --</option>
                        </select>
                    </div>

                    <div class="doctor-select-container">
                        <label for="doctor2Select" style="font-weight: 600;">Step 3: Select Second Doctor</label>
                        <select id="doctor2Select" class="form-control" onchange="updateComparison()" disabled>
                            <option value="">-- Select Doctor 2 --</option>
                        </select>
                    </div>
                </div>

                <div id="comparisonResult" style="display: none;">
                    <table class="comparison-table">
                        <tbody>
                            <tr>
                                <th>Profile</th>
                                <td id="doc1-profile" style="text-align: center;"></td>
                                <td id="doc2-profile" style="text-align: center;"></td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td id="doc1-name" style="font-weight: bold; font-size: 1.1rem;"></td>
                                <td id="doc2-name" style="font-weight: bold; font-size: 1.1rem;"></td>
                            </tr>
                            <tr>
                                <th>Specialization</th>
                                <td id="doc1-spec"></td>
                                <td id="doc2-spec"></td>
                            </tr>
                            <tr>
                                <th>Qualification</th>
                                <td id="doc1-qual"></td>
                                <td id="doc2-qual"></td>
                            </tr>
                            <tr>
                                <th>Experience</th>
                                <td id="doc1-exp"></td>
                                <td id="doc2-exp"></td>
                            </tr>
                            <tr>
                                <th>Consultation Fee</th>
                                <td id="doc1-fee"></td>
                                <td id="doc2-fee"></td>
                            </tr>
                            <tr>
                                <th>Available Days</th>
                                <td id="doc1-days"></td>
                                <td id="doc2-days"></td>
                            </tr>
                            <tr>
                                <th>Action</th>
                                <td id="doc1-action" style="text-align: center;"></td>
                                <td id="doc2-action" style="text-align: center;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div id="emptyComparisonState" style="text-align: center; padding: 40px; color: var(--color-text-muted);">
                    <div style="font-size: 3rem; margin-bottom: 15px;">⚖️</div>
                    <h4>Select two doctors to see their comparison.</h4>
                </div>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
    <script>
        function filterSpecialty() {
            const specialty = document.getElementById('specialtyFilter').value;
            const doc1Select = document.getElementById('doctor1Select');
            const doc2Select = document.getElementById('doctor2Select');
            
            doc1Select.innerHTML = '<option value="">-- Select Doctor 1 --</option>';
            doc2Select.innerHTML = '<option value="">-- Select Doctor 2 --</option>';
            
            if (!specialty) {
                doc1Select.disabled = true;
                doc2Select.disabled = true;
                updateComparison();
                return;
            }
            
            doc1Select.disabled = false;
            doc2Select.disabled = false;
            
            const filteredDoctors = doctorsData.filter(d => d.specialization_name === specialty);
            
            filteredDoctors.forEach(d => {
                doc1Select.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                doc2Select.innerHTML += `<option value="${d.id}">${d.name}</option>`;
            });
            
            updateComparison();
        }

        function updateComparison() {
            const doc1Id = document.getElementById('doctor1Select').value;
            const doc2Id = document.getElementById('doctor2Select').value;
            
            const resultDiv = document.getElementById('comparisonResult');
            const emptyState = document.getElementById('emptyComparisonState');
            
            if (doc1Id && doc2Id && doc1Id !== doc2Id) {
                const doc1 = doctorsData.find(d => d.id == doc1Id);
                const doc2 = doctorsData.find(d => d.id == doc2Id);
                
                populateColumn(1, doc1);
                populateColumn(2, doc2);
                
                resultDiv.style.display = 'block';
                emptyState.style.display = 'none';
            } else {
                resultDiv.style.display = 'none';
                emptyState.style.display = 'block';
                
                if (doc1Id && doc2Id && doc1Id === doc2Id) {
                    emptyState.innerHTML = '<div style="font-size: 3rem; margin-bottom: 15px; color: var(--warning);">⚠️</div><h4>Please select two different doctors to compare.</h4>';
                } else {
                    emptyState.innerHTML = '<div style="font-size: 3rem; margin-bottom: 15px;">⚖️</div><h4>Select two doctors to see their comparison.</h4>';
                }
            }
        }
        
        function populateColumn(colNum, doc) {
            // Profile PIC
            let profileHtml = '';
            if (doc.photo && doc.photo.trim() !== '') {
                // To safely construct image path if photo exists
                profileHtml = `<img src="${doc.photo}" alt="${doc.name}" class="doctor-profile-pic">`;
            } else {
                const initials = doc.name.substring(0, 2).toUpperCase();
                profileHtml = `<div class="empty-placeholder" style="margin: 0 auto 15px auto;">${initials}</div>`;
            }
            document.getElementById(`doc${colNum}-profile`).innerHTML = profileHtml;
            
            // Details
            document.getElementById(`doc${colNum}-name`).textContent = doc.name;
            document.getElementById(`doc${colNum}-spec`).innerHTML = `<span style="background: rgba(99, 102, 241, 0.1); color: var(--primary-start); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">${doc.specialization_name || 'General'}</span>`;
            document.getElementById(`doc${colNum}-qual`).textContent = doc.qualification || 'N/A';
            document.getElementById(`doc${colNum}-exp`).textContent = (doc.experience_years || 0) + '+ Years';
            document.getElementById(`doc${colNum}-fee`).textContent = '₹' + (doc.consultation_charge || doc.new_case_charge || '0');
            document.getElementById(`doc${colNum}-days`).textContent = doc.real_availability || 'Schedule Not Set';
            
            // Action
            document.getElementById(`doc${colNum}-action`).innerHTML = `<a href="book-appointment.php?doctor_id=${doc.id}" class="btn btn-primary">Book Now</a>`;
        }
    </script>
</body>
</html>
