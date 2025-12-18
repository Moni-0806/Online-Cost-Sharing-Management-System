<?php
session_start();
require '../Config/db.php';


if (!isset($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$admin_id = $_SESSION['admin_id'];
$adminDir = "../uploads/admin";

if (!is_dir($adminDir)) mkdir($adminDir, 0755, true);

/*
|--------------------------------------------------------------------------
| PROFILE UPDATE LOGIC
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);

    // Handle Image Upload
    if (!empty($_FILES['profile_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $img_name = "adm_" . time() . "_" . uniqid() . "." . $ext;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $adminDir . "/" . $img_name)) {
            $conn->query("UPDATE admins SET profile_image = '$img_name' WHERE id = $admin_id");
            $_SESSION['profile_img'] = $img_name; // Update session image
        }
    }

    // Handle Password Change
    if (!empty($_POST['new_password'])) {
        $new_pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $conn->query("UPDATE admins SET password = '$new_pass' WHERE id = $admin_id");
    }

    // Update Basic Info
    $stmt = $conn->prepare("UPDATE admins SET name = ?, phone = ?, username = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phone, $username, $admin_id);
    if ($stmt->execute()) {
        $_SESSION['admin_name'] = $name;
        $message = "Profile updated successfully!";
    }
}

// Fetch Admin & Department Info
$query = "SELECT admins.*, departments.name as dept_name 
          FROM admins 
          LEFT JOIN departments ON admins.department_id = departments.id 
          WHERE admins.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_data = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Dashboard | Admin Profile</title>
    <style>
        .admin-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 font-sans flex">

    <?php include './sidebar.php'; ?>

    <div class="flex-1 p-8">
        <div class="max-w-5xl mx-auto">
            
            <div class="mb-8">
                <h2 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Welcome, <?= htmlspecialchars($admin_data['name']) ?></h2>
                <p class="text-slate-600">Manage your profile and department settings for <span class="text-blue-600 font-bold bg-blue-50 px-3 py-1 rounded-full text-sm"><?= htmlspecialchars($admin_data['dept_name'] ?? 'Unassigned') ?></span></p>
            </div>

            <?php if($message): ?>
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-l-4 border-emerald-500 text-emerald-700 p-5 rounded-r-xl mb-8 shadow-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <div>
                            <p class="font-bold">Success!</p>
                            <p class="text-sm"><?= $message ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3">
                    
                    <div class="admin-gradient p-12 text-center text-white flex flex-col items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-indigo-600/20"></div>
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-full blur-2xl opacity-30 group-hover:opacity-50 transition"></div>
                            <img src="../uploads/admin/<?= $admin_data['profile_image'] ?: 'default.png' ?>" 
                                 class="relative w-48 h-48 rounded-full object-cover border-6 border-white shadow-2xl transition group-hover:scale-105 duration-300">
                            <div class="absolute bottom-2 right-2 bg-gradient-to-r from-blue-500 to-indigo-500 w-12 h-12 rounded-full flex items-center justify-center shadow-xl cursor-pointer group-hover:scale-110 transition">
                                <i class="fas fa-camera text-white"></i>
                            </div>
                        </div>
                        <h3 class="mt-6 text-2xl font-bold"><?= htmlspecialchars($admin_data['name']) ?></h3>
                        <span class="text-blue-200 text-sm font-bold px-4 py-2 bg-white/20 rounded-full mt-3 backdrop-blur-sm">
                             <?= htmlspecialchars($admin_data['dept_name'] ?? 'No Department') ?>
                        </span>
                        
                        <div class="mt-8 w-full space-y-3">
                            <label class="block text-sm text-blue-100 font-bold text-left">Update Profile Photo</label>
                            <input type="file" name="profile_image" class="w-full text-sm text-blue-100 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:bg-white/20 file:text-white file:font-semibold hover:file:bg-white/30 cursor-pointer transition backdrop-blur-sm">
                        </div>
                    </div>

                    <div class="lg:col-span-2 p-12 space-y-8">
                        <h4 class="text-2xl font-bold text-slate-800 border-b-2 border-slate-200 pb-4 flex items-center">
                            <i class="fas fa-user-edit mr-3 text-blue-600"></i>Personal Information
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-user mr-2 text-blue-500"></i>Full Name</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($admin_data['name']) ?>" 
                                       class="w-full bg-blue-50/50 border-2 border-blue-200 p-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-at mr-2 text-indigo-500"></i>Username</label>
                                <input type="text" name="username" value="<?= htmlspecialchars($admin_data['username']) ?>" 
                                       class="w-full bg-indigo-50/50 border-2 border-indigo-200 p-4 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-phone mr-2 text-green-500"></i>Phone Number</label>
                                <input type="text" name="phone" value="<?= htmlspecialchars($admin_data['phone']) ?>" 
                                       class="w-full bg-green-50/50 border-2 border-green-200 p-4 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-red-600 mb-3"><i class="fas fa-lock mr-2"></i>New Password <small class="text-slate-500 font-normal ml-2">(Optional)</small></label>
                                <input type="password" name="new_password" placeholder="Enter new password" 
                                       class="w-full bg-red-50/50 border-2 border-red-200 p-4 rounded-xl focus:ring-2 focus:ring-red-400 focus:border-red-400 outline-none transition">
                                <p class="text-xs text-slate-500 mt-2">Leave blank to keep current password</p>
                            </div>
                        </div>

                        <div class="pt-8">
                            <button type="submit" name="update_profile" 
                                    class="w-full md:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-12 py-4 rounded-xl shadow-xl shadow-blue-200 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fas fa-save mr-2"></i>Update My Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>