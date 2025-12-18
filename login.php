<?php
session_start();
require 'Config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Check SuperAdmin table
    $stmt = $conn->prepare("SELECT id, name, password, profile_image FROM superadmin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['superAdmin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['is_superadmin'] = true;
            header("Location: SuperAdmin/dashboard.php");
            exit();
        }
    }

    // 2. Check admins table (Department Admins)
    $stmt = $conn->prepare("SELECT id, name, password, department_id, profile_image FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['dept_id'] = $user['department_id'];
            $_SESSION['profile_img'] = $user['profile_image'];
            $_SESSION['department_id'] = $user['department_id'];
            $_SESSION['is_admin'] = true;
            $_SESSION['role'] = 'admin';
            header("Location: Admin/dashboard.php");
            exit();
        }
    }

    // 3. Check students table (username and id_number as password)
    $username_escaped = mysqli_real_escape_string($conn, $username);
    $query = "SELECT * FROM students WHERE username = '$username_escaped'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($password === $row['id_number']) {
            session_regenerate_id(true);
            $_SESSION['student_id'] = $row['id'];
            $_SESSION['student_name'] = $row['name'];
            header("Location: Students/dashboard.php");
            exit();
        }
    }

    $error = "Invalid username or password!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Login | OCSM Portal</title>
    <style>
        .login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">

    <div class="glass-effect p-10 rounded-3xl shadow-2xl w-full max-w-md border border-white/20">
        <div class="text-center mb-8">
            <div class="bg-gradient-to-br from-blue-600 to-purple-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-xl transform hover:rotate-6 transition-transform">
                <i class="fas fa-shield-alt text-white text-3xl"></i>
            </div>
            <h1 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Welcome Back</h1>
            <p class="text-slate-600 mt-3 font-medium">Sign in to access your portal</p>
        </div>

        <?php if($error): ?>
            <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <p class="text-sm font-semibold"><?= $error ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Username</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-purple-600 transition">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" required 
                           class="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-slate-200 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition bg-white/50"
                           placeholder="Enter your username">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-purple-600 transition">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" required 
                           class="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-slate-200 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition bg-white/50"
                           placeholder="••••••••">
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                <span class="flex items-center justify-center space-x-2">
                    <span>Login to Portal</span>
                    <i class="fas fa-arrow-right"></i>
                </span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-200 text-center">
            <a href="home.php" class="text-sm text-slate-500 hover:text-purple-600 transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
            </a>
            <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mt-4">OCSM System &copy; 2025</p>
        </div>
    </div>

</body>
</html>
