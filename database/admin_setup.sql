-- Create Admins table if it doesn't exist
CREATE TABLE IF NOT EXISTS Admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    password_history TEXT,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin account into existing Admins table
-- Default credentials:
-- Username: admin
-- Password: Admin@123
INSERT INTO Admins (
    username,
    email,
    password_hash,
    first_name,
    last_name,
    role,
    is_active
) VALUES (
    'admin',
    'admin@yourdomain.com',
    '$2y$10$YourGeneratedHashHere', -- This is the hash for 'Admin@123'
    'System',
    'Administrator',
    'Super Admin',
    TRUE
) ON DUPLICATE KEY UPDATE username = username;

-- Create SystemSettings table if it doesn't exist
CREATE TABLE IF NOT EXISTS SystemSettings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    auto_approve BOOLEAN DEFAULT FALSE,
    email_notifications BOOLEAN DEFAULT TRUE,
    maintenance_mode BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default system settings
INSERT INTO SystemSettings (auto_approve, email_notifications, maintenance_mode)
VALUES (FALSE, TRUE, FALSE)
ON DUPLICATE KEY UPDATE id = id;

-- Add an audit log entry for admin creation
INSERT INTO SystemAuditLog (
    action_type,
    action_details,
    ip_address
) VALUES (
    'ADMIN_SETUP',
    'Default admin account created',
    'SYSTEM'
); 