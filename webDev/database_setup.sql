-- Database setup for photography portfolio website
-- Run this script in phpMyAdmin to set up the required database and tables

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS photography_portfolio;

-- Use the database
USE photography_portfolio;


-- Create works table
CREATE TABLE IF NOT EXISTS works (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),
    link VARCHAR(255),
    image VARCHAR(255) NOT NULL,
    date_added DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create archives table for deleted works
CREATE TABLE IF NOT EXISTS archives (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),
    link VARCHAR(255),
    image VARCHAR(255) NOT NULL,
    date_added DATETIME NOT NULL,
    date_archived DATETIME NOT NULL,
    original_id INT(11)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (username: admin, password: admin123)
-- Only run this if the admin doesn't already exist
INSERT INTO admins (username, email, password)
SELECT 'admin', 'admin@example.com', '$2y$10$xTZC9tMVAT5wSJHJjGJ3aeO6fLYj/o3s5h4nK9SxNSCf1nCTKzKWa'
FROM dual
WHERE NOT EXISTS (
    SELECT * FROM admins WHERE username = 'admin'
) LIMIT 1;
-- Add email column to admins table
ALTER TABLE admins ADD COLUMN email VARCHAR(100) NOT NULL UNIQUE AFTER username;

-- Update existing admin user with default email
UPDATE admins SET email = 'admin@example.com' WHERE username = 'admin'; 

-- Notes:
-- 1. The default admin password is 'admin123' (already hashed)
-- 2. Run this script in phpMyAdmin's SQL tab
-- 3. After running this script, you should be able to log in with username 'admin' and password 'admin123'
-- 4. Make sure the database name (photography_portfolio) matches the one in includes/db_mysql.php