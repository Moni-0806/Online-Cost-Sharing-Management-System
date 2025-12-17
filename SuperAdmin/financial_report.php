<?php
session_start();
require_once '../Config/db.php';

// SuperAdmin security check
if (!isset($_SESSION['is_superadmin'])) {
    header("Location: login.php");
    exit();
}

/**
 * 1. Get Grand Totals
 * We calculate 'Pending' by summing everything that is NOT 'paid' 
 * (this includes 'unpaid' and 'partially_paid' statuses from your SQL)
 */
$totals_sql = "SELECT 
    SUM(amount) as grand_total, 
    SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN status != 'paid' THEN amount ELSE 0 END) as total_pending
    FROM assigned_costs";
$totals_result = mysqli_query($conn, $totals_sql);
$totals = mysqli_fetch_assoc($totals_result);

/**
 * 2. Get Department Breakdown
 */
$dept_sql = "SELECT 
    d.name as dept_name,
    SUM(ac.amount) as dept_total,
    SUM(CASE WHEN ac.status = 'paid' THEN ac.amount ELSE 0 END) as dept_paid,
    SUM(CASE WHEN ac.status != 'paid' THEN ac.amount ELSE 0 END) as dept_pending
    FROM departments d
    LEFT JOIN students s ON d.id = s.department_id
    LEFT JOIN assigned_costs ac ON s.id = ac.student_id
    GROUP BY d.id, d.name";
$dept_res = mysqli_query($conn, $dept_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Financial Report | SuperAdmin</title>
</head>
<body class="bg-slate-50 flex">

    <?php include './sidebar.php'; ?>

    <main class="flex-1 p-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-slate-800">Financial Summary</h2>
            <p class="text-slate-500">Accurate revenue tracking across all departments</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-slate-800">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Total Cost Assigned</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">$<?= number_format($totals['grand_total'] ?? 0, 2) ?></h3>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-600">
                <p class="text-green-600 text-xs font-bold uppercase tracking-widest">Total Paid</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">$<?= number_format($totals['total_paid'] ?? 0, 2) ?></h3>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-orange-500">
                <p class="text-orange-500 text-xs font-bold uppercase tracking-widest">Total Pending</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">$<?= number_format($totals['total_pending'] ?? 0, 2) ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-5 font-bold text-slate-700">Department</th>
                        <th class="p-5 font-bold text-slate-700">Total Costs</th>
                        <th class="p-5 font-bold text-green-700">Paid</th>
                        <th class="p-5 font-bold text-orange-700">Pending</th>
                        <th class="p-5 font-bold text-slate-700">Collection Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php while($row = mysqli_fetch_assoc($dept_res)): 
                        $total = $row['dept_total'] ?? 0;
                        $paid = $row['dept_paid'] ?? 0;
                        $pending = $row['dept_pending'] ?? 0;
                        $rate = ($total > 0) ? ($paid / $total) * 100 : 0;
                    ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-5 font-semibold text-slate-800"><?= htmlspecialchars($row['dept_name']) ?></td>
                        <td class="p-5 text-slate-600 font-medium">$<?= number_format($total, 2) ?></td>
                        <td class="p-5 text-green-600 font-bold">$<?= number_format($paid, 2) ?></td>
                        <td class="p-5 text-orange-600 font-bold">$<?= number_format($pending, 2) ?></td>
                        <td class="p-5">
                            <div class="flex items-center">
                                <div class="w-24 bg-slate-100 rounded-full h-1.5 mr-3">
                                    <div class="bg-green-600 h-1.5 rounded-full" style="width: <?= $rate ?>%"></div>
                                </div>
                                <span class="text-sm font-bold text-slate-500"><?= round($rate) ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>