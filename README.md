# E-Queue System

A comprehensive queue management system built with PHP, MySQL, and Tailwind CSS featuring real-time queue monitoring, sentiment analysis, and multi-role access control.

## 🌟 Features

### 👥 User Portal
- **Live Queue Display** - Real-time view of active service windows and waiting queue
- **Smart Ticket Generation** - Get tickets for specific services with requirement information
- **My Ticket Tracking** - Monitor your position in queue with live updates
- **Mandatory Feedback System** - Sentiment analysis-powered feedback collection
- **Queue Position Highlights** - Your ticket is highlighted in the queue display

### 🖥️ Staff/Window Portal
- **Service Management** - Toggle services on/off dynamically
- **Smart Queue Filtering** - Only shows tickets for enabled services
- **Call Next Ticket** - Automated queue management
- **Multi-Channel Notifications** - Email and web push notifications for customers
- **Transaction Status Updates** - Mark tickets as called, serving, or completed

### 🎯 Admin Dashboard
- **Live Queue Monitoring** - Real-time overview of all active queues
- **Window & Staff Management** - Create and assign staff to service windows
- **Comprehensive Analytics** - Queue statistics, service performance, wait times
- **Sentiment Analysis Review** - AI-powered feedback analysis dashboard
- **Chatbot Data Management** - Train and manage chatbot responses
- **User Management** - Full CRUD operations for users and staff

## 📋 System Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- Modern web browser with JavaScript enabled

## 🚀 Quick Start

1. **Clone or download** the repository
2. **Import database**: `mysql -u root -p < database/schema.sql`
3. **Configure**: Edit `config/config.php` with your database credentials
4. **Access**: Navigate to `http://localhost/equeue-system/public`
5. **Login** with default admin credentials:
   - Email: admin@equeue.com
   - Password: password

## 📚 Documentation

### User Workflow
1. Register/Login to user account
2. View queue display to see active windows
3. Get ticket by selecting desired service
4. Monitor queue position in "My Ticket"
5. Receive notification when called
6. Complete transaction
7. Provide mandatory feedback

### Staff Workflow
1. Login to staff account
2. Toggle services on/off as needed
3. View eligible waiting queue
4. Call next ticket
5. Customer receives email/push notification
6. Mark transaction as complete
7. System notifies customer

### Admin Workflow
1. Login to admin account
2. Create staff accounts and assign to windows
3. Monitor live queue across all windows
4. Review analytics and performance metrics
5. Analyze customer feedback sentiment
6. Manage chatbot training data
7. Generate reports

## 🔧 Technical Architecture

### Database Structure
- **users** - User accounts (user/staff/admin roles)
- **windows** - Service windows/counters
- **services** - Available services
- **window_services** - Many-to-many window-service relationships
- **tickets** - Queue tickets with full lifecycle tracking
- **feedback** - Customer feedback with sentiment scores
- **notifications** - System notifications
- **chatbot_data** - Chatbot training data
- **activity_logs** - System activity logging

### Key Features Implementation

#### Sentiment Analysis
- Keyword-based analysis with positive/negative word detection
- Rating integration for accuracy
- Sentiment scoring (-1 to 1 scale)
- Categories: very_positive, positive, neutral, negative, very_negative

#### Mandatory Feedback System
- Blocks new ticket creation if previous feedback pending
- Star rating (1-5) with optional text comment
- Real-time sentiment analysis
- Analytics dashboard integration

#### Real-time Queue Updates
- JavaScript polling (5-second intervals)
- AJAX-based queue refresh
- User ticket highlighting
- Queue position calculation

#### Notification System
- Email notifications via SMTP
- Web push notification support
- Triggered on: ticket called, serving, completed
- Template-based messaging

## 🎨 UI/UX Highlights

- **Responsive Design** - Mobile-first Tailwind CSS implementation
- **Icon Integration** - Font Awesome 6 icons throughout
- **Color Coding** - Status-based visual feedback
- **Real-time Updates** - Live queue and position updates
- **User-Friendly** - Intuitive navigation and clear CTAs

## 🔐 Security Features

- Password hashing (bcrypt)
- SQL injection prevention (PDO prepared statements)
- XSS protection (input sanitization)
- Session management
- Role-based access control
- CSRF protection ready

## 📊 Analytics Capabilities

- Queue performance metrics
- Service utilization statistics
- Average wait times
- Sentiment analysis trends
- Staff performance tracking
- Peak hours identification
- Customer satisfaction scores

## 🤖 Chatbot Integration

The system includes a chatbot data management interface where admins can:
- Create FAQ entries
- Categorize responses
- Add keywords for better matching
- Track usage statistics
- Export data for external chatbot systems

## 🔄 Future Enhancements

- SMS notifications integration
- Advanced analytics with charts
- Mobile app (iOS/Android)
- QR code ticket generation
- Multi-language support
- Advanced reporting engine
- Integration with third-party calendar systems
- Video calling for remote service

## 📝 File Structure

```
equeue-system/
├── config/
│   └── config.php              # Configuration
├── database/
│   └── schema.sql              # Database schema
├── models/
│   ├── User.php                # User model
│   ├── Ticket.php              # Ticket model
│   ├── Service.php             # Service model
│   ├── Window.php              # Window model
│   └── Feedback.php            # Feedback model
├── public/
│   ├── index.php               # Login page
│   ├── register.php            # Registration
│   ├── logout.php              # Logout handler
│   ├── user/                   # User portal
│   ├── staff/                  # Staff portal
│   ├── admin/                  # Admin portal
│   └── api/                    # API endpoints
├── includes/
│   ├── user-navbar.php         # User navigation
│   ├── staff-navbar.php        # Staff navigation
│   └── admin-navbar.php        # Admin navigation
├── INSTALLATION.md             # Installation guide
└── README.md                   # This file
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

## 📄 License

This project is open-source and available under the MIT License.

## 👨‍💻 Developer Notes

### Adding New Services
1. Insert into `services` table via admin panel
2. Services automatically available for ticket generation
3. Assign services to windows in window management

### Customizing Sentiment Analysis
Edit the `analyzeSentiment()` method in `models/Feedback.php`:
- Modify positive/negative keyword lists
- Adjust scoring algorithm
- Add custom sentiment categories

### Extending Notifications
The notification system is modular. To add new notification types:
1. Add type to `notifications` table enum
2. Create notification trigger in relevant model
3. Add notification handler in frontend

## 🐛 Known Issues

- Email notifications require proper SMTP configuration
- Real-time updates use polling (WebSocket upgrade possible)
- Sentiment analysis is basic (can be enhanced with NLP libraries)

## 📞 Support

For technical support or questions:
- Check INSTALLATION.md for setup help
- Review inline code comments
- Open an issue on the repository

---

**Built with ❤️ using PHP, MySQL, and Tailwind CSS**
