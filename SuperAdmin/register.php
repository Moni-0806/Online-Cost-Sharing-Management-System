<?php
require '../Config/db.php';

$result = $conn->query("SELECT COUNT(*) AS total FROM SuperAdmin");
$row = $result->fetch_assoc();

if ($row['total'] > 0) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $name     = trim($_POST['name']);
    $phone    = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $baseDir       = "../uploads";
    $superAdminDir = $baseDir . "/superAdmin";

    if (!is_dir($superAdminDir)) {
        mkdir($superAdminDir, 0755, true);
    }

    $profile_image = "default.png";

    if (!empty($_FILES['profile_image']['name'])) {
        $tmp  = $_FILES['profile_image']['tmp_name'];
        $ext  = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allow = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allow)) {
            $profile_image = time() . "_" . uniqid() . "." . $ext;
            move_uploaded_file($tmp, $superAdminDir . "/" . $profile_image);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Insert SuperAdmin
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO SuperAdmin (username, password, name, phone, profile_image)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $username, $password, $name, $phone, $profile_image);

    if ($stmt->execute()) {
        header("Location: login.php?msg=AdminCreated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SuperAdmin Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 to-gray-800 flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
        
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">
            SuperAdmin Setup
        </h1>
        <p class="text-center text-gray-500 mb-6 text-sm">
            One-time configuration. Choose wisely.
        </p>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">

            <div>
                <label class="text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="name" required
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" required
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone"
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Profile Image</label>
                <input type="file" name="profile_image"
                    class="mt-1 block w-full text-sm text-gray-500
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-lg file:border-0
                           file:bg-blue-50 file:text-blue-700
                           hover:file:bg-blue-100">
            </div>



            <button type="submit"
                class="w-full mt-6 py-3 bg-blue-600 text-white font-semibold rounded-lg
                       hover:bg-blue-700 transition duration-200">
                Create SuperAdmin
            </button>
        </form>

        <p class="text-xs text-center text-gray-400 mt-6">
            This account can only be created once.
        </p>
    </div>

</body>
</html>
