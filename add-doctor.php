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

// Fetch Specializations
$specializations = [];
try {
    $stmt = $db->query("SELECT id, name FROM specializations ORDER BY name");
    $specializations = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Failed to load specializations.";
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $specialization_id = $_POST['specialization_id'];
    $qualification = sanitize($_POST['qualification']);
    $experience = (int)$_POST['experience_years'];
    $new_charge = (float)$_POST['new_case_charge'];
    $old_charge = (float)$_POST['old_case_charge'];
    $status = $_POST['status'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $db->beginTransaction();

            // 1. Create User
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, 'doctor', ?)");
            $stmt->execute([$email, $hashed_password, $status]);
            $user_id = $db->lastInsertId();

            // Handle Photo Upload
            $photoPath = null;
            if (!empty($_POST['photo'])) {
                $data = $_POST['photo'];
                if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                    $data = substr($data, strpos($data, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif
                    
                    if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                        // invalid file type
                    } else {
                        $data = base64_decode($data);
                        if ($data === false) {
                            // decode failed
                        } else {
                            $dir = 'uploads/doctors/';
                            if (!file_exists($dir)) {
                                mkdir($dir, 0777, true);
                            }
                            
                            $filename = 'dr_' . time() . '_' . uniqid() . '.png';
                            $file = $dir . $filename;
                            if(file_put_contents($file, $data)) {
                                $photoPath = $file;
                            }
                        }
                    }
                }
            }

            // 2. Create Doctor Profile
            // Note: doctors table has consultation_charge, new_case_charge, old_case_charge. 
            // We'll set consultation_charge same as new_case_charge for default.
            $stmt = $db->prepare("INSERT INTO doctors (user_id, name, phone, specialization_id, qualification, experience_years, consultation_charge, new_case_charge, old_case_charge, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $name, $phone, $specialization_id, $qualification, $experience, $new_charge, $new_charge, $old_charge, $photoPath]);

            $db->commit();
            $message = "Doctor registered successfully!";
            
            // Redirect after short delay or just show success
            // header("Refresh:2; url=manage-doctors.php");

        } catch (PDOException $e) {
            $db->rollBack();
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $error = "Email already exists.";
            } else {
                $error = "Registration failed: " . $e->getMessage();
                // Debugging: uncomment to see full error on screen if needed
                // echo "SQL Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Doctor - SUDAMA CLINIC Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
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
                    <h1>Add New <span class="text-gradient">Doctor</span></h1>
                    <div class="breadcrumb">
                        <a href="admin-dashboard.php">Dashboard</a>
                        <span>/</span>
                        <span>Add Doctor</span>
                    </div>
                </div>
                <a href="manage-doctors.php" class="btn btn-ghost">← Back to Doctors</a>
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

            <!-- Image Upload & Crop Logic -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
            
            <form method="POST" id="addDoctorForm">
                <!-- Personal Information -->
                <div class="form-section">
                    <h3 class="form-section-title">👤 Personal Information</h3>
                    
                    <!-- Photo Upload -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" id="photoInput" accept="image/*" class="form-control">
                        <input type="hidden" name="photo" id="croppedPhoto">
                        <div id="previewContainer" style="margin-top: 15px; display: none;">
                            <p style="margin-bottom: 5px; font-size: 0.9rem; color: #666;">Preview:</p>
                            <img id="previewImage" style="max-width: 120px; border-radius: 50%; border: 2px solid var(--primary-color); padding: 2px;">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="Dr. John Doe" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="doctor@smartclinic.com" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+91 98765 43210" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Create password" required minlength="8">
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="form-section">
                    <h3 class="form-section-title">🩺 Professional Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Specialization *</label>
                            <select name="specialization_id" class="form-control" required>
                                <option value="">Select Specialization</option>
                                <?php foreach ($specializations as $spec): ?>
                                    <option value="<?php echo $spec['id']; ?>"><?php echo htmlspecialchars($spec['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Qualification *</label>
                            <input type="text" name="qualification" class="form-control" placeholder="MBBS, MD" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Experience (Years) *</label>
                            <input type="number" name="experience_years" class="form-control" placeholder="10" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Consultation Charges -->
                <div class="form-section">
                    <h3 class="form-section-title">💰 Consultation Charges</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">New Case Charge (₹) *</label>
                            <input type="number" name="new_case_charge" class="form-control" placeholder="500" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Follow-up Charge (₹) *</label>
                            <input type="number" name="old_case_charge" class="form-control" placeholder="300" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="form-section" style="display: flex; gap: 20px; justify-content: flex-end;">
                    <button type="button" class="btn btn-ghost" onclick="window.location.href='manage-doctors.php'">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg">Register Doctor</button>
                </div>
            </form>
        </main>
    </div>

    <!-- Cropper Modal -->
    <div id="cropModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:white; padding:20px; border-radius:12px; width:90%; max-width:600px; height:80vh; display:flex; flex-direction:column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <h3 style="margin-bottom:15px; font-size: 1.25rem; font-weight: 600;">Crop Photo</h3>
            <div style="flex-grow:1; overflow:hidden; background:#f0f0f0; border-radius: 8px; position: relative;">
                <img id="cropImage" style="max-width:100%; display: block;">
            </div>
            <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-ghost" onclick="closeCropModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="cropAndSave()">Crop & Save</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let cropper;
        const photoInput = document.getElementById('photoInput');
        const cropModal = document.getElementById('cropModal');
        const cropImage = document.getElementById('cropImage');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        const croppedPhotoInput = document.getElementById('croppedPhoto');

        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    cropImage.src = e.target.result;
                    cropModal.style.display = 'flex';
                    
                    if(cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropImage, {
                        aspectRatio: 1, // Square crop for profile
                        viewMode: 1,
                        autoCropArea: 0.8,
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        function closeCropModal() {
            cropModal.style.display = 'none';
            photoInput.value = ''; // Reset input
            if(cropper) {
                cropper.destroy();
            }
        }

        function cropAndSave() {
            if(cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                });
                
                const base64Image = canvas.toDataURL('image/png');
                
                // Show Preview
                previewImage.src = base64Image;
                previewContainer.style.display = 'block';
                
                // Set Hidden Input
                croppedPhotoInput.value = base64Image;
                
                closeCropModal();
                // Restore input value in case they want to change it again? 
                // No, keeping it cleared avoids re-triggering change if they pick same file, 
                // but usually better to let them pick again.
            }
        }
    </script>
</body>
</html>
