
<?php
session_start();
require_once 'config/config.php';
require_once 'includes/functions.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_group = isset($_GET['group']) ? trim($_GET['group']) : '';

$conn = getDbConnection();

// Get all contact groups for filter dropdown
$stmt = $conn->prepare("SELECT DISTINCT contact_group FROM contacts WHERE user_id = ? AND contact_group != '' ORDER BY contact_group");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$groups_result = $stmt->get_result();
$groups = [];
while ($row = $groups_result->fetch_assoc()) {
    $groups[] = $row['contact_group'];
}

// Prepare query based on search and filter
$query = "SELECT * FROM contacts WHERE user_id = ?";
$params = array($_SESSION['user_id']);
$types = "i";

if (!empty($search)) {
    $search_term = "%$search%";
    $query .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ssss";
}

if (!empty($filter_group)) {
    $query .= " AND contact_group = ?";
    $params[] = $filter_group;
    $types .= "s";
}

$query .= " ORDER BY first_name ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$contacts = $stmt->get_result();

$conn->close();

$page_title = "Contacts";
include 'includes/header.php';
?>

<div class="container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="contact-list-header">
            <h1>Contacts</h1>
            <a href="add_contact.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Contact</a>
        </div>
        
        <div class="filter-controls">
            <form method="GET" action="contacts.php" class="search-form">
                <div class="input-group">
                    <input type="text" name="search" placeholder="Search contacts..." value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                    <select name="group" class="form-control">
                        <option value="">All Groups</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo htmlspecialchars($group); ?>" <?php echo $filter_group === $group ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($group); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    <?php if (!empty($search) || !empty($filter_group)): ?>
                        <a href="contacts.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="contacts-container">
            <?php if ($contacts->num_rows > 0): ?>
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
                        <?php while($contact = $contacts->fetch_assoc()): ?>
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
                <div class="alert alert-info">
                    <?php if (!empty($search) || !empty($filter_group)): ?>
                        No contacts found matching your search criteria. <a href="contacts.php">Clear filters</a>
                    <?php else: ?>
                        No contacts found. <a href="add_contact.php">Add your first contact</a>.
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
