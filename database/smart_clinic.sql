-- SUDAMA CLINIC Database Schema
-- MySQL Database for Appointment & Patient Record System

-- Create database
CREATE DATABASE IF NOT EXISTS smart_clinic;
USE smart_clinic;

-- =====================================================
-- USERS TABLE (Base authentication for all user types)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('patient', 'doctor', 'admin', 'receptionist') NOT NULL DEFAULT 'patient',
    status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PATIENTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other', 'prefer-not-to-say') NOT NULL,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    blood_group VARCHAR(5),
    emergency_contact VARCHAR(20),
    medical_history TEXT,
    allergies TEXT,
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SPECIALIZATIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS specializations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default specializations
INSERT IGNORE INTO specializations (name, description, icon) VALUES
('General Medicine', 'Primary healthcare and general health issues', '🏥'),
('Cardiology', 'Heart and cardiovascular system', '❤️'),
('Dermatology', 'Skin, hair, and nail conditions', '✨'),
('Pediatrics', 'Child healthcare', '👶'),
('Orthopedics', 'Bone and joint specialists', '🦴'),
('Gynecology', 'Women\'s health', '👩‍⚕️'),
('Neurology', 'Brain and nerve care', '🧠'),
('Ophthalmology', 'Eye care and vision', '👁️'),
('ENT', 'Ear, nose, and throat', '👂'),
('Psychiatry', 'Mental health', '🧘'),
('Dentistry', 'Dental and oral health', '🦷'),
('Oncology', 'Cancer treatment', '🎗️'),
('Gastroenterology', 'Digestive system', '🫁'),
('Nephrology', 'Kidney care', '🫘'),
('Pulmonology', 'Lung and respiratory', '🫁');

-- =====================================================
-- DOCTORS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    specialization_id INT,
    qualification VARCHAR(255),
    experience_years INT DEFAULT 0,
    bio TEXT,
    consultation_charge DECIMAL(10,2) DEFAULT 0.00,
    new_case_charge DECIMAL(10,2) DEFAULT 0.00,
    old_case_charge DECIMAL(10,2) DEFAULT 0.00,
    profile_image VARCHAR(255),
    available_days VARCHAR(100) DEFAULT 'Mon,Tue,Wed,Thu,Fri,Sat',
    slot_duration INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_specialization (specialization_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TIME SLOTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    day_of_week ENUM('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    max_patients INT DEFAULT 1,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_doctor_day (doctor_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- APPOINTMENTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    end_time TIME,
    appointment_type ENUM('new', 'followup', 'emergency', 'consultation') DEFAULT 'new',
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no-show') DEFAULT 'pending',
    reason TEXT,
    symptoms TEXT,
    charge DECIMAL(10,2) DEFAULT 0.00,
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    booked_by ENUM('patient', 'receptionist', 'doctor', 'admin') DEFAULT 'patient',
    notes TEXT,
    cancellation_reason TEXT,
    cancelled_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_patient (patient_id),
    INDEX idx_doctor (doctor_id),
    INDEX idx_date (appointment_date),
    INDEX idx_status (status),
    UNIQUE KEY unique_appointment (doctor_id, appointment_date, appointment_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PRESCRIPTIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    diagnosis TEXT,
    medicines JSON,
    instructions TEXT,
    follow_up_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_patient (patient_id),
    INDEX idx_doctor (doctor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MEDICAL RECORDS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT,
    appointment_id INT,
    record_type ENUM('diagnosis', 'lab_report', 'imaging', 'vaccination', 'surgery', 'other') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    file_type VARCHAR(50),
    record_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    INDEX idx_patient (patient_id),
    INDEX idx_record_type (record_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MESSAGES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ADMIN/STAFF TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    phone VARCHAR(20),
    designation VARCHAR(100),
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT DEFAULT ADMIN USER
-- Password: Admin@123 (bcrypt hashed)
-- =====================================================
INSERT IGNORE INTO users (email, password, role, status) VALUES
('admin@smartclinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

INSERT IGNORE INTO staff (user_id, name, phone, designation, department)
SELECT id, 'System Admin', '+91 99999 99999', 'Administrator', 'Management'
FROM users WHERE email = 'admin@smartclinic.com';

-- =====================================================
-- INSERT SAMPLE DOCTORS
-- Password: Doctor@123 (bcrypt hashed)
-- =====================================================
INSERT IGNORE INTO users (email, password, role, status) VALUES
('rajesh.kumar@smartclinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'active'),
('priya.sharma@smartclinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'active'),
('amit.patel@smartclinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'active'),
('sneha.gupta@smartclinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 'inactive');

-- Insert doctor profiles
INSERT IGNORE INTO doctors (user_id, name, phone, specialization_id, qualification, experience_years, consultation_charge, new_case_charge, old_case_charge)
SELECT u.id, 'Dr. Rajesh Kumar', '+91 98765 43210', s.id, 'MBBS, MD', 15, 500.00, 500.00, 300.00
FROM users u, specializations s WHERE u.email = 'rajesh.kumar@smartclinic.com' AND s.name = 'General Medicine';

INSERT IGNORE INTO doctors (user_id, name, phone, specialization_id, qualification, experience_years, consultation_charge, new_case_charge, old_case_charge)
SELECT u.id, 'Dr. Priya Sharma', '+91 98765 43211', s.id, 'MBBS, DM Cardiology', 12, 800.00, 800.00, 500.00
FROM users u, specializations s WHERE u.email = 'priya.sharma@smartclinic.com' AND s.name = 'Cardiology';

INSERT IGNORE INTO doctors (user_id, name, phone, specialization_id, qualification, experience_years, consultation_charge, new_case_charge, old_case_charge)
SELECT u.id, 'Dr. Amit Patel', '+91 98765 43212', s.id, 'MBBS, DCH', 8, 400.00, 400.00, 250.00
FROM users u, specializations s WHERE u.email = 'amit.patel@smartclinic.com' AND s.name = 'Pediatrics';

INSERT IGNORE INTO doctors (user_id, name, phone, specialization_id, qualification, experience_years, consultation_charge, new_case_charge, old_case_charge)
SELECT u.id, 'Dr. Sneha Gupta', '+91 98765 43213', s.id, 'MBBS, MD Dermatology', 10, 600.00, 600.00, 400.00
FROM users u, specializations s WHERE u.email = 'sneha.gupta@smartclinic.com' AND s.name = 'Dermatology';

-- Insert default time slots for doctors
INSERT IGNORE INTO time_slots (doctor_id, day_of_week, start_time, end_time) VALUES
(1, 'Mon', '09:00:00', '09:30:00'), (1, 'Mon', '09:30:00', '10:00:00'), (1, 'Mon', '10:00:00', '10:30:00'),
(1, 'Mon', '10:30:00', '11:00:00'), (1, 'Mon', '11:00:00', '11:30:00'), (1, 'Mon', '11:30:00', '12:00:00'),
(1, 'Mon', '14:00:00', '14:30:00'), (1, 'Mon', '14:30:00', '15:00:00'), (1, 'Mon', '15:00:00', '15:30:00'),
(1, 'Mon', '15:30:00', '16:00:00'), (1, 'Mon', '16:00:00', '16:30:00'), (1, 'Mon', '16:30:00', '17:00:00'),
(1, 'Tue', '09:00:00', '09:30:00'), (1, 'Tue', '09:30:00', '10:00:00'), (1, 'Tue', '10:00:00', '10:30:00'),
(1, 'Tue', '10:30:00', '11:00:00'), (1, 'Tue', '11:00:00', '11:30:00'), (1, 'Tue', '11:30:00', '12:00:00'),
(1, 'Tue', '14:00:00', '14:30:00'), (1, 'Tue', '14:30:00', '15:00:00'), (1, 'Tue', '15:00:00', '15:30:00');

-- =====================================================
-- INSERT SAMPLE RECEPTIONIST
-- Password: Receptionist@123 (bcrypt hashed)
-- =====================================================
INSERT IGNORE INTO users (email, password, role, status) VALUES
('reception@smartclinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'receptionist', 'active');

INSERT IGNORE INTO staff (user_id, name, phone, designation, department)
SELECT id, 'Reception Desk', '+91 98765 00000', 'Receptionist', 'Front Desk'
FROM users WHERE email = 'reception@smartclinic.com';

-- =====================================================
-- INSERT SAMPLE PATIENT
-- Password: Patient@123 (bcrypt hashed)
-- =====================================================
INSERT IGNORE INTO users (email, password, role, status) VALUES
('john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', 'active'),
('jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient', 'active');

INSERT IGNORE INTO patients (user_id, first_name, last_name, phone, date_of_birth, gender, address, city)
SELECT id, 'John', 'Doe', '+91 99887 76655', '1990-05-15', 'male', '123 Main Street', 'Mumbai'
FROM users WHERE email = 'john.doe@email.com';

INSERT IGNORE INTO patients (user_id, first_name, last_name, phone, date_of_birth, gender, address, city)
SELECT id, 'Jane', 'Smith', '+91 99887 76656', '1985-08-22', 'female', '456 Oak Avenue', 'Mumbai'
FROM users WHERE email = 'jane.smith@email.com';

-- =====================================================
-- INSERT SAMPLE APPOINTMENTS
-- =====================================================
INSERT IGNORE INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, appointment_type, status, reason, charge, booked_by)
VALUES
(1, 1, CURDATE(), '10:00:00', 'new', 'confirmed', 'Regular checkup', 500.00, 'patient'),
(2, 2, CURDATE(), '11:30:00', 'followup', 'pending', 'Follow-up consultation', 500.00, 'receptionist'),
(1, 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '14:00:00', 'followup', 'pending', 'Follow-up visit', 300.00, 'patient');

-- =====================================================
-- INSERT SAMPLE PRESCRIPTION
-- =====================================================
INSERT IGNORE INTO prescriptions (appointment_id, patient_id, doctor_id, diagnosis, medicines, instructions, follow_up_date)
VALUES
(1, 1, 1, 'Viral Fever', 
'[{"name": "Paracetamol 500mg", "dosage": "1 tablet", "frequency": "Thrice daily", "duration": "3 days"}, {"name": "Cetirizine 10mg", "dosage": "1 tablet", "frequency": "Once daily", "duration": "5 days"}]',
'Rest advised. Drink plenty of fluids.', DATE_ADD(CURDATE(), INTERVAL 7 DAY));

-- =====================================================
-- INSERT SAMPLE MESSAGE
-- =====================================================
INSERT IGNORE INTO messages (sender_id, receiver_id, subject, message)
SELECT d.user_id, p.user_id, 'Follow-up Reminder', 'Please come for your follow-up appointment. Bring your previous reports.'
FROM doctors d, patients p WHERE d.id = 1 AND p.id = 1;

-- =====================================================
-- VIEWS FOR EASY DATA ACCESS
-- =====================================================
CREATE OR REPLACE VIEW v_appointments AS
SELECT 
    a.id,
    a.appointment_date,
    a.appointment_time,
    a.appointment_type,
    a.status,
    a.reason,
    a.charge,
    a.payment_status,
    p.id as patient_id,
    CONCAT(p.first_name, ' ', p.last_name) as patient_name,
    p.phone as patient_phone,
    d.id as doctor_id,
    d.name as doctor_name,
    s.name as specialization
FROM appointments a
JOIN patients p ON a.patient_id = p.id
JOIN doctors d ON a.doctor_id = d.id
LEFT JOIN specializations s ON d.specialization_id = s.id;

CREATE OR REPLACE VIEW v_doctors AS
SELECT 
    d.id,
    d.name,
    d.phone,
    d.qualification,
    d.experience_years,
    d.consultation_charge,
    d.new_case_charge,
    d.old_case_charge,
    s.name as specialization,
    s.icon as specialty_icon,
    u.email,
    u.status
FROM doctors d
JOIN users u ON d.user_id = u.id
LEFT JOIN specializations s ON d.specialization_id = s.id;
