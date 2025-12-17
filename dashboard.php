<?php
session_start();
require_once '../Config/db.php';
if (!isset($_SESSION['student_id'])) header("Location: login.php");

$sid = $_SESSION['student_id'];
// Fetch student info and department name via JOIN
$sql = "SELECT s.*, d.name as dept_name FROM students s 
        JOIN departments d ON s.department_id = d.id 
        WHERE s.id = $sid";
$student = mysqli_fetch_assoc(mysqli_query($conn, $sql));

// Profile Image Upload Logic
if (isset($_POST['upload'])) {
    $dir = "../uploads/student/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    
    $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $new_name = "student_" . $sid . "_" . time() . "." . $ext;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $dir . $new_name)) {
        // We will store the image name in the tin_number column for now as a placeholder
        mysqli_query($conn, "UPDATE students SET tin_number = '$new_name' WHERE id = $sid");
        header("Location: dashboard.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Student Dashboard</title>
    <style>
        .student-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-cyan-50 flex">
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
            <a href="dashboard.php" class="flex items-center py-3.5 px-4 rounded-xl transition-all duration-300 group bg-gradient-to-r from-cyan-600 to-blue-600 shadow-lg shadow-cyan-500/50 text-white">
                <i class="fas fa-user-circle mr-3 w-5 text-center"></i> 
                <span class="font-semibold">Profile</span>
            </a>
            <a href="my_costs.php" class="flex items-center py-3.5 px-4 rounded-xl transition-all duration-300 group text-slate-400 hover:bg-slate-800 hover:text-white hover:translate-x-1">
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

    <main class="flex-1 p-10">
        <header class="mb-10">
            <h1 class="text-4xl font-extrabold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-2">Welcome, <?php echo $student['name']; ?>!</h1>
            <p class="text-slate-600">Manage your academic profile and view your financial information</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Photo Section -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-slate-200">
                <div class="text-center">
                    <div class="relative group mb-6">
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full blur-2xl opacity-30 group-hover:opacity-50 transition"></div>
                        <img src="<?php echo $student['tin_number'] ? '../uploads/student/'.$student['tin_number'] : '../uploads/student/default.png'; ?>" 
                             class="relative w-40 h-40 rounded-full mx-auto object-cover border-8 border-white shadow-2xl transition group-hover:scale-105 duration-300">
                        <div class="absolute bottom-2 right-2 bg-gradient-to-r from-cyan-500 to-blue-500 w-12 h-12 rounded-full flex items-center justify-center shadow-xl cursor-pointer group-hover:scale-110 transition">
                            <i class="fas fa-camera text-white"></i>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-800 mb-2"><?php echo $student['name']; ?></h3>
                    <span class="text-cyan-600 text-sm font-bold px-4 py-2 bg-cyan-50 rounded-full">
                        Student ID: <?php echo $student['id_number']; ?>
                    </span>
                    
                    <form method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Update Profile Photo</label>
                            <input type="file" name="image" class="w-full text-sm text-slate-600 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-cyan-50 file:to-blue-50 file:text-cyan-700 hover:file:from-cyan-100 hover:file:to-blue-100 cursor-pointer transition">
                        </div>
                        <button name="upload" class="w-full bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                            <i class="fas fa-upload mr-2"></i>Update Photo
                        </button>
                    </form>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-2xl p-10 border border-slate-200">
                <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center border-b-2 border-slate-200 pb-4">
                    <i class="fas fa-graduation-cap mr-3 text-cyan-600"></i>Academic Profile
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 p-6 rounded-2xl border border-cyan-100">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-id-card text-cyan-600 mr-3"></i>
                            <p class="text-slate-600 text-sm font-bold uppercase tracking-wide">Student ID</p>
                        </div>
                        <p class="text-xl font-bold text-slate-800"><?php echo $student['id_number']; ?></p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-building text-blue-600 mr-3"></i>
                            <p class="text-slate-600 text-sm font-bold uppercase tracking-wide">Department</p>
                        </div>
                        <p class="text-xl font-bold text-slate-800"><?php echo $student['dept_name']; ?></p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-6 rounded-2xl border border-indigo-100">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-calendar-alt text-indigo-600 mr-3"></i>
                            <p class="text-slate-600 text-sm font-bold uppercase tracking-wide">Batch Year</p>
                        </div>
                        <p class="text-xl font-bold text-slate-800"><?php echo $student['batch']; ?></p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-2xl border border-purple-100">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-phone text-purple-600 mr-3"></i>
                            <p class="text-slate-600 text-sm font-bold uppercase tracking-wide">Contact Phone</p>
                        </div>
                        <p class="text-xl font-bold text-slate-800"><?php echo $student['phone']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>