<?php
require_once '../config/config.php';
require_once '../config/database.php';

function createDefaultAdmin() {
    $database = new Database();
    $db = $database->getConnection();
    
    // Default admin credentials
    $username = 'admin';
    $password = 'Admin@123';
    $email = 'admin@yourdomain.com';
    $firstName = 'System';
    $lastName = 'Administrator';
    $role = 'Super Admin';
    
    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        // Check if admin already exists
        $query = "SELECT admin_id FROM Admins WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            // Insert new admin
            $query = "INSERT INTO Admins (
                        username, 
                        email, 
                        password_hash, 
                        first_name, 
                        last_name, 
                        role, 
                        is_active
                    ) VALUES (
                        :username, 
                        :email, 
                        :password, 
                        :firstName, 
                        :lastName, 
                        :role, 
                        TRUE
                    )";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $passwordHash);
            $stmt->bindParam(":firstName", $firstName);
            $stmt->bindParam(":lastName", $lastName);
            $stmt->bindParam(":role", $role);
            
            if ($stmt->execute()) {
                // Log the admin creation
                $adminId = $db->lastInsertId();
                $query = "INSERT INTO SystemAuditLog (
                            action_type, 
                            action_details, 
                            ip_address
                        ) VALUES (
                            'ADMIN_CREATED',
                            'Default admin account created (ID: " . $adminId . ")',
                            'SYSTEM'
                        )";
                $db->exec($query);
                
                echo "Default admin account created successfully!\n";
                echo "Username: admin\n";
                echo "Password: Admin@123\n";
                echo "Please change these credentials after first login.\n";
            } else {
                echo "Failed to create admin account.\n";
            }
        } else {
            echo "Admin account already exists.\n";
        }
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Only run this script from command line
if (php_sapi_name() === 'cli') {
    createDefaultAdmin();
} else {
    header("HTTP/1.0 403 Forbidden");
    exit('Access Forbidden');
}
?> 