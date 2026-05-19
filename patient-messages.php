<?php
require_once 'includes/functions.php';
$user = require_login('patient');

$db = get_db_connection();

// Check for unread messages
$unreadStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->execute([$_SESSION['user_id']]);
$unreadCount = $unreadStmt->fetchColumn();
$patientUserId = $user['user_id'];
$patientName = $_SESSION['name'];

// 1. Get List of Doctors (Conversations)
$sql = "
    SELECT DISTINCT u.id as user_id, d.name as doctor_name, s.name as specialization,
           (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = :pid_recv2 AND is_read = 0) as unread_count
    FROM users u
    JOIN doctors d ON u.id = d.user_id
    LEFT JOIN specializations s ON d.specialization_id = s.id
    WHERE u.id IN (
        SELECT sender_id FROM messages WHERE receiver_id = :pid_recv
        UNION
        SELECT receiver_id FROM messages WHERE sender_id = :pid_send
    )
    OR d.id IN (
        SELECT doctor_id FROM appointments WHERE patient_id = (SELECT id FROM patients WHERE user_id = :pid_apt)
    )
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':pid_recv' => $patientUserId,
    ':pid_send' => $patientUserId,
    ':pid_apt' => $patientUserId,
    ':pid_recv2' => $patientUserId
]);
$doctors = $stmt->fetchAll();

// 2. Handle Selected Chat
$chatUserId = isset($_GET['uid']) ? (int)$_GET['uid'] : null;
$chatMessages = [];
$chatDoctor = null;

if ($chatUserId) {
    // Get Doctor Details
    $stmt = $db->prepare("SELECT name FROM doctors WHERE user_id = ?");
    $stmt->execute([$chatUserId]);
    $chatDoctor = $stmt->fetch();

    // Mark as Read
    $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
    $stmt->execute([$chatUserId, $patientUserId]);

    // Handle Send Message
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
        $msg = trim($_POST['message']);
        if (!empty($msg)) {
            $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$patientUserId, $chatUserId, $msg]);
            header("Location: patient-messages.php?uid=$chatUserId");
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
        ':me1' => $patientUserId, 
        ':other1' => $chatUserId,
        ':other2' => $chatUserId, 
        ':me2' => $patientUserId
    ]);
    $chatMessages = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Patient Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        .chat-layout { display: grid; grid-template-columns: 300px 1fr; gap: 20px; height: calc(100vh - 140px); }
        .chat-sidebar { background: white; border: 1px solid var(--glass-border); border-radius: 10px; overflow-y: auto; }
        .contact-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: background 0.2s; }
        .contact-item:hover { background: var(--color-bg-secondary); }
        .contact-item.active { background: rgba(6, 182, 212, 0.1); border-left: 3px solid var(--primary-start); }
        .contact-avatar { width: 40px; height: 40px; background: var(--primary-light); color: var(--primary-start); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .chat-main { background: white; border: 1px solid var(--glass-border); border-radius: 10px; display: flex; flex-direction: column; overflow: hidden; }
        .chat-header { padding: 15px; border-bottom: 1px solid #eee; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); }
        .chat-body { flex: 1; padding: 20px; overflow-y: auto; background: var(--color-bg-secondary); display: flex; flex-direction: column; gap: 10px; }
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
                <li><span style="color: grey;"><?php echo htmlspecialchars($patientName); ?></span></li>
                <li><a href="logout.php" class="btn btn-ghost btn-sm">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <?php include 'includes/patient_sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="page-header">
                <h1>My <span class="text-gradient">Messages</span></h1>
            </div>

            <div class="chat-layout">
                <!-- Sidebar -->
                <div class="chat-sidebar">
                    <div style="padding: 15px; border-bottom: 1px solid #eee; font-weight: bold; color: grey;">My Doctors</div>
                    <?php if (empty($doctors)): ?>
                        <div style="padding: 20px; text-align: center; color: grey;">No doctors found.<br><small>Book an appointment to start chatting.</small></div>
                    <?php else: ?>
                        <?php foreach ($doctors as $d): ?>
                            <a href="?uid=<?php echo $d['user_id']; ?>" class="contact-item <?php echo ($chatUserId == $d['user_id']) ? 'active' : ''; ?>">
                                <div class="contact-avatar">Dr</div>
                                <div>
                                    <div style="font-weight: 500; color: var(--color-text-primary); display: flex; justify-content: space-between; align-items: center;">
                                        <span><?php echo htmlspecialchars($d['doctor_name']); ?></span>
                                        <?php if (isset($d['unread_count']) && $d['unread_count'] > 0): ?>
                                            <span style="background: var(--accent-warning, red); color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem;"><?php echo $d['unread_count']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">
                                        <?php echo htmlspecialchars($d['specialization']); ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Main Chat -->
                <div class="chat-main">
                    <?php if ($chatUserId && $chatDoctor): ?>
                        <div class="chat-header">
                            <h3 style="margin: 0;"><?php echo htmlspecialchars($chatDoctor['name']); ?></h3>
                        </div>
                        
                        <div class="chat-body" id="chatBody">
                            <?php if (empty($chatMessages)): ?>
                                <div style="text-align: center; margin-top: 50px; color: grey;">
                                    Start a conversation with <?php echo htmlspecialchars($chatDoctor['name']); ?>.
                                </div>
                            <?php else: ?>
                                <?php foreach ($chatMessages as $msg): ?>
                                    <div class="message-bubble <?php echo ($msg['sender_id'] == $patientUserId) ? 'message-sent' : 'message-received'; ?>">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                        <div class="message-time">
                                            <?php echo date('h:i A', strtotime($msg['created_at'])); ?>
                                            <?php if ($msg['sender_id'] == $patientUserId): ?>
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
                            <h2>Select a doctor to chat</h2>
                            <p>Choose a doctor from the left sidebar.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        const chatBody = document.getElementById('chatBody');
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    </script>
</body>
</html>
