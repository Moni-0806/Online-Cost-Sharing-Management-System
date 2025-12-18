<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>OCSM - Online Cost Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        .hero-section {
            background-image: linear-gradient(rgba(17, 24, 39, 0.88), rgba(31, 41, 55, 0.92)), 
                              url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        .feature-icon {
            transition: all 0.4s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.15) rotate(5deg);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .delay-1 { animation-delay: 0.2s; opacity: 0; }
        .delay-2 { animation-delay: 0.4s; opacity: 0; }
        .delay-3 { animation-delay: 0.6s; opacity: 0; }
        .delay-4 { animation-delay: 0.8s; opacity: 0; }
    </style>
</head>
<body class="bg-gray-900">

    <!-- Header -->
    <header class="fixed w-full top-0 z-50 bg-gray-900/95 backdrop-blur-md border-b border-gray-800">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">OCSM</h1>
                        <p class="text-xs text-gray-400">Cost Management System</p>
                    </div>
                </div>
                <a href="login.php" 
                   class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section pt-32 pb-20">
        <div class="container mx-auto px-6">
            <!-- Hero Content -->
            <div class="text-center mb-16 floating">
                <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 fade-in-up">
                    Online Cost Sharing Management System
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto fade-in-up delay-1">
                    Streamline your institution's financial operations with cutting-edge technology
                </p>
                <div class="w-24 h-1.5 bg-gradient-to-r from-blue-500 to-purple-600 mx-auto rounded-full mb-12 fade-in-up delay-2"></div>
                
                <!-- CTA Button -->
                <div class="fade-in-up delay-3">
                    <p class="text-gray-400 mb-6 text-lg">Ready to transform your institution's financial management?</p>
                    <a href="login.php" 
                       class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold text-lg px-12 py-4 rounded-full shadow-2xl transform hover:scale-105 transition-all duration-300">
                        Get Started Now <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="mt-24">
                <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-12 fade-in-up">
                    System Features
                </h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
                <!-- Feature 1 -->
                <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 fade-in-up delay-1">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <i class="fas fa-users-cog text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 text-center">Student Management</h3>
                    <p class="text-gray-300 text-center text-sm leading-relaxed">
                        Efficiently manage student records, enrollment, and academic information in one centralized platform
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 fade-in-up delay-2">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <i class="fas fa-dollar-sign text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 text-center">Cost Tracking</h3>
                    <p class="text-gray-300 text-center text-sm leading-relaxed">
                        Track and manage all financial transactions, fees, and payments with real-time updates and accuracy
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 fade-in-up delay-3">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 text-center">Financial Reports</h3>
                    <p class="text-gray-300 text-center text-sm leading-relaxed">
                        Generate comprehensive financial reports and analytics for informed decision-making and planning
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 fade-in-up delay-4">
                    <div class="feature-icon w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3 text-center">Secure Access</h3>
                    <p class="text-gray-300 text-center text-sm leading-relaxed">
                        Role-based access control ensures data security with separate portals for admins, staff, and students
                    </p>
                </div>
            </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800 py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <!-- About -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">OCSM</h3>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Empowering educational institutions with modern financial management solutions for a better tomorrow.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-white font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="login.php" class="text-gray-400 hover:text-blue-400 transition text-sm"><i class="fas fa-chevron-right mr-2 text-xs"></i>Login Portal</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition text-sm"><i class="fas fa-chevron-right mr-2 text-xs"></i>About System</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-blue-400 transition text-sm"><i class="fas fa-chevron-right mr-2 text-xs"></i>Support</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-white font-bold mb-4">Contact Information</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="text-gray-400"><i class="fas fa-envelope mr-2 text-blue-500"></i>support@ocsm.edu</li>
                        <li class="text-gray-400"><i class="fas fa-phone mr-2 text-blue-500"></i>+251 900 000 000</li>
                        <li class="text-gray-400"><i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>Addis Ababa, Ethiopia</li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-500 text-sm">
                    &copy; 2025 OCSM - Online Cost Management System. All Rights Reserved.
                </p>
                <p class="text-gray-600 text-xs mt-2">
                    Designed & Developed with <i class="fas fa-heart text-red-500"></i> for Educational Excellence
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
