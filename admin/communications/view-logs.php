<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$query = "SELECT 
    cl.*, 
    a.username as sender_name,
    COUNT(cr.recipient_id) as total_recipients,
    SUM(CASE WHEN cr.delivery_status = 'Delivered' THEN 1 ELSE 0 END) as delivered_count,
    SUM(CASE WHEN cr.delivery_status = 'Read' THEN 1 ELSE 0 END) as read_count
FROM CommunicationLog cl
JOIN Admins a ON cl.sender_admin_id = a.admin_id
LEFT JOIN CommunicationRecipients cr ON cl.log_id = cr.log_id
GROUP BY cl.log_id
ORDER BY cl.sent_timestamp DESC";

$stmt = $db->query($query);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">
</head>
<body>
<?php include '../includes/admin_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4 class="header">Communication Logs</h4>

            <div class="card">
                <div class="card-content">
                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Sender</th>
                                <th>Recipients</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo date('M d, Y H:i', strtotime($log['sent_timestamp'])); ?></td>
                                    <td>
                                        <span class="chip">
                                            <?php echo $log['communication_type']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($log['sender_name']); ?></td>
                                    <td>
                                        <span class="badge new blue" data-badge-caption="total">
                                            <?php echo $log['total_recipients']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="chip green white-text">
                                            Delivered: <?php echo $log['delivered_count']; ?>
                                        </div>
                                        <div class="chip blue white-text">
                                            Read: <?php echo $log['read_count']; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#modal-<?php echo $log['log_id']; ?>" class="btn-floating btn-small waves-effect waves-light blue modal-trigger">
                                            <i class="material-icons">visibility</i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Modal Structure -->
                                <div id="modal-<?php echo $log['log_id']; ?>" class="modal">
                                    <div class="modal-content">
                                        <h4><?php echo htmlspecialchars($log['subject']); ?></h4>
                                        <p class="grey-text">
                                            Sent on <?php echo date('F d, Y H:i', strtotime($log['sent_timestamp'])); ?>
                                            by <?php echo htmlspecialchars($log['sender_name']); ?>
                                        </p>
                                        <div class="divider"></div>
                                        <p><?php echo nl2br(htmlspecialchars($log['message_body'])); ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modals
    var modals = document.querySelectorAll('.modal');
    M.Modal.init(modals);
});
</script>

<?php include '../../includes/footer.php'; ?>
