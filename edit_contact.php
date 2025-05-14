
<?php
session_start();
require_once 'config/config.php';
require_once 'includes/functions.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: contacts.php");
    exit();
}

$contact_id = (int)$_GET['id'];
$error = '';
$success = '';

$conn = getDbConnection();

// Check if contact belongs to the logged-in user
$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $contact_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $conn->close();
    header("Location: contacts.php");
    exit();
}

$contact = $result->fetch_assoc();

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
        $stmt = $conn->prepare("UPDATE contacts SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, contact_group = ?, notes = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssssssii", $first_name, $last_name, $email, $phone, $address, $contact_group, $notes, $contact_id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $success = "Contact updated successfully!";
            
            // Refresh contact data
            $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $contact_id, $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $contact = $result->fetch_assoc();
        } else {
            $error = "Error updating contact: " . $conn->error;
        }
    }
}

// Get existing groups for dropdown
$stmt = $conn->prepare("SELECT DISTINCT contact_group FROM contacts WHERE user_id = ? AND contact_group != '' ORDER BY contact_group");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$groups_result = $stmt->get_result();
$groups = [];
while ($row = $groups_result->fetch_assoc()) {
    $groups[] = $row['contact_group'];
}

$conn->close();

$page_title = "Edit Contact";
include 'includes/header.php';
?>

<div class="container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1>Edit Contact</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $contact_id; ?>" class="contact-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($contact['first_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($contact['last_name']); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($contact['email']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($contact['phone']); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="form-control" rows="2"><?php echo htmlspecialchars($contact['address']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="contact_group">Group</label>
                <div class="input-group">
                    <input type="text" id="contact_group" name="contact_group" class="form-control" list="groups" value="<?php echo htmlspecialchars($contact['contact_group']); ?>">
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
                <textarea id="notes" name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($contact['notes']); ?></textarea>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Update Contact</button>
                <a href="view_contact.php?id=<?php echo $contact_id; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
