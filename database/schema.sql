
-- =========================================
-- DATABASE: SAFETY REPORTING SYSTEM
-- =========================================

CREATE DATABASE safety_reporting;
USE safety_reporting;

-- 1. TABEL ROLES
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- 2. TABEL DEPARTMENTS
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE
);

-- 3. TABEL USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    department_id INT,
    employee_id VARCHAR(30) UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- 4. TABEL CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- 5. TABEL LOCATIONS
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_name VARCHAR(150) NOT NULL,
    description TEXT
);

-- 6. TABEL REPORTS
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_code VARCHAR(30) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    location_id INT NOT NULL,

    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    risk_level ENUM(
        'Rendah',
        'Sedang',
        'Tinggi',
        'Kritis'
    ) DEFAULT 'Rendah',

    status ENUM(
        'Menunggu Verifikasi',
        'Sedang Ditindaklanjuti',
        'Dalam Perbaikan',
        'Menunggu Verifikasi Hasil',
        'Selesai',
        'Ditolak'
    ) DEFAULT 'Menunggu Verifikasi',

    report_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (location_id) REFERENCES locations(id),

    INDEX idx_report_status (status),
    INDEX idx_report_risk (risk_level),
    INDEX idx_report_date (report_date)
);

-- 7. TABEL REPORT ATTACHMENTS
CREATE TABLE report_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (report_id)
        REFERENCES reports(id)
        ON DELETE CASCADE
);

-- 8. TABEL REPORT HISTORIES
CREATE TABLE report_histories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    user_id INT NOT NULL,

    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (report_id)
        REFERENCES reports(id)
        ON DELETE CASCADE,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
);

-- 9. TABEL REPORT ACTIONS
CREATE TABLE report_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    assigned_to INT NOT NULL,

    action_description TEXT NOT NULL,
    deadline DATE,
    action_status ENUM(
        'Belum Dikerjakan',
        'Sedang Dikerjakan',
        'Selesai'
    ) DEFAULT 'Belum Dikerjakan',

    completion_notes TEXT,
    completed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (report_id)
        REFERENCES reports(id)
        ON DELETE CASCADE,

    FOREIGN KEY (assigned_to)
        REFERENCES users(id)
);
