<?php
session_start();
require '../Config/db.php';
require './department.php';

// Security: SuperAdmin Only
if (!isset($_SESSION['is_superadmin'])) {
    header("Location: login.php");
    exit();
}

// --- Logic: Add/Update Department ---
if (isset($_POST['add_dept']) || isset($_POST['update_dept'])) {
    $name = $_POST['name']; 
    $years = $_POST['years']; 
    $desc = $_POST['description'];

    if (isset($_POST['add_dept'])) {
        $stmt = $conn->prepare("INSERT INTO departments (name, years_to_complete, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $name, $years, $desc);
    } else {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE departments SET name=?, years_to_complete=?, description=? WHERE id=?");
        $stmt->bind_param("sisi", $name, $years, $desc, $id);
    }
    $stmt->execute();
    header("Location: manage_departments.php");
}

// --- Logic: Delete Department ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM departments WHERE id = $id");
    header("Location: manage_departments.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Manage Departments</title>
</head>
<body class="bg-gray-100 flex">

    <?php include './sidebar.php'; ?>

    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Departments</h2>
            <button onclick="openDeptModal()" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 shadow-md">
                <i class="fas fa-plus mr-2"></i> Add New Department
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4 font-semibold">Dept Name</th>
                        <th class="p-4 font-semibold">Duration</th>
                        <th class="p-4 font-semibold">Description</th>
                        <th class="p-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php 
                    $res = $conn->query("SELECT * FROM departments ORDER BY id DESC");
                    while($row = $res->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-medium"><?= $row['name'] ?></td>
                        <td class="p-4 text-blue-600"><?= $row['years_to_complete'] ?> Years</td>
                        <td class="p-4 text-gray-500 text-sm"><?= htmlspecialchars(substr($row['description'], 0, 60)) ?>...</td>
                        <td class="p-4 text-center">
                            <button onclick='editDept(<?= json_encode($row) ?>)' class="text-blue-500 hover:text-blue-700 mr-3"><i class="fas fa-edit"></i></button>
                            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete department?')" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

<div id="deptModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full shadow-2xl">
            <h3 id="deptModalTitle" class="text-xl font-bold mb-4">Add Department</h3>
            <form id="deptForm" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="dept_id">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Department Name</label>
                    <input list="predefined_depts" name="name" id="dept_name_input" 
                           placeholder="Type to search or add new..." 
                           class="w-full border p-3 rounded-lg outline-none focus:ring-2 focus:ring-blue-400" 
                           required autocomplete="off">
                    
                    <datalist id="predefined_depts">
                        <?php foreach($departments as $dept_item): ?>
                            <option value="<?= htmlspecialchars($dept_item) ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <p class="text-xs text-gray-400 mt-1">Select from list or type a unique name.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Years to Complete</label>
                    <input type="number" name="years" id="dept_years_input" placeholder="e.g. 4" class="w-full border p-3 rounded-lg outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="dept_desc_input" placeholder="Optional details..." class="w-full border p-3 rounded-lg h-24"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('deptModal').classList.add('hidden')" class="px-4 py-2 text-gray-500">Cancel</button>
                    <button type="submit" id="dept_submit_btn" name="add_dept" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openDeptModal() {
        document.getElementById('deptForm').reset();
        document.getElementById('dept_id').value = "";
        document.getElementById('deptModalTitle').innerText = "Add Department";
        document.getElementById('dept_submit_btn').name = "add_dept";
        document.getElementById('deptModal').classList.remove('hidden');
    }

    function editDept(data) {
        document.getElementById('deptModal').classList.remove('hidden');
        document.getElementById('deptModalTitle').innerText = "Edit Department";
        document.getElementById('dept_id').value = data.id;
        document.getElementById('dept_name_input').value = data.name;
        document.getElementById('dept_years_input').value = data.years_to_complete;
        document.getElementById('dept_desc_input').value = data.description;
        document.getElementById('dept_submit_btn').name = "update_dept";
    }
    </script>
</body>
</html>