<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Address.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$address = new Address($db);

$message = '';
$addresses = $address->getUserAddresses($_SESSION['user_id']);

if (isset($_POST['add_address'])) {
    $data = [
        'user_id' => $_SESSION['user_id'],
        'address_type' => $_POST['address_type'],
        'street_address' => $_POST['street_address'],
        'city' => $_POST['city'],
        'state' => $_POST['state'],
        'country' => $_POST['country'],
        'postal_code' => $_POST['postal_code'],
        'is_current' => isset($_POST['is_current']) ? 1 : 0
    ];

    if ($address->addAddress($data)) {
        $message = '<div class="green-text">Address added successfully!</div>';
        $addresses = $address->getUserAddresses($_SESSION['user_id']); // Refresh list
    } else {
        $message = '<div class="red-text">Failed to add address.</div>';
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/alumni_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Address Management</h4>
            <?php echo $message; ?>

            <!-- Add New Address -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Add New Address</span>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <select name="address_type" required>
                                    <option value="" disabled selected>Choose type</option>
                                    <option value="Present">Present</option>
                                    <option value="Permanent">Permanent</option>
                                    <option value="Recent">Recent</option>
                                </select>
                                <label>Address Type</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="postal_code" name="postal_code" required>
                                <label for="postal_code">Postal Code</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12">
                                <textarea id="street_address" name="street_address" class="materialize-textarea" required></textarea>
                                <label for="street_address">Street Address</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m4">
                                <input type="text" id="city" name="city" required>
                                <label for="city">City</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="text" id="state" name="state" required>
                                <label for="state">State/Province</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="text" id="country" name="country" required>
                                <label for="country">Country</label>
                            </div>
                        </div>

                        <p>
                            <label>
                                <input type="checkbox" class="filled-in" name="is_current" />
                                <span>This is my current address</span>
                            </label>
                        </p>

                        <button class="btn waves-effect waves-light" type="submit" name="add_address">
                            Add Address
                            <i class="material-icons right">add_location</i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Existing Addresses -->
            <div class="card">
                <div class="card-content">
                    <span class="card-title">My Addresses</span>
                    <?php if (empty($addresses)): ?>
                        <p>No addresses found.</p>
                    <?php else: ?>
                        <?php foreach ($addresses as $addr): ?>
                            <div class="address-card">
                                <div class="address-type">
                                    <?php echo $addr['address_type']; ?>
                                    <?php if ($addr['is_current']): ?>
                                        <span class="new badge blue" data-badge-caption="Current"></span>
                                    <?php endif; ?>
                                </div>
                                <p><?php echo $addr['street_address']; ?></p>
                                <p><?php echo $addr['city'] . ', ' . $addr['state']; ?></p>
                                <p><?php echo $addr['country'] . ' ' . $addr['postal_code']; ?></p>
                            </div>
                            <div class="divider"></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 