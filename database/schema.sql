-- E-Queue System Database Schema
-- Drop existing database if exists
DROP DATABASE IF EXISTS equeue_system;
CREATE DATABASE equeue_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE equeue_system;

-- Users table (for both customers and staff)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    school_id VARCHAR(50) UNIQUE DEFAULT NULL,
    role ENUM('user', 'staff', 'admin') DEFAULT 'user',
    verification_token VARCHAR(255) DEFAULT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- Windows/Counters table
CREATE TABLE windows (
    id INT PRIMARY KEY AUTO_INCREMENT,
    window_number VARCHAR(50) UNIQUE NOT NULL,
    window_name VARCHAR(255) NOT NULL,
    staff_id INT,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_staff (staff_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Services table
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_name VARCHAR(255) NOT NULL,
    service_code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    requirements TEXT,
    estimated_time INT DEFAULT 10, -- in minutes
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (service_code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Window Services (Many-to-Many relationship)
CREATE TABLE window_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    window_id INT NOT NULL,
    service_id INT NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (window_id) REFERENCES windows(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    UNIQUE KEY unique_window_service (window_id, service_id),
    INDEX idx_window (window_id),
    INDEX idx_service (service_id)
) ENGINE=InnoDB;

-- Tickets table
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    window_id INT,
    status ENUM('waiting', 'called', 'serving', 'completed', 'cancelled') DEFAULT 'waiting',
    queue_position INT,
    is_archived BOOLEAN DEFAULT FALSE,
    called_at TIMESTAMP NULL,
    served_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (window_id) REFERENCES windows(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_service (service_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    INDEX idx_queue (service_id, status, created_at)
) ENGINE=InnoDB;

-- Feedback table
CREATE TABLE feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    window_id INT,
    rating INT DEFAULT NULL CHECK (rating BETWEEN 1 AND 5 OR rating IS NULL),
    comment TEXT,
    sentiment ENUM('positive', 'neutral', 'negative', 'very_positive', 'very_negative'),
    sentiment_score DECIMAL(5,4),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (window_id) REFERENCES windows(id) ON DELETE SET NULL,
    INDEX idx_ticket (ticket_id),
    INDEX idx_user (user_id),
    INDEX idx_sentiment (sentiment),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    ticket_id INT,
    type ENUM('ticket_created', 'turn_next', 'now_serving', 'completed'),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Chatbot Data table (for training and responses)
CREATE TABLE chatbot_data (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category VARCHAR(100),
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    keywords TEXT,
    usage_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active (is_active)
) ENGINE=InnoDB;

-- Activity Logs table
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Insert default admin account
INSERT INTO users (email, password, full_name, role) VALUES
('admin@equeue.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');
-- Default password: password (change this in production!)

-- Insert sample services
INSERT INTO services (service_name, service_code, description, requirements, estimated_time) VALUES
('Account Opening', 'ACC-OPEN', 'Open a new account', 'Valid ID, Proof of Address, Initial Deposit', 15),
('Cash Withdrawal', 'CASH-WD', 'Withdraw cash from account', 'Valid ID, Account Number', 5),
('Cash Deposit', 'CASH-DEP', 'Deposit cash to account', 'Account Number, Cash', 5),
('Bills Payment', 'BILL-PAY', 'Pay utility bills', 'Bill Statement, Payment Amount', 8),
('Money Transfer', 'MONEY-TRF', 'Transfer money', 'Valid ID, Recipient Details, Amount', 10),
('Loan Application', 'LOAN-APP', 'Apply for a loan', 'Valid ID, Income Certificate, Collateral Documents', 20),
('Card Services', 'CARD-SVC', 'ATM/Credit Card services', 'Valid ID, Card Number', 10),
('Customer Support', 'CUST-SUP', 'General inquiries and support', 'None', 5);

-- Insert sample windows
INSERT INTO windows (window_number, window_name, is_active) VALUES
('W-01', 'Window 1 - General Services', FALSE),
('W-02', 'Window 2 - Transactions', FALSE),
('W-03', 'Window 3 - Premium Services', FALSE),
('W-04', 'Window 4 - Customer Support', FALSE);

-- Insert sample chatbot data
INSERT INTO chatbot_data (category, question, answer, keywords) VALUES
('Operating Hours', 'What are your operating hours?', 'We are open Monday to Friday from 9:00 AM to 5:00 PM, and Saturday from 9:00 AM to 1:00 PM. We are closed on Sundays and holidays.', 'hours,time,open,operating,schedule'),
('Services', 'What services do you offer?', 'We offer a wide range of services including account opening, cash transactions, bills payment, money transfer, loan applications, and card services. Please select "Get Ticket" to choose your desired service.', 'services,offer,available'),
('Queue', 'How long is the wait time?', 'Wait times vary depending on the service and current queue. You can check the live queue display to see current waiting times. Most services are completed within 5-20 minutes.', 'wait,time,queue,long,how long'),
('Requirements', 'What documents do I need?', 'Required documents depend on the service. Common requirements include valid ID, proof of address, and service-specific documents. Please select your service in "Get Ticket" to see specific requirements.', 'documents,requirements,need,what do i need'),
('Tickets', 'How do I get a ticket?', 'Click on "Get Ticket" in the navigation menu, select your desired service, review the requirements, and confirm. You will receive a ticket number and can track your position in the queue.', 'ticket,get,how to,queue number');
