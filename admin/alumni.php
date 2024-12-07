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

// Handle search and filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$employed = isset($_GET['employed']) ? $_GET['employed'] : '';

$alumni = $admin->getAlumni($search, $year, $employed);
$graduationYears = $admin->getGraduationYears();
?>

<?php include '../includes/header.php'; ?>
<?php include 'includes/admin_navbar.php'; ?>

<div class="container">
    <div class="row">
        <div class="col s12">
            <h4>Alumni Management</h4>
            
            <!-- Search and Filters -->
            <div class="card">
                <div class="card-content">
                    <form method="GET">
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                <label for="search">Search by name or email</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <select name="year">
                                    <option value="">All Years</option>
                                    <?php foreach ($graduationYears as $yr): ?>
                                        <option value="<?php echo $yr; ?>" <?php echo $year == $yr ? 'selected' : ''; ?>>
                                            <?php echo $yr; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Graduation Year</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <select name="employed">
                                    <option value="">All Status</option>
                                    <option value="1" <?php echo $employed === '1' ? 'selected' : ''; ?>>Employed</option>
                                    <option value="0" <?php echo $employed === '0' ? 'selected' : ''; ?>>Not Employed</option>
                                </select>
                                <label>Employment Status</label>
                            </div>
                            <div class="input-field col s12 m2">
                                <button type="submit" class="btn waves-effect waves-light">
                                    <i class="material-icons">search</i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alumni List -->
            <div class="card">
                <div class="card-content">
                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Graduation Year</th>
                                <th>Employment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumni as $alum): ?>
                                <tr>
                                    <td><?php echo $alum['first_name'] . ' ' . $alum['last_name']; ?></td>
                                    <td><?php echo $alum['email']; ?></td>
                                    <td><?php echo $alum['graduation_year']; ?></td>
                                    <td>
                                        <?php if ($alum['current_employment']): ?>
                                            <span class="green-text"><?php echo $alum['current_employment']; ?></span>
                                        <?php else: ?>
                                            <span class="grey-text">Not employed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="#!" onclick="viewProfile(<?php echo $alum['user_id']; ?>)" 
                                           class="btn-small blue waves-effect waves-light">
                                            <i class="material-icons">visibility</i>
                                        </a>
                                        <a href="#!" onclick="editProfile(<?php echo $alum['user_id']; ?>)" 
                                           class="btn-small orange waves-effect waves-light">
                                            <i class="material-icons">edit</i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Profile Modal -->
<div id="viewProfileModal" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4>Alumni Profile</h4>
        <div id="profileContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
    </div>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4>Edit Alumni Profile</h4>
        <div id="editContent" class="row">
            <!-- Content will be loaded here -->
            <div class="progress">
                <div class="indeterminate"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="modal-close waves-effect waves-light btn-flat">Cancel</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all modals
    var modals = document.querySelectorAll('.modal');
    M.Modal.init(modals, {
        dismissible: false, // Modal can't be dismissed by clicking outside
        opacity: 0.5, // Opacity of modal background
        inDuration: 300, // Transition in duration
        outDuration: 200, // Transition out duration
        onOpenStart: function() {
            // Reset content when modal starts opening
            document.getElementById('editContent').innerHTML = `
                <div class="progress">
                    <div class="indeterminate"></div>
                </div>
            `;
        }
    });
});

function viewProfile(userId) {
    fetch('get_profile.php?id=' + userId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('profileContent').innerHTML = data;
            var modal = M.Modal.getInstance(document.getElementById('viewProfileModal'));
            modal.open();
        })
        .catch(error => console.error('Error:', error));
}

function editProfile(userId) {
    const modal = M.Modal.getInstance(document.getElementById('editProfileModal'));
    modal.open();

    // Fetch form content
    fetch('get_edit_form.php?id=' + userId, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(data => {
        document.getElementById('editContent').innerHTML = data;
        
        // Initialize all Materialize form elements
        M.updateTextFields();
        
        // Initialize select dropdowns
        var selects = document.querySelectorAll('select');
        M.FormSelect.init(selects);
        
        // Initialize datepicker
        var datePickers = document.querySelectorAll('.datepicker');
        M.Datepicker.init(datePickers, {
            format: 'yyyy-mm-dd',
            autoClose: true,
            defaultDate: new Date(),
            setDefaultDate: true
        });

        // Add active class to labels
        document.querySelectorAll('.input-field label').forEach(label => {
            label.classList.add('active');
        });
    })
    .catch(error => {
        console.error('Error:', error);
        M.toast({html: 'Error loading edit form'});
    });
}

// Add this function to handle form submission
function saveAlumni(event, userId) {
    event.preventDefault();
    const form = document.getElementById('editAlumniForm');
    const formData = new FormData(form);
    formData.append('user_id', userId);

    fetch('save_alumni.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            M.toast({html: data.message || 'Profile updated successfully'});
            var modal = M.Modal.getInstance(document.getElementById('editProfileModal'));
            modal.close();
            location.reload();
        } else {
            console.error('Server Error:', data);
            M.toast({
                html: data.message || 'Error updating profile', 
                classes: 'red'
            });
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        M.toast({
            html: 'Error updating profile: ' + error.message, 
            classes: 'red'
        });
    });
}
</script>

<?php include '../includes/footer.php'; ?> 