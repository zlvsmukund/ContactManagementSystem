
<?php
session_start();
require_once 'config/config.php';
require_once 'includes/functions.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    header("Location: login.php");
    exit();
}

$page_title = "Dashboard";
include 'includes/header.php';
?>

<div class="container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="dashboard">
            <h1>Contact Management Dashboard</h1>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                    // Get stats
                    $conn = getDbConnection();
                    
                    // Total contacts
                    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM contacts WHERE user_id = ?");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $total_contacts = $result->fetch_assoc()['total'];
                    
                    // Groups count
                    $stmt = $conn->prepare("SELECT COUNT(DISTINCT contact_group) AS total_groups FROM contacts WHERE user_id = ? AND contact_group != ''");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $total_groups = $result->fetch_assoc()['total_groups'];
                    
                    // Recent contacts
                    $stmt = $conn->prepare("SELECT * FROM contacts WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $recent_contacts = $stmt->get_result();
                    
                    $conn->close();
                ?>
                
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Contacts</h3>
                            <p class="stat-number"><?php echo $total_contacts; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Groups</h3>
                            <p class="stat-number"><?php echo $total_groups; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Quick Add</h3>
                            <a href="add_contact.php" class="btn btn-primary">New Contact</a>
                        </div>
                    </div>
                </div>
                
                <div class="recent-contacts">
                    <h2>Recent Contacts</h2>
                    <?php if ($recent_contacts->num_rows > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Group</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($contact = $recent_contacts->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($contact['contact_group']); ?></td>
                                        <td>
                                            <a href="view_contact.php?id=<?php echo $contact['id']; ?>" class="btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                            <a href="edit_contact.php?id=<?php echo $contact['id']; ?>" class="btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                            <a href="delete_contact.php?id=<?php echo $contact['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this contact?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">No contacts added yet. <a href="add_contact.php">Add your first contact</a>.</div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Please <a href="login.php">login</a> to view your contacts.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
