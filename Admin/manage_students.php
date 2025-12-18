<?php
session_start();
require '../Config/db.php';

if (!isset($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$dept_id = $_SESSION['department_id']; 

/*
|--------------------------------------------------------------------------
| LOGIC: ADD / UPDATE STUDENT
|--------------------------------------------------------------------------
*/
if (isset($_POST['save_student'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $id_num = $_POST['id_number'];
    $batch = $_POST['batch'];
    $region = $_POST['region'];
    $woreda = $_POST['woreda'];
    $tin = $_POST['tin_number'];
    $username = $_POST['username'];

    if (!empty($_POST['student_id'])) {
        // UPDATE EXISTING
        $id = $_POST['student_id'];
        // We only update password if a new one is provided
        $stmt = $conn->prepare("UPDATE students SET name=?, phone=?, id_number=?, batch=?, region=?, woreda=?, tin_number=?, username=? WHERE id=? AND department_id=?");
        $stmt->bind_param("sssissssii", $name, $phone, $id_num, $batch, $region, $woreda, $tin, $username, $id, $dept_id);
        $action = "updated";
    } else {
        // INSERT NEW
        $stmt = $conn->prepare("INSERT INTO students (name, phone, id_number, department_id, batch, region, woreda, tin_number, username) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiissss", $name, $phone, $id_num, $dept_id, $batch, $region, $woreda, $tin, $username);
        $action = "registered";
    }

    if ($stmt->execute()) {
        $message = "Student $action successfully!";
    } else {
        $message = "Error: Process failed. Check if ID Number or Username is unique.";
    }
}

// --- Logic: Delete Student ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM students WHERE id = $id AND department_id = $dept_id");
    header("Location: manage_students.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Manage Students</title>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 flex">

    <?php include './sidebar.php'; ?>

    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Student Records</h2>
                <p class="text-slate-600">Manage student registrations and academic information</p>
            </div>
            <button onclick="openModal()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-bold shadow-xl shadow-blue-200 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                <i class="fas fa-plus mr-2"></i> Register Student
            </button>
        </div>

        <?php if($message): ?>
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-l-4 border-emerald-500 text-emerald-700 p-5 mb-8 rounded-r-xl shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <span class="font-semibold"><?= $message ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-users mr-3"></i>Student Directory
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="p-6 text-slate-700 font-bold uppercase tracking-wide text-sm">
                                <i class="fas fa-id-card mr-2 text-blue-600"></i>ID Number
                            </th>
                            <th class="p-6 text-slate-700 font-bold uppercase tracking-wide text-sm">
                                <i class="fas fa-user mr-2 text-indigo-600"></i>Username
                            </th>
                            <th class="p-6 text-slate-700 font-bold uppercase tracking-wide text-sm">
                                <i class="fas fa-user-graduate mr-2 text-purple-600"></i>Full Name
                            </th>
                            <th class="p-6 text-slate-700 font-bold uppercase tracking-wide text-sm">
                                <i class="fas fa-phone mr-2 text-green-600"></i>Phone
                            </th>
                            <th class="p-6 text-center text-slate-700 font-bold uppercase tracking-wide text-sm">
                                <i class="fas fa-cogs mr-2 text-orange-600"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php 
                        $res = $conn->query("SELECT * FROM students WHERE department_id = $dept_id ORDER BY id DESC");
                        while($row = $res->fetch_assoc()): ?>
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 group">
                            <td class="p-6 font-mono font-bold text-blue-600 group-hover:text-blue-700 transition">
                                <span class="bg-blue-100 px-3 py-1 rounded-full text-sm">
                                    <?= $row['id_number'] ?>
                                </span>
                            </td>
                            <td class="p-6 font-semibold text-slate-600 group-hover:text-indigo-700 transition">
                                <?= $row['username'] ?>
                            </td>
                            <td class="p-6 font-semibold text-slate-800 group-hover:text-purple-700 transition">
                                <?= $row['name'] ?>
                            </td>
                            <td class="p-6 text-slate-600 group-hover:text-green-700 transition">
                                <?= $row['phone'] ?>
                            </td>
                            <td class="p-6 text-center">
                                <div class="flex justify-center space-x-3">
                                    <button onclick='editStudent(<?= json_encode($row) ?>)' class="bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 p-2 rounded-lg transition-all duration-300 transform hover:scale-110">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this student?')" class="bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 p-2 rounded-lg transition-all duration-300 transform hover:scale-110">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="studentModal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-3xl p-10 max-w-3xl w-full shadow-2xl overflow-y-auto max-h-[90vh] border border-slate-200">
            <h3 id="modalTitle" class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-8 flex items-center">
                <i class="fas fa-user-plus mr-3 text-blue-600"></i>Register Student
            </h3>
            
            <form method="POST" id="studentForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="student_id" id="form_student_id">
                
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-user mr-2 text-blue-500"></i>Full Name</label>
                    <input type="text" name="name" id="form_name" class="w-full border-2 border-slate-200 bg-slate-50 p-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                </div>
                
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 border-2 border-blue-100 mb-4">
                    <div class="col-span-2 text-blue-800 font-bold text-sm uppercase tracking-wider flex items-center">
                        <i class="fas fa-key mr-2"></i>Login Credentials
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-at mr-2 text-indigo-500"></i>Username</label>
                        <input type="text" name="username" id="form_username" class="w-full border-2 border-indigo-200 bg-white p-4 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-id-card mr-2 text-purple-500"></i>ID Number (Used as Password)</label>
                    <input type="text" name="id_number" id="form_id_number" onkeyup="syncUsername(this.value)" class="w-full border-2 border-purple-200 bg-purple-50/50 p-4 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-phone mr-2 text-green-500"></i>Phone Number</label>
                    <input type="text" name="phone" id="form_phone" class="w-full border-2 border-green-200 bg-green-50/50 p-4 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-calendar mr-2 text-orange-500"></i>Batch Year</label>
                    <input type="number" name="batch" id="form_batch" class="w-full border-2 border-orange-200 bg-orange-50/50 p-4 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-receipt mr-2 text-pink-500"></i>TIN Number</label>
                    <input type="text" name="tin_number" id="form_tin" class="w-full border-2 border-pink-200 bg-pink-50/50 p-4 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-map-marker-alt mr-2 text-cyan-500"></i>Region</label>
                    <input type="text" name="region" id="form_region" class="w-full border-2 border-cyan-200 bg-cyan-50/50 p-4 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-location-arrow mr-2 text-teal-500"></i>Woreda</label>
                    <input type="text" name="woreda" id="form_woreda" class="w-full border-2 border-teal-200 bg-teal-50/50 p-4 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                </div>

                <div class="col-span-2 mt-8 flex space-x-4">
                    <button type="button" onclick="closeModal()" class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-4 rounded-xl transition-all duration-300">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" name="save_student" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-xl shadow-xl shadow-blue-200 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-save mr-2"></i>Save Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const modal = document.getElementById('studentModal');
    const form = document.getElementById('studentForm');

    function openModal() {
        form.reset();
        document.getElementById('form_student_id').value = "";
        document.getElementById('modalTitle').innerText = "Register New Student";
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    // Automatically fill username as the ID Number for convenience
    function syncUsername(val) {
        const idField = document.getElementById('form_student_id').value;
        if(!idField) { // Only auto-sync if it's a new registration
            document.getElementById('form_username').value = val;
        }
    }

    function editStudent(data) {
        openModal();
        document.getElementById('modalTitle').innerText = "Edit Student Profile";
        
        document.getElementById('form_student_id').value = data.id;
        document.getElementById('form_name').value = data.name;
        document.getElementById('form_username').value = data.username;
        document.getElementById('form_id_number').value = data.id_number;
        document.getElementById('form_phone').value = data.phone;
        document.getElementById('form_batch').value = data.batch;
        document.getElementById('form_tin').value = data.tin_number;
        document.getElementById('form_region').value = data.region;
        document.getElementById('form_woreda').value = data.woreda;
    }
    </script>
</body>
</html>