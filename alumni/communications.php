<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Communication.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$communication = new Communication($db);

$messages = $communication->getUserCommunications($_SESSION['user_id']);
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/alumni_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Communications</h4>
            
            <?php if (empty($messages)): ?>
                <div class="card-panel">
                    <p>No communications found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title"><?php echo $message['subject']; ?></span>
                            <p class="grey-text">
                                <?php echo date('F j, Y g:i A', strtotime($message['sent_timestamp'])); ?>
                            </p>
                            <p><?php echo $message['message_body']; ?></p>
                            <span class="badge <?php echo $message['delivery_status'] == 'Read' ? 'green' : 'blue'; ?> white-text">
                                <?php echo $message['delivery_status']; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 