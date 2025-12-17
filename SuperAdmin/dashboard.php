<?php
session_start();
require '../Config/db.php';

// Check if logged in
if (!isset($_SESSION['is_superadmin'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$admin_id = $_SESSION['superAdmin_id'];
$baseDir = "../uploads";
$superAdminDir = $baseDir . "/superAdmin";

// Ensure directory exists
if (!is_dir($superAdminDir)) mkdir($superAdminDir, 0755, true);

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
        $img_name = time() . "_" . uniqid() . "." . $ext;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $superAdminDir . "/" . $img_name)) {
            $conn->query("UPDATE SuperAdmin SET profile_image = '$img_name' WHERE id = $admin_id");
        }
    }

    // Handle Password Change
    if (!empty($_POST['new_password'])) {
        $new_pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
        $conn->query("UPDATE SuperAdmin SET password = '$new_pass' WHERE id = $admin_id");
    }

    // Update Basic Info
    $stmt = $conn->prepare("UPDATE SuperAdmin SET name = ?, phone = ?, username = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phone, $username, $admin_id);
    if ($stmt->execute()) {
        $_SESSION['admin_name'] = $name;
        $message = "Profile updated successfully!";
    }
}

// Fetch fresh data for the form
$admin_data = $conn->query("SELECT * FROM SuperAdmin WHERE id = $admin_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>My Profile | SuperAdmin</title>
</head>
<body class="bg-gray-100 font-sans flex">

    <?php include './sidebar.php'; ?>

    <div class="flex-1 p-10">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-800 mb-8">Account Settings</h2>
            
            <?php if($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    <div class="flex flex-col items-center justify-center border-r border-gray-50 pr-8">
                        <div class="relative group">
                            <img src="../uploads/superAdmin/<?= $admin_data['profile_image'] ?: 'default.png' ?>" 
                                 class="w-48 h-48 rounded-full object-cover shadow-xl border-4 border-white transition group-hover:opacity-90">
                            <div class="mt-6">
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Change Profile Photo</label>
                                <input type="file" name="profile_image" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($admin_data['name']) ?>" 
                                   class="w-full border border-gray-300 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($admin_data['username']) ?>" 
                                   class="w-full border border-gray-300 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($admin_data['phone']) ?>" 
                                   class="w-full border border-gray-300 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <label class="block text-sm font-semibold text-red-600 mb-1">Security</label>
                            <input type="password" name="new_password" placeholder="New Password (Leave blank to keep current)" 
                                   class="w-full border border-gray-300 p-3 rounded-xl focus:ring-2 focus:ring-red-400 outline-none">
                        </div>

                        <button type="submit" name="update_profile" 
                                class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition duration-300">
                            Update My Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>