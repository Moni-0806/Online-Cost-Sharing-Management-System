<?php
// Get the current filename to highlight the active link
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['is_admin'])) {
    header("Location: login.php");
    exit();
}
?>
<div class="w-64 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-800 text-white min-h-screen p-6 flex flex-col shadow-2xl">
<div class="mb-10 text-center">
    <div class="inline-block p-1 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 mb-4 shadow-lg">
        <img 
            src="../uploads/admin/<?=  $_SESSION['profile_img'] ?? 'default.png' ?>"
            alt="Profile"
            class="w-16 h-16 rounded-full object-cover border-4 border-slate-900"
            onerror="this.src='../uploads/admin/default.png'"
        >
    </div>

    <h1 class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">
        Admin Portal
    </h1>

    <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-2 font-bold">
        Department Management
    </p>
</div>


    <nav class="space-y-1 flex-1">
        
        <a href="dashboard.php" 
           class="flex items-center py-3.5 px-4 rounded-xl transition-all duration-300 group <?= ($current_page == 'dashboard.php') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white hover:translate-x-1' ?>">
            <i class="fas fa-th-large mr-3 w-5 text-center"></i> 
            <span class="font-semibold">Dashboard</span>
        </a>

        <div class="pt-6 pb-2 px-4">
            <span class="text-[10px] font-extrabold text-slate-600 uppercase tracking-widest">Management</span>
        </div>
        
        <a href="manage_students.php" 
           class="flex items-center py-3.5 px-4 rounded-xl transition-all duration-300 group <?= ($current_page == 'manage_students.php') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white hover:translate-x-1' ?>">
            <i class="fas fa-user-graduate mr-3 w-5 text-center"></i> 
            <span class="font-semibold">Students</span>
        </a>

        <a href="assign_costs.php" 
           class="flex items-center py-3.5 px-4 rounded-xl transition-all duration-300 group <?= ($current_page == 'assign_costs.php') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/50 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white hover:translate-x-1' ?>">
            <i class="fas fa-file-invoice-dollar mr-3 w-5 text-center"></i> 
            <span class="font-semibold">Assign Cost</span>
        </a>

    </nav>

    <div class="pt-6 border-t border-slate-700">
        <a href="logout.php" class="flex items-center py-3.5 px-4 rounded-xl text-red-400 hover:bg-red-900/30 hover:text-red-300 transition-all duration-300 group">
            <i class="fas fa-sign-out-alt mr-3 w-5 text-center group-hover:translate-x-1 transition-transform"></i> 
            <span class="font-semibold">Logout</span>
        </a>
    </div>
</div>