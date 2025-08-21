# Feedback System

A complete web-based feedback system built with HTML, CSS, JavaScript, PHP, and MySQL. This system provides a modern, responsive interface for collecting user feedback with an admin panel for management.

## Features

### Frontend
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- **Interactive Star Rating**: 1-5 star rating system with hover effects
- **Real-time Validation**: Client-side validation with immediate feedback
- **AJAX Submission**: Form submission without page reload using Fetch API
- **Success/Error Messages**: Clear feedback for form submission status
- **Modern UI**: Beautiful gradient design with smooth animations

### Backend
- **Secure PHP Scripts**: Input validation and sanitization
- **MySQL Database**: Efficient data storage with proper indexing
- **JSON API**: RESTful API responses for frontend communication
- **Error Handling**: Comprehensive error handling and logging

### Admin Panel
- **Password Protection**: Simple authentication system
- **Feedback Management**: View, sort, and delete feedback entries
- **Statistics Dashboard**: Overview of feedback data
- **Sorting Options**: Sort by date, rating, name, or email
- **Responsive Table**: Mobile-friendly data display

## File Structure

```
feedback_system/
├── index.html          # Main feedback form
├── style.css           # CSS styles
├── script.js           # JavaScript functionality
├── config.php          # Database configuration
├── submit_feedback.php # Form submission handler
├── admin.php           # Admin panel
├── schema.sql          # Database schema
└── README.md           # This file
```

## Installation

### Prerequisites
- XAMPP, WAMP, or similar local server environment
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser with JavaScript enabled

### Setup Instructions

1. **Download/Clone the Project**
   ```bash
   # If using git
   git clone <repository-url>
   cd feedback_system
   ```

2. **Set Up Local Server**
   - Copy all files to your web server directory (e.g., `htdocs` for XAMPP)
   - Start Apache and MySQL services

3. **Configure Database**
   - Open `config.php` and update database credentials if needed
   - Default settings work with XAMPP/WAMP:
     - Host: `localhost`
     - Username: `root`
     - Password: `` (empty)
     - Database: `feedback_system`

4. **Create Database**
   - Option 1: Run `schema.sql` in phpMyAdmin
   - Option 2: The system will auto-create the database when first accessed

5. **Access the System**
   - Open `http://localhost/feedback_system/` in your browser
   - Admin panel: `http://localhost/feedback_system/admin.php`

## Usage

### For Users
1. Fill out the feedback form with your name, email, rating, and comments
2. The form validates input in real-time
3. Submit the form to send your feedback
4. Receive confirmation of successful submission

### For Administrators
1. Access the admin panel at `/admin.php`
2. Login with credentials:
   - Username: `admin`
   - Password: `admin123`
3. View feedback statistics and manage submissions
4. Sort feedback by various criteria
5. Delete unwanted feedback entries

## Database Schema

### Feedback Table
```sql
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comments TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Security Features

- **Input Sanitization**: All user inputs are sanitized and validated
- **SQL Injection Prevention**: Uses prepared statements
- **XSS Protection**: Output is properly escaped
- **CSRF Protection**: Form validation on server side
- **Error Handling**: Secure error messages in production

## Customization

### Changing Admin Credentials
Edit `admin.php` and update these lines:
```php
$admin_username = 'your_username';
$admin_password = 'your_secure_password';
```

### Modifying Database Settings
Edit `config.php` and update the database constants:
```php
define('DB_HOST', 'your_host');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Styling Changes
- Modify `style.css` to change the appearance
- Update color schemes, fonts, and layouts
- Responsive breakpoints can be adjusted

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check if MySQL service is running
   - Verify database credentials in `config.php`
   - Ensure database exists

2. **Form Not Submitting**
   - Check browser console for JavaScript errors
   - Verify PHP is enabled in your server
   - Check file permissions

3. **Admin Panel Not Working**
   - Clear browser cache and cookies
   - Check session configuration
   - Verify admin credentials

4. **Styling Issues**
   - Clear browser cache
   - Check CSS file path
   - Verify file permissions

### Debug Mode
Set `DEBUG_MODE` to `true` in `config.php` for detailed error messages:
```php
define('DEBUG_MODE', true);
```

## Performance Optimization

- Database indexes are created for better query performance
- CSS and JavaScript are optimized for fast loading
- Images and assets are minimized
- Caching headers can be added for production

## Future Enhancements

- Email notifications for new feedback
- Export functionality (CSV, PDF)
- Advanced filtering and search
- User authentication system
- API endpoints for external integration
- Dashboard analytics and charts

## License

This project is open source and available under the MIT License.

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review browser console for errors
3. Verify server configuration
4. Test with different browsers

## Contributing

Feel free to submit issues, feature requests, or pull requests to improve the system.

---

**Note**: This system is designed for local development and learning purposes. For production use, implement proper security measures, use HTTPS, and follow security best practices.
