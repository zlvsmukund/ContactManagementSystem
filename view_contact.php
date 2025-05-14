
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
$conn->close();

$page_title = "View Contact";
include 'includes/header.php';
?>

<div class="container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="contact-header">
            <h1><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></h1>
            <div class="contact-actions">
                <a href="edit_contact.php?id=<?php echo $contact_id; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                <a href="delete_contact.php?id=<?php echo $contact_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this contact?');"><i class="fas fa-trash"></i> Delete</a>
                <a href="contacts.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
            </div>
        </div>
        
        <div class="contact-details">
            <div class="detail-card">
                <div class="detail-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="detail-info">
                    <table class="detail-table">
                        <?php if (!empty($contact['email'])): ?>
                            <tr>
                                <th><i class="fas fa-envelope"></i> Email:</th>
                                <td><a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($contact['phone'])): ?>
                            <tr>
                                <th><i class="fas fa-phone"></i> Phone:</th>
                                <td><a href="tel:<?php echo htmlspecialchars($contact['phone']); ?>"><?php echo htmlspecialchars($contact['phone']); ?></a></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($contact['address'])): ?>
                            <tr>
                                <th><i class="fas fa-map-marker-alt"></i> Address:</th>
                                <td><?php echo nl2br(htmlspecialchars($contact['address'])); ?></td>
                            </tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($contact['contact_group'])): ?>
                            <tr>
                                <th><i class="fas fa-tag"></i> Group:</th>
                                <td><span class="group-badge"><?php echo htmlspecialchars($contact['contact_group']); ?></span></td>
                            </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i> Added:</th>
                            <td><?php echo date('F j, Y', strtotime($contact['created_at'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php if (!empty($contact['notes'])): ?>
                <div class="notes-section">
                    <h3><i class="fas fa-sticky-note"></i> Notes</h3>
                    <div class="notes-content">
                        <?php echo nl2br(htmlspecialchars($contact['notes'])); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
