
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

// Check if contact belongs to the logged-in user and delete
$stmt = $conn->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $contact_id, $_SESSION['user_id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success_message'] = "Contact deleted successfully";
} else {
    $_SESSION['error_message'] = "Error deleting contact or contact not found";
}

$conn->close();

// Redirect back to contacts list
header("Location: contacts.php");
exit();
?>
