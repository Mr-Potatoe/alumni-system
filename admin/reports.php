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

// Get report data
$employmentStats = $admin->getEmploymentStatistics();
$yearlyStats = $admin->getYearlyStatistics();
$industryStats = $admin->getIndustryStatistics();

// Handle export if requested
if (isset($_POST['export'])) {
    $reportType = $_POST['report_type'];
    $admin->exportReport($reportType);
}
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/admin_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Reports & Analytics</h4>

            <!-- Export Options -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Export Reports</span>
                    <form method="POST" action="export_alumni.php">
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <select name="report_type" required>
                                    <option value="" disabled selected>Choose report type</option>
                                    <option value="employment">Employment Statistics</option>
                                    <option value="yearly">Yearly Statistics</option>
                                    <option value="industry">Industry Distribution</option>
                                    <option value="complete">Complete Alumni Data</option>
                                </select>
                                <label>Report Type</label>
                            </div>
                            <div class="col s12 m6">
                                <button type="submit" class="btn waves-effect waves-light">
                                    Export to Excel
                                    <i class="material-icons right">file_download</i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Employment Statistics -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Employment Statistics</span>
                    <div class="row">
                        <div class="col s12">
                            <canvas id="employmentChart"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <table class="striped">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employmentStats as $stat): ?>
                                        <tr>
                                            <td><?php echo $stat['status']; ?></td>
                                            <td><?php echo $stat['count']; ?></td>
                                            <td><?php echo number_format($stat['percentage'], 1); ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Industry Distribution -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Industry Distribution</span>
                    <div class="row">
                        <div class="col s12">
                            <canvas id="industryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yearly Statistics -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Yearly Statistics</span>
                    <div class="row">
                        <div class="col s12">
                            <canvas id="yearlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Employment Chart
const empCtx = document.getElementById('employmentChart').getContext('2d');
new Chart(empCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($employmentStats, 'status')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($employmentStats, 'count')); ?>,
            backgroundColor: ['#2196F3', '#4CAF50', '#FFC107', '#F44336']
        }]
    }
});

// Industry Chart
const indCtx = document.getElementById('industryChart').getContext('2d');
new Chart(indCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($industryStats, 'industry')); ?>,
        datasets: [{
            label: 'Number of Alumni',
            data: <?php echo json_encode(array_column($industryStats, 'count')); ?>,
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

// Yearly Chart
const yearCtx = document.getElementById('yearlyChart').getContext('2d');
new Chart(yearCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($yearlyStats, 'year')); ?>,
        datasets: [{
            label: 'Graduates',
            data: <?php echo json_encode(array_column($yearlyStats, 'count')); ?>,
            borderColor: '#2196F3',
            tension: 0.1
        }]
    }
});
</script>

<?php include '../includes/footer.php'; ?> 