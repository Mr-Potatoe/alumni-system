<?php
function checkDepartmentHeadAuth() {
    if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'Department Head') {
        header("Location: ../auth/login.php");
        exit();
    }
} 