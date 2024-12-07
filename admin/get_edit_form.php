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
?>

<form id="editAlumniForm" onsubmit="saveAlumni(event, <?php echo $userId; ?>)">
    <div class="row">
        <!-- Personal Information -->
        <div class="input-field col s12 m4">
            <input type="text" id="first_name" name="first_name" 
                   value="<?php echo htmlspecialchars($alumniData['first_name'] ?? ''); ?>" required>
            <label for="first_name">First Name</label>
        </div>
        <div class="input-field col s12 m4">
            <input type="text" id="middle_name" name="middle_name" 
                   value="<?php echo htmlspecialchars($alumniData['middle_name'] ?? ''); ?>">
            <label for="middle_name">Middle Name</label>
        </div>
        <div class="input-field col s12 m4">
            <input type="text" id="last_name" name="last_name" 
                   value="<?php echo htmlspecialchars($alumniData['last_name'] ?? ''); ?>" required>
            <label for="last_name">Last Name</label>
        </div>

        <!-- Contact Information -->
        <div class="input-field col s12 m6">
            <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($alumniData['email'] ?? ''); ?>" required>
            <label for="email">Email</label>
        </div>
        <div class="input-field col s12 m6">
            <input type="tel" id="phone" name="phone" 
                   value="<?php echo htmlspecialchars($alumniData['contact_number'] ?? ''); ?>">
            <label for="phone">Phone Number</label>
        </div>

        <!-- Academic Information -->
        <div class="input-field col s12 m6">
            <input type="number" id="graduation_year" name="graduation_year" 
                   value="<?php echo htmlspecialchars($alumniData['graduation_year'] ?? ''); ?>" required>
            <label for="graduation_year">Graduation Year</label>
        </div>
        <div class="input-field col s12 m6">
            <select id="graduation_semester" name="graduation_semester">
                <option value="">Select Semester</option>
                <option value="First" <?php echo ($alumniData['graduation_semester'] ?? '') === 'First' ? 'selected' : ''; ?>>First</option>
                <option value="Second" <?php echo ($alumniData['graduation_semester'] ?? '') === 'Second' ? 'selected' : ''; ?>>Second</option>
                <option value="Summer" <?php echo ($alumniData['graduation_semester'] ?? '') === 'Summer' ? 'selected' : ''; ?>>Summer</option>
            </select>
            <label for="graduation_semester">Graduation Semester</label>
        </div>

        <!-- Employment Information -->
        <div class="input-field col s12">
            <input type="text" id="current_employment" name="current_employment" value="<?php echo htmlspecialchars($alumniData['current_employment'] ?? ''); ?>">
            <label for="current_employment">Current Employment</label>
        </div>
        <div class="input-field col s12">
            <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($alumniData['company'] ?? ''); ?>">
            <label for="company">Company</label>
        </div>
        
        <!-- Address Information -->
        <div class="input-field col s12">
            <textarea id="address" name="address" class="materialize-textarea"><?php echo htmlspecialchars($alumniData['address'] ?? ''); ?></textarea>
            <label for="address">Address</label>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <button type="submit" class="btn waves-effect waves-light">
                Save Changes
                <i class="material-icons right">save</i>
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Materialize components
    M.updateTextFields();
    M.textareaAutoResize(document.querySelector('#address'));
    
    // Add active class to all labels with filled inputs
    document.querySelectorAll('.input-field input[value], .input-field textarea:not(:empty)').forEach(input => {
        input.labels[0]?.classList.add('active');
    });
});
</script>

<script>
function saveAlumni(event, userId) {
    event.preventDefault();
    const form = document.getElementById('editAlumniForm');
    const formData = new FormData(form);
    formData.append('user_id', userId);
    
    // Debug: Log form data
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    fetch('save_alumni.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            M.toast({html: 'Profile updated successfully'});
            var modal = M.Modal.getInstance(document.getElementById('editProfileModal'));
            modal.close();
            // Refresh the page or update the table row
            location.reload();
        } else {
            M.toast({html: 'Error updating profile'});
        }
    })
    .catch(error => {
        console.error('Error:', error);
        M.toast({html: 'Error updating profile'});
    });
}
</script> 