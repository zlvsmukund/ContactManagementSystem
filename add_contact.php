
<?php
session_start();
require_once 'config/config.php';
require_once 'includes/functions.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $contact_group = trim($_POST['contact_group']);
    $notes = trim($_POST['notes']);
    
    if (empty($first_name)) {
        $error = "First name is required";
    } else {
        $conn = getDbConnection();
        
        $stmt = $conn->prepare("INSERT INTO contacts (user_id, first_name, last_name, email, phone, address, contact_group, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $_SESSION['user_id'], $first_name, $last_name, $email, $phone, $address, $contact_group, $notes);
        
        if ($stmt->execute()) {
            $success = "Contact added successfully!";
            // Reset form fields
            $first_name = $last_name = $email = $phone = $address = $contact_group = $notes = '';
        } else {
            $error = "Error adding contact: " . $conn->error;
        }
        
        $conn->close();
    }
}

// Get existing groups for dropdown
$conn = getDbConnection();
$stmt = $conn->prepare("SELECT DISTINCT contact_group FROM contacts WHERE user_id = ? AND contact_group != '' ORDER BY contact_group");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$groups_result = $stmt->get_result();
$groups = [];
while ($row = $groups_result->fetch_assoc()) {
    $groups[] = $row['contact_group'];
}
$conn->close();

$page_title = "Add Contact";
include 'includes/header.php';
?>

<div class="container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1>Add New Contact</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="contact-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="form-control" rows="2"><?php echo isset($address) ? htmlspecialchars($address) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="contact_group">Group</label>
                <div class="input-group">
                    <input type="text" id="contact_group" name="contact_group" class="form-control" list="groups" value="<?php echo isset($contact_group) ? htmlspecialchars($contact_group) : ''; ?>">
                    <datalist id="groups">
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo htmlspecialchars($group); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <small class="form-text text-muted">Enter an existing group or create a new one</small>
            </div>
            
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" class="form-control" rows="3"><?php echo isset($notes) ? htmlspecialchars($notes) : ''; ?></textarea>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Save Contact</button>
                <a href="contacts.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
