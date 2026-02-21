# ISPSC E-Queue System - Project Structure & Architecture

## 📂 Directory Structure

```
ispscequeue/
├── config/             # Configuration & DB connection
├── database/           # SQL schemas (queue_schema.sql)
├── models/             # Business Logic (User, Ticket, Service, etc.)
├── includes/           # Shared components (Navbars, AI Chatbot)
├── public/             # Entry point & Frontend
│   ├── admin/          # Admin Portal
│   ├── staff/          # Staff Portal
│   ├── user/           # User Portal
│   └── api/            # REST Endpoints
├── sentiment_analysis/ # Python Microservice
└── logs/               # System logs
```

---

## 🏗️ System Architecture

```
User Browser → PHP Application → MySQL/MariaDB
     ↓              ↓                  ↓
  Tailwind     PDO/Models         Relational Tables
  JavaScript   MVC Pattern        Indexed Queries
  AJAX Polls   Prepared Stmts     Foreign Keys
```

---

## 🔐 Security Checklist

- **Password Hashing**: Bcrypt (`password_hash`).
- **SQL Injection**: Prevented via PDO Prepared Statements.
- **XSS Protection**: Sanitization and output escaping.
- **RBAC**: Directory-level role checks.
- **Environment**: Sensitive keys stored in `.env`.

---

## 🛠️ Technology Stack

- **Backend**: PHP 8.1+
- **Database**: MariaDB/MySQL
- **Frontend**: Tailwind CSS, Vanilla JS
- **Microservices**: Python 3.x (FastAPI)
- **AI**: OpenRouter API

---

## 🗄️ Database Tables (Core)

1. **users**: Users and role management.
2. **tickets**: Full lifecycle of queue tickets.
3. **services**: Available service categories.
4. **windows**: Counter/Window assignments.
5. **feedback**: Sentiment-analyzed customer ratings.
6. **ai_context**: AI Knowledge base.
7. **notifications**: Notification logs.
