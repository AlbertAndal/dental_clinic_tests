<?php
require_once 'includes/config.php';

try {
    // Check if admin already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute(['admin@dental.com']);
    
    if (!$stmt->fetch()) {
        // Create admin user if doesn't exist
        $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            'Admin',
            'User',
            'admin@dental.com',
            password_hash('admin123', PASSWORD_DEFAULT),
            'admin'
        ]);
        echo "Admin user created successfully!<br>";
        echo "Email: admin@dental.com<br>";
        echo "Password: admin123";
    } else {
        echo "Admin user already exists!";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
