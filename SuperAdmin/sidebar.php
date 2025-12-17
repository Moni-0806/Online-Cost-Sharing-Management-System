<?php
// Get the current filename to highlight the active link
$current_page = basename($_SERVER['PHP_SELF']);
if ($_SESSION['is_superadmin'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<div class="w-64 bg-slate-900 text-white min-h-screen p-6 flex flex-col shadow-xl">
<div class="mb-10 text-center">
    <div class="inline-block p-1 rounded-full bg-green-600/20 mb-3">
        <img 
            src="../uploads/superAdmin/<?=  $_SESSION['profile_image'] ?? 'default.png' ?>"
            alt="Profile"
            class="w-14 h-14 rounded-full object-cover border-2 border-green-400"
            onerror="this.src='../uploads/admin/default.png'"
        >
    </div>

    <h1 class="text-xl font-bold tracking-tight">
        SuperAdminPortal
    </h1>

    <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">
         Management
    </p>
</div>


    <nav class="space-y-1 flex-1">
        
        <a href="dashboard.php" 
           class="flex items-center py-3 px-4 rounded-xl transition <?= ($current_page == 'dashboard.php') ? 'bg-green-600 shadow-lg shadow-green-600/30 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <i class="fas fa-th-large mr-3 w-5 text-center"></i> 
            <span class="font-medium">Dashboard</span>
        </a>

        <div class="pt-6 pb-2 px-4">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Management</span>
        </div>
        
        <a href="manage_departments.php" 
           class="flex items-center py-3 px-4 rounded-xl transition <?= ($current_page == 'manage_students.php') ? 'bg-green-600 shadow-lg text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
           <i class="fas fa-building mr-3 w-5 text-center"></i> 
            <span class="font-medium">Departments</span>
        </a>

        <a href="manage_admins.php" 
           class="flex items-center py-3 px-4 rounded-xl transition <?= ($current_page == 'assign_costs.php') ? 'bg-green-600 shadow-lg text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <i class="fas fa-users-cog mr-3 w-5 text-center"></i> 
            <span class="font-medium">Manage Admins</span>
        </a>
        <a href="manage_costs.php" 
           class="flex items-center py-3 px-4 rounded-xl transition <?= ($current_page == 'assign_costs.php') ? 'bg-green-600 shadow-lg text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <i class="fas fa-file-invoice-dollar mr-3 w-5 text-center"></i> 
            <span class="font-medium">Cost Categories</span>
        </a>

        <a href="financial_report.php" 
           class="flex items-center py-3 px-4 rounded-xl transition <?= ($current_page == 'financial_report.php') ? 'bg-green-600 shadow-lg text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
            <i class="fas fa-chart-line mr-3 text-blue-400"></i> 
            <span>Financial Reports</span>
        </a>

    </nav>

    <div class="pt-6 border-t border-slate-800">
        <a href="logout.php" class="flex items-center py-3 px-4 rounded-xl text-red-400 hover:bg-red-900/20 transition group">
            <i class="fas fa-sign-out-alt mr-3 w-5 text-center group-hover:translate-x-1 transition-transform"></i> 
            <span class="font-medium">Logout</span>
        </a>
    </div>
</div>