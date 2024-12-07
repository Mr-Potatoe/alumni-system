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

if (isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    
    try {
        if ($admin->approveUser($userId)) {
            // Log the approval
            $admin->logAudit(
                $_SESSION['admin_id'],
                'USER_APPROVAL',
                "Approved user ID: $userId",
                $_SERVER['REMOTE_ADDR']
            );
            
            // Redirect back to pending page with success message
            $_SESSION['success_message'] = "User successfully approved";
        } else {
            $_SESSION['error_message'] = "Failed to approve user";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    
    // Redirect back to pending page
    header("Location: pending.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request";
    header("Location: pending.php");
    exit();
}
?> 