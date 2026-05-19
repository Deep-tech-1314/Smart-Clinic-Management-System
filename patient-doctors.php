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
$stmt = $db->query("
    SELECT d.*, s.name as specialization_name, u.email 
    FROM doctors d 
    JOIN users u ON d.user_id = u.id 
    LEFT JOIN specializations s ON d.specialization_id = s.id 
    WHERE u.status = 'active'
    ORDER BY d.name ASC
");
$doctors = $stmt->fetchAll();

// Get unique specializations for filter
$specs = $db->query("SELECT * FROM specializations ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors - SUDAMA CLINIC</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .doctor-card {
            background: var(--color-bg-secondary);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--glass-border);
            display: flex;
            flex-direction: column;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-start);
        }

        .doctor-photo-container {
            height: 250px;
            overflow: hidden;
            position: relative;
            background: var(--color-bg-tertiary);
        }

        .doctor-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .doctor-card:hover .doctor-photo {
            transform: scale(1.05);
        }

        .doctor-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .doctor-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-text-primary);
            margin-bottom: 5px;
        }

        .doctor-specialty {
            color: var(--primary-start);
            font-weight: 600;
            margin-bottom: 10px;
            display: inline-block;
            background: rgba(99, 102, 241, 0.1);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .doctor-meta {
            margin-top: auto;
            border-top: 1px solid var(--glass-border);
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: var(--color-text-muted);
        }

        .experience-badge {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .empty-placeholder {
            width: 100%; 
            height: 100%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(139, 92, 246, 0.2));
            color: var(--primary-start);
            font-size: 3rem;
            font-weight: bold;
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
</script>\n</head>
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
                    <h1>Our <span class="text-gradient">Doctors</span></h1>
                    <div class="breadcrumb">
                        <a href="patient-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>Our Doctors</span>
                    </div>
                </div>
                <!-- Filter Dropdown -->
                <div>
                    <select id="specialtyFilter" class="form-control" onchange="filterDoctors()">
                        <option value="all">All Specialties</option>
                        <?php foreach ($specs as $s): ?>
                            <option value="<?php echo htmlspecialchars($s['name']); ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="doctors-grid" id="doctorsContainer">
                <?php foreach ($doctors as $d): ?>
                    <div class="doctor-card" data-specialty="<?php echo htmlspecialchars($d['specialization_name'] ?? ''); ?>">
                        <div class="doctor-photo-container">
                            <?php if (!empty($d['photo']) && file_exists($d['photo'])): ?>
                                <img src="<?php echo htmlspecialchars($d['photo']); ?>" alt="<?php echo htmlspecialchars($d['name']); ?>" class="doctor-photo">
                            <?php else: ?>
                                <div class="empty-placeholder">
                                    <?php echo strtoupper(substr($d['name'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="doctor-info">
                            <h3 class="doctor-name"><?php echo htmlspecialchars($d['name']); ?></h3>
                            <div class="doctor-specialty"><?php echo htmlspecialchars($d['specialization_name'] ?? ''); ?></div>
                            <p style="color: var(--color-text-secondary); font-size: 0.9rem; margin-bottom: 15px; line-height: 1.5;">
                                <?php echo htmlspecialchars($d['qualification']); ?>
                                <?php if($d['bio']): ?>
                                    <br><small><?php echo substr(htmlspecialchars($d['bio']), 0, 100) . '...'; ?></small>
                                <?php endif; ?>
                            </p>
                            
                            <div class="doctor-meta">
                                <div class="experience-badge" title="Experience">
                                    <span>🎓</span> <?php echo $d['experience_years']; ?>+ Years
                                </div>

                            </div>
                            
                            <a href="book-appointment.php?doctor_id=<?php echo $d['id']; ?>" class="btn btn-primary btn-block" style="margin-top: 15px; text-align: center;">Book Appointment</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($doctors)): ?>
                <div class="empty-state">
                    <h3>No doctors available right now.</h3>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <script src="js/main.js"></script>
    <script>
        function filterDoctors() {
            const filter = document.getElementById('specialtyFilter').value;
            const cards = document.querySelectorAll('.doctor-card');
            
            cards.forEach(card => {
                if (filter === 'all' || card.dataset.specialty === filter) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
