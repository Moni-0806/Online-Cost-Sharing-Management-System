<?php
session_start();
require '../Config/db.php';

if (!isset($_SESSION['is_superadmin'])) { header("Location: login.php"); exit(); }

// ADD/UPDATE LOGIC
if (isset($_POST['save_category'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    
    if (!empty($_POST['id'])) {
        $stmt = $conn->prepare("UPDATE cost_categories SET name=?, description=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $desc, $_POST['id']);
    } else {
        $stmt = $conn->prepare("INSERT INTO cost_categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $desc);
    }
    $stmt->execute();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM cost_categories WHERE id = $id");
    header("Location: manage_costs.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Cost Categories</title>
</head>
<body class="bg-gray-100 flex">
    <?php include './sidebar.php'; ?>

    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Cost Categories</h2>
            <button onclick="openCostModal()" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">+ New Category</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">Name</th>
                        <th class="p-4">Description</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res = $conn->query("SELECT * FROM cost_categories ORDER BY name ASC");
                    while($row = $res->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-semibold"><?= $row['name'] ?></td>
                        <td class="p-4 text-gray-500"><?= $row['description'] ?></td>
                        <td class="p-4 text-center">
                            <button onclick='editCost(<?= json_encode($row) ?>)' class="text-blue-600 mr-3"><i class="fas fa-edit"></i></button>
                            <a href="?delete=<?= $row['id'] ?>" class="text-red-500" onclick="return confirm('Delete category?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="costModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full">
            <h3 id="modalTitle" class="text-xl font-bold mb-4">Add Category</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" id="cost_id">
                <input type="text" name="name" id="cost_name" placeholder="Category Name" class="w-full border p-3 rounded-lg" required>
                <textarea name="description" id="cost_desc" placeholder="Description" class="w-full border p-3 rounded-lg"></textarea>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('costModal').classList.add('hidden')" class="text-gray-500">Cancel</button>
                    <button type="submit" name="save_category" class="bg-blue-600 text-white px-6 py-2 rounded-lg">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openCostModal() {
        document.getElementById('cost_id').value = "";
        document.getElementById('cost_name').value = "";
        document.getElementById('cost_desc').value = "";
        document.getElementById('modalTitle').innerText = "Add Category";
        document.getElementById('costModal').classList.remove('hidden');
    }
    function editCost(data) {
        document.getElementById('cost_id').value = data.id;
        document.getElementById('cost_name').value = data.name;
        document.getElementById('cost_desc').value = data.description;
        document.getElementById('modalTitle').innerText = "Edit Category";
        document.getElementById('costModal').classList.remove('hidden');
    }
    </script>
</body>
</html>