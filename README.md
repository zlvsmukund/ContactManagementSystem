
# Contact Management System

A simple PHP-based Contact Management System developed as a college DBMS project.

## Features

- User registration and login system
- Add, view, edit, and delete contacts
- Organize contacts into groups
- Search and filter functionality
- User-friendly dashboard with statistics

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- FontAwesome icons

## Installation

1. Clone or download the project to your local machine
2. Place the project folder in your XAMPP's htdocs directory
3. Start XAMPP and ensure Apache and MySQL services are running
4. Import the database by visiting phpMyAdmin:
   - Create a new database named `contact_management`
   - Import the `contact_management.sql` file
5. Configure the database connection in `config/config.php` if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root'); // Your MySQL username
   define('DB_PASS', ''); // Your MySQL password
   define('DB_NAME', 'contact_management');
   ```
6. Access the application in your browser: `http://localhost/contact-management`

## Default Login Credentials

- Email: john@example.com
- Password: password123

## Project Structure

```
contact-management/
├── assets/
│   └── css/
│       └── styles.css
├── config/
│   └── config.php
├── includes/
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   └── sidebar.php
├── add_contact.php
├── contacts.php
├── delete_contact.php
├── edit_contact.php
├── index.php
├── login.php
├── logout.php
├── register.php
├── view_contact.php
├── contact_management.sql
└── README.md
```

## Future Enhancements

- Password reset functionality
- Export contacts to CSV/PDF
- Contact image uploads
- Email integration
- Advanced searching and filtering
- User roles and permissions

## Credits

Developed by [Your Name] for Database Management Systems course project.
