# E-Queue System Installation & Deployment Guide

## Requirements

- **Web Server**: Apache (with `mod_rewrite` enabled) or Nginx
- **PHP**: 7.4 or higher (8.1+ recommended)
- **Database**: MySQL 5.7+ or MariaDB
- **Python**: 3.8+ (for Sentiment Analysis server)
- **SSL Certificate**: HTTPS is **REQUIRED** for browser notifications and PWA features
- **Composer**: (Optional, for PHP dependencies)

## Installation Steps

### 1. Database Setup

```bash
# Create database and import schema
mysql -u root -p < database/schema.sql
```

_Migration Note_: If moving to production, export your local database using phpMyAdmin or `mysqldump` and import it to your live server.

### 2. Configuration

The system uses a combination of `config/config.php` and environment variables.

1. **Environment File**: Copy `.env.example` (if available) or create a `.env` file in the project root:

   ```env
   # Database
   DB_HOST=localhost
   DB_NAME=equeue_system
   DB_USER=root
   DB_PASS=

   # Email (SMTP)
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password

   # AI & API
   OPENROUTER_API_KEY=your_key
   AI_MODEL=stepfun/step-3.5-flash:free
   AI_URL=http://localhost:8000/analyze
   ```

2. **Main Config**: Edit `config/config.php`:
   - Ensure `BASE_URL` matches your domain (e.g., `https://yourdomain.com/public`)
   - Verify `loadEnv` is called to load your `.env` file

### 3. File Permissions

Ensure the web server can write to these directories:

```bash
chmod 755 -R public/
chmod 755 -R includes/
chmod 755 -R models/
chmod 777 -R logs/
chmod 755 config/
```

### 4. Web Server Configuration

#### Apache (.htaccess)

The system includes an `.htaccess` file in `public/`. Ensure `AllowOverride All` is enabled in your Apache config.

```apache
RewriteEngine On
RewriteBase /public/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L,QSA]
```

#### Nginx

```nginx
location / {
    try_files $uri $uri/ /public/index.php?$args;
}
```

### 5. Default Login Credentials

- **Admin:** `admin@equeue.com` / `password`

## Sentiment Analysis Server (Python)

The sentiment analysis feature runs as a separate microservice.

### 1. Setup Virtual Environment

Navigate to the project root (or `sentiment_analysis` folder depending on your preference, but root is recommended for cleaner structure):

**Windows:**

```powershell
python -m venv .venv
.\.venv\Scripts\Activate.ps1
```

**Linux/Mac:**

```bash
python3 -m venv .venv
source .venv/bin/activate
```

### 2. Install Dependencies

```bash
pip install -r sentiment_analysis/requirements.txt
```

### 3. Run the Server

```bash
cd sentiment_analysis
uvicorn app:app --host 0.0.0.0 --port 8000
```

_Note_: In production, use a process manager like **PM2** or **Systemd** to keep this running.

## Production Checklist

1.  **Status Check**: Visit your site via HTTPS.
2.  **Database**: Log in as admin to verify connectivity.
3.  **Notifications**: Test ticket creation to ensure email/toast notifications work.
4.  **Logs**: Check PHP error logs (`logs/` directory or server logs) for any issues.
5.  **Security**:
    - Change default admin password immediately.
    - Set `display_errors = 0` in php.ini or config.
    - protect `.env` file from public access.

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
- Check database credentials in `.env` and `config/config.php`
- Ensure database exists

### Email Notifications Not Working

- Update SMTP settings in `.env`
- Use app-specific password for Gmail
- Check firewall settings

### Python Server Issues

- Ensure `.venv` is activated before running uvicorn
- Check port 8000 is open/available
- Verify `AI_URL` in PHP config points to the correct Python server address

## Support

For issues, refer to the README.md or open an issue on the repository.
