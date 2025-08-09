# Prize Website

A complete prize website featuring Wheel of Fortune and Mystery Box games with admin panel for prize management. Built with HTML, Tailwind CSS, PHP, MySQL, and jQuery.

## Features

### 🎯 Prize Methods
- **Wheel of Fortune**: Interactive spinning wheel with animated segments
- **Mystery Box**: Animated box opening with surprise reveals

### 📦 Prize System
- Shared prize pool between both methods
- Configurable prize percentages (manual or auto-calculated)
- Quantity tracking and availability management
- Weighted random selection based on percentages

### 🛠️ Admin Panel
- Secure admin authentication
- Inline editable prize management table
- Real-time percentage validation
- Prize statistics and tracking
- Toggle prize availability for each method

### 👥 User System
- User registration and authentication
- Prize history tracking
- Dashboard with statistics
- Responsive design for all devices

## Technology Stack

- **Frontend**: HTML5, Tailwind CSS, jQuery
- **Backend**: PHP (no framework)
- **Database**: MySQL
- **Animations**: Custom CSS animations and transitions

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Instructions

1. **Clone/Download the project**
   ```bash
   # Place all files in your web server directory
   # e.g., /var/www/html/prize_website/
   ```

2. **Configure Database**
   - Edit `includes/config.php` with your database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'prize_website');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

3. **Initialize Database**
   - Visit `http://yoursite.com/setup.php` in your browser
   - Follow the setup instructions
   - **Delete setup.php after completion**

4. **Set Permissions**
   ```bash
   # Ensure web server can read/write files
   chmod -R 755 /path/to/prize_website/
   chown -R www-data:www-data /path/to/prize_website/
   ```

## Default Credentials

### Admin Access
- **URL**: `/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

**⚠️ Change the default admin password immediately after setup!**

## File Structure

```
prize_website/
├── admin/                  # Admin panel
│   ├── api.php            # Admin API endpoints
│   ├── dashboard.php      # Prize management interface
│   ├── login.php          # Admin login
│   └── logout.php         # Admin logout
├── api/                   # User API endpoints
│   ├── prizes.php         # Get available prizes
│   └── spin.php           # Prize selection logic
├── assets/                # Static assets
│   ├── css/
│   │   └── custom.css     # Custom animations and styles
│   ├── js/
│   │   └── app.js         # Main JavaScript application
│   └── images/            # Image assets
├── auth/                  # User authentication
│   ├── login.php          # User login
│   ├── logout.php         # User logout
│   └── register.php       # User registration
├── database/              # Database files
│   └── schema.sql         # Database schema and sample data
├── includes/              # Core PHP files
│   ├── config.php         # Configuration settings
│   ├── database.php       # Database connection class
│   └── functions.php      # Utility functions
├── user/                  # User interface
│   ├── box.php            # Mystery Box game
│   ├── dashboard.php      # User dashboard
│   ├── history.php        # Prize history
│   └── wheel.php          # Wheel of Fortune game
├── index.php              # Homepage
├── setup.php              # Database setup script
└── README.md              # This file
```

## Usage Guide

### For Users

1. **Registration**
   - Visit the homepage and click "Sign Up Now"
   - Fill in your details and create an account
   - Login with your credentials

2. **Playing Games**
   - Access the dashboard after login
   - Choose between Wheel of Fortune or Mystery Box
   - Click to spin/open and win prizes!

3. **Viewing History**
   - Check your prize history from the dashboard
   - See all your wins with timestamps and values

### For Administrators

1. **Login to Admin Panel**
   - Visit `/admin/login.php`
   - Use admin credentials to access the panel

2. **Managing Prizes**
   - View all prizes in the management table
   - Edit fields directly by clicking on them
   - Add new prizes using the "Add Prize" button
   - Toggle prize availability for wheel/box

3. **Setting Percentages**
   - Set manual percentages for specific prizes
   - Remaining percentage is auto-distributed
   - System validates that total doesn't exceed 100%

4. **Monitoring Statistics**
   - View total prizes, wins, and percentage usage
   - Track prize popularity and availability

## Prize Selection Logic

### Percentage Calculation
1. **Manual Percentages**: Admin-defined fixed percentages
2. **Auto-Calculated**: Remaining percentage divided equally among non-manual prizes
3. **Validation**: System ensures total never exceeds 100%

### Random Selection
1. Weighted random selection based on final percentages
2. Prizes with higher percentages have better odds
3. Quantity checking ensures unavailable prizes are excluded

### Prize Awarding
1. Decrease prize quantity by 1
2. Increase times_won counter
3. Record win in user_prizes table
4. Transaction-based for data integrity

## Customization

### Styling
- Modify `assets/css/custom.css` for visual changes
- Tailwind CSS classes can be adjusted in PHP files
- Color schemes and animations are easily customizable

### Functionality
- Add new prize types in the database schema
- Extend API endpoints for additional features
- Modify percentage calculation logic in `includes/functions.php`

### Security
- Change default admin credentials
- Update password requirements in `includes/config.php`
- Implement additional validation as needed

## API Endpoints

### User APIs
- `GET /api/prizes.php?method=wheel|box` - Get available prizes
- `POST /api/spin.php` - Select and award random prize

### Admin APIs
- `POST /admin/api.php` - Prize management operations
  - `action=add` - Add new prize
  - `action=update` - Update prize field
  - `action=delete` - Delete prize

## Database Schema

### Tables
- **users**: User accounts and authentication
- **admin_users**: Admin accounts
- **prizes**: Prize definitions and settings
- **user_prizes**: Prize win history

### Key Fields
- `prizes.percentage`: Manual percentage (nullable)
- `prizes.is_manual_percentage`: Boolean flag for percentage type
- `prizes.enabled_in_wheel/box`: Availability toggles
- `prizes.quantity`: Available quantity
- `prizes.times_won`: Win counter

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `includes/config.php`
   - Ensure MySQL service is running
   - Verify database exists and user has permissions

2. **Percentage Validation Errors**
   - Check that manual percentages don't exceed 100%
   - Ensure at least one prize is available for each method

3. **Animation Issues**
   - Verify `assets/css/custom.css` is loading
   - Check browser console for JavaScript errors
   - Ensure jQuery is loading properly

4. **Permission Denied**
   - Check file permissions (755 for directories, 644 for files)
   - Ensure web server user owns the files

### Debug Mode
Enable debug mode by setting in `includes/config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Security Considerations

- Change default admin password
- Use HTTPS in production
- Implement rate limiting for API endpoints
- Validate and sanitize all user inputs
- Regular database backups
- Keep PHP and MySQL updated

## License

This project is provided as-is for educational and commercial use. Feel free to modify and distribute according to your needs.

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review the code comments
3. Test with sample data first
4. Verify all prerequisites are met

---

**Happy Gaming! 🎮🎁**

