CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) -- e.g., 'resident', 'staff', 'admin'
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255),
    role_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE applications (
    app_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type VARCHAR(100), -- e.g., 'Business Permit', 'Barangay Clearance'
    purpose TEXT,
    current_status VARCHAR(50), -- e.g., 'Filed', 'Under Review', 'Approved'
    remarks TEXT,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE documents (
    doc_id INT AUTO_INCREMENT PRIMARY KEY,
    app_id INT,
    file_name VARCHAR(255),
    file_path VARCHAR(255),
    file_type VARCHAR(50),
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES applications(app_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE statuses (
    status_id INT AUTO_INCREMENT PRIMARY KEY,
    app_id INT,
    status VARCHAR(50),
    changed_by INT,
    changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES applications(app_id)
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(user_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    app_id INT,
    amount DECIMAL(10, 2),
    payment_method VARCHAR(50), -- e.g., 'GCash', 'Bank Transfer'
    payment_status VARCHAR(50), -- e.g., 'Pending', 'Paid'
    paid_at DATETIME,
    FOREIGN KEY (app_id) REFERENCES applications(app_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE notifications (
    notif_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    seen BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE qr_codes (
    qr_id INT AUTO_INCREMENT PRIMARY KEY,
    app_id INT,
    qr_value TEXT, -- stored code or link
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES applications(app_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);

CREATE TABLE logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action TEXT,
    performed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);
ALTER TABLE applications ADD COLUMN extra_data JSON NULL;
