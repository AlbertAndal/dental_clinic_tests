-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('patient', 'admin', 'dentist') DEFAULT 'patient',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    duration INT NOT NULL, -- duration in minutes
    price DECIMAL(10,2) NOT NULL
);

-- Create Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT,
    service_id INT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Insert default services if they don't exist
INSERT IGNORE INTO services (name, description, duration, price) VALUES
('Regular Checkup', 'Comprehensive dental examination and consultation', 30, 50.00),
('Teeth Cleaning', 'Professional dental cleaning and polishing', 60, 80.00),
('Tooth Filling', 'Dental filling procedure for cavities', 45, 120.00),
('Root Canal', 'Root canal treatment for infected/damaged teeth', 90, 500.00),
('Teeth Whitening', 'Professional teeth whitening treatment', 60, 200.00);
