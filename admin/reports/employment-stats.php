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

// Get employment statistics
$employmentStats = $admin->getEmploymentStatistics();
$industryStats = $admin->getIndustryStatistics();
$yearlyEmploymentTrends = $admin->getYearlyEmploymentTrends();
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
            <h4>Employment Statistics</h4>
            
            <!-- Export Button -->
            <div class="fixed-action-btn">
                <a class="btn-floating btn-large blue">
                    <i class="large material-icons">more_vert</i>
                </a>
                <ul>
                    <li>
                        <form method="POST" action="../export_alumni.php" style="display: none;">
                            <input type="hidden" name="report_type" value="employment">
                        </form>
                        <a class="btn-floating green tooltipped" data-position="left" data-tooltip="Export to Excel" 
                           onclick="this.previousElementSibling.submit();">
                            <i class="material-icons">file_download</i>
                        </a>
                    </li>
                    <li>
                        <a class="btn-floating blue tooltipped" data-position="left" data-tooltip="Print Report" 
                           onclick="window.print();">
                            <i class="material-icons">print</i>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Employment Status Overview -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Employment Status Overview</span>
                    <div class="row">
                        <?php foreach ($employmentStats as $stat): ?>
                            <div class="col s12 m4">
                                <div class="card-panel center-align">
                                    <i class="material-icons medium blue-text">work</i>
                                    <h5><?php echo htmlspecialchars($stat['status']); ?></h5>
                                    <h4><?php echo $stat['count']; ?></h4>
                                    <div class="progress">
                                        <div class="determinate blue" style="width: <?php echo $stat['percentage']; ?>%"></div>
                                    </div>
                                    <span class="grey-text"><?php echo number_format($stat['percentage'], 1); ?>%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Industry Distribution -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Industry Distribution</span>
                    <table class="striped">
                        <thead>
                            <tr>
                                <th>Industry</th>
                                <th>Count</th>
                                <th>Distribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($industryStats as $stat): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($stat['industry']); ?></td>
                                    <td><?php echo $stat['count']; ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="determinate blue" 
                                                 style="width: <?php echo ($stat['count'] / array_sum(array_column($industryStats, 'count')) * 100); ?>%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Yearly Employment Trends -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Yearly Employment Trends</span>
                    <table class="striped">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Employment Rate</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($yearlyEmploymentTrends as $trend): ?>
                                <tr>
                                    <td><?php echo $trend['year']; ?></td>
                                    <td><?php echo number_format($trend['rate'], 1); ?>%</td>
                                    <td>
                                        <div class="progress">
                                            <div class="determinate blue" style="width: <?php echo $trend['rate']; ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
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
    // Initialize floating action button
    var elems = document.querySelectorAll('.fixed-action-btn');
    M.FloatingActionButton.init(elems);

    // Initialize tooltips
    var tooltips = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltips);
});
</script>

<?php include '../../includes/footer.php'; ?>
