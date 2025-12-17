<?php
session_start();
require_once '../Config/db.php';
if (!isset($_SESSION['student_id'])) header("Location: login.php");

$sid = $_SESSION['student_id'];
$costs_query = "SELECT ac.*, cc.name as category_name 
                FROM assigned_costs ac 
                JOIN cost_categories cc ON ac.cost_type_id = cc.id 
                WHERE ac.student_id = $sid 
                ORDER BY ac.academic_year DESC";
$results = mysqli_query($conn, $costs_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>My Costs | Student Portal</title>
</head>
<body class="bg-gradient-to-br from-slate-50 to-cyan-50 flex">
    <!-- Sidebar -->
    <div class="w-64 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-800 min-h-screen text-white p-6 flex flex-col shadow-2xl">
        <div class="mb-10 text-center">
            <div class="inline-block p-1 rounded-full bg-gradient-to-r from-cyan-500 to-blue-500 mb-4 shadow-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-graduate text-white text-2xl"></i>
                </div>
            </div>
            <h1 class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                Student Portal
            </h1>
            <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-2 font-bold">
                Academic Management
            </p>
        </div>
        
        <nav class="space-y-2 flex-1">
            <a href="dashboard.php" class="flex items-center py-3.5 px-4 rounded-xl transition-all duration-300 group text-slate-400 hover:bg-slate-800 hover:text-white hover:translate-x-1">
                <i class="fas fa-user-circle mr-3 w-5 text-center"></i> 
                <span class="font-semibold">Profile</span>
            </a>
            <a href="my_costs.php" class="flex items-center py-3.5 px-4 rounded-xl transition-all duration-300 group bg-gradient-to-r from-cyan-600 to-blue-600 shadow-lg shadow-cyan-500/50 text-white">
                <i class="fas fa-file-invoice-dollar mr-3 w-5 text-center"></i> 
                <span class="font-semibold">My Costs</span>
            </a>
        </nav>
        
        <div class="pt-6 border-t border-slate-700">
            <a href="logout.php" class="flex items-center py-3.5 px-4 rounded-xl text-red-400 hover:bg-red-900/30 hover:text-red-300 transition-all duration-300 group">
                <i class="fas fa-sign-out-alt mr-3 w-5 text-center group-hover:translate-x-1 transition-transform"></i> 
                <span class="font-semibold">Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 p-10">
        <header class="mb-10">
            <h2 class="text-4xl font-extrabold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-2">Financial Overview</h2>
            <p class="text-slate-600">Track your academic costs and payment status</p>
        </header>

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-cyan-600 to-blue-600 px-8 py-6">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-receipt mr-3"></i>Cost Breakdown
                </h3>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gradient-to-r from-slate-50 to-slate-100">
                        <tr>
                            <th class="p-6 text-slate-700 font-bold uppercase tracking-wide text-sm">
                                <i class="fas fa-tag mr-2 text-cyan-600"></i>Category
                            </th>
                            <th class="p-6 text-center text-slate-700 font-bold uppercase tracking-wide text-sm">
                                <i class="fas fa-calendar mr-2 text-blue-600"></i>Academic Year
                            </th>
                            <th class="p-6 text-right text-slate-700 font-bold uppercase tracking-wide text-sm">
                                <i class="fas fa-dollar-sign mr-2 text-green-600"></i>Amount
                            </th>
                            <th class="p-6 text-center text-slate-700 font-bold uppercase tracking-wide text-sm">
                                <i class="fas fa-check-circle mr-2 text-purple-600"></i>Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php while($row = mysqli_fetch_assoc($results)): ?>
                        <tr class="hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 transition-all duration-300 group">
                            <td class="p-6 font-semibold text-slate-800 group-hover:text-cyan-700 transition">
                                <?php echo $row['category_name']; ?>
                            </td>
                            <td class="p-6 text-center text-slate-600 font-medium">
                                <span class="bg-slate-100 px-3 py-1 rounded-full text-sm">
                                    <?php echo $row['academic_year']; ?>
                                </span>
                            </td>
                            <td class="p-6 text-right">
                                <span class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                    $<?php echo number_format($row['amount'], 2); ?>
                                </span>
                            </td>
                            <td class="p-6 text-center">
                                <span class="px-4 py-2 rounded-full text-sm font-bold shadow-sm
                                    <?php echo $row['status'] == 'paid' 
                                        ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 border border-green-200' 
                                        : 'bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 border border-amber-200'; ?>">
                                    <i class="fas <?php echo $row['status'] == 'paid' ? 'fa-check-circle' : 'fa-clock'; ?> mr-1"></i>
                                    <?php echo strtoupper($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Summary Footer -->
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-8 py-6 border-t border-slate-200">
                <div class="flex justify-between items-center">
                    <p class="text-slate-600 font-medium">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Contact your department admin for payment assistance
                    </p>
                    <div class="flex space-x-4">
                        <span class="text-sm text-slate-500">
                            <i class="fas fa-circle text-green-500 mr-1"></i>Paid
                        </span>
                        <span class="text-sm text-slate-500">
                            <i class="fas fa-circle text-amber-500 mr-1"></i>Pending
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>