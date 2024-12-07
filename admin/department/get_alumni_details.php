<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/DepartmentHead.php';
require_once '../../includes/department_auth.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'Department Head') {
    http_response_code(403);
    exit('Unauthorized');
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('Missing ID parameter');
}

$database = new Database();
$db = $database->getConnection();
$deptHead = new DepartmentHead($db);

// Get alumni details including employment and address
$query = "SELECT u.*, e.*, ua.*, d.department_name
          FROM users u
          LEFT JOIN employmenthistory e ON u.user_id = e.user_id AND e.is_current = 1
          LEFT JOIN useraddresses ua ON u.user_id = ua.user_id AND ua.is_current = 1
          LEFT JOIN departments d ON u.department_id = d.department_id
          WHERE u.user_id = ? AND u.department_id = ?";

$stmt = $db->prepare($query);
$stmt->execute([$_GET['id'], $_SESSION['department_id']]);
$alumniDetails = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$alumniDetails) {
    http_response_code(404);
    exit('Alumni not found');
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($alumniDetails); 