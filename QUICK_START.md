# E-Queue System - Quick Start Guide

## 5-Minute Setup

### Step 1: Database Setup (2 minutes)
```bash
# Login to MySQL
mysql -u root -p

# Run the schema
mysql -u root -p < database/schema.sql
```

### Step 2: Configure (1 minute)
Edit `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'equeue_system');
```

### Step 3: Test (2 minutes)
1. Open browser: `http://localhost/equeue-system/public`
2. Login with: admin@equeue.com / password
3. Create a test user account
4. Test the flow:
   - Login as user
   - Get a ticket
   - View queue display
   - Submit feedback

## What You Get

### ✅ Fully Working Features
- **User Portal**: Queue display, ticket generation, status tracking, feedback
- **Authentication**: Registration, login, role-based access
- **Queue System**: Real-time updates, position tracking, ticket highlighting
- **Feedback System**: Mandatory feedback with sentiment analysis
- **Database**: Complete schema with 8 services pre-loaded

### 🚧 Ready to Implement
- **Staff Portal**: Dashboard template ready, needs completion
- **Admin Panel**: Structure ready, needs CRUD interfaces
- **Notifications**: Email framework ready, needs SMTP config

## File Checklist

After extraction, you should have:

```
✓ config/config.php
✓ database/schema.sql
✓ models/ (5 PHP files)
✓ public/ (6 core pages)
✓ public/user/ (3 pages)
✓ public/api/ (2 endpoints)
✓ includes/ (1 navbar)
✓ README.md
✓ INSTALLATION.md
✓ PROJECT_STRUCTURE.md
✓ QUICK_START.md (this file)
```

## Test the System

### As Admin:
1. Login: admin@equeue.com / password
2. You should see admin dashboard (when implemented)
3. For now, you'll see user dashboard

### As User:
1. Register new account
2. View Queue Display (should show no active windows initially)
3. Get Ticket (choose any service)
4. View My Ticket (see your ticket and position)
5. Feedback will be required after completion

## Common Issues

**Can't connect to database?**
- Check MySQL is running: `sudo service mysql status`
- Verify credentials in config.php
- Ensure database exists: `SHOW DATABASES;`

**Pages showing 404?**
- Check web server is pointing to `/public` directory
- Verify .htaccess is working (Apache)
- Try: `http://localhost/equeue-system/public/index.php`

**No services showing?**
- Database imported correctly?
- Check: `SELECT * FROM services;`
- Should show 8 pre-loaded services

## What to Build Next

### Priority 1: Staff Portal
- Copy user dashboard structure
- Add service toggle switches
- Implement "Call Next" button
- Add ticket completion interface

### Priority 2: Admin Panel
- Create window management CRUD
- Staff assignment interface
- Basic analytics page
- Sentiment analysis visualization

### Priority 3: Enhancements
- Email notifications (SMTP config needed)
- Charts for analytics
- PDF reports export
- QR code tickets

## System Architecture

```
User Browser → PHP Application → MySQL Database
     ↓              ↓                  ↓
  Tailwind     PDO/Models         InnoDB Tables
  JavaScript   MVC Pattern       Indexed Queries
  AJAX Polls   Prepared Stmts    Foreign Keys
```

## Data Flow

1. **User gets ticket**: POST → Ticket Model → Check feedback → Generate ticket → Insert DB → Return ticket number
2. **View queue**: AJAX → API → Ticket Model → Fetch waiting tickets → Return JSON → Update UI
3. **Submit feedback**: POST → Feedback Model → Sentiment analysis → Insert DB → Unlock new tickets

## Security Checklist

- [x] Password hashing (bcrypt)
- [x] SQL injection prevention (PDO)
- [x] XSS protection (sanitization)
- [x] Session management
- [x] Role-based access control
- [ ] CSRF tokens (implement in forms)
- [ ] Rate limiting (add to login)
- [ ] HTTPS redirect (configure server)

## Performance Tips

- Database has indexes on all foreign keys
- Use connection pooling in production
- Cache active windows list
- Minimize API polling interval
- Consider Redis for queue caching

## Need Help?

1. Check INSTALLATION.md for detailed setup
2. Read PROJECT_STRUCTURE.md for code organization
3. Review inline code comments
4. All models have clear method names and docblocks

## Success Criteria

Your system is working when:
- ✅ Users can register and login
- ✅ Users can get tickets
- ✅ Queue displays in real-time
- ✅ Feedback system blocks duplicate tickets
- ✅ Sentiment analysis categorizes feedback
- ✅ Multiple users can be in queue simultaneously

---

**You're ready to go! Start with the database import and test the user portal.**

Happy coding! 🚀
