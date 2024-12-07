<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Employment.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$employment = new Employment($db);

if (isset($_POST['employment_id'])) {
    $employmentId = $_POST['employment_id'];
    
    if ($employment->deleteEmployment($employmentId, $_SESSION['user_id'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete employment record']);
    }
    exit();
}

header("Location: view.php");
exit();
?> 