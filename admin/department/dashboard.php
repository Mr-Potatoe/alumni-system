<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/DepartmentHead.php';
require_once '../../includes/department_auth.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'Department Head') {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$deptHead = new DepartmentHead($db);

// Get department statistics
$stats = $deptHead->getDepartmentStats($_SESSION['department_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Head Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/custom.css">
</head>
<body>
<?php include '../includes/department_navbar.php'; ?>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h4>Department Dashboard</h4>
                <div class="row">
                    <!-- Statistics Cards -->
                    <div class="col s12 m4">
                        <div class="card blue-grey darken-1">
                            <div class="card-content white-text">
                                <span class="card-title">Total Alumni</span>
                                <h3><?php echo $stats['total_alumni']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card blue darken-1">
                            <div class="card-content white-text">
                                <span class="card-title">Employed Alumni</span>
                                <h3><?php echo $stats['employed_alumni']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card green darken-1">
                            <div class="card-content white-text">
                                <span class="card-title">Recent Graduates</span>
                                <h3><?php echo $stats['recent_graduates']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col s12">
                        <div class="card">
                            <div class="card-content">
                                <span class="card-title">Quick Actions</span>
                                <div class="row">
                                    <div class="col s12 m4">
                                        <a href="send-announcement.php" class="btn-large waves-effect waves-light blue darken-1 w100">
                                            <i class="material-icons left">announcement</i>
                                            Send Announcement
                                        </a>
                                    </div>
                                    <div class="col s12 m4">
                                        <a href="view-alumni.php" class="btn-large waves-effect waves-light green darken-1 w100">
                                            <i class="material-icons left">people</i>
                                            View Alumni
                                        </a>
                                    </div>
                                    <div class="col s12 m4">
                                        <a href="employment-stats.php" class="btn-large waves-effect waves-light orange darken-1 w100">
                                            <i class="material-icons left">work</i>
                                            Employment Stats
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html> 