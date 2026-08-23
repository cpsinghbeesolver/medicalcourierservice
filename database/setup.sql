-- Medical Courier Services Database Setup

-- Create database
CREATE DATABASE IF NOT EXISTS medical_courier CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE medical_courier;

-- Grant privileges (adjust username/password as needed)
-- GRANT ALL PRIVILEGES ON medical_courier.* TO 'root'@'localhost';
-- FLUSH PRIVILEGES;

-- The tables will be created by running: php artisan migrate

-- Sample data insertion (optional, for testing)
-- After running migrations, you can insert test data like:

-- INSERT INTO users (name, email, password, role, phone, status, created_at, updated_at) VALUES
-- ('John Driver', 'driver@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NANh98.pqLHa', 'driver', '+1234567890', 'active', NOW(), NOW()),
-- ('Admin User', 'admin@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NANh98.pqLHa', 'admin', '+0987654321', 'active', NOW(), NOW());
-- Password for both: password123

