# ISPSC E-Queue System - Quick Start Guide

## 🚀 5-Minute Setup

### Step 1: Database Setup
```bash
# Import the schema (Run from project root)
mysql -u root -p < database/queue_schema.sql
```

### Step 2: Configuration
Edit `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'equeue_system');
```

### Step 3: Test the Flow
1. Open browser: `http://localhost/ispscequeue/public`
2. Login with: `admin@ispsc.edu.ph` / `password`
3. Test as Admin:
   - Manage services and windows.
   - Monitor the live queue.
4. Test as User (Register a new account):
   - Get a ticket.
   - View my ticket status.
   - Complete feedback after service.

## 🔑 Default Credentials

- **Admin Account**: `admin@ispsc.edu.ph` / `password`
- **Default Roles**: Admin, Staff, User.

## 📚 Documentation Links

- **Full Installation**: [INSTALLATION.md](file:///c:/xampp/htdocs/ispscequeue/INSTALLATION.md) (Detailed setup, Python microservice, and Troubleshooting)
- **Project Structure**: [PROJECT_STRUCTURE.md](file:///c:/xampp/htdocs/ispscequeue/PROJECT_STRUCTURE.md) (Architecture, Security, and File Map)
- **Thesis Guide**: [THESIS_DEFENSE_GUIDE.txt](file:///c:/xampp/htdocs/ispscequeue/THESIS_DEFENSE_GUIDE.txt) (Defense-ready technical overview)

---

**You're ready to go! Start with the database import and explore the portals.**
