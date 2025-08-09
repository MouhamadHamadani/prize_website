-- Prize Website Database Schema
-- Created for Wheel of Fortune and Mystery Box prize system

-- Create database
CREATE DATABASE IF NOT EXISTS prize_website;
USE prize_website;

-- Users table for authentication and tracking
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Prizes table - shared between wheel and mystery box
CREATE TABLE prizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    quantity INT NOT NULL DEFAULT 0,
    percentage DECIMAL(5,2) NULL,
    is_manual_percentage BOOLEAN DEFAULT FALSE,
    times_won INT DEFAULT 0,
    enabled_in_wheel BOOLEAN DEFAULT TRUE,
    enabled_in_box BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User prizes tracking table
CREATE TABLE user_prizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    prize_id INT NOT NULL,
    method ENUM('wheel', 'box') NOT NULL,
    won_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (prize_id) REFERENCES prizes(id) ON DELETE CASCADE
);

-- Admin users table (separate from regular users)
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample prizes data
INSERT INTO prizes (name, price, quantity, percentage, is_manual_percentage, enabled_in_wheel, enabled_in_box) VALUES
('iPhone 15 Pro', 999.99, 2, 5.0, TRUE, TRUE, TRUE),
('AirPods Pro', 249.99, 5, 10.0, TRUE, TRUE, TRUE),
('$100 Gift Card', 100.00, 10, NULL, FALSE, TRUE, TRUE),
('$50 Gift Card', 50.00, 20, NULL, FALSE, TRUE, TRUE),
('$25 Gift Card', 25.00, 30, NULL, FALSE, TRUE, TRUE),
('$10 Gift Card', 10.00, 50, NULL, FALSE, TRUE, TRUE),
('Free Coffee', 5.00, 100, NULL, FALSE, TRUE, TRUE),
('Sticker Pack', 2.00, 200, NULL, FALSE, TRUE, FALSE);

-- Create indexes for better performance
CREATE INDEX idx_user_prizes_user_id ON user_prizes(user_id);
CREATE INDEX idx_user_prizes_prize_id ON user_prizes(prize_id);
CREATE INDEX idx_user_prizes_method ON user_prizes(method);
CREATE INDEX idx_prizes_enabled_wheel ON prizes(enabled_in_wheel);
CREATE INDEX idx_prizes_enabled_box ON prizes(enabled_in_box);

