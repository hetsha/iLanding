<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth_check.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_company':
                $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
                $business_role = filter_input(INPUT_POST, 'business_role', FILTER_SANITIZE_STRING);
                $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
                $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);
                $card_pic = $_FILES['card_pic']['name'];
                $added_by = filter_input(INPUT_POST, 'added_by', FILTER_SANITIZE_STRING);

                // Upload the card picture if provided
                if ($card_pic) {
                    $target_dir = "uploads/";
                    $target_file = $target_dir . basename($card_pic);
                    move_uploaded_file($_FILES["card_pic"]["tmp_name"], $target_file);
                }

                $sql = "INSERT INTO companies (company_name, business_role, address, contact_number, card_pic, added_by)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $company_name, $business_role, $address, $contact_number, $card_pic, $added_by);
                $stmt->execute();
                break;

            case 'update_company':
                $company_id = filter_input(INPUT_POST, 'company_id', FILTER_SANITIZE_NUMBER_INT);
                $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
                $business_role = filter_input(INPUT_POST, 'business_role', FILTER_SANITIZE_STRING);
                $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
                $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);
                $card_pic = $_FILES['card_pic']['name'];

                // Upload new card picture if provided
                if ($card_pic) {
                    $target_dir = "uploads/";
                    $target_file = $target_dir . basename($card_pic);
                    move_uploaded_file($_FILES["card_pic"]["tmp_name"], $target_file);
                    $sql = "UPDATE companies SET company_name = ?, business_role = ?, address = ?, contact_number = ?, card_pic = ? WHERE company_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssi", $company_name, $business_role, $address, $contact_number, $card_pic, $company_id);
                } else {
                    $sql = "UPDATE companies SET company_name = ?, business_role = ?, address = ?, contact_number = ? WHERE company_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssi", $company_name, $business_role, $address, $contact_number, $company_id);
                }
                $stmt->execute();
                break;

            case 'delete_company':
                $company_id = filter_input(INPUT_POST, 'company_id', FILTER_SANITIZE_NUMBER_INT);
                $sql = "DELETE FROM companies WHERE company_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $company_id);
                $stmt->execute();
                break;
        }
    }
    header("Location: companies.php");
    exit();
}

// Fetch all companies
$sql = "SELECT * FROM companies ORDER BY date_added DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/upparac6.png" rel="icon">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Company Management</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                    <i class='bx bx-plus'></i> Add New Company
                </button>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Company Name</th>
                                            <th>Business Role</th>
                                            <th>Contact Number</th>
                                            <th>Added By</th>
                                            <th>Date Added</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($company = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <a href="#" class="view-details" data-bs-toggle="modal" data-bs-target="#viewCompanyModal"
                                                        data-name="<?php echo htmlspecialchars($company['company_name']); ?>"
                                                        data-role="<?php echo htmlspecialchars($company['business_role']); ?>"
                                                        data-address="<?php echo htmlspecialchars($company['address']); ?>"
                                                        data-contact="<?php echo htmlspecialchars($company['contact_number']); ?>"
                                                        data-pic="<?php echo $company['card_pic']; ?>"
                                                        data-addedby="<?php echo $company['added_by']; ?>">
                                                        <?php echo htmlspecialchars($company['company_name']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($company['business_role']); ?></td>
                                                <td><?php echo htmlspecialchars($company['contact_number']); ?></td>
                                                <td><?php echo htmlspecialchars($company['added_by']); ?></td>
                                                <td><?php echo $company['date_added']; ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCompanyModal"
                                                        data-id="<?php echo $company['company_id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($company['company_name']); ?>"
                                                        data-role="<?php echo htmlspecialchars($company['business_role']); ?>"
                                                        data-address="<?php echo htmlspecialchars($company['address']); ?>"
                                                        data-contact="<?php echo htmlspecialchars($company['contact_number']); ?>"
                                                        data-pic="<?php echo $company['card_pic']; ?>"
                                                        data-addedby="<?php echo $company['added_by']; ?>">Edit</button>
                                                    <form method="POST" action="" style="display:inline;">
                                                        <input type="hidden" name="action" value="delete_company">
                                                        <input type="hidden" name="company_id" value="<?php echo $company['company_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this company?');">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Company Modal -->
    <div class="modal fade" id="addCompanyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_company">
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Business Role</label>
                            <input type="text" class="form-control" name="business_role" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Card Picture</label>
                            <input type="file" class="form-control" name="card_pic">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Added By</label>
                            <select class="form-select" name="added_by" required>
                                <option value="hetd">HETD</option>
                                <option value="het b">HET B</option>
                                <option value="jainam">Jainam</option>
                                <option value="akshat">Akshat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCompanyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_company">
                        <input type="hidden" id="edit-company-id" name="company_id">
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="edit-company-name" name="company_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Business Role</label>
                            <input type="text" class="form-control" id="edit-business-role" name="business_role" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="edit-address" name="address" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="edit-contact-number" name="contact_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Card Picture</label>
                            <input type="file" class="form-control" name="card_pic">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Added By</label>
                            <select class="form-select" id="edit-added-by" name="added_by" required>
                                <option value="hetd">HETD</option>
                                <option value="het b">HET B</option>
                                <option value="jainam">Jainam</option>
                                <option value="akshat">Akshat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- View Company Modal (New for showing full details) -->
    <div class="modal fade" id="viewCompanyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Company Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Company Name:</strong> <span id="view-company-name"></span></p>
                    <p><strong>Business Role:</strong> <span id="view-business-role"></span></p>
                    <p><strong>Address:</strong> <span id="view-address"></span></p>
                    <p><strong>Contact Number:</strong> <span id="view-contact-number"></span></p>
                    <p><strong>Added By:</strong> <span id="view-added-by"></span></p>
                    <div>
                        <strong>Card Picture:</strong><br>
                        <img id="view-card-pic" src="" alt="Company Card Picture" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit Modal functionality
        const editCompanyModal = document.getElementById('editCompanyModal');
        editCompanyModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const companyId = button.getAttribute('data-id');
            const companyName = button.getAttribute('data-name');
            const businessRole = button.getAttribute('data-role');
            const address = button.getAttribute('data-address');
            const contact = button.getAttribute('data-contact');
            const addedBy = button.getAttribute('data-addedby');
            const cardPic = button.getAttribute('data-pic');

            editCompanyModal.querySelector('input[name="company_id"]').value = companyId;
            editCompanyModal.querySelector('input[name="company_name"]').value = companyName;
            editCompanyModal.querySelector('input[name="business_role"]').value = businessRole;
            editCompanyModal.querySelector('textarea[name="address"]').value = address;
            editCompanyModal.querySelector('input[name="contact_number"]').value = contact;
            editCompanyModal.querySelector('input[name="added_by"]').value = addedBy;
            editCompanyModal.querySelector('input[name="card_pic"]').value = cardPic;
        });

        // View Company Details Modal functionality
const viewCompanyModal = document.getElementById('viewCompanyModal');
const viewDetailsLinks = document.querySelectorAll('.view-details');

viewDetailsLinks.forEach(link => {
    link.addEventListener('click', function() {
        const companyName = this.getAttribute('data-name');
        const businessRole = this.getAttribute('data-role');
        const address = this.getAttribute('data-address');
        const contactNumber = this.getAttribute('data-contact');
        const addedBy = this.getAttribute('data-addedby');
        const pic = this.getAttribute('data-pic');

        document.getElementById('view-company-name').textContent = companyName;
        document.getElementById('view-business-role').textContent = businessRole;
        document.getElementById('view-address').textContent = address;
        document.getElementById('view-contact-number').textContent = contactNumber;
        document.getElementById('view-added-by').textContent = addedBy;

        const cardPicElement = document.getElementById('view-card-pic');
        cardPicElement.setAttribute('src', 'uploads/' + pic);
    });
});
    </script>
</body>

</html>