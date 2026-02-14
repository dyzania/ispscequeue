# E-Queue System Installation Guide

## Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependencies)

## Installation Steps

### 1. Database Setup
```bash
# Create database and import schema
mysql -u root -p < database/schema.sql
```

### 2. Configuration
Edit `config/config.php` and update:
- Database credentials (DB_HOST, DB_USER, DB_PASS, DB_NAME)
- SMTP settings for email notifications
- Base URL

### 3. File Permissions
```bash
chmod 755 -R public/
chmod 755 -R includes/
chmod 755 -R models/
```

### 4. Web Server Configuration

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteBase /equeue-system/public/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L,QSA]
```

#### Nginx
```nginx
location /equeue-system {
    try_files $uri $uri/ /equeue-system/public/index.php?$args;
}
```

### 5. Default Login Credentials
- **Admin:** admin@equeue.com / password

## Post-Installation

1. Login as admin
2. Create staff accounts in Admin > Windows
3. Assign staff to windows
4. Enable services for each window
5. Test the system with a user account

## Features Overview

### User Portal
- View live queue display
- Get tickets for services
- Track ticket status and queue position
- Mandatory feedback system with sentiment analysis

### Staff Panel
- Toggle services on/off
- Call next ticket in queue
- Mark transactions as complete
- Email and push notifications

### Admin Dashboard
- Live queue monitoring
- Staff and window management
- Analytics and reports
- Sentiment analysis review
- Chatbot data management

## Troubleshooting

### Database Connection Issues
- Verify MySQL service is running
- Check database credentials in config.php
- Ensure database exists

### Email Notifications Not Working
- Update SMTP settings in config.php
- Use app-specific password for Gmail
- Check firewall settings

### Session Issues
- Ensure session directory is writable
- Check PHP session configuration

## Security Recommendations

1. Change default admin password immediately
2. Use HTTPS in production
3. Set proper file permissions (644 for files, 755 for directories)
4. Enable error logging instead of display_errors in production
5. Use environment variables for sensitive configuration

## Production Hosting (ispsc-queue-system.com)

If you are deploying to a live domain like `ispsc-queue-system.com`:

1. **Base URL**: Change `BASE_URL` in `config/config.php` from `http://localhost/...` to `https://ispsc-queue-system.com/public`.
2. **SSL**: Ensure you have an SSL certificate active. The system is designed for HTTPS.
3. **Paths**: If you host the project in a subfolder, adjust the `BASE_URL` and `.htaccess` `RewriteBase` accordingly.
4. **Environment**: Ensure `display_errors` is turned off (`0`) in `config/config.php` to prevent exposing technical details to users.

## Sentiment Analysis Server Deployment

1. **Python Environment**: On your production server, create a virtual environment and install the required packages:
   ```bash
   python -m venv venv
   source venv/bin/activate  # or venv\Scripts\activate on Windows
   pip install fastapi uvicorn transformers torch
   ```
2. **Execution**: Run the server using `uvicorn`:
   ```bash
   uvicorn app:app --host 0.0.0.0 --port 8000
   ```
3. **Connectivity**: If the Python server and PHP server are on different machines, update the API URL in `models/Feedback.php`.

## Support

For issues and questions, refer to the README.md file.
