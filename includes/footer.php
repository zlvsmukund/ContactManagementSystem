
    <footer>
        <div class="footer-container">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> Contact Management System - College DBMS Project
            </div>
        </div>
    </footer>
    
    <script>
        // Auto close flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const flashMessages = document.querySelectorAll('.alert');
                flashMessages.forEach(function(message) {
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        });
    </script>
</body>
</html>
