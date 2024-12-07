<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';
require_once '../classes/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$admin = new Admin($db);
$user = new User($db);

$stats = $admin->getDashboardStats();
$recentRegistrations = $admin->getRecentRegistrations(5);
$pendingApprovals = $admin->getPendingApprovals(5);
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/admin_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Admin Dashboard</h4>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col s12 m3">
            <div class="card blue white-text">
                <div class="card-content">
                    <span class="card-title">Total Alumni</span>
                    <h4><?php echo $stats['total_alumni']; ?></h4>
                </div>
            </div>
        </div>
        <div class="col s12 m3">
            <div class="card green white-text">
                <div class="card-content">
                    <span class="card-title">Employed</span>
                    <h4><?php echo $stats['employed_alumni']; ?></h4>
                </div>
            </div>
        </div>
        <div class="col s12 m3">
            <div class="card orange white-text">
                <div class="card-content">
                    <span class="card-title">Pending Approval</span>
                    <h4><?php echo $stats['pending_approvals']; ?></h4>
                </div>
            </div>
        </div>
        <div class="col s12 m3">
            <div class="card red white-text">
                <div class="card-content">
                    <span class="card-title">This Year Grads</span>
                    <h4><?php echo $stats['current_year_graduates']; ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Registrations -->
    <div class="row">
        <div class="col s12 m6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Recent Registrations</span>
                    <table class="striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Graduation Year</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRegistrations as $reg): ?>
                                <tr>
                                    <td><?php echo $reg['first_name'] . ' ' . $reg['last_name']; ?></td>
                                    <td><?php echo $reg['graduation_year']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($reg['registration_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="card-action">
                        <a href="registrations.php">View All</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="col s12 m6">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Pending Approvals</span>
                    <table class="striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingApprovals as $pending): ?>
                                <tr>
                                    <td><?php echo $pending['first_name'] . ' ' . $pending['last_name']; ?></td>
                                    <td><?php echo $pending['email']; ?></td>
                                    <td>
                                        <a href="approve.php?id=<?php echo $pending['user_id']; ?>" 
                                           class="btn-small green">Approve</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="card-action">
                        <a href="pending.php">View All Pending</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
