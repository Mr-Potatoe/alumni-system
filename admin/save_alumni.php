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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    
    // Prepare user data based on your database schema
    $userData = [
        // Personal Information
        'first_name' => $_POST['first_name'] ?? '',
        'middle_name' => $_POST['middle_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'contact_number' => $_POST['phone'] ?? '', // Mapped from phone field in form
        'gender' => $_POST['gender'] ?? '',
        
        // Academic Information
        'graduation_year' => $_POST['graduation_year'] ?? null,
        'graduation_semester' => $_POST['graduation_semester'] ?? ''
    ];

    try {
        // Start transaction
        $db->beginTransaction();

        // Update user basic information
        if ($admin->updateAlumni($userId, $userData)) {
            
            // Update address if provided
            if (!empty($_POST['address'])) {
                $addressData = [
                    'user_id' => $userId,
                    'address_type' => 'Present',
                    'street_address' => $_POST['address'],
                    'is_current' => true
                ];
                $admin->updateAlumniAddress($userId, $addressData);
            }

            // Update employment if provided
            if (!empty($_POST['current_employment']) || !empty($_POST['company'])) {
                $employmentData = [
                    'user_id' => $userId,
                    'company_name' => $_POST['company'] ?? '',
                    'position_title' => $_POST['current_employment'] ?? '',
                    'is_current' => true,
                    'employment_type' => 'Full-time' // Default value, modify as needed
                ];
                $admin->updateAlumniEmployment($userId, $employmentData);
            }

            // Log the update in system audit
            $admin->logAudit(
                $_SESSION['admin_id'],
                'ALUMNI_UPDATE',
                "Updated alumni profile for user ID: $userId",
                $_SERVER['REMOTE_ADDR']
            );
            
            $db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update profile');
        }
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Error updating profile: ' . $e->getMessage(),
            'debug' => [
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 