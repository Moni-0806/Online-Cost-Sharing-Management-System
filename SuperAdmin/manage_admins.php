<?php
session_start();
require '../Config/db.php';

if (!isset($_SESSION['is_superadmin'])) {
    header("Location: login.php");
    exit();
}

$adminDir = "../uploads/admin";
if (!is_dir($adminDir)) mkdir($adminDir, 0755, true);

$error_message = "";

// --- Logic: Add/Update Admin ---
if (isset($_POST['add_admin']) || isset($_POST['update_admin'])) {
    $name = $_POST['name']; 
    $user = $_POST['username'];
    $phone = $_POST['phone']; 
    $dept_id = $_POST['dept_id'];
    $is_update = isset($_POST['update_admin']);
    $id = $is_update ? $_POST['id'] : 0;

    // 1. Check if Username already exists (excluding current admin if updating)
    $user_check = $conn->prepare("SELECT id FROM admins WHERE username = ? " . ($is_update ? "AND id != ?" : ""));
    if ($is_update) {
        $user_check->bind_param("si", $user, $id);
    } else {
        $user_check->bind_param("s", $user);
    }
    $user_check->execute();
    if ($user_check->get_result()->num_rows > 0) {
        $error_message = "Error: Username '$user' is already taken!";
    } else {
        // 2. Check if Department already has an admin (strictly 1 admin per dept)
        $dept_check = $conn->prepare("SELECT id FROM admins WHERE department_id = ? " . ($is_update ? "AND id != ?" : ""));
        if ($is_update) {
            $dept_check->bind_param("ii", $dept_id, $id);
        } else {
            $dept_check->bind_param("i", $dept_id);
        }
        $dept_check->execute();
        if ($dept_check->get_result()->num_rows > 0) {
            $error_message = "Error: This department already has an assigned administrator.";
        } else {
            // Proceed with Add or Update
            if (!$is_update) {
                // ADD LOGIC
                $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $img = "default.png";
                if (!empty($_FILES['image']['name'])) {
                    $img = time() . "_" . uniqid() . "." . strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    move_uploaded_file($_FILES['image']['tmp_name'], $adminDir . "/" . $img);
                }
                $stmt = $conn->prepare("INSERT INTO admins (name, username, phone, password, profile_image, department_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssi", $name, $user, $phone, $pass, $img, $dept_id);
                $stmt->execute();
            } else {
                // UPDATE LOGIC
                if (!empty($_POST['password'])) {
                    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    $conn->query("UPDATE admins SET password='$pass' WHERE id=$id");
                }
                if (!empty($_FILES['image']['name'])) {
                    $img = time() . "_" . uniqid() . "." . strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    move_uploaded_file($_FILES['image']['tmp_name'], $adminDir . "/" . $img);
                    $conn->query("UPDATE admins SET profile_image='$img' WHERE id=$id");
                }
                $stmt = $conn->prepare("UPDATE admins SET name=?, username=?, phone=?, department_id=? WHERE id=?");
                $stmt->bind_param("ssssi", $name, $user, $phone, $dept_id, $id);
                $stmt->execute();
            }
            header("Location: manage_admins.php");
            exit();
        }
    }
}

// --- Logic: Delete Admin ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM admins WHERE id = $id");
    header("Location: manage_admins.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Manage Admins</title>
</head>
<body class="bg-gray-100 flex">
    <?php include './sidebar.php'; ?>

    <main class="flex-1 p-10">
        <?php if($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
                <p class="font-bold">Process Stopped</p>
                <p><?= $error_message ?></p>
            </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Department Admins</h2>
            <button onclick="openAdminModal()" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 shadow-md">
                <i class="fas fa-user-plus mr-2"></i> Register New Admin
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">Administrator</th>
                        <th class="p-4">Phone Number</th>
                        <th class="p-4">Department</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php 
                    $res = $conn->query("SELECT admins.*, departments.name as dept_name FROM admins LEFT JOIN departments ON admins.department_id = departments.id ORDER BY admins.id DESC");
                    while($row = $res->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 flex items-center">
                            <img src="../uploads/admin/<?= $row['profile_image'] ?>" class="w-12 h-12 rounded-full mr-4 object-cover">
                            <div>
                                <p class="font-bold text-gray-800"><?= $row['name'] ?></p>
                                <p class="text-xs text-gray-500">@<?= $row['username'] ?></p>
                            </div>
                        </td>
                        <td class="p-4"><?= $row['phone'] ?></td>
                        <td class="p-4"><span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold"><?= $row['dept_name'] ?? 'Unassigned' ?></span></td>
                        <td class="p-4 text-center whitespace-nowrap">
                            <button onclick='editAdmin(<?= json_encode($row) ?>)' class="text-blue-500 hover:text-blue-700 mr-3"><i class="fas fa-edit"></i></button>
                            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Remove admin access?')" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="adminModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full shadow-2xl">
            <h3 id="adminModalTitle" class="text-xl font-bold mb-4 text-gray-800">Assign Admin</h3>
            <form id="adminForm" method="POST" enctype="multipart/form-data" class="space-y-3">
                <input type="hidden" name="id" id="admin_id">
                <input type="text" name="name" id="adm_name" placeholder="Full Name" class="w-full border p-3 rounded-lg" required>
                <input type="text" name="username" id="adm_user" placeholder="Username" class="w-full border p-3 rounded-lg" required>
                <input type="password" name="password" id="adm_pass" placeholder="Password" class="w-full border p-3 rounded-lg">
                <input type="text" name="phone" id="adm_phone" placeholder="Phone Number" class="w-full border p-3 rounded-lg">
                
                <label class="block text-sm font-semibold text-gray-600">Assign to Department</label>
                <select name="dept_id" id="adm_dept" class="w-full border p-3 rounded-lg bg-gray-50">
                    <option value="">-- Select Available Department --</option>
                    <?php 
                    // Only fetch departments that don't have an admin assigned
                    $depts = $conn->query("SELECT id, name FROM departments WHERE id NOT IN (SELECT department_id FROM admins WHERE department_id IS NOT NULL)");
                    while($d = $depts->fetch_assoc()): ?>
                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                    <?php endwhile; ?>
                </select>

                <div class="flex justify-end space-x-3 pt-4 border-t mt-4">
                    <button type="button" onclick="document.getElementById('adminModal').classList.add('hidden')" class="px-4 py-2 text-gray-400">Cancel</button>
                    <button type="submit" id="admin_submit_btn" name="add_admin" class="bg-green-600 text-white px-6 py-2 rounded-lg">Save Admin</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openAdminModal() {
        document.getElementById('adminForm').reset();
        document.getElementById('admin_id').value = "";
        document.getElementById('adminModalTitle').innerText = "Register New Admin";
        document.getElementById('admin_submit_btn').name = "add_admin";
        document.getElementById('adm_pass').required = true;
        
        // Show only available departments in dropdown
        refreshDeptDropdown(null); 
        
        document.getElementById('adminModal').classList.remove('hidden');
    }

    function editAdmin(data) {
        document.getElementById('adminModal').classList.remove('hidden');
        document.getElementById('adminModalTitle').innerText = "Edit Admin Settings";
        document.getElementById('admin_id').value = data.id;
        document.getElementById('adm_name').value = data.name;
        document.getElementById('adm_user').value = data.username;
        document.getElementById('adm_phone').value = data.phone;
        
        // When editing, we need the dropdown to show the current department PLUS available ones
        refreshDeptDropdown(data.department_id, data.dept_name);

        document.getElementById('adm_dept').value = data.department_id;
        document.getElementById('adm_pass').required = false;
        document.getElementById('admin_submit_btn').name = "update_admin";
    }

    // This helper dynamically adds the admin's current department to the dropdown so it's selectable
    function refreshDeptDropdown(currentDeptId, currentDeptName) {
        const select = document.getElementById('adm_dept');
        if(currentDeptId) {
            // Check if option already exists to avoid duplicates
            if(![...select.options].some(opt => opt.value == currentDeptId)) {
                const opt = document.createElement('option');
                opt.value = currentDeptId;
                opt.text = currentDeptName;
                select.add(opt);
            }
        }
    }
    </script>
</body>
</html>