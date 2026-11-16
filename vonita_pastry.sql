-- Vonita Pastry database
-- Import this in phpMyAdmin (http://localhost/phpmyadmin) > Import

CREATE DATABASE IF NOT EXISTS vonita_pastry;
USE vonita_pastry;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    contact_number VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin account
-- Email: admin@vonitapastry.com
-- Password: admin12345  (palitan pagkatapos ng testing)
INSERT IGNORE INTO users (first_name, last_name, email, password, role, contact_number, address)
VALUES ('Admin', 'Vonita', 'admin@vonitapastry.com',
        '$2y$12$hEcjmooJhYzb045exycNVOHrRKNBNlJVjce8iKquzEK2IVF66oB9u',
        'admin', '09123456789', '123 Main Street, Puerto Princesa City, Palawan');
