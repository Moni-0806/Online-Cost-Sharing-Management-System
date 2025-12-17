<?php
session_start();
require '../Config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM SuperAdmin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['superAdmin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['is_superadmin'] = true;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>SuperAdmin Login</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Admin Login</h2>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4 text-sm text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" required class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-indigo-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-indigo-200">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">Login</button>
        </form>

        <?php
        $check = $conn->query("SELECT id FROM SuperAdmin LIMIT 1");
        if ($check->num_rows == 0): ?>
            <div class="mt-6 text-center">
                <a href="register.php" class="text-indigo-600 text-sm hover:underline">Setup SuperAdmin Account</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>