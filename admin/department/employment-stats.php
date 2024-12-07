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

$stats = $deptHead->getEmploymentStats($_SESSION['department_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employment Statistics - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include '../includes/department_navbar.php'; ?>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h4>Employment Statistics</h4>

                <!-- Employment Overview -->
                <div class="row">
                    <div class="col s12 m4">
                        <div class="card blue-grey darken-1">
                            <div class="card-content white-text">
                                <span class="card-title">Employment Rate</span>
                                <h3><?php echo $stats['employment_rate']; ?>%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card blue darken-1">
                            <div class="card-content white-text">
                                <span class="card-title">Average Time to Employment</span>
                                <h3><?php echo $stats['avg_time_to_employment']; ?> months</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card green darken-1">
                            <div class="card-content white-text">
                                <span class="card-title">Top Industry</span>
                                <h3><?php echo $stats['top_industry']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employment Type Distribution -->
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Employment Type Distribution</span>
                        <canvas id="employmentTypeChart"></canvas>
                    </div>
                </div>

                <!-- Industry Distribution -->
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Industry Distribution</span>
                        <canvas id="industryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        // Employment Type Chart
        new Chart(document.getElementById('employmentTypeChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($stats['employment_types'])); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($stats['employment_types'])); ?>,
                    backgroundColor: ['#2196F3', '#4CAF50', '#FF9800', '#F44336', '#9C27B0']
                }]
            }
        });

        // Industry Chart
        new Chart(document.getElementById('industryChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($stats['industries'])); ?>,
                datasets: [{
                    label: 'Number of Alumni',
                    data: <?php echo json_encode(array_values($stats['industries'])); ?>,
                    backgroundColor: '#2196F3'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html> 