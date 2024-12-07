<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../classes/DepartmentHead.php';
require_once '../../includes/department_auth.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'Department Head') {
    header("Location: ../auth/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$deptHead = new DepartmentHead($db);

// Get filters
$graduation_year = isset($_GET['graduation_year']) ? $_GET['graduation_year'] : '';
$employment_status = isset($_GET['employment_status']) ? $_GET['employment_status'] : '';

// Get alumni list
$alumni = $deptHead->getDepartmentAlumni($_SESSION['department_id'], [
    'graduation_year' => $graduation_year,
    'employment_status' => $employment_status
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Department Alumni - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<?php include '../includes/department_navbar.php'; ?>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h4>Department Alumni</h4>
                
                <!-- Filters -->
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Filters</span>
                        <form method="GET" class="row">
                            <div class="input-field col s12 m4">
                                <select name="graduation_year">
                                    <option value="">All Years</option>
                                    <?php 
                                    $currentYear = date('Y');
                                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                                        $selected = ($graduation_year == $year) ? 'selected' : '';
                                        echo "<option value='$year' $selected>$year</option>";
                                    }
                                    ?>
                                </select>
                                <label>Graduation Year</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <select name="employment_status">
                                    <option value="">All Status</option>
                                    <option value="employed" <?php echo $employment_status === 'employed' ? 'selected' : ''; ?>>Employed</option>
                                    <option value="unemployed" <?php echo $employment_status === 'unemployed' ? 'selected' : ''; ?>>Unemployed</option>
                                </select>
                                <label>Employment Status</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <button class="btn waves-effect waves-light" type="submit">
                                    Apply Filters
                                    <i class="material-icons right">filter_list</i>
                                </button>
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
                                    <th>Graduation Year</th>
                                    <th>Email</th>
                                    <th>Current Employment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alumni as $alum): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($alum['first_name'] . ' ' . $alum['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($alum['graduation_year']); ?></td>
                                    <td><?php echo htmlspecialchars($alum['email']); ?></td>
                                    <td><?php echo htmlspecialchars($alum['current_employment'] ?? 'Not specified'); ?></td>
                                    <td>
                                        <a href="#" onclick="viewAlumniDetails(<?php echo $alum['user_id']; ?>)"
                                           class="btn-small waves-effect waves-light blue modal-trigger" data-target="alumniModal">
                                            <i class="material-icons">visibility</i>
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

    <!-- Alumni Modal Structure -->
    <div id="alumniModal" class="modal modal-fixed-footer">
        <div class="modal-content">
            <h4>Alumni Details</h4>
            <div id="alumniDetails">
                <div class="progress">
                    <div class="indeterminate"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var selects = document.querySelectorAll('select');
            M.FormSelect.init(selects);
            
            // Initialize modal
            var modals = document.querySelectorAll('.modal');
            M.Modal.init(modals);
        });

        function viewAlumniDetails(userId) {
            const modal = M.Modal.getInstance(document.getElementById('alumniModal'));
            modal.open();
            
            // Reset modal content
            document.getElementById('alumniDetails').innerHTML = `
                <div class="progress">
                    <div class="indeterminate"></div>
                </div>
            `;
            
            // Fetch alumni details
            fetch(`get_alumni_details.php?id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('alumniDetails').innerHTML = `
                        <div class="row">
                            <div class="col s12">
                                <h5>Personal Information</h5>
                                <p><strong>Name:</strong> ${data.first_name} ${data.middle_name ? data.middle_name + ' ' : ''}${data.last_name}</p>
                                <p><strong>Email:</strong> ${data.email}</p>
                                <p><strong>Contact Number:</strong> ${data.contact_number || 'Not specified'}</p>
                                <p><strong>Gender:</strong> ${data.gender || 'Not specified'}</p>
                                <p><strong>Birth Date:</strong> ${data.birth_date || 'Not specified'}</p>
                                
                                <h5>Academic Information</h5>
                                <p><strong>Department:</strong> ${data.department_name}</p>
                                <p><strong>Graduation Year:</strong> ${data.graduation_year}</p>
                                <p><strong>Graduation Semester:</strong> ${data.graduation_semester || 'Not specified'}</p>
                                
                                <h5>Current Employment</h5>
                                <p><strong>Company:</strong> ${data.company_name || 'Not specified'}</p>
                                <p><strong>Position:</strong> ${data.position_title || 'Not specified'}</p>
                                <p><strong>Employment Type:</strong> ${data.employment_type || 'Not specified'}</p>
                                <p><strong>Industry:</strong> ${data.industry || 'Not specified'}</p>
                                <p><strong>Location:</strong> ${data.location || 'Not specified'}</p>
                                
                                <h5>Current Address</h5>
                                <p><strong>Street:</strong> ${data.street_address || 'Not specified'}</p>
                                <p><strong>City:</strong> ${data.city || 'Not specified'}</p>
                                <p><strong>State:</strong> ${data.state || 'Not specified'}</p>
                                <p><strong>Country:</strong> ${data.country || 'Not specified'}</p>
                                <p><strong>Postal Code:</strong> ${data.postal_code || 'Not specified'}</p>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    document.getElementById('alumniDetails').innerHTML = `
                        <div class="red-text">Error loading alumni details. Please try again.</div>
                    `;
                });
        }
    </script>
</body>
</html> 