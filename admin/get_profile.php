<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Admin.php';

if (!isset($_SESSION['admin_id'])) {
    exit('Unauthorized');
}

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$userId = isset($_GET['id']) ? $_GET['id'] : 0;
$alumniData = $admin->getAlumniDetails($userId);

if (!$alumniData) {
    exit('Alumni not found');
}

// Get current employment and address data
$currentEmployment = $admin->getCurrentEmployment($userId);
$currentAddress = $admin->getCurrentAddress($userId);
?>

<div class="row">
    <div class="col s12">
        <!-- Personal Information -->
        <h5>Personal Information</h5>
        <p><strong>Name:</strong>
            <?php
            // Handle each name part separately
            $firstName = htmlspecialchars(trim((string)($alumniData['first_name'] ?? '')));
            $middleName = htmlspecialchars(trim((string)($alumniData['middle_name'] ?? '')));
            $lastName = htmlspecialchars(trim((string)($alumniData['last_name'] ?? '')));

            // Build the full name
            $fullName = $firstName;
            if (!empty($middleName)) {
                $fullName .= ' ' . $middleName;
            }
            if (!empty($lastName)) {
                $fullName .= ' ' . $lastName;
            }

            // Output the sanitized name
            echo $fullName;
            ?>
        </p>

        <p><strong>Email:</strong> <?php echo htmlspecialchars($alumniData['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($alumniData['contact_number'] ?: 'Not provided'); ?></p>

        <!-- Academic Information -->
        <h5>Academic Information</h5>
        <p><strong>Graduation Year:</strong> <?php echo htmlspecialchars($alumniData['graduation_year']); ?></p>
        <p><strong>Graduation Semester:</strong> <?php echo htmlspecialchars($alumniData['graduation_semester'] ?: 'Not provided'); ?></p>

        <!-- Employment Information -->
        <h5>Employment Information</h5>
        <?php if (!empty($alumniData['current_employment']) || !empty($alumniData['company'])): ?>
            <p><strong>Current Position:</strong> <?php echo htmlspecialchars($alumniData['current_employment'] ?: 'Not provided'); ?></p>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($alumniData['company'] ?: 'Not provided'); ?></p>
        <?php else: ?>
            <p>No employment information available</p>
        <?php endif; ?>

        <!-- Address Information -->
        <h5>Address</h5>
        <?php if (!empty($alumniData['address'])): ?>
            <p><?php echo htmlspecialchars($alumniData['address']); ?></p>
        <?php else: ?>
            <p>No address information available</p>
        <?php endif; ?>
    </div>
</div>