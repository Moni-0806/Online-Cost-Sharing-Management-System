<?php
session_start();
require '../Config/db.php';

if (!isset($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}
$message = "";
$message_type = ""; 
$dept_id = $_SESSION['department_id'];
$admin_id = $_SESSION['admin_id'];

/*
|--------------------------------------------------------------------------
| LOGIC: TOGGLE STATUS (One-click)
|--------------------------------------------------------------------------
*/
if (isset($_GET['toggle_status'])) {
    $id = intval($_GET['toggle_status']);
    $new_status = $_GET['current'] == 'paid' ? 'unpaid' : 'paid';
    $conn->query("UPDATE assigned_costs SET status = '$new_status' WHERE id = $id");
    header("Location: assign_costs.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| LOGIC: DELETE
|--------------------------------------------------------------------------
*/
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $conn->query("DELETE FROM assigned_costs WHERE id = $del_id");
    header("Location: assign_costs.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| LOGIC: SAVE / UPDATE
|--------------------------------------------------------------------------
*/
if (isset($_POST['save_assignment'])) {
    $student_id = $_POST['student_id'];
    $cost_type_id = $_POST['cost_type_id'];
    $amount = $_POST['amount'];
    $acad_year = trim($_POST['academic_year']);
    $assign_id = $_POST['assign_id'];

    $check_query = "SELECT id FROM assigned_costs WHERE student_id = ? AND cost_type_id = ? AND academic_year = ?";
    if (!empty($assign_id)) { $check_query .= " AND id != ?"; }
    
    $check = $conn->prepare($check_query);
    if (!empty($assign_id)) { $check->bind_param("iisi", $student_id, $cost_type_id, $acad_year, $assign_id); } 
    else { $check->bind_param("iis", $student_id, $cost_type_id, $acad_year); }
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $message = "Error: This cost is already assigned to this student for $acad_year.";
        $message_type = "error";
    } else {
        if (!empty($assign_id)) {
            $stmt = $conn->prepare("UPDATE assigned_costs SET student_id=?, cost_type_id=?, amount=?, academic_year=? WHERE id=?");
            $stmt->bind_param("iidsi", $student_id, $cost_type_id, $amount, $acad_year, $assign_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO assigned_costs (student_id, cost_type_id, amount, academic_year, assigned_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iidsi", $student_id, $cost_type_id, $amount, $acad_year, $admin_id);
        }
        $stmt->execute();
        $message = "Success! Cost assignment saved."; $message_type = "success";
    }
}

// Fetch Summary Stats
$stats = $conn->query("SELECT 
    COUNT(DISTINCT student_id) as total_students,
    SUM(amount) as total_assigned,
    SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_collected,
    SUM(CASE WHEN status = 'unpaid' THEN amount ELSE 0 END) as total_pending
    FROM assigned_costs ac JOIN students s ON ac.student_id = s.id 
    WHERE s.department_id = $dept_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Cost Management</title>
    <script>
        function openModal() {
            document.getElementById('assignmentForm').reset();
            document.getElementById('form_assign_id').value = "";
            document.getElementById('modalTitle').innerText = "New Cost Assignment";
            document.getElementById('costModal').classList.remove('hidden');
        }
        function closeModal() { document.getElementById('costModal').classList.add('hidden'); }
        function editAssignment(data) {
            document.getElementById('costModal').classList.remove('hidden');
            document.getElementById('modalTitle').innerText = "Edit Assignment";
            document.getElementById('form_assign_id').value = data.id;
            document.getElementById('form_student_id').value = data.student_id;
            document.getElementById('form_cost_type_id').value = data.cost_type_id;
            document.getElementById('form_amount').value = data.amount;
            document.getElementById('form_academic_year').value = data.academic_year;
        }
    </script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 flex">
    <?php include './sidebar.php'; ?>

    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">Financial Dashboard</h2>
                <p class="text-slate-600">Manage cost assignments and track payment status</p>
            </div>
            <button onclick="openModal()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-bold shadow-xl shadow-blue-200 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                <i class="fas fa-plus mr-2"></i> Assign Cost
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-8 rounded-3xl border-2 border-slate-200 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-users text-3xl text-slate-600"></i>
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">STUDENTS</span>
                </div>
                <p class="text-3xl font-bold text-slate-900 mb-1"><?= number_format($stats['total_students']) ?></p>
                <p class="text-sm text-slate-500">Students Billed</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-3xl border-2 border-blue-200 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-file-invoice-dollar text-3xl text-blue-600"></i>
                    <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-bold">ASSIGNED</span>
                </div>
                <p class="text-3xl font-bold text-blue-900 mb-1"><?= number_format($stats['total_assigned'] ?? 0, 2) ?></p>
                <p class="text-sm text-blue-600">ETB Total Assigned</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-8 rounded-3xl border-2 border-emerald-200 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-check-circle text-3xl text-emerald-600"></i>
                    <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-xs font-bold">COLLECTED</span>
                </div>
                <p class="text-3xl font-bold text-emerald-900 mb-1"><?= number_format($stats['total_collected'] ?? 0, 2) ?></p>
                <p class="text-sm text-emerald-600">ETB Total Collected</p>
            </div>
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-8 rounded-3xl border-2 border-amber-200 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-clock text-3xl text-amber-600"></i>
                    <span class="bg-amber-100 text-amber-600 px-3 py-1 rounded-full text-xs font-bold">PENDING</span>
                </div>
                <p class="text-3xl font-bold text-amber-900 mb-1"><?= number_format($stats['total_pending'] ?? 0, 2) ?></p>
                <p class="text-sm text-amber-600">ETB Total Pending</p>
            </div>
        </div>

        <?php if($message): ?>
            <div class="p-6 mb-8 rounded-2xl border-l-4 shadow-lg <?= $message_type == 'error' ? 'bg-gradient-to-r from-red-50 to-red-100 border-red-500 text-red-700' : 'bg-gradient-to-r from-emerald-50 to-teal-50 border-emerald-500 text-emerald-700' ?>">
                <div class="flex items-center">
                    <i class="fas <?= $message_type == 'error' ? 'fa-exclamation-circle' : 'fa-check-circle' ?> mr-3 text-xl"></i>
                    <span class="font-semibold"><?= $message ?></span>
                </div>
            </div>
        <?php endif; ?>

<div class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
    <!-- Table Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
        <h3 class="text-xl font-bold text-white flex items-center">
            <i class="fas fa-table mr-3"></i>Cost Assignments
        </h3>
    </div>
    <table class="w-full text-left">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="p-4 text-xs font-bold text-slate-500 uppercase">Student / Category</th>
                <th class="p-4 text-xs font-bold text-slate-500 uppercase">Amount</th>
                <th class="p-4 text-xs font-bold text-slate-500 uppercase">Year</th>
                <th class="p-4 text-xs font-bold text-slate-500 uppercase">Status</th>
                <th class="p-4 text-xs font-bold text-slate-500 uppercase text-center">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php 
            $res = $conn->query("SELECT ac.*, s.name as s_name, s.id_number, ct.name as c_name 
                                 FROM assigned_costs ac 
                                 JOIN students s ON ac.student_id = s.id 
                                 JOIN cost_categories ct ON ac.cost_type_id = ct.id 
                                 WHERE s.department_id = $dept_id 
                                 ORDER BY s.name ASC, ac.id DESC");
            
            $rows = $res->fetch_all(MYSQLI_ASSOC);
            $current_student = "";
            $student_total = 0;
            $student_paid = 0;

            foreach ($rows as $index => $row):
                $is_new_student = ($row['s_name'] !== $current_student);
                
                // If it's a new student and NOT the first row, show the previous student's subtotal
                if ($is_new_student && $index > 0): ?>
                    <tr class="bg-slate-50/80 border-t-2">
                        <td class="p-4 font-bold text-slate-600 text-right uppercase text-[10px]">Student Total:</td>
                        <td class="p-4 font-black text-slate-900 border-t" colspan="1">
                            <?= number_format($student_total, 2) ?> ETB
                        </td>
                        <td class="p-4 text-xs" colspan="3">
                            <span class="text-emerald-600 font-bold">Paid: <?= number_format($student_paid, 2) ?></span> 
                            <span class="text-slate-300 mx-2">|</span>
                            <span class="text-amber-600 font-bold">Pending: <?= number_format($student_total - $student_paid, 2) ?></span>
                        </td>
                    </tr>
                <?php 
                    // Reset counters for the new student
                    $student_total = 0; 
                    $student_paid = 0;
                endif;

                $current_student = $row['s_name'];
                $student_total += $row['amount'];
                if($row['status'] == 'paid') $student_paid += $row['amount'];
                ?>

                <tr class="hover:bg-slate-50/30">
                    <td class="p-4">
                        <?php if($is_new_student): ?>
                            <p class="font-bold text-blue-700 mb-1"><?= $row['s_name'] ?></p>
                        <?php endif; ?>
                        <div class="flex items-center text-slate-600 ml-4">
                            <i class="fas fa-caret-right mr-2 text-slate-300"></i>
                            <span><?= $row['c_name'] ?></span>
                        </div>
                    </td>
                    <td class="p-4 font-medium text-slate-700"><?= number_format($row['amount'], 2) ?></td>
                    <td class="p-4 text-slate-500 text-sm"><?= $row['academic_year'] ?></td>
                    <td class="p-4">
                        <a href="?toggle_status=<?= $row['id'] ?>&current=<?= $row['status'] ?>" 
                           class="px-3 py-1 rounded-full text-[10px] font-bold uppercase transition-all <?= $row['status'] == 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                           <?= $row['status'] ?>
                        </a>
                    </td>
                    <td class="p-4 text-center">
                        <button onclick='editAssignment(<?= json_encode($row) ?>)' class="text-blue-400 hover:text-blue-600 mx-2"><i class="fas fa-edit text-xs"></i></button>
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete?')" class="text-slate-300 hover:text-red-500 mx-2"><i class="fas fa-trash text-xs"></i></a>
                    </td>
                </tr>

                <?php 
                // Handle the Subtotal for the very last student in the list
                if ($index === count($rows) - 1): ?>
                    <tr class="bg-slate-50/80 border-t-2">
                        <td class="p-4 font-bold text-slate-600 text-right uppercase text-[10px]">Student Total:</td>
                        <td class="p-4 font-black text-slate-900" colspan="1"><?= number_format($student_total, 2) ?> ETB</td>
                        <td class="p-4 text-xs" colspan="3">
                            <span class="text-emerald-600 font-bold">Paid: <?= number_format($student_paid, 2) ?></span> 
                            <span class="text-slate-300 mx-2">|</span>
                            <span class="text-amber-600 font-bold">Pending: <?= number_format($student_total - $student_paid, 2) ?></span>
                        </td>
                    </tr>
                <?php endif; 
            endforeach; ?>
        </tbody>
    </table>
</div>
    </main>

    <div id="costModal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-3xl p-10 max-w-2xl w-full shadow-2xl border border-slate-200">
            <h3 id="modalTitle" class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-8 flex items-center">
                <i class="fas fa-file-invoice-dollar mr-3 text-blue-600"></i>Assign Cost
            </h3>
            <form method="POST" id="assignmentForm" class="space-y-6">
                <input type="hidden" name="assign_id" id="form_assign_id">
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-user-graduate mr-2 text-blue-500"></i>Select Student</label>
                    <select name="student_id" id="form_student_id" class="w-full border-2 border-blue-200 bg-blue-50/50 p-4 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                        <?php 
                        $students = $conn->query("SELECT id, name FROM students WHERE department_id = $dept_id ORDER BY name ASC");
                        while($s = $students->fetch_assoc()) echo "<option value='{$s['id']}'>{$s['name']}</option>";
                        ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-tags mr-2 text-indigo-500"></i>Cost Category</label>
                    <select name="cost_type_id" id="form_cost_type_id" class="w-full border-2 border-indigo-200 bg-indigo-50/50 p-4 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" required>
                        <?php 
                        $costs = $conn->query("SELECT id, name FROM cost_categories ORDER BY name ASC");
                        while($c = $costs->fetch_assoc()) echo "<option value='{$c['id']}'>{$c['name']}</option>";
                        ?>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-dollar-sign mr-2 text-green-500"></i>Amount (ETB)</label>
                        <input type="number" step="0.01" name="amount" id="form_amount" class="w-full border-2 border-green-200 bg-green-50/50 p-4 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition" placeholder="0.00" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fas fa-calendar-alt mr-2 text-purple-500"></i>Academic Year</label>
                        <input type="text" name="academic_year" id="form_academic_year" class="w-full border-2 border-purple-200 bg-purple-50/50 p-4 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition" placeholder="2024/25" required>
                    </div>
                </div>
                
                <div class="flex space-x-4 pt-6">
                    <button type="button" onclick="closeModal()" class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-4 rounded-xl transition-all duration-300">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" name="save_assignment" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-xl shadow-xl shadow-blue-200 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-save mr-2"></i>Save Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>