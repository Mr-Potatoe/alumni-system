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

// Get all registrations
$registrations = $admin->getAllRegistrations();
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/admin_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>All Registrations</h4>
            
            <div class="card">
                <div class="card-content">
                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Graduation Year</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($registrations)): ?>
                                <tr>
                                    <td colspan="5" class="center-align">No registrations found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($registrations as $reg): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reg['first_name'] . ' ' . $reg['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['email']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['graduation_year']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($reg['registration_date'])); ?></td>
                                        <td>
                                            <span class="chip <?php echo getStatusClass($reg['registration_status']); ?>">
                                                <?php echo htmlspecialchars($reg['registration_status']); ?>
                                            </span>
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

<?php
function getStatusClass($status) {
    switch ($status) {
        case 'Approved':
            return 'green white-text';
        case 'Pending':
            return 'orange white-text';
        case 'Rejected':
            return 'red white-text';
        default:
            return 'grey white-text';
    }
}
?>

<?php include '../includes/footer.php'; ?> 