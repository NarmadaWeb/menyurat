CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE village_profile (
    id INT PRIMARY KEY DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    logo VARCHAR(255),
    kades_name VARCHAR(100),
    kades_nip VARCHAR(50)
);

CREATE TABLE mail_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10)
);

CREATE TABLE incoming_mails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(50) NOT NULL,
    date_received DATE NOT NULL,
    sender VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    status ENUM('new', 'processed', 'finished') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE outgoing_mails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(50) NOT NULL,
    date_sent DATE NOT NULL,
    recipient VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE dispositions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mail_id INT,
    receiver_id INT,
    instruction TEXT,
    deadline DATE,
    notes TEXT,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mail_id) REFERENCES incoming_mails(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

CREATE TABLE citizen_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_number VARCHAR(50) NOT NULL UNIQUE,
    nik VARCHAR(20) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    mail_type_id INT,
    description TEXT,
    status ENUM('new', 'processed', 'finished', 'rejected') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mail_type_id) REFERENCES mail_types(id)
);

CREATE TABLE request_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT,
    file_path VARCHAR(255),
    file_type VARCHAR(50),
    FOREIGN KEY (request_id) REFERENCES citizen_requests(id) ON DELETE CASCADE
);

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default roles
INSERT INTO roles (name) VALUES ('Admin'), ('Kades'), ('Staf');

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, full_name, role_id) VALUES ('admin', '$2y$10$8WkY.fW2mH1uE0r8U3l.O.VvX/0.XmO.e.W0V.W0V.W0V.W0V.W0V', 'Administrator', 1);

-- Insert default village profile
INSERT INTO village_profile (name, address, phone, email, kades_name) VALUES ('Desa Pendua', 'Jl. Raya Pendua No. 01, Kayangan, Kabupaten Lombok Utara, NTB 83353', '081234567890', 'kontak@pendua.desa.id', 'Abu Bakar, S.Adm');
