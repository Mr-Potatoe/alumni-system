<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

if (isset($_POST['report_type'])) {
    $reportType = $_POST['report_type'];
    
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="alumni_report_' . date('Y-m-d') . '.xls"');
    
    // Start the HTML table
    echo '<table border="1">';
    
    switch ($reportType) {
        case 'employment':
            $stats = $admin->getEmploymentStatistics();
            echo '<tr><th>Employment Status</th><th>Count</th><th>Percentage</th></tr>';
            foreach ($stats as $stat) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($stat['status']) . '</td>';
                echo '<td>' . htmlspecialchars($stat['count']) . '</td>';
                echo '<td>' . htmlspecialchars(number_format($stat['percentage'], 1)) . '%</td>';
                echo '</tr>';
            }
            break;
            
        case 'yearly':
            $stats = $admin->getYearlyStatistics();
            echo '<tr><th>Graduation Year</th><th>Number of Graduates</th></tr>';
            foreach ($stats as $stat) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($stat['year']) . '</td>';
                echo '<td>' . htmlspecialchars($stat['count']) . '</td>';
                echo '</tr>';
            }
            break;
            
        case 'industry':
            $stats = $admin->getIndustryStatistics();
            echo '<tr><th>Industry</th><th>Number of Alumni</th></tr>';
            foreach ($stats as $stat) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($stat['industry']) . '</td>';
                echo '<td>' . htmlspecialchars($stat['count']) . '</td>';
                echo '</tr>';
            }
            break;
            
        case 'complete':
            $alumni = $admin->getAllAlumniData();
            if (!empty($alumni)) {
                // Headers
                echo '<tr>';
                foreach (array_keys($alumni[0]) as $header) {
                    echo '<th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $header))) . '</th>';
                }
                echo '</tr>';
                
                // Data
                foreach ($alumni as $record) {
                    echo '<tr>';
                    foreach ($record as $value) {
                        echo '<td>' . htmlspecialchars($value ?? '') . '</td>';
                    }
                    echo '</tr>';
                }
            }
            break;
    }
    
    echo '</table>';
    exit();
}

// If we get here, something went wrong
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid report type']); 