-- Create database (run this manually in PostgreSQL)
-- CREATE DATABASE php_ajax_app;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create index on email for faster searches
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);

-- Create index on name for faster searches
CREATE INDEX IF NOT EXISTS idx_users_name ON users(name);

-- Insert sample data
INSERT INTO users (name, email, phone) VALUES 
('John Doe', 'john.doe@example.com', '+1-555-0123'),
('Jane Smith', 'jane.smith@example.com', '+1-555-0124'),
('Mike Johnson', 'mike.johnson@example.com', '+1-555-0125')
ON CONFLICT (email) DO NOTHING;

