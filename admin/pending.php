<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

// Get all pending approvals
$pendingApprovals = $admin->getAllPendingApprovals();
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/admin_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Pending Approvals</h4>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="card-panel green white-text">
                    <?php 
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="card-panel red white-text">
                    <?php 
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-content">
                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Graduation Year</th>
                                <th>Registration Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingApprovals)): ?>
                                <tr>
                                    <td colspan="5" class="center-align">No pending approvals</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pendingApprovals as $pending): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pending['first_name'] . ' ' . $pending['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($pending['email']); ?></td>
                                        <td><?php echo htmlspecialchars($pending['graduation_year']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($pending['registration_date'])); ?></td>
                                        <td>
                                            <a href="approve.php?id=<?php echo $pending['user_id']; ?>" 
                                               class="btn-small green">Approve</a>
                                            <a href="#" onclick="rejectUser(<?php echo $pending['user_id']; ?>)"
                                               class="btn-small red">Reject</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function rejectUser(userId) {
    if (confirm('Are you sure you want to reject this user?')) {
        window.location.href = `reject.php?id=${userId}`;
    }
}
</script>

<?php include '../includes/footer.php'; ?> 