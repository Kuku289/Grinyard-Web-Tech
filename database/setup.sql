-- Create database if not exists
CREATE DATABASE IF NOT EXISTS grinyard_db;
USE grinyard_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'farmer', 'officer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT NOT NULL,
    officer_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (officer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user if not exists
INSERT INTO users (name, email, password, role) 
SELECT 'Admin', 'admin@grinyard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@grinyard.com'
);
