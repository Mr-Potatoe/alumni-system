<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Dummy data
$dummy_users = [
    [
        'email' => 'john.doe@example.com',
        'password' => 'password123',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'middle_name' => 'Michael',
        'graduation_year' => 2020,
        'contact_number' => '1234567890',
        'gender' => 'Male',
        'birth_date' => '1998-05-15'
    ],
    [
        'email' => 'jane.smith@example.com',
        'password' => 'password123',
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'middle_name' => 'Elizabeth',
        'graduation_year' => 2021,
        'contact_number' => '0987654321',
        'gender' => 'Female',
        'birth_date' => '1999-08-22'
    ],
    // Add more dummy users as needed
];

// Insert dummy users
foreach($dummy_users as $dummy_user) {
    try {
        if($user->register($dummy_user)) {
            echo "Created user: {$dummy_user['email']}<br>";
            
            // Set as approved for testing
            $query = "UPDATE Users SET registration_status = 'Approved', email_verified = TRUE 
                     WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $dummy_user['email']);
            $stmt->execute();
            
            echo "Approved user: {$dummy_user['email']}<br>";
        } else {
            echo "Failed to create user: {$dummy_user['email']}<br>";
        }
    } catch (Exception $e) {
        echo "Error creating user {$dummy_user['email']}: " . $e->getMessage() . "<br>";
    }
}

echo "Dummy data creation completed!";