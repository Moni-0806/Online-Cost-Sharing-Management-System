<?php
// Test password hashing

// SuperAdmin password: 12345678
$superadmin_password = '12345678';
$superadmin_hash = password_hash($superadmin_password, PASSWORD_DEFAULT);
echo "SuperAdmin password hash for '12345678': " . $superadmin_hash . "<br><br>";

// Admin password: admin123
$admin_password = 'admin123';
$admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);
echo "Admin password hash for 'admin123': " . $admin_hash . "<br><br>";

// Test the existing hashes
$existing_superadmin_hash = '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa';
$existing_admin_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Testing SuperAdmin password '12345678' against existing hash: ";
echo password_verify('12345678', $existing_superadmin_hash) ? "MATCH ✓" : "NO MATCH ✗";
echo "<br>";

echo "Testing Admin password 'admin123' against existing hash: ";
echo password_verify('admin123', $existing_admin_hash) ? "MATCH ✓" : "NO MATCH ✗";
echo "<br><br>";

// Check database connection
require 'Config/db.php';

echo "<h3>Checking database records:</h3>";

// Check superadmin table
$result = $conn->query("SELECT id, username, name FROM superadmin");
if ($result && $result->num_rows > 0) {
    echo "SuperAdmin records found: " . $result->num_rows . "<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- ID: {$row['id']}, Username: {$row['username']}, Name: {$row['name']}<br>";
    }
} else {
    echo "No SuperAdmin records found!<br>";
}

echo "<br>";

// Check admins table
$result = $conn->query("SELECT id, username, name FROM admins");
if ($result && $result->num_rows > 0) {
    echo "Admin records found: " . $result->num_rows . "<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- ID: {$row['id']}, Username: {$row['username']}, Name: {$row['name']}<br>";
    }
} else {
    echo "No Admin records found!<br>";
}
?>
