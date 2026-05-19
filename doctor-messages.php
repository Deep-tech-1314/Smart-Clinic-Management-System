<?php
require_once 'includes/functions.php';
require_login('doctor');

$db = get_db_connection();
$doctorUserId = $_SESSION['user_id'];
$doctorName = $_SESSION['name'];

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();

// 1. Get List of Patients (Conversations)
// We get unique user IDs who have exchanged messages with this doctor OR have appointments
// For simplicity, let's look for existing message threads + patients with appointments
$sql = "
    SELECT DISTINCT u.id as user_id, p.first_name, p.last_name, p.user_id as patient_user_id,
           (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = :did_recv2 AND is_read = 0) as unread_count
    FROM users u
    JOIN patients p ON u.id = p.user_id
    WHERE u.id IN (
        SELECT sender_id FROM messages WHERE receiver_id = :did_recv
        UNION
        SELECT receiver_id FROM messages WHERE sender_id = :did_send
    )
    OR p.id IN (
        SELECT patient_id FROM appointments WHERE doctor_id = :doctor_table_id
    )
";

// We need the doctor's ID from the doctors table for the appointment check
$stmtDoc = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
$stmtDoc->execute([$doctorUserId]);
$docTableId = $stmtDoc->fetchColumn();

// Execute main query with unique parameters
$stmt = $db->prepare($sql);
$stmt->execute([
    ':did_recv' => $doctorUserId, 
    ':did_send' => $doctorUserId, 
    ':doctor_table_id' => $docTableId,
    ':did_recv2' => $doctorUserId
]);
$patients = $stmt->fetchAll();

// 2. Handle Selected Chat
$chatUserId = isset($_GET['uid']) ? (int)$_GET['uid'] : null;
$chatMessages = [];
$chatPatient = null;

if ($chatUserId) {
    // Get Patient Details
    $stmt = $db->prepare("SELECT first_name, last_name FROM patients WHERE user_id = ?");
    $stmt->execute([$chatUserId]);
    $chatPatient = $stmt->fetch();

    // Mark as Read
    $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
    $stmt->execute([$chatUserId, $doctorUserId]);

    // Handle Send Message
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
        $msg = trim($_POST['message']);
        if (!empty($msg)) {
            $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$doctorUserId, $chatUserId, $msg]);
            // Refresh to show new message
             header("Location: doctor-messages.php?uid=$chatUserId");
             exit;
        }
    }

    // Get Messages
    $stmt = $db->prepare("
        SELECT * FROM messages 
        WHERE (sender_id = :me1 AND receiver_id = :other1) 
           OR (sender_id = :other2 AND receiver_id = :me2)
        ORDER BY created_at ASC
    ");
    $stmt->execute([
        ':me1' => $doctorUserId, 
        ':other1' => $chatUserId,
        ':other2' => $chatUserId, 
        ':me2' => $doctorUserId
    ]);
    $chatMessages = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Doctor Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        .chat-layout { display: grid; grid-template-columns: 300px 1fr; gap: 20px; height: calc(100vh - 140px); }
        
        /* Sidebar List */
        .chat-sidebar { background: white; border: 1px solid var(--glass-border); border-radius: 10px; overflow-y: auto; }
        .contact-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: background 0.2s; }
        .contact-item:hover { background: var(--color-bg-secondary); }
        .contact-item.active { background: rgba(6, 182, 212, 0.1); border-left: 3px solid var(--primary-start); }
        .contact-avatar { width: 40px; height: 40px; background: var(--primary-light); color: var(--primary-start); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        
        /* Chat Area */
        .chat-main { background: white; border: 1px solid var(--glass-border); border-radius: 10px; display: flex; flex-direction: column; overflow: hidden; }
        .chat-header { padding: 15px; border-bottom: 1px solid #eee; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); }
        .chat-body { flex: 1; padding: 20px; overflow-y: auto; background: var(--color-bg-secondary); display: flex; flex-direction: column; gap: 10px; }
        
        /* Bubbles */
        .message-bubble { max-width: 70%; padding: 10px 15px; border-radius: 15px; position: relative; font-size: 0.95rem; line-height: 1.4; }
        .message-sent { align-self: flex-end; background: var(--primary-start); color: white; border-bottom-right-radius: 2px; }
        .message-received { align-self: flex-start; background: white; border: 1px solid #ddd; border-bottom-left-radius: 2px; }
        .message-time { font-size: 0.7rem; margin-top: 5px; opacity: 0.7; text-align: right; }
        
        .chat-input-area { padding: 15px; background: white; border-top: 1px solid #eee; display: flex; gap: 10px; }
        .chat-input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px; outline: none; }
        .chat-input:focus { border-color: var(--primary-start); }
        
        .empty-state { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: grey; }
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
                <li><a href="doctor-dashboard.php" class="nav-link">Dashboard</a></li>
                 <li><a href="doctor-appointments.php" class="nav-link">Appointments</a></li>
                 <li><a href="doctor-schedule.php"><span class="nav-icon">🕒</span> Schedule</a></li>
                <li><a href="doctor-messages.php" class="nav-link active">Messages
                    <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                        <span style="background: var(--accent-warning, red); color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem; vertical-align: top; margin-left: 2px;"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/doctor_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="page-header">
                <h1>Messages</h1>
            </div>

            <div class="chat-layout">
                <!-- Sidebar -->
                <div class="chat-sidebar">
                    <div style="padding: 15px; border-bottom: 1px solid #eee; font-weight: bold; color: grey;">Recent Patients</div>
                    <?php if (empty($patients)): ?>
                        <div style="padding: 20px; text-align: center; color: grey;">No conversations yet.</div>
                    <?php else: ?>
                        <?php foreach ($patients as $p): ?>
                            <a href="?uid=<?php echo $p['user_id']; ?>" class="contact-item <?php echo ($chatUserId == $p['user_id']) ? 'active' : ''; ?>">
                                <div class="contact-avatar">
                                    <?php echo strtoupper(substr($p['first_name'], 0, 1)); ?>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 500; color: var(--color-text-primary); display: flex; justify-content: space-between; align-items: center;">
                                        <span><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></span>
                                        <?php if (isset($p['unread_count']) && $p['unread_count'] > 0): ?>
                                            <span style="background: var(--accent-warning, red); color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem;"><?php echo $p['unread_count']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">Patient</div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Main Chat -->
                <div class="chat-main">
                    <?php if ($chatUserId && $chatPatient): ?>
                        <div class="chat-header">
                            <h3 style="margin: 0;"><?php echo htmlspecialchars($chatPatient['first_name'] . ' ' . $chatPatient['last_name']); ?></h3>
                        </div>
                        
                        <div class="chat-body" id="chatBody">
                            <?php if (empty($chatMessages)): ?>
                                <div style="text-align: center; margin-top: 50px; color: grey;">
                                    Start a conversation with <?php echo htmlspecialchars($chatPatient['first_name']); ?>.
                                </div>
                            <?php else: ?>
                                <?php foreach ($chatMessages as $msg): ?>
                                    <div class="message-bubble <?php echo ($msg['sender_id'] == $doctorUserId) ? 'message-sent' : 'message-received'; ?>">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                        <div class="message-time">
                                            <?php echo date('h:i A', strtotime($msg['created_at'])); ?>
                                            <?php if ($msg['sender_id'] == $doctorUserId): ?>
                                                <?php echo ($msg['is_read']) ? '✓✓' : '✓'; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <form class="chat-input-area" method="POST">
                            <input type="text" name="message" class="chat-input" placeholder="Type a message..." required autocomplete="off">
                            <button type="submit" class="btn btn-primary" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; padding: 0;">➤</button>
                        </form>
                    <?php else: ?>
                        <div class="empty-state">
                            <div style="font-size: 4rem; margin-bottom: 20px;">💬</div>
                            <h2>Select a patient to chat</h2>
                            <p>Choose a contact from the left sidebar to view history.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Scroll to bottom of chat
        const chatBody = document.getElementById('chatBody');
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    </script>
</body>
</html>
