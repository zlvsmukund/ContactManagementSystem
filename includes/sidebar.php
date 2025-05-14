
<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-th-large"></i>
        <span>Navigation</span>
    </div>
    
    <nav class="sidebar-nav">
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="contacts.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>All Contacts</span>
        </a>
        
        <a href="add_contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add_contact.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-plus"></i>
            <span>Add Contact</span>
        </a>
        
        <?php
        // Get user groups for sidebar navigation
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT DISTINCT contact_group FROM contacts WHERE user_id = ? AND contact_group != '' ORDER BY contact_group LIMIT 5");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $groups_result = $stmt->get_result();
        
        if ($groups_result->num_rows > 0):
        ?>
            <div class="sidebar-header second-header">
                <i class="fas fa-tags"></i>
                <span>Groups</span>
            </div>
            
            <?php while($group = $groups_result->fetch_assoc()): ?>
                <a href="contacts.php?group=<?php echo urlencode($group['contact_group']); ?>">
                    <i class="fas fa-tag"></i>
                    <span><?php echo htmlspecialchars($group['contact_group']); ?></span>
                </a>
            <?php endwhile; ?>
        <?php endif; ?>
        
        <?php $conn->close(); ?>
    </nav>
</div>
