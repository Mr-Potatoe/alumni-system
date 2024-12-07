<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/Admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $admin = new Admin($db);

    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $admin->login($username, $password);

    if ($result['success']) {
        $_SESSION['admin_id'] = $result['admin_id'];
        $_SESSION['admin_role'] = $result['role'];
        $_SESSION['admin_name'] = $result['name'];
        $_SESSION['department_id'] = $result['department_id'] ?? null;

        // Route based on role
        switch ($result['role']) {
            case 'Department Head':
                header("Location: ../department/dashboard.php");
                break;
            case 'Super Admin':
                header("Location: ../dashboard.php");
                break;
            case 'Staff':
                header("Location: ../staff/dashboard.php");
                break;
            default:
                header("Location: ../dashboard.php");
        }
        exit();
    } else {
        $_SESSION['login_error'] = 'Invalid username or password';
        header("Location: login.php");
        exit();
    }
} 